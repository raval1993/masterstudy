<?php

use MasterStudy\Lms\Plugin\PostType;

add_action(
	'admin_enqueue_scripts',
	function () {
		$version = ( WP_DEBUG ) ? time() : STM_LMS_VERSION;

		stm_lms_register_script( 'admin/lms_sub_menu' );
		/** enqueue styles **/
		wp_enqueue_style( 'stm_lms_icons', STM_LMS_URL . 'assets/icons/style.css', null, STM_LMS_VERSION );
		wp_enqueue_style( 'stm_lms_starter_theme', STM_LMS_URL . 'includes/starter-theme/assets/main.css', array( 'wp-admin' ), $version );

		/** enqueue javascript **/
		wp_enqueue_script( 'stm_lms_starter_theme', STM_LMS_URL . 'includes/starter-theme/assets/main.js', array( 'jquery-core' ), $version, true );
		wp_localize_script(
			'stm_lms_starter_theme',
			'stm_lms_starter_theme_data',
			array(
				'stm_lms_admin_ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
			)
		);

		wp_enqueue_style( 'stm_lms_icons', STM_LMS_URL . 'assets/icons/style.css', null, $version );
		stm_lms_register_script( 'admin/admin', array( 'jquery' ), true );
		stm_lms_register_script( 'admin/sortable_menu', array( 'jquery' ), true );

		$disabled_pages = apply_filters( 'masterstudy_lms_vuejs_disabled_pages', array( 'cost_calculator_builder', 'cookie-consent', 'cookie-law-info' ) );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! in_array( sanitize_text_field( $_GET['page'] ?? '' ), $disabled_pages, true ) ) {
			stm_lms_register_script( 'payout/user-search', array( 'vue.js', 'vue-select.js' ) );

			wp_localize_script(
				'stm-lms-payout/user-search',
				'stm_payout_url_data',
				array(
					'url' => get_site_url() . STM_LMS_BASE_API_URL,
				)
			);
		}

		stm_lms_register_style( 'nuxy/main' );

		wp_register_script( 'masterstudy-vue', STM_LMS_URL . 'assets/js/vendors/vue.js', array(), $version, true );
		wp_register_script( 'masterstudy-vue-resource', STM_LMS_URL . 'assets/js/vendors/vue-resource.js', array( 'masterstudy-vue' ), $version, true );
		wp_register_script( 'masterstudy-vue-range-slider', STM_LMS_URL . 'assets/js/vendors/vue-range-slider.js', array( 'masterstudy-vue' ), $version, true );
	}
);

/** Add icons for gutenberg blocks */
function stm_lms_gutenberg_block_styles() {
	wp_enqueue_style( 'stm_lms_icons', STM_LMS_URL . 'assets/icons/style.css', null, STM_LMS_VERSION );
	wp_enqueue_style( 'linear', STM_LMS_URL . 'libraries/nuxy/taxonomy_meta/assets/linearicons/linear.css', null, STM_LMS_VERSION, 'all' );
}

add_action( 'enqueue_block_assets', 'stm_lms_gutenberg_block_styles' );

/** Show notice to install starter theme */

function stm_lms_add_theme_caps() {
	$instructors   = array();
	$admin_users   = array();
	$admin_users[] = get_role( 'administrator' );
	$instructors[] = get_role( 'stm_lms_instructor' );

	if ( ! empty( $admin_users ) ) {
		foreach ( $admin_users as $user ) {
			if ( empty( $user ) ) {
				continue;
			}

			foreach ( array( 'publish', 'delete', 'delete_others', 'delete_private', 'delete_published', 'edit', 'edit_others', 'edit_private', 'edit_published', 'read_private' ) as $cap ) {
				$user->add_cap( "{$cap}_stm_lms_posts" );
			}

			$user->add_cap( 'edit_elementor_libraries' );
			$user->add_cap( 'edit_others_elementor_libraries' );
			$user->add_cap( 'delete_elementor_libraries' );
			$user->add_cap( 'delete_others_elementor_libraries' );
			$user->add_cap( 'read_elementor_libraries' );
			$user->add_cap( 'publish_elementor_libraries' );
		}
	}

	if ( ! empty( $instructors ) ) {
		foreach ( $instructors as $user ) {
			if ( empty( $user ) ) {
				continue;
			}
			foreach ( array( 'publish', 'delete', 'edit' ) as $cap ) {
				$user->add_cap( 'edit_posts' );
				$user->add_cap( "{$cap}_stm_lms_posts" );
			}

			$user->add_cap( 'edit_elementor_libraries' );
			$user->add_cap( 'delete_elementor_libraries' );
			$user->add_cap( 'read_elementor_libraries' );
			$user->add_cap( 'publish_elementor_libraries' );
		}
	}
}
add_action( 'init', 'stm_lms_add_theme_caps' );

function masterstudy_lms_addons_dynamic_url( $addon_key ) {
	$addons_site_url = 'https://stylemixthemes.com/wordpress-lms-plugin/addons/';
	$addon_urls      = array(
		'udemy-course-importer'   => 'udemy-importer',
		'statistics-and-payouts'  => 'statistics-payouts',
		'live-streaming'          => 'zoom-google-meet-live-streams',
		'multi-instructors'       => 'co-instructors',
		'google-classroom'        => 'google-classrooms',
		'the-gradebook'           => 'gradebook',
		'zoom-video-conferencing' => 'zoom-conference',
		'certificate-builder'     => 'certificates',
		'media-file-manager'      => 'file-upload-manager',
		'google-meet'             => 'zoom-google-meet-live-streams',
	);

	return $addons_site_url . ( $addon_urls[ $addon_key ] ?? $addon_key ) . '/';
}

add_action(
	'wp_ajax_stm_lms_hide_announcement',
	function () {
		check_ajax_referer( 'stm_lms_hide_announcement', 'nonce' );
		set_transient( 'stm_lms_app_notice', '1', MONTH_IN_SECONDS );
	}
);

function stm_lms_deny_instructor_admin() {
	if ( ! wp_doing_ajax() && ! empty( STM_LMS_Options::get_option( 'deny_instructor_admin', '' ) && ! current_user_can( 'manage_options' ) ) ) {
		if ( isset( $_GET['page'] ) && 'certificate_builder' === $_GET['page'] ) {
			return;
		}

		wp_safe_redirect( STM_LMS_User::user_page_url() );
		die();
	}
}
add_action( 'admin_init', 'stm_lms_deny_instructor_admin' );

add_action(
	'save_post_stm-courses',
	function ( $post_id, $post, $update ) {
		if ( ! $update ) {
			return;
		}
		$created = get_option( 'stm_lms_course_created', false );
		if ( ! $created ) {
			$data = array(
				'show_time'   => time(),
				'step'        => 0,
				'prev_action' => '',
			);
			set_transient( 'stm_masterstudy-lms-learning-management-system_single_notice_setting', $data );
			update_option( 'stm_lms_course_created', true );
		}
	},
	20,
	3
);

add_action(
	'delete_user',
	function ( $user_id ) {
		$the_query = array(
			'post_type' => array( 'stm-reviews' ),
			'author'    => $user_id,
		);
		$posts     = new WP_Query( $the_query );
		if ( ! empty( $posts ) ) {
			foreach ( $posts->posts as $post ) {
				wp_delete_post( $post->ID );
			}
		}
		wp_reset_postdata();
	}
);

add_action(
	'stm_admin_notice_rate_masterstudy-lms-learning-management-system_single',
	function ( $data ) {
		if ( is_array( $data ) ) {
			$data['title']   = 'Yoo-hoo!';
			$data['content'] = 'You have created your first course! Enjoyed? Help us to rate <strong>MasterStudy 5 Stars!</strong>';
		}

		return $data;
	},
	100
);

add_filter(
	'admin_url',
	function ( $url, $path ) {
		if ( is_network_admin() ) {
			return $url;
		}
		static $in_filter = false;
		if ( $in_filter ) {
			return $url; // prevent recursion
		}
		$in_filter = true;

		$post_types = array(
			'stm-courses'      => 'edit-course',
			'stm-lessons'      => 'edit-lesson',
			'stm-questions'    => 'edit-question',
			'stm-quizzes'      => 'edit-quiz',
			'stm-assignments'  => 'edit-assignment',
			'stm-google-meets' => 'edit-google-meet',
		);

		if ( strpos( $path, 'post-new.php?post_type=' ) !== false ) {
			$query_args = wp_parse_url( $path, PHP_URL_QUERY );
			parse_str( $query_args, $query_params );

			$post_type = $query_params['post_type'] ?? '';

			if ( array_key_exists( $post_type, $post_types ) ) {
				$url = ms_plugin_user_account_url( $post_types[ $post_type ] );
			}
		}

		$in_filter = false;

		return $url;
	},
	10,
	2
);

add_action(
	'edit_form_after_title',
	function ( $post ) {
		$edit_url = ms_plugin_edit_course_builder_url( $post->post_type );

		if ( empty( $edit_url ) ) {
			return;
		}

		if ( PostType::QUESTION === $post->post_type ) {
			?>
			<div class="inside">
				<div class="question-title">
					<?php echo wp_kses_post( get_the_title( $post->ID ) ); ?>
				</div>
			</div>
			<?php
		}
		?>
		<div id="ms-lms-course-builder">
		<?php if ( home_url( '/' ) === $edit_url ) { ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=stm-lms-settings#section_routes' ) ); ?>">
				<img src="<?php echo esc_url( STM_LMS_URL . 'assets/icons/global/file-not-found.png' ); ?>">
				<p><?php echo esc_html__( 'You can make changes within Course Builder only if you set up the User Account page', 'masterstudy-lms-learning-management-system' ); ?></p>
				<div class="button button-primary button-hero button-edit-course-builder">
					<?php echo esc_html__( 'Go to settings', 'masterstudy-lms-learning-management-system' ); ?>
				</div>
			</a>
		<?php } else { ?>
			<a href="<?php echo esc_url( "{$edit_url}{$post->ID}/" ); ?>">
				<div class="button button-primary button-hero button-edit-course-builder">
					<i class="dashicons-before dashicons-edit-large" aria-hidden="true"></i>
					<?php echo esc_html__( 'Edit with Course Builder', 'masterstudy-lms-learning-management-system' ); ?>
				</div>
			</a>
		<?php } ?>
		</div>
		<?php
	},
	1
);

add_action(
	'post_row_actions',
	function ( $actions, $post ) {
		if ( 'trash' === $post->post_status ) {
			return $actions;
		}

		$default_wp_edit_post_types = array(
			PostType::COURSE,
			PostType::LESSON,
			PostType::QUIZ,
			PostType::QUESTION,
			PostType::ASSIGNMENT,
		);

		if ( in_array( $post->post_type, $default_wp_edit_post_types, true ) && isset( $actions['edit'] ) ) {
			$actions['edit'] = sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				esc_url( admin_url( 'post.php?post=' . absint( $post->ID ) . '&action=edit' ) ),
				esc_attr(
					sprintf(
						/* translators: %s: Post title. */
						__( 'Edit &#8220;%s&#8221;' ),
						_draft_or_post_title( $post->ID )
					)
				),
				__( 'Edit' )
			);
		}

		$edit_url = ms_plugin_edit_course_builder_url( $post->post_type );

		if ( ! empty( $edit_url ) && home_url( '/' ) !== $edit_url ) {
			$actions[] = sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				esc_url( $edit_url . "{$post->ID}/" ),
				esc_attr__( 'Edit with Course Builder', 'masterstudy-lms-learning-management-system' ),
				esc_html__( 'Edit with Course Builder', 'masterstudy-lms-learning-management-system' )
			);
		}

		return $actions;
	},
	1,
	2
);

function stm_lms_route_trash_page_handler( $post_id ) {
	if ( get_post_type( $post_id ) === 'page' ) {
		$settings = get_option( 'stm_lms_settings', array() );
		$pages    = array( 'user_url', 'instructor_url_profile', 'student_url_profile', 'wishlist_url', 'checkout_url', 'memberships_url' );

		foreach ( $pages as $page ) {
			if ( isset( $settings[ $page ] ) && intval( $settings[ $page ] ) === $post_id ) {
				$settings[ $page ] = '';
			}
		}

		update_option( 'stm_lms_settings', $settings );
	}
}
add_action( 'trashed_post', 'stm_lms_route_trash_page_handler' );

function masterstudy_plugin_escape_question_title( $title, $post_id ) {
	if ( is_admin() && PostType::QUESTION === get_post_type( $post_id ) ) {
		return wp_strip_all_tags( $title );
	}

	return $title;
}
add_filter( 'the_title', 'masterstudy_plugin_escape_question_title', 10, 2 );

// TODO need to remove after integration Single Question Editor On Course Builder !!!
function masterstudy_plugin_save_questions_custom_title( $post_id, $post, $update ) {
	global $wpdb;

	if (
		PostType::QUESTION !== $post->post_type ||
		( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
		! current_user_can( 'edit_post', $post_id )
	) {
		return;
	}

	if ( isset( $_POST['question_title'] ) ) {
		$editor_question_title = stripslashes( $_POST['question_title'] );

		if ( $post->post_title !== $editor_question_title ) {
			$wpdb->update(
				$wpdb->posts,
				array( 'post_title' => $editor_question_title ),
				array( 'ID' => $post_id ),
				array( '%s' ),
				array( '%d' )
			);

			clean_post_cache( $post_id );
		}
	}
}
add_action( 'save_post', 'masterstudy_plugin_save_questions_custom_title', 10, 3 );
