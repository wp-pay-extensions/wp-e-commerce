<?php

namespace Pronamic\WordPress\Pay\Extensions\WPeCommerce;

/**
 * Title: WP e-Commerce
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
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
}
