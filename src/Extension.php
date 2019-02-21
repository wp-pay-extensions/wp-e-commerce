<?php
/**
 * Extension.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\WPeCommerce
 */

namespace Pronamic\WordPress\Pay\Extensions\WPeCommerce;

use Pronamic\WordPress\Pay\Core\Statuses;
use Pronamic\WordPress\Pay\Extensions\WPeCommerce\Gateways\Gateway;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Plugin;

/**
 * Title: WP eCommerce extension
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.2
 * @since   1.0.0
 */
class Extension {
	/**
	 * Slug
	 *
	 * @var string
	 */
	const SLUG = 'wp-e-commerce';

	/**
	 * Option for payment method
	 *
	 * @var string
	 */
	const OPTION_PRONAMIC_PAYMENT_METHOD = 'pronamic_pay_pronamic_wpsc_payment_method';

	/**
	 * Bootstrap
	 */
	public static function bootstrap() {
		// Add gateways.
		add_filter( 'wpsc_merchants_modules', array( __CLASS__, 'merchants_modules' ) );

		// Save gateway options.
		add_action( 'wpsc_submit_gateway_options', array( __CLASS__, 'submit_gateway_options' ) );

		// Update payment status.
		add_action( 'pronamic_payment_status_update_' . self::SLUG, array( __CLASS__, 'status_update' ), 10, 2 );

		// Filters.
		add_filter( 'pronamic_payment_redirect_url_' . self::SLUG, array( __CLASS__, 'redirect_url' ), 10, 2 );
		add_filter( 'pronamic_payment_source_text_' . self::SLUG, array( __CLASS__, 'source_text' ), 10, 2 );
		add_filter( 'pronamic_payment_source_description_' . self::SLUG, array( __CLASS__, 'source_description' ), 10, 2 );
		add_filter( 'pronamic_payment_source_url_' . self::SLUG, array( __CLASS__, 'source_url' ), 10, 2 );
	}

	/**
	 * Merchants modules
	 *
	 * @param array $gateways Gateways.
	 *
	 * @return array
	 */
	public static function merchants_modules( $gateways = array() ) {
		global $nzshpcrt_gateways, $num, $wpsc_gateways, $gateway_checkout_form_fields;

		$classes = array(
			'Gateway'             => array(
				'name'         => __( 'Pronamic', 'pronamic_ideal' ),
				'display_name' => __( 'Pronamic', 'pronamic_ideal' ),
				'internalname' => 'wpsc_merchant_pronamic',
			),
			'AfterPayGateway'     => array(
				'name'         => __( 'Pronamic - AfterPay', 'pronamic_ideal' ),
				'display_name' => __( 'AfterPay', 'pronamic_ideal' ),
				'internalname' => 'wpsc_merchant_pronamic_afterpay',
			),
			'BancontactGateway'   => array(
				'name'         => __( 'Pronamic - Bancontact', 'pronamic_ideal' ),
				'display_name' => __( 'Bancontact', 'pronamic_ideal' ),
				'internalname' => 'wpsc_merchant_pronamic_bancontact',
			),
			'BankTransferGateway' => array(
				'name'         => __( 'Pronamic - Bank Transfer', 'pronamic_ideal' ),
				'display_name' => __( 'Bank Transfer', 'pronamic_ideal' ),
				'internalname' => 'wpsc_merchant_pronamic_bank_transfer',
			),
			'CreditCardGateway'   => array(
				'name'         => __( 'Pronamic - Credit Card', 'pronamic_ideal' ),
				'display_name' => __( 'Credit Card', 'pronamic_ideal' ),
				'internalname' => 'wpsc_merchant_pronamic_credit_card',
			),
			'FocumGateway'        => array(
				'name'         => __( 'Pronamic - Focum', 'pronamic_ideal' ),
				'display_name' => __( 'Focum', 'pronamic_ideal' ),
				'internalname' => 'wpsc_merchant_pronamic_focum',
			),
			'GiropayGateway'      => array(
				'name'         => __( 'Pronamic - Giropay', 'pronamic_ideal' ),
				'display_name' => __( 'Giropay', 'pronamic_ideal' ),
				'internalname' => 'wpsc_merchant_pronamic_giropay',
			),
			'IDealGateway'        => array(
				'name'         => __( 'Pronamic - iDEAL', 'pronamic_ideal' ),
				'image'        => plugins_url( '/images/ideal/icon-32x32.png', Plugin::$file ),
				'display_name' => __( 'iDEAL', 'pronamic_ideal' ),
				'internalname' => 'wpsc_merchant_pronamic_ideal',
			),
			'MaestroGateway'      => array(
				'name'         => __( 'Pronamic - Maestro', 'pronamic_ideal' ),
				'display_name' => __( 'Maestro', 'pronamic_ideal' ),
				'internalname' => 'wpsc_merchant_pronamic_maestro',
			),
			'PayPalGateway'       => array(
				'name'         => __( 'Pronamic - PayPal', 'pronamic_ideal' ),
				'display_name' => __( 'PayPal', 'pronamic_ideal' ),
				'internalname' => 'wpsc_merchant_pronamic_paypal',
			),
			'SofortGateway'       => array(
				'name'         => __( 'Pronamic - SOFORT', 'pronamic_ideal' ),
				'display_name' => __( 'SOFORT', 'pronamic_ideal' ),
				'internalname' => 'wpsc_merchant_pronamic_sofort',
			),
		);

		foreach ( $classes as $class => $args ) {
			$class_name = sprintf(
				__NAMESPACE__ . '\Gateways\%s',
				$class
			);

			$args = wp_parse_args(
				$args,
				array(
					'api_version'            => 2.0,
					'class_name'             => $class_name,
					'has_recurring_billing'  => false,
					'wp_admin_cannot_cancel' => false,
					'requirements'           => array(
						'php_version'   => 5.3,
						'extra_modules' => array(),
					),
					'form'                   => $class_name . '::admin_config_form',
					'submit_function'        => $class_name . '::admin_config_submit',
				)
			);

			$gateways[] = $args;
		}

		$gateway_checkout_form_fields['wpsc_merchant_pronamic']       = Gateways\Gateway::advanced_inputs();
		$gateway_checkout_form_fields['wpsc_merchant_pronamic_ideal'] = Gateways\IDealGateway::advanced_inputs();

		return $gateways;
	}

	/**
	 * Process gateway options submit.
	 */
	public static function submit_gateway_options() {
		// Get gateways.
		$gateways = self::merchants_modules();

		foreach ( $gateways as $gateway ) {
			if ( ! isset( $gateway['submit_function'] ) ) {
				continue;
			}

			// Call admin config submit function for gateway.
			call_user_func( $gateway['submit_function'] );
		}
	}

	/**
	 * Update lead status of the specified payment
	 *
	 * @param Payment $payment      Payment.
	 * @param bool    $can_redirect Whether or not to redirect.
	 */
	public static function status_update( Payment $payment, $can_redirect = false ) {
		$merchant = new Gateway( $payment->get_source_id() );

		switch ( $payment->status ) {
			case Statuses::CANCELLED:
				$merchant->set_purchase_processed_by_purchid( WPeCommerce::PURCHASE_STATUS_INCOMPLETE_SALE );

				break;

			case Statuses::EXPIRED:
			case Statuses::FAILURE:
				break;

			case Statuses::SUCCESS:
				/*
				 * Transactions results
				 *
				 * @link https://github.com/wp-e-commerce/WP-e-Commerce/blob/v3.8.9.5/wpsc-merchants/paypal-pro.merchant.php#L303
				 */
				$session_id = $payment->get_meta( 'wpsc_session_id' );

				transaction_results( $session_id );

				$merchant->set_purchase_processed_by_purchid( WPeCommerce::PURCHASE_STATUS_ACCEPTED_PAYMENT );

				break;

			case Statuses::OPEN:
			default:
				break;
		}
	}

	/**
	 * Payment redirect URL filter.
	 *
	 * @param string  $url     Redirect URL.
	 * @param Payment $payment Payment.
	 *
	 * @return string
	 */
	public static function redirect_url( $url, Payment $payment ) {
		// URL arguments.
		$args = array(
			'sessionid' => $payment->get_meta( 'wpsc_session_id' ),
			'gateway'   => 'wpsc_merchant_pronamic',
		);

		switch ( $payment->status ) {
			case Statuses::CANCELLED:
				/*
				 * Remove 'sessionid' paramater from the transaction URL, so customers
				 * will get a message 'Sorry your transaction was not accepted.'.
				 *
				 * @link https://plugins.trac.wordpress.org/browser/wp-e-commerce/tags/3.8.8.3/wpsc-theme/functions/wpsc-transaction_results_functions.php#L94
				 */
				unset( $args['sessionid'] );

				$args['return'] = 'cancel';

				break;
			case Statuses::EXPIRED:
			case Statuses::FAILURE:
				/*
				 * Remove 'sessionid' paramater from the transaction URL, so customers
				 * will get a message 'Sorry your transaction was not accepted.'.
				 *
				 * @link https://plugins.trac.wordpress.org/browser/wp-e-commerce/tags/3.8.8.3/wpsc-theme/functions/wpsc-transaction_results_functions.php#L94
				 */
				unset( $args['sessionid'] );

				$args['return'] = 'error';

				break;
			case Statuses::SUCCESS:
			case Statuses::OPEN:
			default:
				break;
		}

		$url = add_query_arg(
			$args,
			get_option( 'transact_url' )
		);

		return $url;
	}

	/**
	 * Source text.
	 *
	 * @param string  $text    Source text.
	 * @param Payment $payment Payment.
	 *
	 * @return string
	 */
	public static function source_text( $text, Payment $payment ) {
		$text = __( 'WP e-Commerce', 'pronamic_ideal' ) . '<br />';

		$text .= sprintf(
			'<a href="%s">%s</a>',
			add_query_arg(
				array(
					'page'           => 'wpsc-sales-logs',
					'purchaselog_id' => $payment->get_source_id(),
				),
				admin_url( 'index.php' )
			),
			/* translators: %s: payment source id */
			sprintf( __( 'Purchase #%s', 'pronamic_ideal' ), $payment->get_source_id() )
		);

		return $text;
	}

	/**
	 * Source description.
	 *
	 * @param string  $description Source description.
	 * @param Payment $payment     Payment.
	 *
	 * @return string
	 */
	public static function source_description( $description, Payment $payment ) {
		return __( 'WP e-Commerce Purchase', 'pronamic_ideal' );
	}

	/**
	 * Source URL.
	 *
	 * @param string  $url     Source URL.
	 * @param Payment $payment Payment.
	 *
	 * @return string
	 */
	public static function source_url( $url, Payment $payment ) {
		$url = add_query_arg(
			array(
				'page' => 'wpsc-purchase-logs',
				'c'    => 'item_details',
				'id'   => $payment->get_source_id(),
			),
			admin_url( 'index.php' )
		);

		return $url;
	}
}
