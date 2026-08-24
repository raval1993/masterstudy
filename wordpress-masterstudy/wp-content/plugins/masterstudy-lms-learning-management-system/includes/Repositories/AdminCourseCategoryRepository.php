<?php

namespace MasterStudy\Lms\Repositories;

use MasterStudy\Lms\Plugin\Taxonomy;

final class AdminCourseCategoryRepository {

	private string $taxonomy = Taxonomy::COURSE_CATEGORY;

	private array $meta_keys = array(
		'course_page_style',
		'course_image',
		'course_icon',
		'course_color',
	);

	/**
	 * @return \WP_Term|\WP_Error|null
	 */
	public function create( array $data ): mixed {
		$parent      = ( ! empty( $data['parent_category'] ) ) ? (int) $data['parent_category'] : 0;
		$description = $data['description'] ?? '';

		$term = wp_insert_term(
			$data['category'],
			$this->taxonomy,
			compact( 'parent', 'description' )
		);

		if ( is_wp_error( $term ) ) {
			return $term;
		}

		$term_id = $term['term_id'] ?? null;

		if ( $term_id ) {
			$this->save_meta( $term_id, $data );
		}

		return get_term( $term_id );
	}

	/**
	 * @return array<\WP_Term>
	 */
	public function get_all(): array {
		return get_terms(
			array(
				'hide_empty' => false,
				'taxonomy'   => $this->taxonomy,
			)
		);
	}

	/**
	 * @return \WP_Term|\WP_Error|null
	 */
	public function get( int $id ): mixed {
		return get_term( $id, $this->taxonomy );
	}

	/**
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

		$result = wp_update_term( $id, $this->taxonomy, $args );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$this->save_meta( $id, $data );

		return get_term( $result['term_id'], $this->taxonomy );
	}

	/**
	 * @return bool|\WP_Error
	 */
	public function delete( int $id ): mixed {
		$result = wp_delete_term( $id, $this->taxonomy );

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

		$base_args = array(
			'taxonomy'   => $this->taxonomy,
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		);

		if ( $search ) {
			$base_args['search'] = $search;
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

		foreach ( $items as $item ) {
			$item->count = \STM_LMS_Courses::get_children_terms_count( $item->term_id );
		}

		$items = array_values( $items );

		return compact( 'items', 'total', 'pages' );
	}

	private function save_meta( int $term_id, array $data ): void {
		foreach ( $this->meta_keys as $key ) {
			if ( isset( $data[ $key ] ) ) {
				$value = 'course_image' === $key ? absint( $data[ $key ] ) : sanitize_text_field( $data[ $key ] );
				update_term_meta( $term_id, $key, $value );
			}
		}
	}
}
