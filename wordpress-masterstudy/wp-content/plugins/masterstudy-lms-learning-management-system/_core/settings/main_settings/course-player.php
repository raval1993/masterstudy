<?php
function stm_lms_settings_course_player_section() {
	$course_player_settings = array(
		'name'  => esc_html__( 'Course Player', 'masterstudy-lms-learning-management-system' ),
		'label' => esc_html__( 'Course Player Settings', 'masterstudy-lms-learning-management-system' ),
		'icon'  => 'stmlms-chalkboard-teacher',
	);

	$course_player_primary_fields = array(
		'course_player_theme_mode'                  => array(
			'type'    => 'select',
			'label'   => esc_html__( 'Default Theme', 'masterstudy-lms-learning-management-system' ),
			'options' => array(
				''  => esc_html__( 'Light', 'masterstudy-lms-learning-management-system' ),
				'1' => esc_html__( 'Dark', 'masterstudy-lms-learning-management-system' ),
			),
			'value'   => '',
			'hint'    => esc_html__( 'Pick a default look for the lesson page that users will see first', 'masterstudy-lms-learning-management-system' ),
		),
		'course_player_theme_fonts'                 => array(
			'type'        => 'checkbox',
			'label'       => esc_html__( 'Use theme fonts', 'masterstudy-lms-learning-management-system' ),
			'description' => esc_html__( 'Use the fonts from the theme on the lesson page', 'masterstudy-lms-learning-management-system' ),
			'value'       => false,
		),
		'course_player_brand_icon_navigation'       => array(
			'type'        => 'checkbox',
			'label'       => esc_html__( 'Show brand icon in navigation', 'masterstudy-lms-learning-management-system' ),
			'description' => esc_html__( 'Show a brand icon (like a logo) in the navigation bar', 'masterstudy-lms-learning-management-system' ),
			'value'       => false,
		),
		'course_player_brand_icon_navigation_image' => array(
			'type'       => 'image',
			'label'      => esc_html__( 'Upload an image for navigation', 'masterstudy-lms-learning-management-system' ),
			'hint'       => esc_html__( 'Upload a square image to use as the brand icon in the navigation bar', 'masterstudy-lms-learning-management-system' ),
			'dependency' => array(
				'key'   => 'course_player_brand_icon_navigation',
				'value' => 'not_empty',
			),
		),
		'course_player_discussions_sidebar'         => array(
			'type'        => 'checkbox',
			'label'       => esc_html__( 'Discussions board sidebar', 'masterstudy-lms-learning-management-system' ),
			'description' => esc_html__( 'Show a sidebar for discussion boards where instructors and students can chat', 'masterstudy-lms-learning-management-system' ),
			'value'       => true,
		),
		'course_player_youtube_video_player'        => array(
			'type'        => 'checkbox',
			'label'       => esc_html__( 'Use MasterStudy player for videos from Youtube', 'masterstudy-lms-learning-management-system' ),
			'description' => esc_html__( 'Play YouTube videos with the MasterStudy player', 'masterstudy-lms-learning-management-system' ),
			'value'       => false,
		),
		'course_player_vimeo_video_player'          => array(
			'type'        => 'checkbox',
			'label'       => esc_html__( 'Use MasterStudy player for videos from Vimeo', 'masterstudy-lms-learning-management-system' ),
			'description' => esc_html__( 'Play Vimeo videos with the MasterStudy player', 'masterstudy-lms-learning-management-system' ),
			'value'       => false,
		),
		'lesson_materials_title'                    => array(
			'type'        => 'text',
			'label'       => esc_html__( 'Lesson Materials Section Title', 'masterstudy-lms-learning-management-system' ),
			'value'       => esc_html__( 'Lesson Materials', 'masterstudy-lms-learning-management-system' ),
			'description' => esc_html__( 'Set the title for the downloadable materials section in the Course Player.', 'masterstudy-lms-learning-management-system' ),
		),
	);

	$course_player_settings['fields'] = $course_player_primary_fields;

	if ( STM_LMS_Helpers::is_pro_plus() ) {
		$course_player_pro_fields = array(
			'course_player_video_strict_mode' => array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Disable video seeking', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'This setting prevents users from skipping or rewinding videos.', 'masterstudy-lms-learning-management-system' ),
				'value'       => false,
			),
			'video_questions_title'           => array(
				'type'        => 'text',
				'label'       => esc_html__( 'Video Questions Section Title', 'masterstudy-lms-learning-management-system' ),
				'value'       => esc_html__( 'Video Questions', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Set the title for the video questions section in the Course Player.', 'masterstudy-lms-learning-management-system' ),
			),
			'video_questions_list_show'       => array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Video Questions Block', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Allows you to hide or show the questions block in video lessons.', 'masterstudy-lms-learning-management-system' ),
				'value'       => true,
			),
			'course_allow_students_bookmarks' => array(
				'type'  => 'checkbox',
				'label' => esc_html__( 'Allow students to create bookmarks in PDF lessons', 'masterstudy-lms-learning-management-system' ),
				'hint'  => esc_html__( 'Let students add bookmarks in PDF lessons to quickly revisit important pages.', 'masterstudy-lms-learning-management-system' ),
			),
			'pdf_bookmarks_section_title'     => array(
				'type'        => 'text',
				'label'       => esc_html__( 'Bookmarks Section Title', 'masterstudy-lms-learning-management-system' ),
				'value'       => esc_html__( 'Bookmarks', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Set the title for the bookmarks section in the Course Player.', 'masterstudy-lms-learning-management-system' ),
			),
			'course_player_lesson_notes'      => array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Lesson Notes', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Students will be able to create notes for the lessons they study and access them later from their profile', 'masterstudy-lms-learning-management-system' ),
				'value'       => false,
			),
		);

		$course_player_settings['fields'] = array_merge( $course_player_primary_fields, $course_player_pro_fields );
	}

	return $course_player_settings;
}
