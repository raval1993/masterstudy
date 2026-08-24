<?php

add_action( 'vc_after_init', 'masterstudy_authorization_form_vc' );

/**
 * Callback function to register the Masterstudy Authorization Form shortcode with Visual Composer.
 */
function masterstudy_authorization_form_vc() {
	vc_map(
		array(
			'name'           => esc_html__( 'Masterstudy Authorization Form', 'masterstudy-lms-learning-management-system' ),
			'base'           => 'masterstudy_authorization_form',
			'icon'           => 'masterstudy_authorization_form',
			'description'    => esc_html__( 'Display authorization form', 'masterstudy-lms-learning-management-system' ),
			'html_template'  => STM_LMS_Templates::vc_locate_template( 'vc_templates/masterstudy_authorization_form' ),
			'php_class_name' => 'WPBakeryShortCode_Masterstudy_Authorization_Form',
			'params'         => array(
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Form type', 'masterstudy-lms-learning-management-system' ),
					'param_name' => 'type',
					'value'      => array(
						__( 'Login', 'masterstudy-lms-learning-management-system' )        => 'login',
						__( 'Registration', 'masterstudy-lms-learning-management-system' ) => 'register',
					),
				),
			),
		)
	);
}

if ( class_exists( 'WPBakeryShortCode' ) ) {
	/**
	 * WPBakeryShortCode_Masterstudy_Authorization_Form class definition.
	 */
	class WPBakeryShortCode_Masterstudy_Authorization_Form extends WPBakeryShortCode {
	}
}
