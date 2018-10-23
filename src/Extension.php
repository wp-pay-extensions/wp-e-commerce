<?php

namespace Pronamic\WordPress\Pay\Extensions\WPeCommerce;

use Pronamic\WordPress\Pay\Core\Statuses;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Plugin;

/**
 * Title: WP eCommerce extension
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
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
		// Add gateway to gateways
		add_filter( 'wpsc_merchants_modules', array( __CLASS__, 'merchants_modules' ) );

		// Update payment status when returned from iDEAL
		add_action( 'pronamic_payment_status_update_' . self::SLUG, array( __CLASS__, 'status_update' ), 10, 2 );

		// Filters
		add_filter( 'pronamic_payment_redirect_url_' . self::SLUG, array( __CLASS__, 'redirect_url' ), 10, 2 );
		add_filter( 'pronamic_payment_source_text_' . self::SLUG, array( __CLASS__, 'source_text' ), 10, 2 );
		add_filter( 'pronamic_payment_source_description_' . self::SLUG, array( __CLASS__, 'source_description' ), 10, 2 );
		add_filter( 'pronamic_payment_source_url_' . self::SLUG, array( __CLASS__, 'source_url' ), 10, 2 );
	}

	/**
	 * Merchants modules
	 *
	 * @param array $gateways
	 *
	 * @return array
	 */
	public static function merchants_modules( $gateways ) {
		global $nzshpcrt_gateways, $num, $wpsc_gateways, $gateway_checkout_form_fields;

		$gateways[] = array(
			'name'                   => __( 'Pronamic', 'pronamic_ideal' ),
			'api_version'            => 2.0,
			'class_name'             => __NAMESPACE__ . '\Gateway',
			'has_recurring_billing'  => false,
			'wp_admin_cannot_cancel' => false,
			'display_name'           => __( 'Pronamic', 'pronamic_ideal' ),
			'requirements'           => array(
				'php_version'   => 5.3,
				'extra_modules' => array(),
			),
			'form'                   => array( __NAMESPACE__ . '\Gateway', 'admin_config_form' ),
			'submit_function'        => array( __NAMESPACE__ . '\Gateway', 'admin_config_submit' ),
			'internalname'           => 'wpsc_merchant_pronamic',
		);

		$gateways[] = array(
			'name'                   => __( 'Pronamic iDEAL', 'pronamic_ideal' ),
			'api_version'            => 2.0,
			'image'                  => plugins_url( '/images/ideal/icon-32x32.png', Plugin::$file ),
			'class_name'             => __NAMESPACE__ . '\IDealGateway',
			'has_recurring_billing'  => false,
			'wp_admin_cannot_cancel' => false,
			'display_name'           => __( 'iDEAL', 'pronamic_ideal' ),
			'requirements'           => array(
				'php_version'   => 5.3,
				'extra_modules' => array(),
			),
			'form'                   => array( __NAMESPACE__ . '\IDealGateway', 'admin_config_form' ),
			'submit_function'        => array( __NAMESPACE__ . '\IDealGateway', 'admin_config_submit' ),
			'internalname'           => 'wpsc_merchant_pronamic_ideal',
		);

		$gateway_checkout_form_fields['wpsc_merchant_pronamic']       = Gateway::advanced_inputs();
		$gateway_checkout_form_fields['wpsc_merchant_pronamic_ideal'] = IDealGateway::advanced_inputs();

		return $gateways;
	}

	/**
	 * Update lead status of the specified payment
	 *
	 * @param Payment $payment
	 * @param bool    $can_redirect
	 */
	public static function status_update( Payment $payment, $can_redirect = false ) {
		$merchant = new Gateway( $payment->get_source_id() );

		$data = new PaymentData( $merchant );

		$url = $data->get_normal_return_url();

		switch ( $payment->status ) {
			case Statuses::CANCELLED:
				$merchant->set_purchase_processed_by_purchid( WPeCommerce::PURCHASE_STATUS_INCOMPLETE_SALE );

				$url = $data->get_cancel_url();

				break;
			case Statuses::EXPIRED:
				break;
			case Statuses::FAILURE:
				break;
			case Statuses::SUCCESS:
				/*
				 * Transactions results
				 *
				 * @link https://github.com/wp-e-commerce/WP-e-Commerce/blob/v3.8.9.5/wpsc-merchants/paypal-pro.merchant.php#L303
				 */
				$session_id = get_post_meta( $payment->get_id(), '_pronamic_payment_wpsc_session_id', true );

				transaction_results( $session_id );

				$merchant->set_purchase_processed_by_purchid( WPeCommerce::PURCHASE_STATUS_ACCEPTED_PAYMENT );

				$url = $data->get_success_url();

				break;
			case Statuses::OPEN:
				break;
			default:
				break;
		}

		if ( $can_redirect ) {
			wp_redirect( $url );

			exit;
		}
	}

	/**
	 * Payment redirect URL filter.
	 *
	 * @param string  $url
	 * @param Payment $payment
	 *
	 * @return string
	 */
	public static function redirect_url( $url, Payment $payment ) {
		$merchant = new Gateway( $payment->get_source_id() );

		$data = new PaymentData( $merchant );

		$url = $data->get_normal_return_url();

		switch ( $payment->status ) {
			case Statuses::CANCELLED:
				return $data->get_cancel_url();
			case Statuses::EXPIRED:
				break;
			case Statuses::FAILURE:
				break;
			case Statuses::SUCCESS:
				return $data->get_success_url();
			case Statuses::OPEN:
			default:
				break;
		}

		return $url;
	}

	/**
	 * Source text.
	 *
	 * @param string  $text
	 * @param Payment $payment
	 *
	 * @return string
	 */
	public static function source_text( $text, Payment $payment ) {
		$text = __( 'WP e-Commerce', 'pronamic_ideal' ) . '<br />';

		$text .= sprintf(
			'<a href="%s">%s</a>',
			add_query_arg( array(
				'page'           => 'wpsc-sales-logs',
				'purchaselog_id' => $payment->get_source_id(),
			), admin_url( 'index.php' ) ),
			/* translators: %s: payment source id */
			sprintf( __( 'Purchase #%s', 'pronamic_ideal' ), $payment->get_source_id() )
		);

		return $text;
	}

	/**
	 * Source description.
	 *
	 * @param string  $description
	 * @param Payment $payment
	 *
	 * @return string
	 */
	public static function source_description( $description, Payment $payment ) {
		return __( 'WP e-Commerce Purchase', 'pronamic_ideal' );
	}

	/**
	 * Source URL.
	 *
	 * @param string  $url
	 * @param Payment $payment
	 *
	 * @return string
	 */
	public static function source_url( $url, Payment $payment ) {
		$url = add_query_arg( array(
			'page'           => 'wpsc-sales-logs',
			'purchaselog_id' => $payment->get_source_id(),
		), admin_url( 'index.php' ) );

		return $url;
	}
}
