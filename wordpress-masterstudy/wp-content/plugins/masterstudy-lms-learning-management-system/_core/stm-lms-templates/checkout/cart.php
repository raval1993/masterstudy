<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

stm_lms_register_style( 'cart' );
wp_enqueue_script( 'vue-resource.js' );
stm_lms_register_script( 'cart' );

$taxes_display = STM_LMS_Helpers::taxes_display();

wp_localize_script(
	'stm-lms-cart',
	'stm_lms_checkout_settings',
	array(
		'stm_lms_cart_has_subscription' => ( STM_LMS_Cart::cart_has_subscription_item() ? 'true' : 'false' ),
		'tax_rates'                     => STM_LMS_Options::get_option( 'taxes', array() ),
		'tax_included'                  => $taxes_display['enabled'] && $taxes_display['included'],
		'tax_enabled'                   => $taxes_display['enabled'],
		'currency_symbol'               => STM_LMS_Options::get_option( 'currency_symbol', '$' ),
		'currency_position'             => STM_LMS_Options::get_option( 'currency_position', 'left' ),
		'currency_thousands'            => STM_LMS_Options::get_option( 'currency_thousands', ',' ),
		'currency_decimals'             => STM_LMS_Options::get_option( 'currency_decimals', '.' ),
		'decimals_num'                  => STM_LMS_Options::get_option( 'decimals_num', '2' ),
	),
);

$user = STM_LMS_User::get_current_user();

if ( empty( $user['id'] ) ) {
	if ( STM_LMS_Guest_Checkout::guest_enabled() ) {
		STM_LMS_Templates::show_lms_template( 'checkout/guest-checkout' );
	} else {
		STM_LMS_Templates::show_lms_template( 'checkout/not-logged-in' );
	}
} else {
	$user_id = $user['id'];
	STM_LMS_Templates::show_lms_template( 'checkout/items', compact( 'user_id' ) );
}
