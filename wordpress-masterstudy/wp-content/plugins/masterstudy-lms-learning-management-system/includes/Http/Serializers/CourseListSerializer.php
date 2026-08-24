<?php

namespace MasterStudy\Lms\Http\Serializers;

use MasterStudy\Lms\Plugin\Taxonomy;
use MasterStudy\Lms\Utility\WpDate;

final class CourseListSerializer extends AbstractSerializer {

	/**
	 * @param \WP_Post $course
	 * @return array
	 */
	public function toArray( $course ): array {
		$terms = wp_get_post_terms( $course->ID, Taxonomy::COURSE_CATEGORY );
		$term  = is_array( $terms ) && ! empty( $terms ) ? $terms[0] : null;

		$author          = get_user_by( 'id', $course->post_author );
		$curriculum_info = \STM_LMS_Course::curriculum_info( $course->ID );
		$modified        = WpDate::format_gmt_for_site(
			(string) $course->post_modified_gmt,
			(string) $course->post_modified
		);

		return array(
			'id'                 => (int) $course->ID,
			'title'              => get_the_title( $course->ID ),
			'category'           => $term ? array(
				'id'   => (int) $term->term_id,
				'name' => (string) $term->name,
				'slug' => (string) $term->slug,
			) : null,
			'date'               => (string) $course->post_date,
			'date_formatted'     => \STM_LMS_Helpers::format_date( $course->post_date ),
			'modified'           => $modified['datetime'],
			'modified_gmt'       => (string) $course->post_modified_gmt,
			'modified_formatted' => $modified['formatted'],
			'status'             => (string) $course->post_status,
			'view_url'           => $this->get_view_url( $course ),
			'current_students'   => (int) get_post_meta( $course->ID, 'current_students', true ),
			'lessons_count'      => (int) $curriculum_info['lessons'],
			'quizzes_count'      => (int) $curriculum_info['quizzes'],
			'assignments_count'  => (int) $curriculum_info['assignments'],
			'pricing'            => $this->get_pricing( $course->ID ),
			'author'             => $author ? array(
				'id'   => (int) $author->ID,
				'name' => (string) $author->display_name,
			) : null,
		);
	}

	/**
	 * @param int $course_id
	 * @return array
	 */
	private function get_pricing( int $course_id ): array {
		$pricing_mode = get_post_meta( $course_id, 'pricing_mode', true );

		if ( empty( $pricing_mode ) || 'free' === $pricing_mode ) {
			return array(
				'mode'  => 'free',
				'plans' => array(),
			);
		}

		if ( 'affiliate' === $pricing_mode ) {
			$affiliate_price = get_post_meta( $course_id, 'affiliate_course_price', true );

			return array(
				'mode'  => 'affiliate',
				'plans' => array(
					array(
						'type'  => 'one_time',
						'price' => '' !== $affiliate_price ? (float) $affiliate_price : null,
					),
				),
			);
		}

		$plans = array();

		$single_sale = get_post_meta( $course_id, 'single_sale', true );
		if ( ! empty( $single_sale ) ) {
			$price      = get_post_meta( $course_id, 'price', true );
			$sale_price = get_post_meta( $course_id, 'sale_price', true );

			$plans[] = array(
				'type'       => 'one_time',
				'price'      => '' !== $price ? (float) $price : null,
				'sale_price' => '' !== $sale_price ? (float) $sale_price : null,
			);
		}

		$enterprise = get_post_meta( $course_id, 'enterprise', true );
		if ( 'on' === $enterprise ) {
			$enterprise_price = get_post_meta( $course_id, 'enterprise_price', true );

			$plans[] = array(
				'type'  => 'enterprise',
				'price' => '' !== $enterprise_price ? (float) $enterprise_price : null,
			);
		}

		$not_membership = get_post_meta( $course_id, 'not_membership', true );
		if ( empty( $not_membership ) ) {
			$plans[] = array(
				'type' => 'membership',
			);
		}

		$points = get_post_meta( $course_id, 'points', true );
		if ( 'on' === $points ) {
			$points_price = get_post_meta( $course_id, 'points_price', true );

			$plans[] = array(
				'type'  => 'points',
				'price' => '' !== $points_price ? (float) $points_price : null,
			);
		}

		$subscriptions = get_post_meta( $course_id, 'subscriptions', true );
		if ( 'on' === $subscriptions ) {
			$plan = array(
				'type' => 'subscription',
			);

			if ( function_exists( 'stm_lms_subscription_plan_items_table_name' ) ) {
				global $wpdb;

				$table = stm_lms_subscription_plan_items_table_name( $wpdb );
				$count = (int) $wpdb->get_var(
					$wpdb->prepare(
						// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
						"SELECT COUNT( DISTINCT plan_id ) FROM {$table} WHERE object_id = %d",
						$course_id
					)
				);

				$plan['count'] = $count;
			}

			$plans[] = $plan;
		}

		return array(
			'mode'  => 'paid',
			'plans' => $plans,
		);
	}

	private function get_view_url( \WP_Post $post ): string {
		$post_type = get_post_type_object( $post->post_type );

		if ( ! $post_type || ! is_post_type_viewable( $post_type ) ) {
			return '';
		}

		if ( in_array( $post->post_status, array( 'publish', 'private' ), true ) ) {
			return esc_url_raw( (string) get_permalink( $post ) );
		}

		if ( 'trash' === $post->post_status || ! current_user_can( 'edit_post', $post->ID ) ) {
			return '';
		}

		return esc_url_raw( (string) get_preview_post_link( $post ) );
	}
}
