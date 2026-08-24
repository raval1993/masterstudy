<?php
function masterstudy_lms_add_course_template_column_to_pages( $columns ) {
	if ( ( ! STM_LMS_Helpers::is_pro_plus() && ! STM_LMS_Helpers::is_ms_starter_purchased() ) || ! class_exists( '\Elementor\Plugin' ) ) {
		return $columns;
	}

	$new_columns = array();

	foreach ( $columns as $key => $label ) {
		$new_columns[ $key ] = $label;

		if ( 'title' === $key ) {
			$new_columns['course_page_style'] = __( 'Use as course page', 'masterstudy-lms-learning-management-system' );
		}
	}

	return $new_columns;
}
add_filter( 'manage_pages_columns', 'masterstudy_lms_add_course_template_column_to_pages' );

function masterstudy_lms_fill_course_template_column_on_pages( $column_name, $post_id ) {
	if ( ( ! STM_LMS_Helpers::is_pro_plus() && ! STM_LMS_Helpers::is_ms_starter_purchased() ) || ! class_exists( '\Elementor\Plugin' ) ) {
		return;
	}

	if ( 'course_page_style' === $column_name ) {
		$page_styles    = masterstudy_lms_get_my_templates();
		$style_label    = __( 'None', 'masterstudy-lms-learning-management-system' );
		$current_style  = get_post_meta( $post_id, 'masterstudy_elementor_course_page_style', true );
		$current_course = get_post_meta( $post_id, 'masterstudy_elementor_course_page', true );
		$course_label   = ! empty( $current_course ) ? get_the_title( intval( $current_course ) ) : __( 'None', 'masterstudy-lms-learning-management-system' );

		if ( ! empty( $current_style ) && ! empty( $page_styles ) ) {
			$matched = array_filter(
				$page_styles,
				function( $style ) use ( $current_style ) {
					return isset( $style['name'] ) && $style['name'] === $current_style;
				}
			);

			if ( ! empty( $matched ) ) {
				$style = reset( $matched );
				if ( isset( $style['title'] ) ) {
					$style_label = esc_html( $style['title'] );
				}
			}
		}
		?>
		<div
			class="masterstudy-templates-page-button"
			data-id="edit_page"
			data-post-id="<?php echo esc_attr( $post_id ); ?>"
			data-current-style="<?php echo esc_attr( $current_style ); ?>"
			data-current-course="<?php echo esc_attr( $current_course ); ?>"
		>
			<div class="masterstudy-templates-page-button__wrapper">
				<div class="masterstudy-templates-page-button__block">
					<span class="masterstudy-templates-page-button__block-title">
						<?php echo esc_html__( 'Template', 'masterstudy-lms-learning-management-system' ); ?>:
					</span>
					<span class="masterstudy-templates-page-button__template"><?php echo esc_html( $style_label ); ?></span>
				</div>
				<div class="masterstudy-templates-page-button__block">
					<span class="masterstudy-templates-page-button__block-title">
						<?php echo esc_html__( 'Course', 'masterstudy-lms-learning-management-system' ); ?>:
					</span>
					<span class="masterstudy-templates-page-button__course"><?php echo esc_html( $course_label ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}
}
add_action( 'manage_pages_custom_column', 'masterstudy_lms_fill_course_template_column_on_pages', 10, 2 );

function masterstudy_lms_get_template_library() {
	global $wpdb;

	return $wpdb->get_results(
		"
		SELECT ID AS id, post_title AS title, post_name AS name
		FROM {$wpdb->prefix}posts
		WHERE post_type = 'elementor_library'
		AND ID IN (
			SELECT post_id FROM {$wpdb->prefix}postmeta
			WHERE meta_key = 'masterstudy_elementor_course_template'
			AND meta_value != ''
		)
		",
		ARRAY_A
	);
}

function masterstudy_lms_get_my_templates( $admin = true ) {
	global $wpdb;

	$current_user_id = get_current_user_id();

	$sql = "
		SELECT ID AS id, post_title AS title, post_name AS name
		FROM {$wpdb->prefix}posts
		WHERE post_type = 'elementor_library'
		AND post_status = 'publish'
	";

	if ( $admin ) {
		$sql .= ' AND post_author = %d';
	}

	$sql .= " AND ID IN (
		SELECT post_id FROM {$wpdb->prefix}postmeta
		WHERE meta_key = 'masterstudy_elementor_course_user_template'
		AND meta_value != ''
	)";

	if ( $admin ) {
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$sql = $wpdb->prepare( $sql, $current_user_id );
	}
	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$results = $wpdb->get_results( $sql, ARRAY_A );

	return array_map(
		function( $item ) {
			$item['elementor'] = true;
			return $item;
		},
		$results
	);
}

function masterstudy_lms_not_remove_elementor_library_templates( $delete, $post_id ) {
	global $wpdb;

	$post = get_post( $post_id );

	if ( $post && 'elementor_library' === $post->post_type ) {
		$linked_post_ids = $wpdb->get_col(
			"SELECT post_id
			FROM {$wpdb->postmeta}
			WHERE meta_key = 'masterstudy_elementor_course_template'"
		);

		$linked_post_ids = array_map( 'intval', $linked_post_ids );

		if ( in_array( $post->ID, $linked_post_ids, true ) ) {
			return false;
		}
	}

	return $delete;
}
add_filter( 'pre_delete_post', 'masterstudy_lms_not_remove_elementor_library_templates', 10, 2 );

function masterstudy_lms_get_elementor_page_context( $post_id = null ) {
	if ( is_null( $post_id ) ) {
		$post_id = get_the_ID();
	}

	$is_course_template = false;
	$course_for_page    = '';

	$post_type = get_post_type( $post_id );

	if ( 'elementor_library' === $post_type ) {
		$is_course_template = ! empty( get_post_meta( $post_id, 'masterstudy_elementor_course_template', true ) );
	} elseif ( 'page' === $post_type ) {
		$course_for_page = get_post_meta( $post_id, 'masterstudy_elementor_course_page', true );
	}

	return array(
		'is_course_template' => $is_course_template,
		'course_for_page'    => $course_for_page,
	);
}

function masterstudy_lms_import_elementor_templates() {
	global $wpdb;
	global $wp_filesystem;

	if ( ! $wp_filesystem ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
	}

	$templates_path = STM_LMS_PATH . '/includes/elementor/templates/';
	$template_files = glob( $templates_path . '*.json' );

	if ( empty( $template_files ) ) {
		return;
	}

	$templates = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT p.ID, p.post_name, pm.meta_value
			FROM {$wpdb->posts} p
			INNER JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID
			WHERE p.post_type = %s
			AND pm.meta_key = %s",
			'elementor_library',
			'masterstudy_elementor_course_template'
		),
		ARRAY_A
	);

	$existing_meta_values = array_filter( array_column( $templates, 'meta_value' ) );
	$existing_slugs       = array_filter( array_column( $templates, 'post_name' ) );

	foreach ( $template_files as $file_path ) {
		$file_name     = basename( $file_path, '.json' );
		$file_contents = $wp_filesystem->get_contents( $file_path );

		if ( ! $file_contents ) {
			continue;
		}

		$template_data = json_decode( $file_contents, true );

		if ( empty( $template_data['content'] ) ) {
			continue;
		}

		$slug = sanitize_title( $template_data['title'] ?? $file_name );

		if ( in_array( $file_name, $existing_meta_values, true ) || in_array( $slug, $existing_slugs, true ) ) {
			continue;
		}

		$post_id = wp_insert_post(
			array(
				'post_title'  => esc_html( $template_data['title'] ?? ucfirst( $file_name ) ),
				'post_name'   => $slug,
				'post_type'   => 'elementor_library',
				'post_status' => 'publish',
				'meta_input'  => array(
					'_elementor_data'          => wp_json_encode( $template_data['content'] ),
					'_elementor_edit_mode'     => 'builder',
					'_elementor_template_type' => $template_data['type'] ?? 'page',
					'_elementor_page_settings' => $template_data['page_settings'] ?? array(),
					'masterstudy_elementor_course_template' => $file_name,
				),
			)
		);

		$inserted_slug = get_post_field( 'post_name', $post_id );

		if ( $inserted_slug !== $slug ) {
			wp_delete_post( $post_id, true );
		}
	}
}
register_activation_hook( MS_LMS_FILE, 'masterstudy_lms_import_elementor_templates' );
