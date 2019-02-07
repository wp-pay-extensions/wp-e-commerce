<?php

namespace Pronamic\WordPress\Pay\Extensions\WPeCommerce\Gateways;

use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * PayPal Gateway.
 *
 * @author  Remco Tolsma
 * @version 2.0.2
 * @version 2.0.2
 */
class PayPalGateway extends Gateway {
	/**
	 * Payment method
	 *
	 * @var string $payment_method
	 */
	const PAYMENT_METHOD = PaymentMethods::PAYPAL;
}
