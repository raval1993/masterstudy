<?php

namespace MasterStudy\Lms\Http\Controllers\Review;

use MasterStudy\Lms\Http\Serializers\AdminReviewSerializer;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Repositories\AdminReviewRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class CreateReviewController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'content'    => 'nullable|string',
				'course_id'  => 'required|integer',
				'student_id' => 'required|integer',
				'mark'       => 'required|integer',
				'status'     => 'nullable|string|contains_list,publish;pending;draft',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$data = $validator->get_validated();

		$course = get_post( absint( $data['course_id'] ) );
		if ( ! $course || PostType::COURSE !== $course->post_type ) {
			return WpResponseFactory::validation_failed( array( 'course_id' => 'Course not found.' ) );
		}

		$user = get_userdata( absint( $data['student_id'] ) );
		if ( ! $user ) {
			return WpResponseFactory::validation_failed( array( 'student_id' => 'User not found.' ) );
		}

		$data['title'] = wp_strip_all_tags(
			sprintf(
				/* translators: %1$s: course title, %2$s: user name */
				esc_html__( 'Review on %1$s by %2$s', 'masterstudy-lms-learning-management-system' ),
				get_the_title( $course ),
				$user->user_login
			)
		);

		$repository = new AdminReviewRepository();
		$post_id    = $repository->create_review( $data );

		if ( 0 === $post_id ) {
			return WpResponseFactory::error( 'Failed to create review.' );
		}

		$post = $repository->get_review( $post_id );

		return WpResponseFactory::created(
			( new AdminReviewSerializer() )->toDetailArray( $post )
		);
	}
}
