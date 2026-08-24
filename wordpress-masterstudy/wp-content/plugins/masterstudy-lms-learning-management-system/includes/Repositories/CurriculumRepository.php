<?php

namespace MasterStudy\Lms\Repositories;

use MasterStudy\Lms\Database\CurriculumMaterial;
use MasterStudy\Lms\Database\CurriculumSection;
use MasterStudy\Lms\Http\Serializers\CurriculumMaterialSerializer;
use MasterStudy\Lms\Http\Serializers\CurriculumSectionSerializer;

class CurriculumRepository {
	/**
	 * @param object $db CurriculumSection|CurriculumMaterial
	 * @param object $item CurriculumSection|CurriculumMaterial
	 */
	public function reorder( $db, $item, ?int $new_oder = null, bool $added = false ): void {
		$decrease     = ! $added;
		$where_clause = isset( $item->course_id ) ? 'course_id' : 'section_id';
		$query        = $db->query()
			->where( $where_clause, $item->{$where_clause} )
			->where_not( 'id', $item->id );

		if ( ! empty( $new_oder ) ) {
			$decrease = $new_oder > $item->order;
			$query->where_between( 'order', array( $item->order, $new_oder ) );
		} else {
			$query->where_gte( 'order', $item->order );
		}

		$results = $query->find();

		foreach ( $results as $item ) {
			$item->order += $decrease ? -1 : 1;
			$item->order  = max( 1, $item->order ); // Prevent negative values
			$item->save();
		}
	}

	public function get_curriculum( int $course_id, bool $joined = false ): array {
		$sections  = ( new CurriculumSectionSerializer() )->collectionToArray(
			( new CurriculumSectionRepository() )->get_course_sections( $course_id )
		);
		$materials = ! empty( $sections )
			? ( new CurriculumMaterialSerializer() )->collectionToArray(
				( new CurriculumMaterialRepository() )->get_section_materials( array_column( $sections, 'id' ) )
			)
			: array();

		if ( $joined ) {
			foreach ( $sections as &$section ) {
				$section['materials'] = array_values(
					array_filter(
						$materials,
						function ( $material ) use ( $section ) {
							return $material['section_id'] === $section['id'];
						}
					)
				);
			}

			return $sections;
		} else {
			return apply_filters(
				'masterstudy_lms_course_curriculum',
				compact( 'sections', 'materials' ),
				$course_id
			);
		}
	}

	public function get_lesson_course_ids( int $post_id ): array {
		return $this->get_post_course_ids( $post_id );
	}

	public function get_post_course_ids( int $post_id ): array {
		$materials = ( new CurriculumMaterialRepository() )->find_by_post( $post_id );

		if ( ! empty( $materials ) ) {
			$sections = ( new CurriculumSectionRepository() )->find_by_ids(
				array_column( $materials, 'section_id' )
			);

			return ! empty( $sections )
				? array_unique( array_column( $sections, 'course_id' ) )
				: array();
		}

		return array();
	}

	public function get_course_counts_by_lesson_ids( array $lesson_ids ): array {
		return $this->get_course_counts_by_post_ids( $lesson_ids );
	}

	/**
	 * @param array<int, int> $post_ids
	 *
	 * @return array<int, array<int, int>>
	 */
	public function get_course_ids_by_post_ids( array $post_ids ): array {
		global $wpdb;

		$post_ids = array_values(
			array_filter(
				array_map( 'absint', $post_ids )
			)
		);

		if ( empty( $post_ids ) ) {
			return array();
		}

		$materials_table = esc_sql( ( new CurriculumMaterial() )->get_table() );
		$sections_table  = esc_sql( ( new CurriculumSection() )->get_table() );
		$placeholders    = implode( ',', array_fill( 0, count( $post_ids ), '%d' ) );

		$results = $wpdb->get_results(
			$wpdb->prepare(
				// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
				"SELECT DISTINCT materials.post_id, sections.course_id FROM {$materials_table} AS materials
				INNER JOIN {$sections_table} AS sections ON sections.id = materials.section_id
				WHERE materials.post_id IN ({$placeholders})",
				$post_ids
				// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
			),
			ARRAY_A
		);

		if ( ! is_array( $results ) ) {
			return array();
		}

		$course_ids_by_post = array();

		foreach ( $results as $result ) {
			$post_id   = isset( $result['post_id'] ) ? (int) $result['post_id'] : 0;
			$course_id = isset( $result['course_id'] ) ? (int) $result['course_id'] : 0;

			if ( $post_id <= 0 || $course_id <= 0 ) {
				continue;
			}

			if ( ! isset( $course_ids_by_post[ $post_id ] ) ) {
				$course_ids_by_post[ $post_id ] = array();
			}

			$course_ids_by_post[ $post_id ][ $course_id ] = $course_id;
		}

		return array_map( 'array_values', $course_ids_by_post );
	}

	public function get_course_counts_by_post_ids( array $post_ids ): array {
		global $wpdb;

		$post_ids = array_values(
			array_filter(
				array_map( 'absint', $post_ids )
			)
		);

		if ( empty( $post_ids ) ) {
			return array();
		}

		$materials_table = esc_sql( ( new CurriculumMaterial() )->get_table() );
		$sections_table  = esc_sql( ( new CurriculumSection() )->get_table() );
		$placeholders    = implode( ',', array_fill( 0, count( $post_ids ), '%d' ) );

		$results = $wpdb->get_results(
			$wpdb->prepare(
				// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
				"SELECT materials.post_id, COUNT(DISTINCT sections.course_id) AS course_count FROM {$materials_table} AS materials
				INNER JOIN {$sections_table} AS sections ON sections.id = materials.section_id
				WHERE materials.post_id IN ({$placeholders})
				GROUP BY materials.post_id",
				$post_ids
				// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
			),
			ARRAY_A
		);

		if ( ! is_array( $results ) ) {
			return array();
		}

		$counts = array();

		foreach ( $results as $result ) {
			$post_id = isset( $result['post_id'] ) ? (int) $result['post_id'] : 0;

			if ( $post_id > 0 ) {
				$counts[ $post_id ] = isset( $result['course_count'] ) ? (int) $result['course_count'] : 0;
			}
		}

		return $counts;
	}

	public function duplicate_curriculum( int $course_id, int $new_course_id, string $target_lang = '' ): void {
		$curriculum_sections = ( new CurriculumRepository() )->get_curriculum( $course_id, true );

		if ( array_key_exists( 'sitepress', $GLOBALS ) && class_exists( 'SitePress' ) ) {
			global $sitepress;

			$wpml_sync_settings = $sitepress->get_setting( \WPML_Element_Sync_Settings_Factory::KEY_POST_SYNC_OPTION, array() );
		}

		foreach ( $curriculum_sections as $section ) {
			$section['course_id'] = $new_course_id;
			$section['title']     = apply_filters(
				'wpml_translate_single_string',
				$section['title'],
				'masterstudy-lms-learning-management-system',
				"section_title_{$section['id']}",
				$target_lang
			);

			$new_section = ( new CurriculumSectionRepository() )->create( $section );

			if ( ! empty( $new_section->id ) ) {
				foreach ( $section['materials'] as $material ) {
					$material['section_id'] = $new_section->id;

					if ( ! empty( $wpml_sync_settings ) ) {
						$return_original_if_missing = 1 !== intval( $wpml_sync_settings[ get_post_type( $material['post_id'] ) ] ?? 0 );
						$material['post_id']        = apply_filters( 'wpml_object_id', $material['post_id'], 'post', $return_original_if_missing, $target_lang );
					}

					if ( ! empty( $material['post_id'] ) ) {
						( new CurriculumMaterialRepository() )->create( $material );
					}
				}
			}
		}
	}
}
