<?php

namespace Pronamic\WordPress\Pay\Extensions\WPeCommerce\Gateways;

use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Bank Transfer Gateway.
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class BankTransferGateway extends Gateway {
	/**
	 * Payment method
	 *
	 * @var string $payment_method
	 */
	const PAYMENT_METHOD = PaymentMethods::BANK_TRANSFER;
}
