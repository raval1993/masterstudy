<?php

namespace MasterStudy\Lms\Repositories;

use MasterStudy\Lms\Plugin\Taxonomy;

final class QuestionCategoryRepository {
	/**
	 * @param array $data
	 *
	 * @return array|\WP_Error|\WP_Term|null
	 */
	public function create( array $data ): mixed {
		$parent      = ( ! empty( $data['parent_category'] ) ) ? (int) $data['parent_category'] : 0;
		$description = $data['description'] ?? '';
		$term        = wp_insert_term(
			$data['category'],
			Taxonomy::QUESTION_CATEGORY,
			compact( 'parent', 'description' )
		);

		if ( is_wp_error( $term ) ) {
			return $term;
		}

		return get_term( $term['term_id'] ?? null );
	}

	/**
	 * @return array<\WP_Term>
	 */
	public function get_all(): array {
		return get_terms(
			array(
				'hide_empty' => false,
				'taxonomy'   => Taxonomy::QUESTION_CATEGORY,
			)
		);
	}

	/**
	 * @param int $id
	 *
	 * @return \WP_Term|\WP_Error|null
	 */
	public function get( int $id ): mixed {
		return get_term( $id, Taxonomy::QUESTION_CATEGORY );
	}

	/**
	 * @param int $id
	 * @param array $data
	 *
	 * @return \WP_Term|\WP_Error|null
	 */
	public function update( int $id, array $data ): mixed {
		$args = array();
		if ( ! empty( $data['category'] ) ) {
			$args['name'] = $data['category'];
		}
		if ( ! empty( $data['slug'] ) ) {
			$args['slug'] = $data['slug'];
		}
		if ( isset( $data['description'] ) ) {
			$args['description'] = $data['description'];
		}
		if ( isset( $data['parent_category'] ) ) {
			$args['parent'] = (int) $data['parent_category'];
		}
		$result = wp_update_term( $id, Taxonomy::QUESTION_CATEGORY, $args );
		if ( is_wp_error( $result ) ) {
			return $result;
		}
		return get_term( $result['term_id'], Taxonomy::QUESTION_CATEGORY );
	}

	/**
	 * @param int $id
	 *
	 * @return bool|\WP_Error
	 */
	public function delete( int $id ): mixed {
		$result = wp_delete_term( $id, Taxonomy::QUESTION_CATEGORY );
		if ( is_wp_error( $result ) ) {
			return $result;
		}
		return true;
	}

	/**
	 * @return array{ items: array<\WP_Term>, total: int, pages: int }
	 */
	public function get_list( array $params ): array {
		$per_page = max( 1, min( 100, (int) ( $params['per_page'] ?? 10 ) ) );
		$page     = max( 1, (int) ( $params['page'] ?? 1 ) );
		$search   = sanitize_text_field( $params['search'] ?? '' );
		$sort     = sanitize_text_field( $params['sort'] ?? '' );

		$base_args = array(
			'taxonomy'   => Taxonomy::QUESTION_CATEGORY,
			'hide_empty' => false,
		);
		if ( $search ) {
			$base_args['search'] = $search;
		}

		if ( '' !== $sort && class_exists( 'STM_LMS_Helpers' ) ) {
			$sort_params = \STM_LMS_Helpers::get_sort_params_by_string( $sort );
			$key         = $sort_params['key'] ?? '';
			$direction   = strtoupper( (string) ( $sort_params['direction'] ?? 'ASC' ) );

			if ( in_array( $key, array( 'name', 'count' ), true ) ) {
				$base_args['orderby'] = $key;
				$base_args['order']   = in_array( $direction, array( 'ASC', 'DESC' ), true ) ? $direction : 'ASC';
			}
		}

		$total = (int) get_terms( array_merge( $base_args, array( 'fields' => 'count' ) ) );
		$pages = (int) ceil( $total / $per_page );

		$items = get_terms(
			array_merge(
				$base_args,
				array(
					'number' => $per_page,
					'offset' => ( $page - 1 ) * $per_page,
				)
			)
		);

		if ( is_wp_error( $items ) ) {
			$items = array();
		}

		$items = array_values( $items );

		return compact( 'items', 'total', 'pages' );
	}
}
