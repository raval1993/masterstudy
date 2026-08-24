<?php

use MasterStudy\Lms\Plugin\Addons;

function stm_lms_quiz_types( $single = false ) {
	$types = array(
		'type'        => 'select',
		'label'       => esc_html__( 'Quiz style', 'masterstudy-lms-learning-management-system' ),
		'description' => esc_html__( 'Choose how quizzes are shown', 'masterstudy-lms-learning-management-system' ),
		'options'     => array(
			'default'    => esc_html__( 'One page', 'masterstudy-lms-learning-management-system' ),
			'pagination' => esc_html__( 'Pagination', 'masterstudy-lms-learning-management-system' ),
		),
		'value'       => 'default',
	);

	if ( $single ) {
		$types['options'] = array(
			'global' => esc_html__( 'Default style', 'masterstudy-lms-learning-management-system' ),
		) + $types['options'];

		$types['value'] = 'global';

		$types['hint'] = esc_html__( 'Select the style of displaying questions in the quiz.', 'masterstudy-lms-learning-management-system' );

	}

	return $types;
}

function stm_lms_settings_quiz_section() {
	$quiz_fields = array(
		'name'   => esc_html__( 'Quiz', 'masterstudy-lms-learning-management-system' ),
		'label'  => esc_html__( 'Quiz Settings', 'masterstudy-lms-learning-management-system' ),
		'icon'   => 'stmlms-question-2',
		'fields' => array(
			'quiz_attempts'         => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Attempts to retake quizzes', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'You can choose limited or unlimited attempts for students to retake quizzes.', 'masterstudy-lms-learning-management-system' ),
				'options'     => array(
					'unlimited' => esc_html__( 'Unlimited attempts', 'masterstudy-lms-learning-management-system' ),
					'limited'   => esc_html__( 'Limited attempts', 'masterstudy-lms-learning-management-system' ),
				),
				'value'       => 'unlimited',
			),
			'show_attempts_history' => array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Quiz Attempt History', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Let students view their past quiz attempts in the course player', 'masterstudy-lms-learning-management-system' ),
			),
			'retry_after_passing'   => array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Retake After Passing', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Allow students to retake the quiz even after passing', 'masterstudy-lms-learning-management-system' ),
			),
			'quiz_style'            => stm_lms_quiz_types(),
			'pro_banner'            => array(
				'type'  => 'pro_banner',
				'label' => esc_html__( 'Question Media', 'masterstudy-lms-learning-management-system' ),
				'img'   => STM_LMS_URL . 'assets/img/pro-features/addons/question-media-addon.png',
				'desc'  => esc_html__( 'Make your quizzes more interactive and engaging with the Question Media addon. Let admins and instructors create quiz questions while adding videos, audio, and images.', 'masterstudy-lms-learning-management-system' ),
				'hint'  => esc_html__( 'Unlock', 'masterstudy-lms-learning-management-system' ),
			),
		),
	);
	if ( ! is_ms_lms_addon_enabled( Addons::QUESTION_MEDIA ) && STM_LMS_Helpers::is_pro() ) {
		$quiz_fields['fields']['question_media_certificate'] = array(
			'type'        => 'pro_banner',
			'label'       => esc_html__( 'Question Media Addon', 'masterstudy-lms-learning-management-system' ),
			'img'         => STM_LMS_URL . 'assets/img/pro-features/addons/question-media-addon.png',
			'desc'        => sprintf( 'Let admins and instructors create interactive quiz questions while adding videos, audio, and images.' ),
			'search'      => esc_html__( 'Question Media', 'masterstudy-lms-learning-management-system' ),
			'is_enable'   => ! is_ms_lms_addon_enabled( Addons::QUESTION_MEDIA ) && STM_LMS_Helpers::is_pro_plus(),
			'is_pro_plus' => true,
			'hint'        => '',
			'utm_url'     => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=mswpadmin&utm_medium=question-media-addon-button&utm_campaign=masterstudy-plugin',
		);
	}

	return $quiz_fields;
}
