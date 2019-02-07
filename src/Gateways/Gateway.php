<?php

namespace Pronamic\WordPress\Pay\Extensions\WPeCommerce\Gateways;

use Pronamic\WordPress\Pay\Extensions\WPeCommerce\Extension;
use Pronamic\WordPress\Pay\Admin\AdminModule;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Plugin;
use Pronamic\WordPress\Pay\Util as Pay_Util;
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
	 * @var string $payment_method
	 */
	const PAYMENT_METHOD = null;

	/**
	 * Construct and initialize an Pronamic merchant class
	 *
	 * @param null $purchase_id
	 * @param bool $is_receiving
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
	 * @return string
	 */
	private static function get_config_id() {
		$name = self::get_option_config_id();

		$config_id = get_option( $name, null );

		return $config_id;
	}

	/**
	 * Construct value array specific data array
	 */
	public function construct_value_array() {
		// No specific data for this merchant
		return array();
	}

	/**
	 * Submit to gateway
	 */
	public function submit() {
		$config_id = self::get_config_id();

		// Set process to 'order_received' (2)
		// @link https://plugins.trac.wordpress.org/browser/wp-e-commerce/tags/3.8.7.6.2/wpsc-includes/merchant.class.php#L301
		// @link https://plugins.trac.wordpress.org/browser/wp-e-commerce/tags/3.8.7.6.2/wpsc-core/wpsc-functions.php#L115
		$this->set_purchase_processed_by_purchid( WPeCommerce::PURCHASE_STATUS_ORDER_RECEIVED );

		$gateway = Plugin::get_gateway( $config_id );

		if ( ! $gateway ) {
			return;
		}

		$data = new PaymentData( $this );

		$payment_method = static::PAYMENT_METHOD;

		if ( null !== $payment_method ) {
			$gateway->set_payment_method( $payment_method );
		}

		$payment = Plugin::start( $config_id, $gateway, $data, $payment_method );

		update_post_meta( $payment->get_id(), '_pronamic_payment_wpsc_purchase_id', $data->get_purchase_id() );
		update_post_meta( $payment->get_id(), '_pronamic_payment_wpsc_session_id', $data->get_session_id() );

		$error = $gateway->get_error();

		if ( is_wp_error( $error ) ) {
			Plugin::render_errors( $error );
		} else {
			$gateway->redirect( $payment );
		}
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
	 * Admin config submit
	 */
	public static function admin_config_submit() {
		// Config ID
		$name = self::get_option_config_id();

		if ( filter_has_var( INPUT_POST, $name ) ) {
			$config_id = filter_input( INPUT_POST, $name, FILTER_SANITIZE_STRING );

			update_option( $name, $config_id );
		}
	}

	/**
	 * Advanced inputs
	 *
	 * @return string
	 */
	public static function advanced_inputs() {
		$config_id = self::get_config_id();

		$gateway = Plugin::get_gateway( $config_id );

		if ( ! $gateway ) {
			return;
		}

		$payment_method = static::PAYMENT_METHOD;

		if ( null !== $payment_method ) {
			$gateway->set_payment_method( $payment_method );
		}

		return $gateway->get_input_html();
	}
}
