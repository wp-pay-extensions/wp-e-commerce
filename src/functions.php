<?php

function pronamic_ideal_wpsc_merchant_form() {
	return Pronamic_WP_Pay_Extensions_WPeCommerce_IDealMerchant::admin_config_form();
}

function pronamic_ideal_wpsc_merchant_submit_function() {
	return Pronamic_WP_Pay_Extensions_WPeCommerce_IDealMerchant::admin_config_submit();
}
