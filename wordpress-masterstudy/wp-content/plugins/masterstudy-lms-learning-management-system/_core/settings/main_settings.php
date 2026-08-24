<?php

use MasterStudy\Lms\Plugin\Addons;

require_once STM_LMS_PATH . '/settings/main_settings/settings.php';

add_filter(
	'wpcfto_options_page_setup',
	function ( $setups ) {
		$fields        = array(
			'section_1'             => stm_lms_settings_general_section(),
			'section_2'             => stm_lms_settings_courses_section(),
			'section_course'        => stm_lms_settings_course_section(),
			'section_course_player' => stm_lms_settings_course_player_section(),
			'section_quiz'          => stm_lms_settings_quiz_section(),
			'section_ecommerce'     => stm_lms_settings_ecommerce_section(),
			'section_4'             => stm_lms_settings_profiles_section(),
			'section_7'             => stm_lms_settings_grades_section(),
			'ai_lab'                => stm_lms_settings_ai_lab_section(),
			'section_6'             => stm_lms_settings_certificates_section(),
			'section_analytics'     => stm_lms_settings_analytics_section(),
		);
		$footer_fields = array(
			'section_routes'     => stm_lms_settings_route_section(),
			'gdpr'               => stm_lms_settings_gdpr_section(),
			'stm_lms_shortcodes' => stm_lms_settings_shortcodes_section(),
			'section_5'          => stm_lms_settings_google_api_section(),
		);
		$fields        = array_merge(
			apply_filters( 'stm_lms_main_settings_fields', $fields ),
			$footer_fields
		);

		$course_section_submenus = array(
			'section_2'             => esc_html__( 'General', 'masterstudy-lms-learning-management-system' ),
			'section_course'        => esc_html__( 'Course', 'masterstudy-lms-learning-management-system' ),
			'section_course_player' => esc_html__( 'Course Player', 'masterstudy-lms-learning-management-system' ),
			'section_quiz'          => esc_html__( 'Quiz', 'masterstudy-lms-learning-management-system' ),
			'section_drip_content'  => esc_html__( 'Drip Content', 'masterstudy-lms-learning-management-system' ),
			'section_zoom'          => esc_html__( 'Zoom', 'masterstudy-lms-learning-management-system' ),
			'section_assignments'   => esc_html__( 'Assignments', 'masterstudy-lms-learning-management-system' ),
			'section_coming_soon'   => esc_html__( 'Upcoming Courses', 'masterstudy-lms-learning-management-system' ),
			'section_course_bundle' => esc_html__( 'Course Bundles', 'masterstudy-lms-learning-management-system' ),
			'section_trial_courses' => esc_html__( 'Trial Courses', 'masterstudy-lms-learning-management-system' ),
			'section_group_courses' => esc_html__( 'Enterprise Courses', 'masterstudy-lms-learning-management-system' ),
			'section_scorm'         => esc_html__( 'SCORM', 'masterstudy-lms-learning-management-system' ),
		);
		$course_section_keys     = array_keys( $course_section_submenus );
		$normalize_dependency    = static function ( &$dependency ) use ( $course_section_keys ) {
			if ( ! is_array( $dependency ) ) {
				return;
			}

			if ( isset( $dependency['section'] ) && in_array( $dependency['section'], $course_section_keys, true ) ) {
				$dependency['section'] = 'section_2';
			}

			foreach ( $dependency as &$dependency_item ) {
				if ( is_array( $dependency_item ) && isset( $dependency_item['section'] ) && in_array( $dependency_item['section'], $course_section_keys, true ) ) {
					$dependency_item['section'] = 'section_2';
				}
			}
			unset( $dependency_item );
		};

		if ( isset( $fields['section_2']['fields'] ) && is_array( $fields['section_2']['fields'] ) ) {
			$merged_fields = array();

			foreach ( $course_section_submenus as $section_key => $submenu_name ) {
				if ( empty( $fields[ $section_key ]['fields'] ) || ! is_array( $fields[ $section_key ]['fields'] ) ) {
					continue;
				}

				$is_first_field = true;
				foreach ( $fields[ $section_key ]['fields'] as $field_key => $field ) {
					if ( ! is_array( $field ) ) {
						continue;
					}

					$field['submenu'] = $submenu_name;
					if ( $is_first_field ) {
						$field['submenu_title'] = true;
					}

					if ( isset( $field['dependency'] ) ) {
						$normalize_dependency( $field['dependency'] );
					}

					$merged_key = $field_key;
					if ( isset( $merged_fields[ $merged_key ] ) ) {
						$merged_key = $field_key . '_' . preg_replace( '/[^a-z0-9_]/i', '_', $section_key );
						$i          = 2;
						while ( isset( $merged_fields[ $merged_key ] ) ) {
							$merged_key = $field_key . '_' . preg_replace( '/[^a-z0-9_]/i', '_', $section_key ) . '_' . $i;
							++$i;
						}
					}

					$merged_fields[ $merged_key ] = $field;
					$is_first_field               = false;
				}

				if ( 'section_2' !== $section_key ) {
					unset( $fields[ $section_key ] );
				}
			}

			if ( ! empty( $merged_fields ) ) {
				$fields['section_2']['fields'] = $merged_fields;
			}
		}

		foreach ( $fields as &$section ) {
			if ( empty( $section['fields'] ) || ! is_array( $section['fields'] ) ) {
				continue;
			}

			foreach ( $section['fields'] as &$field ) {
				if ( is_array( $field ) && isset( $field['dependency'] ) ) {
					$normalize_dependency( $field['dependency'] );
				}
			}
			unset( $field );
		}
		unset( $section );

		$setups[] = array(
			'option_name' => 'stm_lms_settings',
			'title'       => esc_html__( 'Settings', 'masterstudy-lms-learning-management-system' ),
			'sub_title'   => esc_html__( 'by StylemixThemes', 'masterstudy-lms-learning-management-system' ),
			'logo'        => STM_LMS_URL . 'assets/admin/icon.svg',
			'page'        => array(
				'page_title' => esc_html__( 'Settings', 'masterstudy-lms-learning-management-system' ),
				'menu_title' => 'MasterStudy',
				'menu_slug'  => 'stm-lms-settings',
				'icon'       => STM_LMS_URL . 'assets/admin/icon.png',
				'position'   => 3,
			),
			'fields'      => $fields,
		);

		return $setups;
	},
	5,
	1
);

add_action(
	'admin_menu',
	function () {
		$post_type_data = get_post_type_object( 'stm-ent-groups' );

		if ( empty( $post_type_data ) ) {
			return;
		}

		add_submenu_page(
			'stm-lms-settings',
			$post_type_data->label,
			$post_type_data->label,
			'manage_options',
			'/edit.php?post_type=stm-ent-groups',
			null,
			79
		);
	},
	10001
);

add_action(
	'admin_menu',
	function () {
		if ( ! defined( 'STM_LMS_PRO_PATH' ) ) {
			add_submenu_page(
				'stm-lms-settings',
				__( 'Upgrade', 'masterstudy-lms-learning-management-system' ),
				'<span class="stm-lms-unlock-pro-btn"><span class="stm-lms-unlock-wrap-span">' . __( 'Unlock PRO', 'masterstudy-lms-learning-management-system' ) . '</span></span>',
				'manage_options',
				'stm-lms-go-pro',
				'stm_lms_render_go_pro',
			);
		}
	},
	100003
);

if ( ! STM_LMS_Helpers::is_pro() && ! STM_LMS_Helpers::is_pro_plus() ) {
	add_action( 'admin_menu', 'add_submenu_pages', 100002 );

	function add_submenu_pages() {
		if ( ! defined( 'STM_LMS_PRO_PATH' ) ) {
			$addons = Addons::list();

			foreach ( $addons as $key => $addon ) {
				if ( 'email_branding' === $key ) {
					continue;
				}
				add_submenu_page(
					'stm-lms-settings',
					$addon['name'],
					$addon['name'],
					'manage_options',
					$addon['documentation'],
					function () use ( $key ) {
						unlock_addons_callback( $key );
					}
				);
			}
		}
	}

	function unlock_addons_callback( $addon ) {
		$version = ( WP_DEBUG ) ? time() : STM_LMS_VERSION;

		wp_enqueue_style( 'stm_lms_unlock_addons', STM_LMS_URL . 'assets/css/stm_lms_unlock_addons.css', null, $version );
		wp_enqueue_style( 'masterstudy-analytics-preview-page' );
		wp_enqueue_script( 'masterstudy-analytics-preview-page' );
		require_once STM_LMS_PATH . '/stm-lms-templates/journey/free-journey-addons-sidebars.php';
	}
}

add_action(
	'admin_menu',
	function () {
		if ( isset( $_GET['page'] ) && 'masterstudy-starter-demo-import' === $_GET['page'] ) {
			wp_safe_redirect( 'https://stylemixthemes.com/wordpress-lms-plugin/starter-templates/' );
			exit;
		}
	},
	100003
);

add_filter(
	'admin_body_class',
	function ( $classes ) {
		if ( ! defined( 'STM_LMS_PRO_PATH' ) ) {
			$classes .= ' not-lms-pro';
		}

		return $classes;
	}
);

add_action(
	'admin_menu',
	function () {
		if ( function_exists( 'masterstudy_lms_sort_admin_submenu_pages' ) ) {
			return;
		}

		global $submenu;

		if ( empty( $submenu['stm-lms-settings'] ) || ! is_array( $submenu['stm-lms-settings'] ) ) {
			return;
		}

		$settings_item          = null;
		$help_center_item       = null;
		$templates_item         = null;
		$addons_item            = null;
		$unlock_pro_item        = null;
		$statistics_item        = null;
		$reviews_item           = null;
		$payouts_item           = null;
		$coupons_item           = null;
		$orders_item            = null;
		$students_item          = null;
		$point_statistics_item  = null;
		$courses_item           = null;
		$course_category_item   = null;
		$question_category_item = null;
		$reordered_items        = array();
		$has_submissions_item   = false;

		foreach ( $submenu['stm-lms-settings'] as $item ) {
			$item_slug = $item[2] ?? '';

			if ( 'stm-lms-settings' === $item_slug ) {
				if ( null === $settings_item || false !== strpos( $item[0] ?? '', 'stm-lms-settings-menu-title' ) ) {
					$settings_item = $item;
				}
				continue;
			}

			if ( 'stm-support-page-masterstudy' === $item_slug ) {
				$help_center_item = $item;
				continue;
			}

			if ( 'admin.php?page=masterstudy-starter-demo-import' === $item_slug ) {
				$templates_item = $item;
				continue;
			}

			if ( 'stm-addons' === $item_slug ) {
				$addons_item = $item;
				continue;
			}

			if ( 'stm-lms-go-pro' === $item_slug ) {
				$unlock_pro_item = $item;
				continue;
			}

			if ( 'stm_lms_statistics' === $item_slug ) {
				$statistics_item = $item;
				continue;
			}

			if ( 'manage_orders' === $item_slug ) {
				$orders_item = $item;
				continue;
			}

			if ( 'manage_students' === $item_slug ) {
				$students_item = $item;
				continue;
			}

			if ( 'point_system_statistics' === $item_slug ) {
				$point_statistics_item = $item;
				continue;
			}

			if ( 'manage_coupons' === $item_slug ) {
				$coupons_item = $item;
				continue;
			}

			if ( false !== strpos( $item_slug, 'post_type=stm-reviews' ) ) {
				$reviews_item = $item;
				continue;
			}

			if ( false !== strpos( $item_slug, 'post_type=stm-payout' ) ) {
				$payouts_item = $item;
				continue;
			}

			if ( 'stm-lms-courses-link' === $item_slug ) {
				$courses_item = $item;
				continue;
			}

			if ( false !== strpos( $item_slug, 'taxonomy=stm_lms_course_taxonomy' ) ) {
				$course_category_item = $item;
				continue;
			}

			if ( false !== strpos( $item_slug, 'taxonomy=stm_lms_question_taxonomy' ) ) {
				$question_category_item = $item;
				continue;
			}

			if ( false !== strpos( $item_slug, 'post_type=stm-user-assignment' ) ) {
				$has_submissions_item = true;
			}

			$reordered_items[] = $item;
		}

		if ( null === $settings_item ) {
			return;
		}

		$is_pro_enabled = defined( 'STM_LMS_PRO_PATH' );

		$submenu['stm-lms-settings'] = array();
		$settings_group_inserted     = false;

		if ( null !== $courses_item ) {
			$submenu['stm-lms-settings'][] = $courses_item;

			if ( null !== $course_category_item ) {
				$submenu['stm-lms-settings'][] = $course_category_item;
				$course_category_item          = null;
			}
		}

		foreach ( $reordered_items as $item ) {
			$submenu['stm-lms-settings'][] = $item;

			if ( false !== strpos( $item[2] ?? '', 'post_type=stm-questions' ) && null !== $question_category_item ) {
				$submenu['stm-lms-settings'][] = $question_category_item;
				$question_category_item        = null;
			}

			if (
				( $has_submissions_item && false !== strpos( $item[2] ?? '', 'post_type=stm-user-assignment' ) ) ||
				( ! $has_submissions_item && false !== strpos( $item[2] ?? '', 'post_type=stm-questions' ) )
			) {
				if ( null !== $orders_item ) {
					$submenu['stm-lms-settings'][] = $orders_item;
					$orders_item                   = null;
				}

				if ( null !== $coupons_item ) {
					$submenu['stm-lms-settings'][] = $coupons_item;
					$coupons_item                  = null;
				}

				if ( null !== $reviews_item ) {
					$submenu['stm-lms-settings'][] = $reviews_item;
					$reviews_item                  = null;
				}

				if ( null !== $payouts_item ) {
					$submenu['stm-lms-settings'][] = $payouts_item;
					$payouts_item                  = null;
				}

				if ( null !== $statistics_item ) {
					$submenu['stm-lms-settings'][] = $statistics_item;
					$statistics_item               = null;
				}
			}

			if ( 'manage_users' === ( $item[2] ?? '' ) ) {
				if ( null !== $students_item ) {
					$submenu['stm-lms-settings'][] = $students_item;
					$students_item                 = null;
				}

				if ( null !== $point_statistics_item ) {
					$submenu['stm-lms-settings'][] = $point_statistics_item;
					$point_statistics_item         = null;
				}

				if ( ! $is_pro_enabled && ! $settings_group_inserted ) {
					$submenu['stm-lms-settings'][] = $settings_item;

					if ( null !== $help_center_item ) {
						$submenu['stm-lms-settings'][] = $help_center_item;
					}

					if ( null !== $templates_item ) {
						$submenu['stm-lms-settings'][] = $templates_item;
					}

					if ( null !== $addons_item ) {
						$submenu['stm-lms-settings'][] = $addons_item;
					}

					$settings_group_inserted = true;
				}
			}

			if ( $is_pro_enabled && false !== strpos( $item[2] ?? '', 'post_type=stm-ent-groups' ) ) {
				$submenu['stm-lms-settings'][] = $settings_item;

				if ( null !== $help_center_item ) {
					$submenu['stm-lms-settings'][] = $help_center_item;
				}

				if ( null !== $templates_item ) {
					$submenu['stm-lms-settings'][] = $templates_item;
				}

				if ( null !== $addons_item ) {
					$submenu['stm-lms-settings'][] = $addons_item;
				}

				$settings_group_inserted = true;
			}
		}

		if ( ! $settings_group_inserted ) {
			$submenu['stm-lms-settings'][] = $settings_item;

			if ( null !== $help_center_item ) {
				$submenu['stm-lms-settings'][] = $help_center_item;
			}

			if ( null !== $templates_item ) {
				$submenu['stm-lms-settings'][] = $templates_item;
			}

			if ( null !== $addons_item ) {
				$submenu['stm-lms-settings'][] = $addons_item;
			}
		}

		if ( null !== $orders_item ) {
			$submenu['stm-lms-settings'][] = $orders_item;
		}

		if ( null !== $students_item ) {
			$submenu['stm-lms-settings'][] = $students_item;
		}

		if ( null !== $point_statistics_item ) {
			$submenu['stm-lms-settings'][] = $point_statistics_item;
		}

		if ( null !== $coupons_item ) {
			$submenu['stm-lms-settings'][] = $coupons_item;
		}

		if ( null !== $reviews_item ) {
			$submenu['stm-lms-settings'][] = $reviews_item;
		}

		if ( null !== $payouts_item ) {
			$submenu['stm-lms-settings'][] = $payouts_item;
		}

		if ( null !== $statistics_item ) {
			$submenu['stm-lms-settings'][] = $statistics_item;
		}

		if ( null !== $course_category_item ) {
			$submenu['stm-lms-settings'][] = $course_category_item;
		}

		if ( null !== $question_category_item ) {
			$submenu['stm-lms-settings'][] = $question_category_item;
		}

		if ( null !== $unlock_pro_item ) {
			$submenu['stm-lms-settings'][] = $unlock_pro_item;
		}
	},
	999999
);

function stm_lms_render_go_pro() {
	$version = WP_DEBUG ? time() : STM_LMS_DB_VERSION;

	wp_enqueue_style( 'stm_lms_go_pro', STM_LMS_URL . 'assets/css/stm_lms_gopro.css', null, $version );

	require_once STM_LMS_PATH . '/stm-lms-templates/stm-lms-go-pro.php';
}

add_action( 'admin_footer', 'stm_lms_render_feature_request' );
add_action( 'admin_head', 'stm_lms_fix_top_level_settings_link' );

function stm_lms_render_feature_request() {
	echo '<a id="feature-request" href="https://stylemixthemes.cnflx.io/boards/masterstudy-lms" target="_blank" style="display: none;">
		<img src=' . esc_url( STM_LMS_URL . 'assets/svg/feature-request.svg' ) . '>
		<span>' . esc_html__( 'Create a roadmap with us:<br>Vote for next feature', 'masterstudy-lms-learning-management-system' ) . '</span>
	</a>';
}

function stm_lms_fix_top_level_settings_link() {
	?>
	<script>
		jQuery(function($) {
			$('#toplevel_page_stm-lms-settings > a').attr('href', '<?php echo esc_js( admin_url( 'admin.php?page=stm-lms-settings' ) ); ?>');
		});
	</script>
	<?php
}
