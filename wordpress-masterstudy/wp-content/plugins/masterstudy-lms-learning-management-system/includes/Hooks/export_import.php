<?php

use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Repositories\CurriculumRepository;

/*
 * Hook for courses export to preserve sections and materials for correct connection courses and lessons when import
 */
add_action(
	'export_wp',
	function ( $args ) {
		global $wpdb;

		// Run this action only on course or all export
		if ( 'all' !== $args['content'] && PostType::COURSE !== $args['content'] ) {
			return;
		}

		// Get all ids of courses without auto-draft
		$course_ids = $wpdb->get_col( "SELECT ID FROM $wpdb->posts WHERE post_type = 'stm-courses' AND post_status != 'auto-draft'" );

		if ( empty( $course_ids ) ) {
			return;
		}

		foreach ( $course_ids as $course_id ) {
			$curriculum = ( new CurriculumRepository() )->get_curriculum( $course_id, false, false );

			if ( empty( $curriculum['sections'] ) ) {
				continue;
			}

			$export_payload = wp_json_encode(
				array(
					'sections'  => $curriculum['sections'],
					'materials' => $curriculum['materials'],
				),
				JSON_UNESCAPED_UNICODE
			);

			update_post_meta( $course_id, '_stm_export_sections', $export_payload );
		}
	}
);

function stm_lms_restore_curriculum( int $course_id, string $raw_payload ) {
	global $wpdb;

	$payload = json_decode( $raw_payload, true );
	if ( empty( $payload['sections'] ) ) {
		return;
	}

	$sections        = $payload['sections'];
	$materials       = $payload['materials'] ?? array();
	$sections_table  = stm_lms_curriculum_sections_name( $wpdb );
	$materials_table = stm_lms_curriculum_materials_name( $wpdb );

	// Create sections
	$section_map = array();
	foreach ( $sections as $row ) {
		$wpdb->replace(
			$sections_table,
			array(
				'title'     => $row['title'],
				'course_id' => $course_id,
				'order'     => (int) $row['order'],
			),
			array( '%s', '%d', '%d' )
		);
		$section_map[ $row['id'] ] = $wpdb->insert_id;
	}

	// Create materials
	foreach ( $materials as $row ) {
		$new_section = $section_map[ $row['section_id'] ] ?? 0;
		if ( ! $new_section ) {
			continue;
		}

		$new_post_id = $wpdb->get_var(
			$wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type = %s", $row['post_name'], $row['post_type'] )
		);

		if ( empty( $new_post_id ) ) {
			continue;
		}

		$wpdb->replace(
			$materials_table,
			array(
				'post_id'    => $new_post_id,
				'post_type'  => $row['post_type'],
				'section_id' => $new_section,
				'order'      => (int) $row['order'],
			),
			array( '%d', '%s', '%d', '%d' )
		);
	}
}

/*
 * Hook for importing materials and sections
 */
add_action(
	'import_end',
	function () {
		$query = new WP_Query(
			array(
				'post_type'      => PostType::COURSE,
				'meta_key'       => '_stm_export_sections',
				'posts_per_page' => -1,
			)
		);

		foreach ( $query->posts as $course ) {
			$payload = get_post_meta( $course->ID, '_stm_export_sections', true );
			stm_lms_restore_curriculum( $course->ID, $payload );
			delete_post_meta( $course->ID, '_stm_export_sections' );
		}
	}
);
