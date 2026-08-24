<?php
namespace MasterStudy\Lms\Http\Controllers\Course;

use MasterStudy\Lms\Plugin\PostType;
use WP_REST_Request;
use WP_REST_Response;

class GetInstructorCoursesController {

	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$per_page = (int) ( $request->get_param( 'per_page' ) ?? 10 );
		$page     = (int) ( $request->get_param( 'page' ) ?? 1 );
		$status   = (string) ( $request->get_param( 'status' ) ?? '' );
		$render   = (string) ( $request->get_param( 'render' ) ?? 'json' );
		$user_id  = (int) ( $request->get_param( 'user' ) ?? 0 );
		$current_user_id = \get_current_user_id();
		$is_author_query = ( $user_id > 0 && $user_id === $current_user_id );
		$include_private = $is_author_query;

		$page     = max( 1, $page );
		$per_page = max( 1, $per_page );

		$post_status = 'published' === $status ? 'publish' : $status;
		$available_statuses = array( 'publish', 'draft', 'pending', 'rejected' );

		if ( $include_private ) {
			$available_statuses[] = 'private';
		}

		$args = array(
			'author'         => $user_id,
			'post_type'      => PostType::COURSE,
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'post_status'    => empty( $post_status ) ? $available_statuses : $post_status,
		);

		if ( ! empty( $post_status ) && 'private' === $post_status && ! $include_private ) {
			$args['post_status'] = $available_statuses;
		}

		if ( ! empty( $post_status ) && 'coming_soon_status' === $post_status ) {
			$args['meta_query'][] = array(
				'key'     => $post_status,
				'value'   => '1',
				'compare' => '=',
			);
		}

		$payload = \STM_LMS_Instructor::get_instructor_courses( $args, $per_page );

		if ( 'html' === $render ) {

			$reviews = \STM_LMS_Options::get_option( 'course_tab_reviews', true );

			ob_start();
			if ( ! empty( $payload['posts'] ) ) {
				foreach ( $payload['posts'] as $course ) {
					\STM_LMS_Templates::show_lms_template(
						'components/course/card/default',
						array(
							'course'          => $course,
							'public'          => false,
							'reviews'         => (bool) $reviews,
							'student_card'    => false,
							'instructor_card' => true,
						)
					);
				}
			}
			$html = ob_get_clean();

			$pagination = '';
			if ( ! empty( $payload['pages'] ) && (int) $payload['pages'] > 1 ) {
				ob_start();
				\STM_LMS_Templates::show_lms_template(
					'components/pagination',
					array(
						'max_visible_pages' => 5,
						'total_pages'       => (int) $payload['pages'],
						'current_page'      => $page,
						'dark_mode'         => false,
						'is_queryable'      => false,
						'done_indicator'    => false,
						'is_api'            => true,
						'thin'              => false,
					)
				);
				$pagination = ob_get_clean();
			}

			$payload['html']       = $html;
			$payload['pagination'] = $pagination;
			$payload['page']       = $page;
		}

		return new WP_REST_Response( $payload );
	}
}
