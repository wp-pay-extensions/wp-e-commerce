<?php
/**
 * WP eCommerce Dependency
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\WPeCommerce
 */

namespace Pronamic\WordPress\Pay\Extensions\WPeCommerce;

use Pronamic\WordPress\Pay\Dependencies\Dependency;

/**
 * WP eCommerce Dependency
 *
 * @author  Reüel van der Steege
 * @version 2.1.1
 * @since   2.1.1
 */
class WPeCommerceDependency extends Dependency {
	/**
	 * Is met.
	 *
	 * @link
	 * @return bool True if dependency is met, false otherwise.
	 */
	public function is_met() {
		if ( ! \class_exists( '\WP_eCommerce' ) ) {
			return false;
		}

		return true;
	}
}
