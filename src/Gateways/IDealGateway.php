<?php

namespace Pronamic\WordPress\Pay\Extensions\WPeCommerce\Gateways;

use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Title: WP e-Commerce iDEAL gateway
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class IDealGateway extends Gateway {
	/**
	 * Payment method
	 *
	 * @var string $payment_method
	 */
	const PAYMENT_METHOD = PaymentMethods::IDEAL;
}
