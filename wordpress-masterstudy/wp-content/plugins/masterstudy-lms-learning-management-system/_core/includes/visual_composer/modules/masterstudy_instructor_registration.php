<?php

add_action( 'vc_after_init', 'masterstudy_instructor_registration_vc' );

/**
 * Callback function to register the Masterstudy Instructor Registration shortcode with Visual Composer.
 */
function masterstudy_instructor_registration_vc() {
	vc_map(
		array(
			'name'           => esc_html__( 'Masterstudy Instructor Registration', 'masterstudy-lms-learning-management-system' ),
			'base'           => 'masterstudy_instructor_registration',
			'icon'           => 'masterstudy_instructor_registration',
			'description'    => esc_html__( 'Display the instructor registration form', 'masterstudy-lms-learning-management-system' ),
			'html_template'  => STM_LMS_Templates::vc_locate_template( 'vc_templates/masterstudy_instructor_registration' ),
			'php_class_name' => 'WPBakeryShortCode_Masterstudy_Instructor_Registration',
			'params'         => array(),
		)
	);
}

if ( class_exists( 'WPBakeryShortCode' ) ) {
	/**
	 * WPBakeryShortCode_Masterstudy_Instructor_Registration class definition.
	 */
	class WPBakeryShortCode_Masterstudy_Instructor_Registration extends WPBakeryShortCode {
	}
}
