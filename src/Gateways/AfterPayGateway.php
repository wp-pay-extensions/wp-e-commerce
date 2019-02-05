<?php

namespace Pronamic\WordPress\Pay\Extensions\WPeCommerce\Gateways;

use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * AfterPay Gateway.
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class AfterPayGateway extends Gateway {
	/**
	 * Payment method
	 *
	 * @var string $payment_method
	 */
	const PAYMENT_METHOD = PaymentMethods::AFTERPAY;
}
