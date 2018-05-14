<?php

namespace Pronamic\WordPress\Pay\Extensions\WPeCommerce;

use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Title: WP e-Commerce iDEAL gateway
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
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

	/**
	 * Config ID option name.
	 *
	 * @var string
	 */
	const OPTION_CONFIG_ID = 'pronamic_pay_ideal_wpsc_config_id';
}
