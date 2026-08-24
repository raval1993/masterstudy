<?php

function stm_lms_settings_google_api_section() {
	return array(
		'name'   => esc_html__( 'Recaptcha', 'masterstudy-lms-learning-management-system' ),
		'label'  => esc_html__( 'Recaptcha', 'masterstudy-lms-learning-management-system' ),
		'icon'   => 'stmlms-google',
		'fields' => array(
			'recaptcha_site_key'    => array(
				'type'        => 'text',
				'label'       => esc_html__( 'Recaptcha site key', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( "Enter the unique key provided by Google to use Google's reCAPTCHA service on your website", 'masterstudy-lms-learning-management-system' ),
			),
			'recaptcha_private_key' => array(
				'type'        => 'text',
				'label'       => esc_html__( 'Recaptcha private key', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Enter the private key provided by Google for the reCAPTCHA service', 'masterstudy-lms-learning-management-system' ),
			),
		),
	);
}
