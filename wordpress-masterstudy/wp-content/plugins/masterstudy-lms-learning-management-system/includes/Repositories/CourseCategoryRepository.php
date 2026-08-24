<?php

namespace MasterStudy\Lms\Repositories;

use MasterStudy\Lms\Plugin\Taxonomy;

final class CourseCategoryRepository {

	private string $taxonomy = Taxonomy::COURSE_CATEGORY;

	/**
	 * @return array|int[]|\WP_Error
	 */
	public function create( array $data ) {
		$parent = ( ! empty( $data['parent_category'] ) ) ? intval( $data['parent_category'] ) : 0;

		return wp_insert_term(
			$data['category'],
			$this->taxonomy,
			compact( 'parent' )
		);
	}

	public function list( array $args ): array {
		$page     = max( 1, (int) ( $args['page'] ?? 1 ) );
		$per_page = (int) ( $args['per_page'] ?? 10 );
		$per_page = max( 1, min( 100, $per_page ) );
		$search   = $args['search'] ?? '';

		$offset = ( $page - 1 ) * $per_page;

		$args = array(
			'taxonomy'     => $this->taxonomy,
			'hide_empty'   => false,
			'number'       => $per_page,
			'offset'       => $offset,
			'orderby'      => 'name',
			'order'        => 'ASC',

			'hierarchical' => false,
		);

		if ( ! empty( $search ) ) {
			$args['search'] = $search;
		}

		$total_args = array(
			'taxonomy'   => $this->taxonomy,
			'hide_empty' => false,
		);

		if ( ! empty( $search ) ) {
			$total_args['search'] = $search;
		}

		$categories = get_terms( $args );
		$total      = (int) wp_count_terms( $total_args );

		foreach ( $categories as $category ) {
			$category->count = \STM_LMS_Courses::get_children_terms_count( $category->term_id );
		}

		return array(
			'items' => $categories,
			'total' => $total,
		);
	}
}
