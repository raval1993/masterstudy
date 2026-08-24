<?php

namespace MasterStudy\Lms\Repositories;

use MasterStudy\Lms\Http\WpResponseFactory;

/**
 * Repository class for managing course templates with Elementor.
 */
class CourseTemplateRepository {
	public function update( string $template ): bool {
		if ( empty( $template ) ) {
			return false;
		}

		$settings = get_option( 'stm_lms_settings' );

		if ( $settings['course_style'] === $template ) {
			return true;
		}

		$settings['course_style'] = $template;

		return update_option( 'stm_lms_settings', $settings );
	}

	public function modify_template( string $title, int $post_id ) {
		$updated_post_data = array(
			'ID'          => $post_id,
			'post_title'  => $title,
			'post_name'   => sanitize_title( $title ),
			'post_status' => 'publish',
		);

		$post_id = wp_update_post( $updated_post_data );

		if ( is_wp_error( $post_id ) ) {
			return false;
		}

		return wp_update_post( $updated_post_data );
	}

	public function copy( string $title, int $duplicate_id ) {
		$elementor_data          = get_post_meta( $duplicate_id, '_elementor_data', true );
		$elementor_edit_mode     = get_post_meta( $duplicate_id, '_elementor_edit_mode', true );
		$elementor_template_type = get_post_meta( $duplicate_id, '_elementor_template_type', true );
		$elementor_page_settings = get_post_meta( $duplicate_id, '_elementor_page_settings', true );

		$post_id = wp_insert_post(
			array(
				'post_title'  => $title,
				'post_name'   => sanitize_title( $title ),
				'post_type'   => 'elementor_library',
				'post_status' => 'publish',
				'meta_input'  => array(
					'_elementor_data'                            => $elementor_data,
					'_elementor_edit_mode'                       => $elementor_edit_mode,
					'_elementor_template_type'                   => $elementor_template_type,
					'_elementor_page_settings'                   => $elementor_page_settings,
					'masterstudy_elementor_course_user_template' => 'yes',
				),
			)
		);

		if ( is_wp_error( $post_id ) ) {
			return false;
		}

		$post = get_post( $post_id );

		return array(
			'status'   => 'success',
			'template' => array(
				'id'        => $post_id,
				'title'     => $post->post_title,
				'name'      => $post->post_name,
				'elementor' => true,
			),
		);
	}

	public function save_page( string $template, int $course_id, int $page_id ) {
		if ( ! current_user_can( 'edit_pages' ) || empty( $course_id ) || empty( $page_id ) || empty( $template ) ) {
			return false;
		}

		$result = self::convert_page_to_course_template( $page_id, $course_id, $template );

		if ( $result ) {
			return array(
				'status' => 'success',
				'course' => $result,
			);
		}

		return false;
	}

	public function category_template( string $template, int $term_id ) {
		if ( ! empty( $term_id ) && ! empty( $template ) ) {
			$current_template = get_term_meta( $term_id, 'course_page_style', true );

			if ( $current_template === $template ) {
				return true;
			}

			$template = 'none' === $template ? '' : $template;
			$updated  = update_term_meta( $term_id, 'course_page_style', $template );

			return (bool) $updated;
		}

		return false;
	}

	public static function convert_page_to_course_template( $page_id, $course_id, $template_name ) {
		global $wpdb;

		$template = $wpdb->get_row(
			$wpdb->prepare(
				"
				SELECT ID, post_title, post_name
				FROM {$wpdb->prefix}posts
				WHERE post_type = 'elementor_library'
				AND post_status = 'publish'
				AND post_name = %s
				LIMIT 1
				",
				$template_name
			),
			ARRAY_A
		);

		if ( empty( $template ) ) {
			return false;
		}

		$template_data_raw = get_post_meta( $template['ID'], '_elementor_data', true );
		$template_data     = json_decode( $template_data_raw, true );

		update_post_meta( $page_id, '_elementor_data', wp_json_encode( $template_data ) );
		update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
		update_post_meta( $page_id, '_elementor_page_settings', array() );
		update_post_meta( $page_id, 'masterstudy_elementor_course_page', intval( $course_id ) );
		update_post_meta( $page_id, 'masterstudy_elementor_course_page_style', sanitize_text_field( $template_name ) );

		$course_title = get_the_title( intval( $course_id ) );

		if ( class_exists( '\Elementor\Plugin' ) ) {
			\Elementor\Plugin::instance()->files_manager->clear_cache();
		}

		return $course_title ?? false;
	}

	public function exists( int $template_id ): bool {
		return get_post_meta( $template_id, '_elementor_data', true );
	}

	public function delete( int $post_id ) {
		if ( ! empty( $post_id ) ) {
			return wp_delete_post( $post_id, true );
		}

		return false;
	}

	public function create( string $title ) {
		if ( ! empty( $title ) ) {
			$post_id = wp_insert_post(
				array(
					'post_title'  => $title,
					'post_name'   => sanitize_title( $title ),
					'post_type'   => 'elementor_library',
					'post_status' => 'publish',
					'meta_input'  => array(
						'_elementor_data'                            => '',
						'_elementor_edit_mode'                       => 'builder',
						'_elementor_template_type'                   => 'page',
						'_elementor_page_settings'                   => array(),
						'masterstudy_elementor_course_user_template' => 'yes',
					),
				)
			);

			if ( ! is_wp_error( $post_id ) ) {
				$post = get_post( $post_id );

				return array(
					'status'   => 'success',
					'template' => array(
						'id'        => $post_id,
						'title'     => $post->post_title,
						'name'      => $post->post_name,
						'elementor' => true,
					),
				);
			}
		}

		return false;
	}
}
