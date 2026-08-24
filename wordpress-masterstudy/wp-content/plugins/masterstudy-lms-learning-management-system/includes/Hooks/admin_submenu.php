<?php

/**
 * Admin menu and submenu pages.
 */

function masterstudy_analytics_main_page() {
	add_menu_page(
		esc_html__( 'Revenue', 'masterstudy-lms-learning-management-system' ),
		esc_html__( 'Analytics', 'masterstudy-lms-learning-management-system' ),
		'manage_options',
		'revenue',
		'masterstudy_analytics_revenue_page',
		'dashicons-chart-area',
		4
	);
}
add_action( 'admin_menu', 'masterstudy_analytics_main_page' );

function masterstudy_analytics_revenue_page() {
	if ( STM_LMS_Helpers::is_pro_plus() ) {
		STM_LMS_Templates::show_lms_template( 'components/admin-react-app/main' );
	} else {
		STM_LMS_Templates::show_lms_template( 'analytics-preview' );
	}
}

function masterstudy_remove_admin_notices() {
	$screen = get_current_screen();
	$pages  = array(
		'toplevel_page_revenue',
		'analytics_page_engagement',
		'analytics_page_users',
		'analytics_page_reviews',
		'toplevel_page_grades',
		'masterstudy_page_manage_orders',
		'masterstudy_page_manage_lessons',
		'masterstudy_page_manage_quizzes',
		'masterstudy_page_manage_assignments',
		'masterstudy_page_manage_students',
		'masterstudy_page_manage_instructors',
		'masterstudy_page_manage_questions',
		'masterstudy_page_manage_memberships',
		'masterstudy_page_manage_membership_plans',
		'masterstudy_page_manage_bundles',
		'masterstudy_page_manage_coupons',
		'masterstudy_page_manage_questions_categories',
		'masterstudy_page_manage_courses_categories',
		'masterstudy_page_manage_courses',
		'masterstudy_page_manage_google_meets',
		'masterstudy_page_manage_students_assignments',
		'masterstudy_page_manage_reviews',
		'masterstudy_page_manage_enterprise_groups',
		'stm-lms-settings_page_stm_lms_statistics',
		'toplevel_page_manage_courses',
		'toplevel_page_manage_lessons',
		'toplevel_page_manage_quizzes',
		'toplevel_page_manage_questions',
		'toplevel_page_manage_reviews',
		'toplevel_page_manage_assignments',
		'toplevel_page_manage_payouts',
		'toplevel_page_manage_bundles',
		'toplevel_page_manage_enterprise_groups',
		'toplevel_page_manage_certificates',
		'masterstudy_page_point_system_statistics',
		'classrooms_page_google_classrooms',
		'masterstudy_page_stm_lms_statistics',
	);

	if ( in_array( $screen->id, $pages, true ) ) {
		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );
	}
}
add_action( 'admin_head', 'masterstudy_remove_admin_notices' );

function masterstudy_lms_get_admin_submenu_item_by_slug( $slug, $registered_only = false ) {
	$slug_variants = masterstudy_lms_get_admin_submenu_slug_variants( $slug );

	foreach ( masterstudy_lms_resolve_admin_submenu_items( $registered_only ) as $item ) {
		$item_slugs = array_merge( array( $item['slug'] ), $item['aliases'] );

		foreach ( $item_slugs as $item_slug ) {
			$item_slug_variants = masterstudy_lms_get_admin_submenu_slug_variants( $item_slug );

			if ( array_intersect( $slug_variants, $item_slug_variants ) ) {
				return $item;
			}
		}
	}

	return null;
}

function masterstudy_lms_can_access_admin_submenu_item( $item ) {
	$capability = $item['capability'] ?? 'manage_options';

	if ( is_callable( $capability ) ) {
		$capability = call_user_func( $capability );
	}

	if ( empty( $capability ) ) {
		$capability = 'manage_options';
	}

	if ( ! current_user_can( $capability ) ) {
		return false;
	}

	$access_callback = $item['access_callback'] ?? null;

	if ( empty( $access_callback ) ) {
		return true;
	}

	if ( ! is_callable( $access_callback ) ) {
		return (bool) $access_callback;
	}

	return (bool) call_user_func( $access_callback, $item );
}

function masterstudy_lms_get_instructor_react_menu_items() {
	$items = array(
		'manage_courses'   => array(
			'page_title' => esc_html__( 'Courses', 'masterstudy-lms-learning-management-system' ),
			'menu_title' => esc_html__( 'Courses', 'masterstudy-lms-learning-management-system' ),
			'capability' => 'stm_lms_instructor',
			'menu_slug'  => 'manage_courses',
			'icon_url'   => 'dashicons-admin-post',
			'position'   => 26,
			'app_slug'   => 'react_courses',
		),
		'manage_lessons'   => array(
			'page_title' => esc_html__( 'Lessons', 'masterstudy-lms-learning-management-system' ),
			'menu_title' => esc_html__( 'Lessons', 'masterstudy-lms-learning-management-system' ),
			'capability' => 'stm_lms_instructor',
			'menu_slug'  => 'manage_lessons',
			'icon_url'   => 'dashicons-admin-post',
			'position'   => 27,
			'app_slug'   => 'react_lessons',
		),
		'manage_quizzes'   => array(
			'page_title' => esc_html__( 'Quizzes', 'masterstudy-lms-learning-management-system' ),
			'menu_title' => esc_html__( 'Quizzes', 'masterstudy-lms-learning-management-system' ),
			'capability' => 'stm_lms_instructor',
			'menu_slug'  => 'manage_quizzes',
			'icon_url'   => 'dashicons-admin-post',
			'position'   => 28,
			'app_slug'   => 'react_quizzes',
		),
		'manage_questions' => array(
			'page_title' => esc_html__( 'Questions', 'masterstudy-lms-learning-management-system' ),
			'menu_title' => esc_html__( 'Questions', 'masterstudy-lms-learning-management-system' ),
			'capability' => 'stm_lms_instructor',
			'menu_slug'  => 'manage_questions',
			'icon_url'   => 'dashicons-admin-post',
			'position'   => 29,
			'app_slug'   => 'react_questions',
		),
		'manage_reviews'   => array(
			'page_title' => esc_html__( 'Reviews', 'masterstudy-lms-learning-management-system' ),
			'menu_title' => esc_html__( 'Reviews', 'masterstudy-lms-learning-management-system' ),
			'capability' => 'stm_lms_instructor',
			'menu_slug'  => 'manage_reviews',
			'icon_url'   => 'dashicons-admin-post',
			'position'   => 30,
			'app_slug'   => 'react_reviews',
		),
	);

	return apply_filters( 'masterstudy_lms_instructor_react_menu_items', $items );
}

function masterstudy_lms_get_instructor_react_menu_item_by_slug( $slug ) {
	foreach ( masterstudy_lms_get_instructor_react_menu_items() as $item ) {
		if ( ! empty( $item['menu_slug'] ) && $slug === $item['menu_slug'] ) {
			return $item;
		}
	}

	return null;
}

function masterstudy_lms_can_access_instructor_react_menu_item( $item ) {
	$capability = $item['capability'] ?? 'stm_lms_instructor';

	return ! current_user_can( 'manage_options' ) && current_user_can( $capability );
}

function masterstudy_lms_render_admin_react_page() {
	$current_page = sanitize_text_field( wp_unslash( $_GET['page'] ?? '' ) );
	$item         = masterstudy_lms_get_admin_submenu_item_by_slug( $current_page, true );

	if ( ! empty( $item ) && masterstudy_lms_can_access_admin_submenu_item( $item ) ) {
		STM_LMS_Templates::show_lms_template( 'components/admin-react-app/main' );
		return;
	}

	$item = masterstudy_lms_get_instructor_react_menu_item_by_slug( $current_page );

	if ( ! empty( $item ) && masterstudy_lms_can_access_instructor_react_menu_item( $item ) ) {
		STM_LMS_Templates::show_lms_template( 'components/admin-react-app/main' );
		return;
	}

	wp_die( esc_html__( 'You are not allowed to access this page.', 'masterstudy-lms-learning-management-system' ) );
}

function masterstudy_lms_get_admin_submenu_items() {
	$items = array(
		'manage_courses'                                 => array(
			'aliases'    => array(
				'edit.php?post_type=stm-courses',
				'/edit.php?post_type=stm-courses',
				'stm-lms-courses-link',
			),
			'order'      => 10,
			'register'   => true,
			'page_title' => esc_html__( 'Courses', 'masterstudy-lms-learning-management-system' ),
			'menu_title' => esc_html__( 'Courses', 'masterstudy-lms-learning-management-system' ),
			'capability' => 'manage_options',
			'callback'   => 'masterstudy_lms_render_admin_react_page',
		),
		'manage_lessons'                                 => array(
			'aliases'    => array( 'edit.php?post_type=stm-lessons' ),
			'order'      => 30,
			'register'   => true,
			'page_title' => esc_html__( 'Lessons', 'masterstudy-lms-learning-management-system' ),
			'menu_title' => esc_html__( 'Lessons', 'masterstudy-lms-learning-management-system' ),
			'capability' => 'manage_options',
			'callback'   => 'masterstudy_lms_render_admin_react_page',
		),
		'manage_quizzes'                                 => array(
			'aliases'    => array( 'edit.php?post_type=stm-quizzes' ),
			'order'      => 40,
			'register'   => true,
			'page_title' => esc_html__( 'Quizzes', 'masterstudy-lms-learning-management-system' ),
			'menu_title' => esc_html__( 'Quizzes', 'masterstudy-lms-learning-management-system' ),
			'capability' => 'manage_options',
			'callback'   => 'masterstudy_lms_render_admin_react_page',
		),
		'manage_questions'                               => array(
			'aliases'    => array(
				'edit.php?post_type=stm-questions',
				'/edit.php?post_type=stm-questions',
			),
			'order'      => 50,
			'register'   => true,
			'page_title' => esc_html__( 'Questions', 'masterstudy-lms-learning-management-system' ),
			'menu_title' => esc_html__( 'Questions', 'masterstudy-lms-learning-management-system' ),
			'capability' => 'manage_options',
			'callback'   => 'masterstudy_lms_render_admin_react_page',
		),
		'manage_reviews'                                 => array(
			'aliases'    => array(
				'edit.php?post_type=stm-reviews',
				'/edit.php?post_type=stm-reviews',
			),
			'order'      => 93,
			'register'   => true,
			'page_title' => esc_html__( 'Reviews', 'masterstudy-lms-learning-management-system' ),
			'menu_title' => esc_html__( 'Reviews', 'masterstudy-lms-learning-management-system' ),
			'capability' => 'manage_options',
			'callback'   => 'masterstudy_lms_render_admin_react_page',
		),
		'manage_courses_categories'                      => array(
			'aliases'    => array(
				'edit-tags.php?taxonomy=stm_lms_course_taxonomy',
				'/edit-tags.php?taxonomy=stm_lms_course_taxonomy',
				'edit-tags.php?taxonomy=stm_lms_course_taxonomy&post_type=stm-courses',
				'/edit-tags.php?taxonomy=stm_lms_course_taxonomy&post_type=stm-courses',
			),
			'order'      => 11,
			'register'   => true,
			'page_title' => esc_html__( 'Course Category', 'masterstudy-lms-learning-management-system' ),
			'menu_title' => '<span class="stm-lms-contextual-submenu-title"><span class="stm-lms-menu-text">' . esc_html__( '⤷ Course Category', 'masterstudy-lms-learning-management-system' ) . '</span></span>',
			'capability' => 'manage_options',
			'callback'   => 'masterstudy_lms_render_admin_react_page',
		),
		'manage_questions_categories'                    => array(
			'aliases'    => array(
				'edit-tags.php?taxonomy=stm_lms_question_taxonomy',
				'/edit-tags.php?taxonomy=stm_lms_question_taxonomy',
				'edit-tags.php?taxonomy=stm_lms_question_taxonomy&post_type=stm-questions',
				'/edit-tags.php?taxonomy=stm_lms_question_taxonomy&post_type=stm-questions',
			),
			'order'      => 51,
			'register'   => true,
			'page_title' => esc_html__( 'Question Category', 'masterstudy-lms-learning-management-system' ),
			'menu_title' => '<span class="stm-lms-contextual-submenu-title"><span class="stm-lms-menu-text">' . esc_html__( '⤷ Question Category', 'masterstudy-lms-learning-management-system' ) . '</span></span>',
			'capability' => 'manage_options',
			'callback'   => 'masterstudy_lms_render_admin_react_page',
		),
		'manage_orders'                                  => array(
			'order'      => 90,
			'register'   => true,
			'page_title' => esc_html__( 'Orders', 'masterstudy-lms-learning-management-system' ),
			'menu_title' => esc_html__( 'Orders', 'masterstudy-lms-learning-management-system' ),
			'capability' => 'manage_options',
			'callback'   => 'masterstudy_lms_render_admin_react_page',
		),
		'manage_instructors'                             => array(
			'aliases'    => array( 'manage_users' ),
			'order'      => 120,
			'register'   => true,
			'page_title' => esc_html__( 'Instructors', 'masterstudy-lms-learning-management-system' ),
			'menu_title' => '<span class="stm-lms-instructors-menu-title">' . esc_html__( 'Instructors', 'masterstudy-lms-learning-management-system' ) . '</span>',
			'capability' => 'manage_options',
			'callback'   => 'masterstudy_lms_render_admin_react_page',
		),
		'manage_students'                                => array(
			'order'      => 130,
			'register'   => true,
			'page_title' => esc_html__( 'Students', 'masterstudy-lms-learning-management-system' ),
			'menu_title' => '<span class="stm-lms-students-menu-title"><span class="stm-lms-menu-text">' . esc_html__( 'Students', 'masterstudy-lms-learning-management-system' ) . '</span></span>',
			'capability' => 'manage_options',
			'callback'   => 'masterstudy_lms_render_admin_react_page',
		),
		'stm-lms-settings'                               => array(
			'order'      => 170,
			'register'   => true,
			'page_title' => 'MasterStudy',
			'menu_title' => '<span class="stm-lms-settings-menu-title">' . esc_html__( 'Settings', 'masterstudy-lms-learning-management-system' ) . '</span>',
			'capability' => 'manage_options',
		),
		'stm-support-page-masterstudy'                   => array(
			'order'      => 180,
			'register'   => true,
			'page_title' => esc_html__( 'Help Center', 'masterstudy-lms-learning-management-system' ),
			'menu_title' => esc_html__( 'Help Center', 'masterstudy-lms-learning-management-system' ),
			'capability' => 'manage_options',
			'callback'   => 'masterstudy_lms_render_support_page',
		),
		'admin.php?page=masterstudy-starter-demo-import' => array(
			'order'      => 190,
			'register'   => true,
			'page_title' => esc_html__( 'MasterStudy templates', 'masterstudy-lms-learning-management-system' ),
			'menu_title' => '<span class="stm-lms-templates-menu-title">' . esc_html__( 'MasterStudy', 'masterstudy-lms-learning-management-system' ) . ' <strong>' . esc_html__( 'Templates', 'masterstudy-lms-learning-management-system' ) . '</strong></span>',
			'capability' => 'manage_options',
			'condition'  => static function () {
				return ! STM_LMS_Helpers::is_theme_activated();
			},
		),
		'stm-lms-online-testing'                         => array(
			'order'    => 230,
			'register' => false,
		),
		'stm-addons'                                     => array(
			'order'    => 200,
			'register' => false,
		),
		'google_meet_settings'                           => array(
			'aliases'  => array( 'google-meet' ),
			'order'    => 158,
			'register' => false,
		),
		'mslms_zoom_settings'                            => array(
			'aliases'  => array( 'zoom-video-conferencing' ),
			'order'    => 159,
			'register' => false,
		),
		'upcoming-course-status'                         => array(
			'aliases'  => array( 'coming-soon' ),
			'order'    => 155,
			'register' => false,
		),
		'sequential_drip_content'                        => array(
			'aliases'  => array( 'drip-content' ),
			'order'    => 156,
			'register' => false,
		),
		'assignments_settings'                           => array(
			'aliases'  => array( 'assignments' ),
			'order'    => 157,
			'register' => false,
		),
		'media_library_settings'                         => array(
			'aliases'  => array( 'media-file-manager' ),
			'order'    => 151,
			'register' => false,
		),
		'enterprise_courses'                             => array(
			'aliases'  => array( 'group-courses' ),
			'order'    => 152,
			'register' => false,
		),
		'scorm_settings'                                 => array(
			'aliases'  => array( 'scorm' ),
			'order'    => 153,
			'register' => false,
		),
		'email_manager_settings'                         => array(
			'aliases'  => array( 'email-manager' ),
			'order'    => 140,
			'register' => false,
		),
		'form_builder'                                   => array(
			'aliases'  => array( 'lms-form-editor' ),
			'order'    => 150,
			'register' => false,
		),
		'course_bundle_settings'                         => array(
			'aliases'  => array( 'course-bundles' ),
			'order'    => 154,
			'register' => false,
		),
	);

	return apply_filters( 'masterstudy_lms_admin_submenu_items', $items );
}

function masterstudy_lms_normalize_admin_submenu_slug( $slug ) {
	return ltrim( (string) $slug, '/' );
}

function masterstudy_lms_get_admin_submenu_slug_variants( $slug ) {
	$slug       = (string) $slug;
	$normalized = masterstudy_lms_normalize_admin_submenu_slug( $slug );
	$variants   = array( $slug, $normalized );

	return array_values( array_unique( array_filter( $variants, 'strlen' ) ) );
}

function masterstudy_lms_resolve_admin_submenu_items( $registered_only = false ) {
	$resolved_items = array();

	foreach ( masterstudy_lms_get_admin_submenu_items() as $key => $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		$item['slug']     = isset( $item['slug'] ) ? (string) $item['slug'] : (string) $key;
		$item['aliases']  = array_values(
			array_filter(
				array_map(
					'strval',
					(array) ( $item['aliases'] ?? array() )
				)
			)
		);
		$item['order']    = isset( $item['order'] ) ? (int) $item['order'] : PHP_INT_MAX;
		$item['register'] = ! empty( $item['register'] );

		if ( ! empty( $item['condition'] ) && is_callable( $item['condition'] ) && ! call_user_func( $item['condition'] ) ) {
			continue;
		}

		if ( $registered_only && ! $item['register'] ) {
			continue;
		}

		$resolved_items[ $key ] = $item;
	}

	return $resolved_items;
}

function masterstudy_lms_register_admin_submenu_pages() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	foreach ( masterstudy_lms_resolve_admin_submenu_items( true ) as $item ) {
		$capability = $item['capability'] ?? 'manage_options';
		$callback   = $item['callback'] ?? null;

		if ( is_callable( $capability ) ) {
			$capability = call_user_func( $capability );
		}

		if ( empty( $capability ) ) {
			$capability = 'manage_options';
		}

		if ( empty( $callback ) ) {
			add_submenu_page(
				'stm-lms-settings',
				$item['page_title'],
				$item['menu_title'],
				$capability,
				$item['slug']
			);

			continue;
		}

		add_submenu_page(
			'stm-lms-settings',
			$item['page_title'],
			$item['menu_title'],
			$capability,
			$item['slug'],
			$callback
		);
	}
}
add_action( 'admin_menu', 'masterstudy_lms_register_admin_submenu_pages', 100001 );

function masterstudy_lms_register_instructor_react_menu_pages() {
	if ( current_user_can( 'manage_options' ) || ! current_user_can( 'stm_lms_instructor' ) ) {
		return;
	}

	foreach ( masterstudy_lms_get_instructor_react_menu_items() as $item ) {
		remove_menu_page( $item['menu_slug'] );

		add_menu_page(
			$item['page_title'],
			$item['menu_title'],
			$item['capability'] ?? 'stm_lms_instructor',
			$item['menu_slug'],
			'masterstudy_lms_render_admin_react_page',
			$item['icon_url'] ?? 'dashicons-welcome-learn-more',
			$item['position'] ?? null
		);
	}
}
add_action( 'admin_menu', 'masterstudy_lms_register_instructor_react_menu_pages', 100001 );

function masterstudy_lms_sort_admin_submenu_pages() {
	global $submenu;

	if ( empty( $submenu['stm-lms-settings'] ) || ! is_array( $submenu['stm-lms-settings'] ) ) {
		return;
	}

	$registered_items = masterstudy_lms_resolve_admin_submenu_items();

	uasort(
		$registered_items,
		static function ( $first_item, $second_item ) {
			return $first_item['order'] <=> $second_item['order'];
		}
	);

	$matched_items = array();
	$leading_items = array();
	$slug_map      = array();
	$unknown_items = array();

	foreach ( $registered_items as $key => $item ) {
		foreach ( masterstudy_lms_get_admin_submenu_slug_variants( $item['slug'] ) as $slug_variant ) {
			$slug_map[ $slug_variant ] = $key;
		}

		foreach ( $item['aliases'] as $alias ) {
			foreach ( masterstudy_lms_get_admin_submenu_slug_variants( $alias ) as $slug_variant ) {
				$slug_map[ $slug_variant ] = $key;
			}
		}
	}

	foreach ( $submenu['stm-lms-settings'] as $index => $submenu_item ) {
		$submenu_slug            = $submenu_item[2] ?? '';
		$normalized_submenu_slug = masterstudy_lms_normalize_admin_submenu_slug( $submenu_slug );

		$matched_key = $slug_map[ $submenu_slug ] ?? $slug_map[ $normalized_submenu_slug ] ?? null;

		if ( null !== $matched_key ) {
			$matched_items[ $matched_key ][] = array(
				'index'           => $index,
				'slug'            => $submenu_slug,
				'normalized_slug' => $normalized_submenu_slug,
				'item'            => $submenu_item,
			);
			continue;
		}

		$unknown_items[] = $submenu_item;
	}

	$sorted_items = array();

	foreach ( $registered_items as $key => $registered_item ) {
		if ( empty( $matched_items[ $key ] ) ) {
			continue;
		}

		$selected_item            = null;
		$registered_slug_variants = masterstudy_lms_get_admin_submenu_slug_variants( $registered_item['slug'] );

		if ( 'stm-lms-settings' === $registered_item['slug'] && ! empty( $registered_item['menu_title'] ) ) {
			foreach ( $matched_items[ $key ] as $matched_item ) {
				if ( ( $matched_item['item'][0] ?? '' ) === $registered_item['menu_title'] ) {
					$selected_item = $matched_item['item'];
					break;
				}
			}
		}

		foreach ( $matched_items[ $key ] as $matched_item ) {
			if ( null !== $selected_item ) {
				break;
			}

			if ( in_array( $matched_item['slug'], $registered_slug_variants, true ) || in_array( $matched_item['normalized_slug'], $registered_slug_variants, true ) ) {
				$selected_item = $matched_item['item'];
				break;
			}
		}

		if ( null === $selected_item ) {
			$selected_item = $matched_items[ $key ][0]['item'];
		}

		if ( ! empty( $registered_item['menu_title'] ) ) {
			$selected_item[0] = $registered_item['menu_title'];
		}

		if ( 'stm-lms-settings' === $registered_item['slug'] && count( $matched_items[ $key ] ) > 1 ) {
			foreach ( $matched_items[ $key ] as $matched_item ) {
				if ( $matched_item['item'] === $selected_item ) {
					continue;
				}

				$leading_items[] = $matched_item['item'];
			}
		}

		$sorted_items[] = $selected_item;
	}

	if ( ! defined( 'STM_LMS_PRO_PATH' ) ) {
		$anchor_slugs = array(
			'stm-lms-settings',
			'stm-support-page-masterstudy',
			'admin.php?page=masterstudy-starter-demo-import',
			'stm-addons',
		);
		$anchor_items = array();
		$free_items   = array();

		foreach ( $sorted_items as $sorted_item ) {
			$sorted_item_slug = masterstudy_lms_normalize_admin_submenu_slug( $sorted_item[2] ?? '' );

			if ( in_array( $sorted_item_slug, $anchor_slugs, true ) ) {
				$anchor_items[ $sorted_item_slug ] = $sorted_item;
				continue;
			}

			$free_items[] = $sorted_item;
		}

		if ( ! empty( $anchor_items ) ) {
			$ordered_anchor_items = array();

			foreach ( $anchor_slugs as $anchor_slug ) {
				if ( isset( $anchor_items[ $anchor_slug ] ) ) {
					$ordered_anchor_items[] = $anchor_items[ $anchor_slug ];
				}
			}

			$sorted_items     = array();
			$anchors_inserted = false;

			foreach ( $free_items as $free_item ) {
				$sorted_items[] = $free_item;

				if ( 'manage_students' === masterstudy_lms_normalize_admin_submenu_slug( $free_item[2] ?? '' ) ) {
					$sorted_items     = array_merge( $sorted_items, $ordered_anchor_items );
					$anchors_inserted = true;
				}
			}

			if ( ! $anchors_inserted ) {
				$sorted_items = array_merge( $sorted_items, $ordered_anchor_items );
			}
		}
	}

	$submenu['stm-lms-settings'] = array_merge( $leading_items, $sorted_items, $unknown_items );
}
add_action( 'admin_menu', 'masterstudy_lms_sort_admin_submenu_pages', 100005 );

function masterstudy_lms_get_admin_submenu_badge_matchers(): array {
	return array(
		'reviews'     => static function ( $slug ) {
			return 'manage_reviews' === $slug || false !== strpos( $slug, 'post_type=stm-reviews' );
		},
		'instructors' => static function ( $slug ) {
			return 'manage_instructors' === $slug || false !== strpos( $slug, 'page=manage_instructors' );
		},
		'courses'     => static function ( $slug ) {
			return 'manage_courses' === $slug
				|| 'stm-lms-courses-link' === $slug
				|| false !== strpos( $slug, 'post_type=stm-courses' );
		},
	);
}

function masterstudy_lms_render_admin_submenu_badge( string $badge_key, array $badge ): string {
	$count = (int) ( $badge['count'] ?? 0 );

	return sprintf(
		' <span class="awaiting-mod update-plugins count-%1$d" data-masterstudy-menu-badge="%2$s">'
			. '<span class="pending-count" aria-hidden="true">%1$d</span>'
			. '<span class="screen-reader-text">%3$s</span>'
		. '</span>',
		$count,
		esc_attr( $badge_key ),
		esc_html( (string) ( $badge['label'] ?? '' ) )
	);
}

function masterstudy_lms_add_admin_submenu_badges() {
	global $submenu;

	if ( empty( $submenu['stm-lms-settings'] ) || ! is_array( $submenu['stm-lms-settings'] ) ) {
		return;
	}

	$repository = new \MasterStudy\Lms\Repositories\AdminMenuBadgeRepository();
	$badges     = $repository->get_badges();
	$matchers   = masterstudy_lms_get_admin_submenu_badge_matchers();

	foreach ( $submenu['stm-lms-settings'] as &$item ) {
		$slug = $item[2] ?? '';

		foreach ( $badges as $key => $badge ) {
			if ( $badge['count'] <= 0 || empty( $matchers[ $key ] ) || ! $matchers[ $key ]( $slug ) ) {
				continue;
			}

			$item[0] .= masterstudy_lms_render_admin_submenu_badge( $key, $badge );
			unset( $badges[ $key ] );
			break;
		}

		if ( empty( $badges ) ) {
			break;
		}
	}
	unset( $item );
}
add_action( 'admin_menu', 'masterstudy_lms_add_admin_submenu_badges', 999999 );
