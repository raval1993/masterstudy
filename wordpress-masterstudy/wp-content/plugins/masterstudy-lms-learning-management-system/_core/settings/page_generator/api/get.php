<?php

function stm_lms_generate_pages_list() {
	$list = array(
		'user_url'     => esc_html__( 'User Account', 'masterstudy-lms-learning-management-system' ),
		'wishlist_url' => esc_html__( 'Wishlist', 'masterstudy-lms-learning-management-system' ),
		'checkout_url' => esc_html__( 'Checkout', 'masterstudy-lms-learning-management-system' ),
	);

	if ( STM_LMS_Options::get_option( 'instructor_public_profile', true ) ) {
		$list['instructor_url_profile'] = esc_html__( 'Instructor Public Account', 'masterstudy-lms-learning-management-system' );
	}

	if ( STM_LMS_Options::get_option( 'student_public_profile', true ) ) {
		$list['student_url_profile'] = esc_html__( 'Student Public Account', 'masterstudy-lms-learning-management-system' );
	}

	$enabled_addons = get_option( 'stm_lms_addons' );

	if ( defined( 'STM_LMS_PRO_PATH' ) && isset( $enabled_addons['certificate_builder'] ) && 'on' === $enabled_addons['certificate_builder'] ) {
		$list['certificate_page_url'] = esc_html__( 'Certificate Page', 'masterstudy-lms-learning-management-system' );
	}

	if ( defined( 'STM_LMS_PRO_PATH' ) && isset( $enabled_addons['subscriptions'] ) && 'on' === $enabled_addons['subscriptions'] ) {
		$list['memberships_url'] = esc_html__( 'Memberships', 'masterstudy-lms-learning-management-system' );
	}

	return $list;
}

function stm_lms_archive_page_list() {
	return array(
		'courses_page_elementor' => array(
			'title' => esc_html__( 'Courses page (for Elementor)', 'masterstudy-lms-learning-management-system' ),
			'slug'  => 'courses-archive-elementor',
		),
		'courses_page_gutenberg' => array(
			'title' => esc_html__( 'Courses page (for Gutenberg)', 'masterstudy-lms-learning-management-system' ),
			'slug'  => 'courses-archive-gutenberg',
		),
	);
}

function stm_lms_display_post_states( $states, $post ) {
	$pages = array(
		'user_url'               => esc_html__( 'MasterStudy Private Account', 'masterstudy-lms-learning-management-system' ),
		'instructor_url_profile' => esc_html__( 'MasterStudy Instructor Profile Public Page', 'masterstudy-lms-learning-management-system' ),
		'student_url_profile'    => esc_html__( 'MasterStudy Student Profile Public Page', 'masterstudy-lms-learning-management-system' ),
		'wishlist_url'           => esc_html__( 'MasterStudy Wishlist', 'masterstudy-lms-learning-management-system' ),
		'checkout_url'           => esc_html__( 'MasterStudy Checkout', 'masterstudy-lms-learning-management-system' ),
		'courses_page'           => esc_html__( 'MasterStudy Courses', 'masterstudy-lms-learning-management-system' ),
		'certificate_page_url'   => esc_html__( 'MasterStudy Certificate', 'masterstudy-lms-learning-management-system' ),
	);

	foreach ( $pages as $page_option => $page_state ) {
		$page_id = STM_LMS_Options::get_option( $page_option );

		if ( ! empty( $page_id ) && $page_id === $post->ID ) {
			$states[] = $page_state;
		}
	}

	if ( STM_LMS_Helpers::masterstudy_lms_is_course_coming_soon( $post->ID ) ) {
		$states[] = 'Upcoming';
	}

	return $states;
}
add_filter( 'display_post_states', 'stm_lms_display_post_states', 10, 2 );

function stm_lms_get_course_page_by_meta( $meta_key ) {
	$pages = get_pages(
		array(
			'post_status' => 'publish',
			'meta_key'    => $meta_key,
			'meta_value'  => 'yes',
			'number'      => 1,
		)
	);

	return ! empty( $pages[0] ) ? array(
		'id'    => $pages[0]->ID,
		'title' => $pages[0]->post_title,
	) : array();
}

function stm_lms_get_generated_archive_pages() {
	return array(
		'elementor' => stm_lms_get_course_page_by_meta( 'elementor_courses_page' ),
		'gutenberg' => stm_lms_get_course_page_by_meta( 'gutenberg_courses_page' ),
	);
}

function stm_lms_has_generated_archive_pages( $pages ) {
	$generated_pages = stm_lms_get_generated_archive_pages( $pages );

	return ! empty( $generated_pages['elementor'] ) && ! empty( $generated_pages['gutenberg'] );
}

function stm_lms_get_generated_pages( $pages ) {
	$disabled_pages = array(
		'checkout_url' => 'woocommerce_checkout',
	);

	$generated_pages = array();
	foreach ( $pages as $page_slug => $page_name ) {
		$page_id = STM_LMS_Options::get_option( $page_slug );

		if ( ! empty( $page_id ) && get_post_status( $page_id ) === 'publish' ) {
			$generated_pages[ $page_slug ] = array(
				'id'   => $page_id,
				'name' => $page_name,
			);
		}
	}

	foreach ( $disabled_pages as $page_slug => $option ) {
		$option_enabled = STM_LMS_Options::get_option( $option );

		if ( $option_enabled ) {
			$generated_pages[ $page_slug ] = 'unavailable';
		}
	}

	return $generated_pages;
}

function stm_lms_has_generated_pages( $pages ) {
	$generated_pages = stm_lms_get_generated_pages( $pages );

	return count( $generated_pages ) >= count( $pages );
}

function stm_lms_ajax_genearte_pages() {
	if ( ! current_user_can( 'manage_options' ) ) {
		die;
	}

	$pages = json_decode( file_get_contents( 'php://input' ), true );
	stm_lms_generate_pages( $pages );

	if ( ! stm_lms_has_generated_archive_pages( stm_lms_archive_page_list() ) ) {
		stm_lms_generate_archive_pages();
	}

	wp_send_json( 'OK' );
}
add_action( 'wp_ajax_stm_generate_pages', 'stm_lms_ajax_genearte_pages' );

function stm_lms_masterstudy_importer_done_pages() {
	stm_lms_autogenerate_pages();

	if ( ! stm_lms_has_generated_archive_pages( stm_lms_archive_page_list() ) ) {
		stm_lms_generate_archive_pages();
	}
}
add_action( 'stm_masterstudy_importer_done', 'stm_lms_masterstudy_importer_done_pages' );

function stm_lms_generate_pages( $pages ) {
	global $wpdb;

	$page_opt = array();

	foreach ( $pages as $page_option => $page_title ) {
		$page_id = STM_LMS_Options::get_option( $page_option );

		if ( ! empty( $page_id ) && get_post_status( $page_id ) === 'publish' ) {
			continue;
		}

		$page_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'lms_page' AND meta_value = %s",
				$page_option
			)
		);

		if ( empty( $page_id ) || 'publish' !== get_post_status( $page_id ) ) {
			$my_post = array(
				'post_title'  => $page_title,
				'post_type'   => 'page',
				'post_status' => 'publish',
			);
			$page_id = wp_insert_post( $my_post );
		}

		update_post_meta( $page_id, 'title', 'hide' );
		update_post_meta( $page_id, 'breadcrumbs', 'hide' );
		update_post_meta( $page_id, 'lms_page', $page_option );

		/*Replace in options*/
		$page_opt[ $page_option ] = $page_id;
	}

	$options = array();

	if ( ! empty( $page_opt ) ) {
		$options = get_option( 'stm_lms_settings', array() );

		foreach ( $page_opt as $option => $page_id ) {
			$options[ $option ] = $page_id;
		}

		update_option( 'stm_lms_settings', $options );

		do_action( 'stm_lms_pages_generated' );
	}

	do_action( 'masterstudy_add_shortcode_memberships_page', null, $options );
}

function stm_lms_generate_archive_pages() {
	$archive_pages         = stm_lms_archive_page_list();
	$existing_courses_page = get_page_by_path( 'courses', OBJECT, 'page' );

	foreach ( $archive_pages as $page_key => $page_info ) {
		if ( $existing_courses_page ) {
			if ( 'courses-archive' === $page_info['slug'] ) {
				continue;
			}
		}

		if ( get_page_by_path( $page_info['slug'], OBJECT, 'page' ) ) {
			continue;
		}

		$page = array(
			'post_title'  => $page_info['title'],
			'post_status' => 'publish',
			'post_type'   => 'page',
			'post_name'   => $page_info['slug'],
		);

		$page_id = wp_insert_post( $page );

		if ( ! is_wp_error( $page_id ) ) {
			$page_meta = array();

			if ( 'courses_page_elementor' === $page_key ) {
				$page_meta['courses_page_type']      = 'elementor';
				$page_meta['elementor_courses_page'] = 'yes';
				$page_meta['_elementor_edit_mode']   = 'builder';
				$page_meta['_elementor_data']        = json_decode( wp_remote_retrieve_body( wp_remote_get( STM_LMS_URL . 'settings/demo_import/sample_data/archive_elementor.json' ) ), true );
			} elseif ( 'courses_page_gutenberg' === $page_key ) {
				$page_meta['courses_page_type']      = 'gutenberg';
				$page_meta['gutenberg_courses_page'] = 'yes';
				wp_update_post(
					array(
						'ID'           => $page_id,
						'post_content' => wp_remote_retrieve_body( wp_remote_get( STM_LMS_URL . 'settings/demo_import/sample_data/archive_gutenberg.xml' ) ),
					)
				);
			}

			foreach ( $page_meta as $meta_key => $meta_value ) {
				update_post_meta( $page_id, $meta_key, $meta_value );
			}
		}
	}
}

function stm_lms_autogenerate_pages() {
	stm_lms_generate_pages( stm_lms_generate_pages_list() );
}
register_activation_hook( MS_LMS_FILE, 'stm_lms_autogenerate_pages' );
