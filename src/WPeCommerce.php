<?php

namespace Pronamic\WordPress\Pay\Extensions\WPeCommerce;

use Pronamic\WordPress\Money\Money;
use Pronamic\WordPress\Money\TaxedMoney;
use Pronamic\WordPress\Pay\Address;
use Pronamic\WordPress\Pay\Customer;
use Pronamic\WordPress\Pay\ContactName;
use Pronamic\WordPress\Pay\Payments\PaymentLines;
use Pronamic\WordPress\Pay\Payments\PaymentLineType;

/**
 * Title: WP e-Commerce
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class WPeCommerce {
	/**
	 * Purche status
	 *
	 * @link https://plugins.trac.wordpress.org/browser/wp-e-commerce/tags/3.8.7.6.2/wpsc-core/wpsc-functions.php#L115
	 * @var int
	 */
	const PURCHASE_STATUS_INCOMPLETE_SALE = 1;

	/**
	 * Purche status
	 *
	 * @link https://plugins.trac.wordpress.org/browser/wp-e-commerce/tags/3.8.7.6.2/wpsc-core/wpsc-functions.php#L115
	 * @var int
	 */
	const PURCHASE_STATUS_ORDER_RECEIVED = 2;

	/**
	 * Purche status
	 *
	 * @link https://plugins.trac.wordpress.org/browser/wp-e-commerce/tags/3.8.7.6.2/wpsc-core/wpsc-functions.php#L115
	 * @var int
	 */
	const PURCHASE_STATUS_ACCEPTED_PAYMENT = 3;

	/**
	 * Purche status
	 *
	 * @link https://plugins.trac.wordpress.org/browser/wp-e-commerce/tags/3.8.7.6.2/wpsc-core/wpsc-functions.php#L115
	 * @var int
	 */
	const PURCHASE_STATUS_JOB_DISPATCHED = 4;

	/**
	 * Purche status
	 *
	 * @link https://plugins.trac.wordpress.org/browser/wp-e-commerce/tags/3.8.7.6.2/wpsc-core/wpsc-functions.php#L115
	 * @var int
	 */
	const PURCHASE_STATUS_CLOSED_ORDER = 5;

	/**
	 * Purche status
	 *
	 * @link https://plugins.trac.wordpress.org/browser/wp-e-commerce/tags/3.8.7.6.2/wpsc-core/wpsc-functions.php#L115
	 * @var int
	 */
	const PURCHASE_STATUS_DECLINED_PAYMENT = 6;

	/**
	 * Check if WP e-Comerce is active (Automattic/developer style)
	 *
	 * @link https://github.com/wp-e-commerce/WP-e-Commerce/blob/v3.8.9.5/wp-shopping-cart.php#L11
	 * @link https://github.com/Automattic/developer/blob/1.1.2/developer.php#L73
	 *
	 * @return boolean
	 */
	public static function is_active() {
		return class_exists( 'WP_eCommerce' );
	}

	/**
	 * Get currency from cart data.
	 *
	 * @param array $cart_data Cart data.
	 * @return string|null
	 */
	public function get_currency_from_cart_data( $cart_data ) {
		if ( ! array_key_exists( 'store_currency', $cart_data ) ) {
			return;
		}

		return $cart_data['store_currency'];
	}

	/**
	 * Get address from cart data.
	 *
	 * @param array  $cart_data Cart data.
	 * @param string $key       Cart data key.
	 * @return Address|null
	 */
	public static function get_address_from_cart_data( $cart_data, $key ) {
		if ( ! array_key_exists( $key, $cart_data ) ) {
			return null;
		}

		$data = $cart_data[ $key ];

		if ( ! is_array( $data ) ) {
			return null;
		}

		$data = array_filter( $data );

		$address = new Address();

		$contact_name = new ContactName();

		if ( array_key_exists( 'first_name', $data ) ) {
			$contact_name->set_first_name( $data['first_name'] );
		}

		if ( array_key_exists( 'last_name', $data ) ) {
			$contact_name->set_last_name( $data['last_name'] );
		}

		$address->set_name( $contact_name );

		if ( array_key_exists( 'address', $data ) ) {
			$address->set_line_1( $data['address'] );
		}

		if ( array_key_exists( 'city', $data ) ) {
			$address->set_city( $data['city'] );
		}

		if ( array_key_exists( 'state', $data ) ) {
			$address->set_region( $data['state'] );
		}

		if ( array_key_exists( 'country', $data ) ) {
			$address->set_country_code( $data['country'] );
		}

		if ( array_key_exists( 'post_code', $data ) ) {
			$address->set_postal_code( $data['post_code'] );
		}

		if ( array_key_exists( 'phone', $data ) ) {
			$address->set_phone( $data['phone'] );
		}

		return $address;
	}

	/**
	 * Get payment lines from cart data.
	 *
	 * @param array $cart_data Cart data.
	 * @return PaymentLines|null
	 */
	public static function get_payment_lines_from_cart_items( $items ) {
		if ( ! is_array( $items ) ) {
			return null;
		}

		$currency = WPeCommerce::get_currency_from_cart_data( $cart_data );

		$lines = new PaymentLines();

		foreach ( $items as $item ) {
			$line = $lines->new_line();

			$quantity = 1;

			$total_price_value = null;
			$total_tax_value   = null;

			$unit_price_value = null;
			$unit_tax_value   = null;

			if ( array_key_exists( 'product_id', $item ) ) {
				$line->set_id( $item['product_id'] );
			}

			if ( array_key_exists( 'name', $item ) ) {
				$line->set_name( $item['name'] );
			}

			if ( array_key_exists( 'quantity', $item ) ) {
				$quantity = intval( $item['quantity'] );
			}

			$line->set_quantity( $quantity );

			if ( array_key_exists( 'tax', $item ) ) {
				$total_tax_value = $item['tax'];
				$unit_tax_value  = $total_tax_value / $quantity;
			}

			if ( array_key_exists( 'price', $item ) ) {
				$unit_price_value = $item['price'];
				$total_price_value = $unit_price_value * $quantity;

				$line->set_unit_price( new TaxedMoney( $unit_price_value, $currency, $unit_tax_value ) );
				$line->set_total_amount( new TaxedMoney( $total_price_value, $currency, $total_tax_value ) );
			}
		}

		return $lines;
	}
}
