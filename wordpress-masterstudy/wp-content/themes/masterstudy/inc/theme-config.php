<?php

if ( ! empty( $_GET['lms-layout'] ) ) {

	add_action( 'init', 'stm_set_layout' );

	function stm_set_layout() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die;
		}
		update_option( 'stm_lms_layout', sanitize_text_field( $_GET['lms-layout'] ) );
	}
}

if ( ! function_exists( 'stm_get_layout' ) ) {
	function stm_get_layout() {
		return get_option( 'stm_lms_layout', '' );
	}
}

if ( ! function_exists( 'stm_get_layout_is_mobile' ) ) {
	function stm_get_layout_is_mobile() {
		$is_mobile = true;
		if ( stm_get_layout() == '' || stm_get_layout() == 'default' || stm_get_layout() == 'language_center' || stm_get_layout() == 'distance-learning' ) {
			$is_mobile = false;
		}
		return $is_mobile;
	}
}

function masterstudy_get_demos() {
	$demos = array(
		'classic_lms'       => array(
			'label'    => esc_html__( 'Classic LMS', 'masterstudy' ),
			'slug'     => 'classic_lms',
			'live_url' => 'classic-lms/',
		),
		'online-light'      => array(
			'label'    => esc_html__( 'LMS Light', 'masterstudy' ),
			'slug'     => 'white_lms',
			'live_url' => 'white-lms/',
		),
		'udemy'             => array(
			'label'    => esc_html__( 'Udemy Affiliate', 'masterstudy' ),
			'slug'     => 'udemy-affiliate',
			'live_url' => 'udemy-affiliate/',
		),
		'academy'           => array(
			'label'    => esc_html__( 'Academy', 'masterstudy' ),
			'slug'     => 'academy',
			'live_url' => 'academy/',
		),
		'online-dark'       => array(
			'label'    => esc_html__( 'LMS Dark', 'masterstudy' ),
			'slug'     => 'dark_lms',
			'live_url' => 'dark-lms/',
		),
		'default'           => array(
			'label'    => esc_html__( 'Offline Courses', 'masterstudy' ),
			'slug'     => 'ms',
			'live_url' => 'ms/',
		),
		'course_hub'        => array(
			'label'    => esc_html__( 'Course Hub', 'masterstudy' ),
			'slug'     => 'course_hub',
			'live_url' => 'course-hub/',
		),
		'single_instructor' => array(
			'label'    => esc_html__( 'Private Instructor', 'masterstudy' ),
			'slug'     => 'single_instructor',
			'live_url' => 'one-instructor/',
		),
		'language_center'   => array(
			'label'    => esc_html__( 'Language Center', 'masterstudy' ),
			'slug'     => 'language_center',
			'live_url' => 'language-center/',
		),
		'rtl-demo'          => array(
			'label'    => esc_html__( 'RTL Demo', 'masterstudy' ),
			'slug'     => 'rtl_demo',
			'live_url' => 'rtl-demo/',
		),
		'buddypress-demo'   => array(
			'label'    => esc_html__( 'BuddyPress Demo', 'masterstudy' ),
			'slug'     => 'buddypress-demo',
			'live_url' => 'buddypress-demo/',
		),
		'classic-lms-2'     => array(
			'label'    => esc_html__( 'Classic LMS 2', 'masterstudy' ),
			'slug'     => 'classic_lms_2',
			'live_url' => 'classic-lms-2/',
		),
		'distance-learning' => array(
			'label'    => esc_html__( 'Distance Learning', 'masterstudy' ),
			'slug'     => 'distance_learning',
			'live_url' => 'distance-learning/',
		),
		'cooking'           => array(
			'label'    => esc_html__( 'Cooking courses', 'masterstudy' ),
			'slug'     => 'cooking_courses',
			'live_url' => 'cooking-courses/',
		),
		'tech'              => array(
			'label'    => esc_html__( 'Coding School', 'masterstudy' ),
			'slug'     => 'tech',
			'live_url' => 'tech/',
		),
	);

	return $demos;
}

function stm_layout_plugins( $layout = 'default', $get_layouts = false ) {
	$required = array(
		'envato-market',
		'stm-post-type',
		'breadcrumb-navxt',
		'contact-form-7',
	);

	$plugins = array(
		'default'           => array(
			'revslider',
			'woocommerce',
			'breadcrumb-navxt',
			'contact-form-7',
		),
		'online-light'      => array(
			'masterstudy-lms-learning-management-system',
			'masterstudy-lms-learning-management-system-pro',
			'paid-memberships-pro',
			'woocommerce',
		),
		'online-dark'       => array(
			'revslider',
			'masterstudy-lms-learning-management-system',
			'masterstudy-lms-learning-management-system-pro',
			'paid-memberships-pro',
			'woocommerce',
		),
		'academy'           => array(
			'masterstudy-lms-learning-management-system',
			'masterstudy-lms-learning-management-system-pro',
			'paid-memberships-pro',
			'woocommerce',
		),
		'course_hub'        => array(
			'masterstudy-lms-learning-management-system',
			'masterstudy-lms-learning-management-system-pro',
			'paid-memberships-pro',
			'woocommerce',
		),
		'classic_lms'       => array(
			'revslider',
			'masterstudy-lms-learning-management-system',
			'masterstudy-lms-learning-management-system-pro',
			'paid-memberships-pro',
			'woocommerce',
		),
		'udemy'             => array(
			'revslider',
			'contact-form-7',
			'breadcrumb-navxt',
			'masterstudy-lms-learning-management-system',
			'masterstudy-lms-learning-management-system-pro',
			'paid-memberships-pro',
			'woocommerce',
		),
		'single_instructor' => array(
			'revslider',
			'contact-form-7',
			'breadcrumb-navxt',
			'masterstudy-lms-learning-management-system',
			'masterstudy-lms-learning-management-system-pro',
			'woocommerce',
		),
		'language_center'   => array(
			'woocommerce',
			'breadcrumb-navxt',
			'contact-form-7',
		),
		'rtl-demo'          => array(
			'revslider',
			'contact-form-7',
			'breadcrumb-navxt',
			'masterstudy-lms-learning-management-system',
			'masterstudy-lms-learning-management-system-pro',
			'woocommerce',
		),
		'buddypress-demo'   => array(
			'revslider',
			'contact-form-7',
			'breadcrumb-navxt',
			'masterstudy-lms-learning-management-system',
			'masterstudy-lms-learning-management-system-pro',
			'buddypress',
			'woocommerce',
		),
		'classic-lms-2'     => array(
			'revslider',
			'masterstudy-lms-learning-management-system',
			'masterstudy-lms-learning-management-system-pro',
			'paid-memberships-pro',
			'woocommerce',
		),
		'distance-learning' => array(
			'masterstudy-lms-learning-management-system',
			'masterstudy-lms-learning-management-system-pro',
			'eroom-zoom-meetings-webinar',
			'woocommerce',
		),
		'cooking'           => array(
			'masterstudy-lms-learning-management-system',
			'masterstudy-lms-learning-management-system-pro',
			'woocommerce',
		),
		'tech'              => array(
			'revslider',
			'masterstudy-lms-learning-management-system',
			'masterstudy-lms-learning-management-system-pro',
			'woocommerce',
		),
	);

	if ( $get_layouts ) {
		return $plugins;
	}

	return array_merge( $required, $plugins[ $layout ] );
}

function get_stm_theme_secondary_required_plugins() {
	$plugins = array(
		'js_composer',
		'elementor',
		'masterstudy-elementor-widgets',
		'header-footer-elementor',
	);

	return $plugins;
}

function get_stm_theme_elementor_addon() {
	return 'masterstudy-elementor-widgets';
}

function do_stm_reset_theme_options() {
	delete_option( 'stm_lms_layout' );
	delete_option( 'stm_hb_settings' );
}
