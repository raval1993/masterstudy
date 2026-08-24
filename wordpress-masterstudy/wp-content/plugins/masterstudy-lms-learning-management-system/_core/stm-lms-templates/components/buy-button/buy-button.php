<?php
/**
 * @var int $post_id
 * @var int $item_id
 * @var int $user_id
 * @var bool $has_access
 * @var bool $prerequisite_preview
 * @var bool $hide_group_course
 * @var bool $dark_mode
 */

use MasterStudy\Lms\Enums\PricingMode;

wp_enqueue_style( 'masterstudy-buy-button' );
wp_enqueue_script( 'masterstudy-buy-button' );
wp_localize_script(
	'masterstudy-buy-button',
	'masterstudy_buy_button_data',
	array(
		'ajax_url'        => admin_url( 'admin-ajax.php' ),
		'get_nonce'       => wp_create_nonce( 'stm_lms_add_to_cart' ),
		'get_guest_nonce' => wp_create_nonce( 'stm_lms_add_to_cart_guest' ),
		'item_id'         => $post_id,
	)
);

$course_price          = STM_LMS_Course::get_course_price( $post_id );
$single_sale           = get_post_meta( $post_id, 'single_sale', true );
$pricing_mode          = get_post_meta( $post_id, 'pricing_mode', true );
$has_access            = isset( $has_access ) ? $has_access : STM_LMS_User::has_course_access( $post_id, '', false );
$prerequisite_passed   = true;
$is_course_coming_soon = false;

if ( method_exists( 'STM_LMS_Helpers', 'masterstudy_lms_is_course_coming_soon' ) ) {
	$is_course_coming_soon = STM_LMS_Helpers::masterstudy_lms_is_course_coming_soon( $post_id );
}

if ( class_exists( 'STM_LMS_Prerequisites' ) ) {
	$prerequisite_passed = STM_LMS_Prerequisites::is_prerequisite( true, $post_id );
}

$button_classes = array(
	'masterstudy-buy-button',
	$course_price ? '' : 'masterstudy-buy-button-centred',
	$dark_mode ? 'masterstudy-buy-button_style-dark-mode' : '',
);

do_action( 'stm_lms_before_button_mixed', $post_id );

if ( apply_filters( 'stm_lms_before_button_stop', false, $post_id ) && false === $has_access ) {
	return false;
}

if ( PricingMode::AFFILIATE !== $pricing_mode || ! class_exists( 'STM_LMS_Courses_Pro' ) ) {
	if ( ( $has_access || PricingMode::FREE === $pricing_mode ) && $prerequisite_passed ) :
		/* Including the button template for free courses */
		STM_LMS_Templates::show_lms_template(
			'components/buy-button/free-courses/free-courses',
			array(
				'post_id'               => $post_id,
				'user_id'               => $user_id,
				'button_classes'        => $button_classes,
				'is_course_coming_soon' => $is_course_coming_soon,
			)
		);
	else :
		/* Including the button template for paid courses */
		STM_LMS_Templates::show_lms_template(
			'components/buy-button/paid-courses/paid-courses',
			array(
				'post_id'              => $post_id,
				'button_classes'       => $button_classes,
				'prerequisite_preview' => $prerequisite_preview,
				'only_membership'      => ! $single_sale,
				'prerequisite_passed'  => $prerequisite_passed,
				'hide_group_course'    => $hide_group_course,
			)
		);
	endif;
} else {
	STM_LMS_Templates::show_lms_template(
		'components/buy-button/paid-courses/affiliate',
		array(
			'course_id' => $post_id,
		)
	);
}
