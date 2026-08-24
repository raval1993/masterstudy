<?php

namespace MasterStudy\Lms\Http\Controllers\Course;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class UpdateCourseStatusController {
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
				'status' => 'required|string|contains_list,publish;pending;draft;trash;private',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$status = (string) $validator->get_validated()['status'];

		if ( 'trash' === $status ) {
			if ( ! current_user_can( 'delete_post', $course_id ) ) {
				return WpResponseFactory::forbidden();
			}

			$result = wp_trash_post( $course_id );
			if ( false === $result ) {
				return WpResponseFactory::error(
					esc_html__( 'Unable to move course to trash.', 'masterstudy-lms-learning-management-system' )
				);
			}

			return new WP_REST_Response(
				array(
					'success' => true,
					'id'      => $course_id,
					'status'  => $status,
				)
			);
		}

		if ( 'trash' === get_post_status( $course_id ) ) {
			$untrash = wp_untrash_post( $course_id );
			if ( ! $untrash ) {
				return WpResponseFactory::error(
					esc_html__( 'Unable to restore course from trash.', 'masterstudy-lms-learning-management-system' )
				);
			}
		}

		$updated = wp_update_post(
			array(
				'ID'          => $course_id,
				'post_status' => $status,
			),
			true
		);

		if ( is_wp_error( $updated ) ) {
			return WpResponseFactory::error( $updated->get_error_message() );
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'id'      => $course_id,
				'status'  => $status,
			)
		);
	}
}
