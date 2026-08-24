<?php
function stm_lms_settings_general_section() {
	return array(
		'name'   => esc_html__( 'General', 'masterstudy-lms-learning-management-system' ),
		'label'  => esc_html__( 'General Settings', 'masterstudy-lms-learning-management-system' ),
		'icon'   => 'stmlms-sliders-h',
		'fields' => array(
			/*GROUP STARTED*/
			'main_color'            => array(
				'group'       => 'started',
				'type'        => 'color',
				'label'       => esc_html__( 'Main color', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Set the core website color', 'masterstudy-lms-learning-management-system' ),
				'columns'     => '33',
				'group_title' => esc_html__( 'Colors', 'masterstudy-lms-learning-management-system' ),
			),
			'secondary_color'       => array(
				'group'       => 'ended',
				'type'        => 'color',
				'label'       => esc_html__( 'Secondary color', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Set the secondary color for the site', 'masterstudy-lms-learning-management-system' ),
				'columns'     => '33',
			),
			/*GROUP ENDED*/

			/*GROUP STARTED*/
			'accent_color'          => array(
				'group'       => 'started',
				'type'        => 'color',
				'format'      => 'rgba',
				'value'       => 'rgba(34,122,255,1)',
				'label'       => esc_html__( 'Accent', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Pick a color for buttons, quiz info, chosen options, progress bar, notice background, links, and Trial course badge in the Course Player', 'masterstudy-lms-learning-management-system' ),
				'columns'     => '33',
				'group_title' => esc_html__( 'Base colors*', 'masterstudy-lms-learning-management-system' ),
			),
			'danger_color'          => array(
				'type'        => 'color',
				'format'      => 'rgba',
				'value'       => 'rgba(255,57,69,1)',
				'label'       => esc_html__( 'Danger', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Select a color for required but unfilled fields, wrong options chosen in quizzes, and notifications for failed quizzes and assignments', 'masterstudy-lms-learning-management-system' ),
				'columns'     => '33',
			),
			'warning_color'         => array(
				'type'        => 'color',
				'format'      => 'rgba',
				'value'       => 'rgba(255,168,0,1)',
				'label'       => esc_html__( 'Warning', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Choose a color for warnings', 'masterstudy-lms-learning-management-system' ),
				'columns'     => '33',
			),
			'success_color'         => array(
				'type'        => 'color',
				'format'      => 'rgba',
				'value'       => 'rgba(97,204,47,1)',
				'label'       => esc_html__( 'Success', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Choose a color for wrong options chosen in quizzes, and notifications for passed quizzes and assignments', 'masterstudy-lms-learning-management-system' ),
				'columns'     => '33',
			),
			'base_colors_info'      => array(
				'group'       => 'ended',
				'type'        => 'notification_message',
				'description' => esc_html__( '* These colors will be applied to Course Player pages, Authorization pages and popups. In future updates, they will be applied to all pages and Accent color will replace Main color.', 'masterstudy-lms-learning-management-system' ),
			),
			/*GROUP ENDED*/

			'print_page_logo'       => array(
				'type'    => 'image',
				'label'   => esc_html__( 'Logo Upload', 'masterstudy-lms-learning-management-system' ),
				'hint'    => esc_html__( 'Upload your brand\'s logo image for the print page', 'masterstudy-lms-learning-management-system' ),
				'pro'     => true,
				'pro_url' => admin_url( 'admin.php?page=stm-lms-go-pro&source=print-logo' ),
			),
			'deny_instructor_admin' => array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Restrict WordPress Dashboard access for instructors and students', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Instructors and students will be prevented from accessing the WordPress dashboard. Instead, they will be redirected to their personal account dashboards.', 'masterstudy-lms-learning-management-system' ),
			),
			'course_builder_fonts'  => array(
				'type'     => 'typography',
				'label'    => esc_html__( 'Course Builder Font Family', 'masterstudy-lms-learning-management-system' ),
				'excluded' => array(
					'backup-font',
					'font-size',
					'font-weight',
					'line-height',
					'letter-spacing',
					'word-spacing',
					'text-transform',
					'text-align',
					'subset',
					'google-weight',
					'color',
				),
			),
			'ms_plugin_preloader'   => array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Loading animation', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'An animation that shows when something is loading', 'masterstudy-lms-learning-management-system' ),
				'value'       => false,
			),
		),
	);
}
