<?php

namespace MasterStudy\Lms\Repositories;

use MasterStudy\Lms\Plugin\PostType;

final class ReviewRepository {
	const POST_TYPE = 'stm-reviews';

	public function get_instructor_reviews( array $params ) {
		$args = array(
			'post_type'      => PostType::COURSE,
			'author__in'     => array( $params['user'] ),
			'posts_per_page' => -1,
			'fields'         => 'ids',
		);

		if ( ! empty( $params['course'] ) ) {
			$args['s'] = $params['course'];
		}

		$courses = get_posts( $args );

		if ( empty( $courses ) ) {
			return array();
		}

		$reviews_args = array(
			'post_type'      => PostType::REVIEW,
			'meta_query'     => array(
				array(
					'key'     => 'review_course',
					'value'   => $courses,
					'compare' => 'IN',
				),
			),
			'posts_per_page' => $params['pp'],
			'paged'          => $params['page'],
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		if ( ! empty( $params['rating'] ) ) {
			$reviews_args['meta_query'][] = array(
				'key'     => 'review_mark',
				'value'   => array( $params['rating'], $params['rating'] + 0.99 ),
				'type'    => 'DECIMAL',
				'compare' => 'BETWEEN',
			);
		}

		$reviews  = new \WP_Query( $reviews_args );
		$response = array();

		if ( ! empty( $reviews->posts ) ) {
			foreach ( $reviews->posts as $review ) {
				$response['reviews'][] = \STM_LMS_Templates::load_lms_template(
					'components/review-card',
					array(
						'review' => $review,
					)
				);
			}

			if ( $reviews->max_num_pages > 1 ) {
				$response['pagination'] = \STM_LMS_Templates::load_lms_template(
					'components/pagination',
					array(
						'max_visible_pages' => 5,
						'total_pages'       => $reviews->max_num_pages,
						'current_page'      => $params['page'],
						'dark_mode'         => false,
						'is_queryable'      => false,
						'done_indicator'    => false,
						'is_hidden'         => false,
						'is_api'            => true,
					)
				);
			}

			$response['total_pages'] = $reviews->max_num_pages;
			$response['total_posts'] = $reviews->found_posts;
		}

		return $response;
	}
}
