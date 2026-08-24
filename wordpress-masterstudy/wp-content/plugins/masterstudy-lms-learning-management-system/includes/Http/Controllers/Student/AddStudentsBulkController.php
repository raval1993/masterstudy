<?php

namespace MasterStudy\Lms\Http\Controllers\Student;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\CourseRepository;
use MasterStudy\Lms\Repositories\StudentsRepository;
use WP_REST_Request;
use WP_REST_Response;

class AddStudentsBulkController {

	public function __invoke( WP_REST_Request $request ) {
		$raw  = (string) $request->get_body();
		$body = json_decode( $raw, true );

		if ( empty( $body ) || ! is_array( $body ) ) {
			return WpResponseFactory::validation_failed(
				array(
					'body' => array( esc_html__( 'Invalid JSON body.', 'masterstudy-lms-learning-management-system' ) ),
				)
			);
		}

		$course_id = isset( $body['course_id'] ) ? absint( $body['course_id'] ) : 0;
		$students  = isset( $body['students'] ) && is_array( $body['students'] ) ? $body['students'] : array();

		if ( ! $course_id ) {
			return WpResponseFactory::validation_failed(
				array(
					'course_id' => array( esc_html__( 'Course ID is required.', 'masterstudy-lms-learning-management-system' ) ),
				)
			);
		}

		if ( empty( $students ) ) {
			return WpResponseFactory::validation_failed(
				array(
					'students' => array( esc_html__( 'Students array is required.', 'masterstudy-lms-learning-management-system' ) ),
				)
			);
		}

		if ( ! ( new CourseRepository() )->exists( $course_id ) ) {
			return WpResponseFactory::not_found();
		}

		$sanitized = array();
		$errors    = array();

		foreach ( $students as $index => $student ) {
			$email = isset( $student['email'] ) ? sanitize_email( (string) $student['email'] ) : '';

			if ( empty( $email ) || ! is_email( $email ) ) {
				$errors[ 'students.' . $index . '.email' ] = array(
					esc_html__( 'Valid email is required.', 'masterstudy-lms-learning-management-system' ),
				);
				continue;
			}

			$sanitized[] = array(
				'email'      => $email,
				'first_name' => isset( $student['first_name'] ) ? sanitize_text_field( (string) $student['first_name'] ) : '',
				'last_name'  => isset( $student['last_name'] ) ? sanitize_text_field( (string) $student['last_name'] ) : '',
			);
		}

		if ( ! empty( $errors ) ) {
			return WpResponseFactory::validation_failed( $errors );
		}

		$result = ( new StudentsRepository() )->add_students_bulk( $course_id, $sanitized );

		return new WP_REST_Response( $result );
	}
}
