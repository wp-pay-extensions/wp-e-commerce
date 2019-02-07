<?php

namespace Pronamic\WordPress\Pay\Extensions\WPeCommerce\Gateways;

use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Giropay Gateway.
 *
 * @author  Remco Tolsma
 * @version 2.0.2
 * @version 2.0.2
 */
class GiropayGateway extends Gateway {
	/**
	 * Payment method
	 *
	 * @var string $payment_method
	 */
	const PAYMENT_METHOD = PaymentMethods::GIROPAY;
}
