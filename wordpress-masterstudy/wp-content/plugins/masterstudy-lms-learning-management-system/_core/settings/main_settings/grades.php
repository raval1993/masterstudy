<?php

function stm_lms_settings_grades_default_values() {
	return array(
		array(
			'grade' => 'A+',
			'point' => 5,
			'range' => array( 90, 100 ),
			'color' => '#227AFF',
		),
		array(
			'grade' => 'A',
			'point' => 4.5,
			'range' => array( 80, 89 ),
			'color' => '#61CC2F',
		),
		array(
			'grade' => 'A-',
			'point' => 4,
			'range' => array( 70, 79 ),
			'color' => '#61CC2F',
		),
		array(
			'grade' => 'B+',
			'point' => 3.5,
			'range' => array( 60, 69 ),
			'color' => '#FFA800',
		),
		array(
			'grade' => 'B',
			'point' => 3,
			'range' => array( 50, 59 ),
			'color' => '#FFA800',
		),
		array(
			'grade' => 'B-',
			'point' => 2.5,
			'range' => array( 40, 49 ),
			'color' => '#FFA800',
		),
		array(
			'grade' => 'C',
			'point' => 2,
			'range' => array( 30, 39 ),
			'color' => '#FF3945',
		),
		array(
			'grade' => 'C-',
			'point' => 1,
			'range' => array( 0, 29 ),
			'color' => '#e50505',
		),
	);
}

function stm_lms_settings_grades_section() {
	$grades_settings_fields = array(
		'name'   => esc_html__( 'Grades', 'masterstudy-lms-learning-management-system' ),
		'label'  => esc_html__( 'Grades Settings', 'masterstudy-lms-learning-management-system' ),
		'icon'   => 'masterstudy-grades-icon',
		'fields' => array(
			'grades_display'          => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Result display', 'masterstudy-lms-learning-management-system' ),
				'options'     => array(
					'grade'   => esc_html__( 'Grade', 'masterstudy-lms-learning-management-system' ),
					'point'   => esc_html__( 'Point', 'masterstudy-lms-learning-management-system' ),
					'percent' => esc_html__( 'Percent (%)', 'masterstudy-lms-learning-management-system' ),
				),
				'value'       => 'grade',
				'description' => esc_html__( 'Select the format to display scores to student', 'masterstudy-lms-learning-management-system' ),
			),
			'grades_scores_separator' => array(
				'type'        => 'text',
				'label'       => esc_html__( 'Score Separator', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Choose the symbol or text to separate the score from the maximum value', 'masterstudy-lms-learning-management-system' ),
				'value'       => '/',
			),
			'grades_page_display'     => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Grades Display on Course Page', 'masterstudy-lms-learning-management-system' ),
				'options'     => array(
					'tab'     => esc_html__( 'Show as separate tab', 'masterstudy-lms-learning-management-system' ),
					'sidebar' => esc_html__( 'Show in sidebar', 'masterstudy-lms-learning-management-system' ),
					'off'     => esc_html__( 'Do not show', 'masterstudy-lms-learning-management-system' ),
				),
				'value'       => 'tab',
				'description' => esc_html__( 'Select how grades will be shown', 'masterstudy-lms-learning-management-system' ),
			),
			'grades_table'            => array(
				'type'    => 'grades_table',
				'label'   => esc_html__( 'Grades Table', 'masterstudy-lms-learning-management-system' ),
				'options' => array(
					'grade' => array(
						'title' => esc_html__( 'Grade name', 'masterstudy-lms-learning-management-system' ),
						'type'  => 'text',
						'width' => '20%',
					),
					'point' => array(
						'title' => esc_html__( 'Grade point', 'masterstudy-lms-learning-management-system' ),
						'type'  => 'number',
						'width' => '40%',
					),
					'range' => array(
						'title' => esc_html__( 'Grade range', 'masterstudy-lms-learning-management-system' ),
						'type'  => 'range',
						'width' => '40%',
					),
					'color' => array(
						'title' => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
						'type'  => 'color',
						'for'   => 'name',
					),
				),
				'value'   => stm_lms_settings_grades_default_values(),
			),
		),
	);

	$is_grades_enabled = is_ms_lms_addon_enabled( 'grades' );
	$is_pro_plus       = STM_LMS_Helpers::is_pro_plus();

	if ( ! $is_pro_plus || ( $is_pro_plus && ! $is_grades_enabled ) ) {
		$grades_settings_fields = array(
			'name'   => esc_html__( 'Grades', 'masterstudy-lms-learning-management-system' ),
			'label'  => esc_html__( 'Grades Settings', 'masterstudy-lms-learning-management-system' ),
			'icon'   => 'masterstudy-grades-icon',
			'fields' => array(
				'pro_banner_grades' => array(
					'type'        => 'pro_banner',
					'label'       => esc_html__( 'Grades', 'masterstudy-lms-learning-management-system' ),
					'img'         => STM_LMS_URL . 'assets/img/pro-features/addons/grades.png',
					'desc'        => esc_html__( 'Evaluate students, grade assignments and quizzes, and give individual feedback. Check their progress at a glance with all scores organized in one place.', 'masterstudy-lms-learning-management-system' ),
					'hint'        => esc_html__( 'Unlock', 'masterstudy-lms-learning-management-system' ),
					'is_enable'   => $is_pro_plus && ! $is_grades_enabled,
					'is_pro_plus' => true,
					'search'      => esc_html__( 'Grades', 'masterstudy-lms-learning-management-system' ),
					'utm_url'     => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=mswpadmin&utm_medium=grades&utm_campaign=masterstudy-plugin',
				),
			),
		);
	}

	return $grades_settings_fields;
}
