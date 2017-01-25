<?php

/**
 * Title: WP e-Commerce Pronamic merchant
 * Description:
 * Copyright: Copyright (c) 2005 - 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.1.0
 * @since 1.0.0
 */
class Pronamic_WP_Pay_Extensions_WPeCommerce_PronamicMerchant extends wpsc_merchant {
	/**
	 * Construct and initialize an Pronamic merchant class
	 */
	public function __construct( $purchase_id = null, $is_receiving = false ) {
		parent::__construct( $purchase_id, $is_receiving );

		$this->name = __( 'Pronamic', 'pronamic_ideal' );
	}

	//////////////////////////////////////////////////

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
		$config_id = get_option( Pronamic_WP_Pay_Extensions_WPeCommerce_Extension::OPTION_PRONAMIC_CONFIG_ID );

		// Set process to 'order_received' (2)
		// @see http://plugins.trac.wordpress.org/browser/wp-e-commerce/tags/3.8.7.6.2/wpsc-includes/merchant.class.php#L301
		// @see http://plugins.trac.wordpress.org/browser/wp-e-commerce/tags/3.8.7.6.2/wpsc-core/wpsc-functions.php#L115
		$this->set_purchase_processed_by_purchid( Pronamic_WP_Pay_Extensions_WPeCommerce_WPeCommerce::PURCHASE_STATUS_ORDER_RECEIVED );

		$gateway = Pronamic_WP_Pay_Plugin::get_gateway( $config_id );

		if ( $gateway ) {
			$data = new Pronamic_WP_Pay_Extensions_WPeCommerce_PaymentData( $this );

			$payment_method = get_option( Pronamic_WP_Pay_Extensions_WPeCommerce_Extension::OPTION_PRONAMIC_PAYMENT_METHOD );

			$gateway->set_payment_method( $payment_method );

			$payment = Pronamic_WP_Pay_Plugin::start( $config_id, $gateway, $data, $payment_method );

			update_post_meta( $payment->get_id(), '_pronamic_payment_wpsc_purchase_id', $data->get_purchase_id() );
			update_post_meta( $payment->get_id(), '_pronamic_payment_wpsc_session_id', $data->get_session_id() );

			$error = $gateway->get_error();

			if ( is_wp_error( $error ) ) {
				Pronamic_WP_Pay_Plugin::render_errors( $error );
			} else {
				$gateway->redirect( $payment );
			}
		}
	}

	//////////////////////////////////////////////////

	/**
	 * Admin configuration form
	 */
	public static function admin_config_form() {
		$html = '';

		$html .= '<tr>';
		$html .= '	<td class="wpsc_CC_details">';
		$html .= '		' . __( 'Configuration', 'pronamic_ideal' );
		$html .= '	</td>';
		$html .= '	<td>';
		$html .= Pronamic_WP_Pay_Admin::dropdown_configs( array(
			'name' => Pronamic_WP_Pay_Extensions_WPeCommerce_Extension::OPTION_PRONAMIC_CONFIG_ID,
			'echo' => false,
		) );
		$html .= '	</td>';
		$html .= '</tr>';

		$config_id = get_option( Pronamic_WP_Pay_Extensions_WPeCommerce_Extension::OPTION_PRONAMIC_CONFIG_ID );

		$gateway = Pronamic_WP_Pay_Plugin::get_gateway( $config_id );

		if ( $gateway ) {
			$payment_method_field = $gateway->get_payment_method_field();

			if ( $payment_method_field ) {
				$choices = $payment_method_field['choices'];

				$name = Pronamic_WP_Pay_Extensions_WPeCommerce_Extension::OPTION_PRONAMIC_PAYMENT_METHOD;

				$payment_method = get_option( $name );

				$options = Pronamic_WP_HTML_Helper::select_options_grouped( $choices, $payment_method );
				// Double quotes are not working, se we replace them with an single quote
				$options = str_replace( '"', '\'', $options );

				$html .= '<tr>';
				$html .= '	<td class="wpsc_CC_details">';
				$html .= '		' . __( 'Payment Method', 'pronamic_ideal' );
				$html .= '	</td>';
				$html .= '	<td>';
				$html .= sprintf( "<select name='%s' id='%s'>", $name, $name );
				$html .= sprintf( '%s', $options );
				$html .= sprintf( '</select>' );
				$html .= '	</td>';
				$html .= '</tr>';
			}
		}

		return $html;
	}

	/**
	 * Admin config submit
	 */
	public static function admin_config_submit() {
		// Config ID
		$name = Pronamic_WP_Pay_Extensions_WPeCommerce_Extension::OPTION_PRONAMIC_CONFIG_ID;

		if ( filter_has_var( INPUT_POST, $name ) ) {
			$config_id = filter_input( INPUT_POST, $name, FILTER_SANITIZE_STRING );

			update_option( $name, $config_id );
		}

		// Payment method
		$name = Pronamic_WP_Pay_Extensions_WPeCommerce_Extension::OPTION_PRONAMIC_PAYMENT_METHOD;

		if ( filter_has_var( INPUT_POST, $name ) ) {
			$payment_method = filter_input( INPUT_POST, $name, FILTER_SANITIZE_STRING );

			update_option( $name, $payment_method );
		}

		return true;
	}

	//////////////////////////////////////////////////

	/**
	 * Advanced inputs
	 *
	 * @return string
	 */
	public static function advanced_inputs() {
		$output = '';

		$config_id = get_option( Pronamic_WP_Pay_Extensions_WPeCommerce_Extension::OPTION_PRONAMIC_CONFIG_ID );

		$gateway = Pronamic_WP_Pay_Plugin::get_gateway( $config_id );

		if ( $gateway ) {
			$payment_method = get_option( Pronamic_WP_Pay_Extensions_WPeCommerce_Extension::OPTION_PRONAMIC_PAYMENT_METHOD );

			$gateway->set_payment_method( $payment_method );

			$output = $gateway->get_input_html();
		}

		return $output;
	}
}
