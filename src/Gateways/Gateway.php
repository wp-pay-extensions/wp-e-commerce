<?php
/**
 * Gateway.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\WPeCommerce
 */

namespace Pronamic\WordPress\Pay\Extensions\WPeCommerce\Gateways;

use Pronamic\WordPress\Money\TaxedMoney;
use Pronamic\WordPress\Pay\Admin\AdminModule;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Customer;
use Pronamic\WordPress\Pay\Extensions\WPeCommerce\WPeCommerce;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Plugin;
use wpsc_merchant;

/**
 * Title: WP e-Commerce Pronamic gateway
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.2
 * @since   1.0.0
 */
class Gateway extends wpsc_merchant {
	/**
	 * Payment method
	 *
	 * @var null|string $payment_method
	 */
	const PAYMENT_METHOD = null;

	/**
	 * Construct and initialize an Pronamic merchant class
	 *
	 * @param null|string $purchase_id  Purchase ID.
	 * @param bool        $is_receiving Whether or not is receiving.
	 */
	public function __construct( $purchase_id = null, $is_receiving = false ) {
		parent::__construct( $purchase_id, $is_receiving );

		$this->name = PaymentMethods::get_name( static::PAYMENT_METHOD, __( 'Pronamic', 'pronamic_ideal' ) );
	}

	/**
	 * Get option config ID.
	 *
	 * @return string
	 */
	private static function get_option_config_id() {
		$method = static::PAYMENT_METHOD;
		$method = ( null === $method ) ? 'pronamic' : $method;

		$name = sprintf(
			'pronamic_pay_%s_wpsc_config_id',
			$method
		);

		return $name;
	}

	/**
	 * Get config ID.
	 *
	 * @return int
	 */
	private static function get_config_id() {
		$name = self::get_option_config_id();

		$config_id = get_option( $name, null );

		return (int) $config_id;
	}

	/**
	 * Submit to gateway
	 */
	public function submit() {
		global $wpsc_cart;

		$config_id = self::get_config_id();

		/*
		 * Set purchase processed to 'order_received' (2).
		 *
		 * @link https://plugins.trac.wordpress.org/browser/wp-e-commerce/tags/3.8.7.6.2/wpsc-includes/merchant.class.php#L301
		 * @link https://plugins.trac.wordpress.org/browser/wp-e-commerce/tags/3.8.7.6.2/wpsc-core/wpsc-functions.php#L115
		 */
		$this->set_purchase_processed_by_purchid( WPeCommerce::PURCHASE_STATUS_ORDER_RECEIVED );

		$gateway = Plugin::get_gateway( $config_id );

		if ( ! $gateway ) {
			return;
		}

		$payment_method = static::PAYMENT_METHOD;

		if ( null !== $payment_method ) {
			$gateway->set_payment_method( $payment_method );
		}

		// Payment.
		$payment = new Payment();

		// Customer.
		$customer = new Customer();

		$payment->set_customer( $customer );

		if ( isset( $this->cart_data['email_address'] ) ) {
			$customer->set_email( $this->cart_data['email_address'] );
		}

		// Billing address.
		$billing_address = WPeCommerce::get_address_from_cart_data( $this->cart_data, 'billing_address' );

		if ( null !== $billing_address ) {
			$payment->set_billing_address( $billing_address );

			$billing_address->set_email( $customer->get_email() );

			// Name.
			$name = $customer->get_name();

			if ( null === $name ) {
				$customer->set_name( $billing_address->get_name() );
			}

			// Phone.
			$phone = $customer->get_phone();

			if ( null === $phone ) {
				$customer->set_phone( $billing_address->get_phone() );
			}
		}

		// Shipping address.
		$shipping_address = WPeCommerce::get_address_from_cart_data( $this->cart_data, 'shipping_address' );

		if ( null !== $shipping_address ) {
			$payment->set_shipping_address( $shipping_address );

			$shipping_address->set_email( $customer->get_email() );

			// Name.
			$name = $customer->get_name();

			if ( null === $name ) {
				$customer->set_name( $shipping_address->get_name() );
			}

			// Phone.
			$phone = $customer->get_phone();

			if ( null === $phone ) {
				$customer->set_phone( $shipping_address->get_phone() );
			}
		}

		// Payment lines.
		$payment_lines = WPeCommerce::get_payment_lines_from_cart_items( $wpsc_cart->cart_items, $this->cart_data );

		if ( null !== $payment_lines ) {
			$payment->set_lines( $payment_lines );
		}

		// Other.
		$payment->title = sprintf(
			/* translators: %s: payment data title */
			__( 'Payment for %s', 'pronamic_ideal' ),
			sprintf(
				/* translators: %s: order id */
				__( 'WP eCommerce order %s', 'pronamic_ideal' ),
				$this->purchase_id
			)
		);

		$payment->config_id   = $config_id;
		$payment->order_id    = $this->purchase_id;
		$payment->description = sprintf(
			/* translators: %s: purchase id */
			__( 'Order %s', 'pronamic_ideal' ),
			$this->purchase_id
		);
		$payment->source    = 'wp-e-commerce';
		$payment->source_id = $this->purchase_id;

		if ( isset( $this->cart_data['email_address'] ) ) {
			$payment->email = $this->cart_data['email_address'];
		}

		$payment->method = $payment_method;

		// Set total amount.
		$payment->set_total_amount(
			new TaxedMoney(
				$this->cart_data['total_price'],
				WPeCommerce::get_currency_from_cart_data( $this->cart_data ),
				WPeCommerce::get_total_tax()
			)
		);

		// Start payment.
		$payment = Plugin::start_payment( $payment );

		// Meta.
		if ( isset( $this->purchase_id ) ) {
			$payment->set_meta( 'wpsc_purchase_id', $this->purchase_id );
		}

		if ( isset( $this->cart_data['session_id'] ) ) {
			$payment->set_meta( 'wpsc_session_id', $this->cart_data['session_id'] );
		}

		// Handle errors.
		$error = $gateway->get_error();

		if ( is_wp_error( $error ) ) {
			Plugin::render_errors( $error );

			return;
		}

		// Redirect.
		$gateway->redirect( $payment );
	}

	/**
	 * Admin configuration form
	 */
	public static function admin_config_form() {
		$name = self::get_option_config_id();

		$html = '';

		$html .= '<tr>';
		$html .= '	<td class="wpsc_CC_details">';
		$html .= '		' . __( 'Configuration', 'pronamic_ideal' );
		$html .= '	</td>';
		$html .= '	<td>';
		$html .= AdminModule::dropdown_configs(
			array(
				'name' => $name,
				'echo' => false,
			)
		);
		$html .= '	</td>';
		$html .= '</tr>';

		return $html;
	}

	/**
	 * Admin config submit.
	 *
	 * @return void
	 */
	public static function admin_config_submit() {
		// Config ID.
		$name = self::get_option_config_id();

		if ( ! filter_has_var( INPUT_POST, $name ) ) {
			return;
		}

		$config_id = filter_input( INPUT_POST, $name, FILTER_SANITIZE_STRING );

		update_option( $name, $config_id );
	}

	/**
	 * Advanced inputs
	 *
	 * @return null|string
	 */
	public static function advanced_inputs() {
		$config_id = self::get_config_id();

		$gateway = Plugin::get_gateway( $config_id );

		if ( ! $gateway ) {
			return null;
		}

		$payment_method = static::PAYMENT_METHOD;

		if ( null !== $payment_method ) {
			$gateway->set_payment_method( $payment_method );
		}

		return $gateway->get_input_html();
	}
}
