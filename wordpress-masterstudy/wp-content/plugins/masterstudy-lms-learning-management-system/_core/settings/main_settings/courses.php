<?php

/**
 * Course section settings.
 */
function stm_lms_settings_courses_section() {
	$pages = WPCFTO_Settings::stm_get_post_type_array( 'page' );

	$courses_settings_fields = array(
		'name'   => esc_html__( 'Courses', 'masterstudy-lms-learning-management-system' ),
		'label'  => esc_html__( 'Courses Settings', 'masterstudy-lms-learning-management-system' ),
		'icon'   => 'stmlms-book-2',
		'fields' => array(
			'demo_import'                       => array(
				'type' => 'demo_import',
			),
			/*GROUP STARTED*/
			'courses_page'                      => array(
				'group'       => 'started',
				'group_title' => esc_html__( 'Page Layout', 'masterstudy-lms-learning-management-system' ),
				'type'        => 'select',
				'label'       => esc_html__( 'Courses page', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Choose the main page where all courses are shown', 'masterstudy-lms-learning-management-system' ),
				'options'     => $pages,
				'columns'     => '50',
			),
			'courses_view'                      => array(
				'type'        => 'radio',
				'label'       => esc_html__( 'Courses page layout', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Choose how the courses look on the page', 'masterstudy-lms-learning-management-system' ),
				'options'     => array(
					'grid'    => esc_html__( 'Grid', 'masterstudy-lms-learning-management-system' ),
					'list'    => esc_html__( 'List', 'masterstudy-lms-learning-management-system' ),
					'masonry' => esc_html__( 'Masonry', 'masterstudy-lms-learning-management-system' ),
				),
				'soon'        => array( 'masonry' => true ),
				'value'       => 'grid',
				'columns'     => '50',
			),
			'courses_per_row'                   => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Courses per row', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Define how many courses are shown in a single row', 'masterstudy-lms-learning-management-system' ),
				'options'     => array(
					'2' => 2,
					'3' => 3,
					'4' => 4,
					'6' => 6,
				),
				'value'       => '4',
				'columns'     => '33',
			),
			'courses_per_page'                  => array(
				'type'        => 'number',
				'label'       => esc_html__( 'Courses per page', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Define how many courses are shown on one page', 'masterstudy-lms-learning-management-system' ),
				'value'       => '9',
				'columns'     => '33',
			),
			'load_more_type'                    => array(
				'group'       => 'ended',
				'type'        => 'select',
				'label'       => esc_html__( 'Load more type', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Choose how to load more courses', 'masterstudy-lms-learning-management-system' ),
				'options'     => array(
					'button'   => esc_html__( 'Button', 'masterstudy-lms-learning-management-system' ),
					'infinite' => esc_html__( 'Infinite Scrolling', 'masterstudy-lms-learning-management-system' ),
				),
				'value'       => 'button',
				'columns'     => '33',
			),
			/*GROUP ENDED*/

			/*GROUP STARTED*/
			'course_card_style'                 => array(
				'group'       => 'started',
				'group_title' => esc_html__( 'Course Card', 'masterstudy-lms-learning-management-system' ),
				'type'        => 'radio',
				'label'       => esc_html__( 'Course card style', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Choose how to show a course card', 'masterstudy-lms-learning-management-system' ),
				'options'     => array(
					'style_1' => esc_html__( 'Default', 'masterstudy-lms-learning-management-system' ),
					'style_2' => esc_html__( 'Price on Hover', 'masterstudy-lms-learning-management-system' ),
					'style_3' => esc_html__( 'Scale on Hover', 'masterstudy-lms-learning-management-system' ),
				),
				'value'       => 'style_1',
				'columns'     => '50',
			),
			'course_card_view'                  => array(
				'type'        => 'radio',
				'label'       => esc_html__( 'Course card info', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Put the info in the center or on the right side', 'masterstudy-lms-learning-management-system' ),
				'options'     => array(
					'center' => esc_html__( 'Center', 'masterstudy-lms-learning-management-system' ),
					'right'  => esc_html__( 'Right', 'masterstudy-lms-learning-management-system' ),
				),
				'dependency'  => array(
					'key'   => 'course_card_style',
					'value' => 'style_1',
				),
				'value'       => 'center',
				'columns'     => '50',
			),
			'courses_image_size'                => array(
				'type'        => 'text',
				'label'       => esc_html__( 'Course image size', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Set the size for course images, like 330x185', 'masterstudy-lms-learning-management-system' ),
				'value'       => '330x185',
				'columns'     => '50',
				'hint'        => __( 'Set custom image size. The size will be taken from the nearest standard size in WordPress gallery, but it will not be smaller than the dimensions specified in this setting, provided that the uploaded image has larger dimensions.', 'masterstudy-lms-learning-management-system' ),
			),
			'course_card_display_info'          => array(
				'group'       => 'ended',
				'type'        => 'notification_message',
				'description' => sprintf(
				// translators: Card display information.
					__( 'These settings only apply if you use the <a href="%1$s" target="_blank">Archive</a> page for your Course Catalog. If you use WPBakery or Elementor, set these up in the LMS widgets <a href="%2$s" target="_blank">Learn more</a>', 'masterstudy-lms-learning-management-system' ),
					esc_url( 'https://stylemixthemes.com/wp/what-is-a-wordpress-archive-page/' ),
					esc_url( 'https://docs.stylemixthemes.com/masterstudy-lms/lms-settings/courses#course-card' )
				),
			),

			/*GROUP ENDED*/

			'enable_lazyload'                   => array(
				'type'        => 'checkbox',
				'toggle'      => true,
				'label'       => esc_html__( 'Lazy loading', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Turn this on to load images only when users scroll to them. This makes the page load faster', 'masterstudy-lms-learning-management-system' ),
			),
			'courses_categories_slug'           => array(
				'type'        => 'text',
				'label'       => esc_html__( 'Category slug', 'masterstudy-lms-learning-management-system' ),
				'value'       => 'stm_lms_course_category',
				'description' => sprintf(
				// translators: Card display information.
					__( 'A unique name that shows in the URL before the category, like in <br/> example.com/blog/course-category/templates. Here, "course-category" is the slug, and "templates" is the category', 'masterstudy-lms-learning-management-system' ),
				),
			),
			'enable_courses_filter'             => array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Filters on the Archive page', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Allow users to filter courses on the Archive page', 'masterstudy-lms-learning-management-system' ),
			),
			'course_categories_sort_alpha'      => array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Sort course categories alphabetically', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Enable this to display course categories in alphabetical order in the filters sidebar on courses page.', 'masterstudy-lms-learning-management-system' ),
			),

			/*GROUP STARTED*/
			'enable_courses_filter_category'    => array(
				'group'       => 'started',
				'type'        => 'checkbox',
				'group_title' => esc_html__( 'Course Filters', 'masterstudy-lms-learning-management-system' ),
				'label'       => esc_html__( 'Enable filter - Category', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Let users filter courses by category', 'masterstudy-lms-learning-management-system' ),
				'toggle'      => false,
				'columns'     => '33',
				'dependency'  => array(
					'key'   => 'enable_courses_filter',
					'value' => 'not_empty',
				),
			),
			'enable_courses_filter_subcategory' => array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Enable filter - Subcategory', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Let users filter courses by subcategory', 'masterstudy-lms-learning-management-system' ),
				'toggle'      => false,
				'columns'     => '33',
				'dependency'  => array(
					'key'   => 'enable_courses_filter',
					'value' => 'not_empty',
				),
			),
			'enable_courses_filter_levels'      => array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Enable filter - Levels', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Let users filter courses by difficulty level', 'masterstudy-lms-learning-management-system' ),
				'toggle'      => false,
				'columns'     => '33',
				'dependency'  => array(
					'key'   => 'enable_courses_filter',
					'value' => 'not_empty',
				),
			),
			'enable_courses_filter_rating'      => array(
				'type'         => 'checkbox',
				'label'        => esc_html__( 'Enable filter - Rating', 'masterstudy-lms-learning-management-system' ),
				'description'  => esc_html__( 'Let users filter courses by rating', 'masterstudy-lms-learning-management-system' ),
				'toggle'       => false,
				'columns'      => '33',
				'dependency'   => array(
					array(
						'key'   => 'enable_courses_filter',
						'value' => 'not_empty',
					),
					array(
						'key'     => 'course_tab_reviews',
						'value'   => 'not_empty',
						'section' => 'section_course',
					),
				),
				'dependencies' => '&&',
			),
			'enable_courses_filter_status'      => array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Enable filter - Status', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Let users filter courses by their status', 'masterstudy-lms-learning-management-system' ),
				'toggle'      => false,
				'columns'     => '33',
				'dependency'  => array(
					'key'   => 'enable_courses_filter',
					'value' => 'not_empty',
				),
			),
			'enable_courses_filter_instructor'  => array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Enable filter - Instructor', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Let users filter courses by the instructor', 'masterstudy-lms-learning-management-system' ),
				'toggle'      => false,
				'columns'     => '33',
				'dependency'  => array(
					'key'   => 'enable_courses_filter',
					'value' => 'not_empty',
				),
			),
			'enable_courses_filter_price'       => array(
				'group'       => 'ended',
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Enable filter - Price', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Let users filter courses by price', 'masterstudy-lms-learning-management-system' ),
				'toggle'      => false,
				'columns'     => '33',
				'dependency'  => array(
					'key'   => 'enable_courses_filter',
					'value' => 'not_empty',
				),
			),
			/*GROUP ENDED*/
		),
	);

	if ( is_ms_lms_addon_enabled( \MasterStudy\Lms\Plugin\Addons::COOMING_SOON ) && STM_LMS_Helpers::is_pro_plus() ) {
		$courses_filter_field              = array(
			'enable_courses_filter_availability' => array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Enable filter - Availability', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Courses  Let users filter courses by availability for completion', 'masterstudy-lms-learning-management-system' ),
				'toggle'      => false,
				'columns'     => '33',
				'dependency'  => array(
					'key'   => 'enable_courses_filter',
					'value' => 'not_empty',
				),
				'pro'         => true,
				'pro_url'     => admin_url( 'admin.php?page=stm-lms-go-pro' ),
			),
		);
		$courses_settings_fields['fields'] = array_merge( $courses_settings_fields['fields'], $courses_filter_field );
	}

	if ( STM_LMS_Helpers::is_pro() ) {
		$featured_courses = array(
			'enable_featured_courses'    => array(
				'group'       => 'started',
				'group_title' => esc_html__( 'Featured Courses', 'masterstudy-lms-learning-management-system' ),
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Featured Courses', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Enable this option to highlight selected courses as featured throughout your site.', 'masterstudy-lms-learning-management-system' ),
				'value'       => true,
				'pro'         => true,
			),
			'courses_featured_num'       => array(
				'type'        => 'number',
				'label'       => esc_html__( 'Number of featured courses', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Define how many courses you want to highlight as special', 'masterstudy-lms-learning-management-system' ),
				'value'       => 1,
				'dependency'  => array(
					'key'   => 'enable_featured_courses',
					'value' => 'not_empty',
				),
			),
			'disable_featured_courses'   => array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Show Featured Courses at the Top of the Courses Page', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Display featured courses before all others on the courses page. This does not work for pages created with page builders (Elementor, Gutenberg etc.).', 'masterstudy-lms-learning-management-system' ),
				'dependency'  => array(
					'key'   => 'enable_featured_courses',
					'value' => 'not_empty',
				),
			),
			'number_featured_in_archive' => array(
				'group'        => 'ended',
				'type'         => 'number',
				'label'        => esc_html__( 'Number of Courses in Featured Section', 'masterstudy-lms-learning-management-system' ),
				'description'  => esc_html__( 'Set how many featured courses are displayed inside the featured block on the courses archive page. The list is trimmed to this number. This does not work for pages created with page builders.', 'masterstudy-lms-learning-management-system' ),
				'value'        => 3,
				'dependency'   => array(
					array(
						'key'   => 'disable_featured_courses',
						'value' => 'not_empty',
					),
					array(
						'key'   => 'enable_featured_courses',
						'value' => 'not_empty',
					),
				),
				'dependencies' => '&&',
			),
		);
	} else {
		$featured_courses = array(
			'enable_featured_courses' => array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Featured Courses', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Enable this option to highlight selected courses as featured throughout your site.', 'masterstudy-lms-learning-management-system' ),
				'value'       => true,
				'pro'         => true,
			),
		);
	}

	$courses_settings_fields['fields'] = array_merge( $courses_settings_fields['fields'], $featured_courses );

	$pro_banner = array(
		'pro_banner' => array(
			'type'  => 'pro_banner',
			'label' => esc_html__( 'Upcoming Course Status Addon', 'masterstudy-lms-learning-management-system' ),
			'img'   => STM_LMS_URL . 'assets/img/pro-features/upcoming_nuxy_banner.png',
			'desc'  => esc_html__( 'Promote courses that are not ready for enrollment. Give a preview of the upcoming courses and a countdown to the launch date.', 'masterstudy-lms-learning-management-system' ),
			'hint'  => esc_html__( 'Unlock', 'masterstudy-lms-learning-management-system' ),
		),
	);

	$courses_settings_fields['fields'] = array_merge( $courses_settings_fields['fields'], $pro_banner );

	return $courses_settings_fields;
}
