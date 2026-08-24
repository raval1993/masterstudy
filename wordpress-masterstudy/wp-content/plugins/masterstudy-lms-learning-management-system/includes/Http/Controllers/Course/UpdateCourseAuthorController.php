<?php

namespace MasterStudy\Lms\Http\Controllers\Course;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class UpdateCourseAuthorController {
	public function __invoke( int $course_id, WP_REST_Request $request ): WP_REST_Response {
		if ( PostType::COURSE !== get_post_type( $course_id ) ) {
			return WpResponseFactory::not_found();
		}

		if ( ! current_user_can( 'edit_post', $course_id ) ) {
			return WpResponseFactory::forbidden();
		}

		$json_params = $request->get_json_params();

		$validator = new Validator(
			! empty( $json_params ) ? $json_params : $request->get_params(),
			array(
				'author_id' => 'required|integer',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$author_id = (int) $validator->get_validated()['author_id'];

		$user = get_user_by( 'id', $author_id );

		if ( false === $user ) {
			return WpResponseFactory::validation_failed(
				array( 'author_id' => esc_html__( 'User not found.', 'masterstudy-lms-learning-management-system' ) )
			);
		}

		$updated = wp_update_post(
			array(
				'ID'          => $course_id,
				'post_author' => $author_id,
			),
			true
		);

		if ( is_wp_error( $updated ) ) {
			return WpResponseFactory::error( $updated->get_error_message() );
		}

		return new WP_REST_Response(
			array(
				'success'   => true,
				'id'        => $course_id,
				'author_id' => $author_id,
			)
		);
	}
}
