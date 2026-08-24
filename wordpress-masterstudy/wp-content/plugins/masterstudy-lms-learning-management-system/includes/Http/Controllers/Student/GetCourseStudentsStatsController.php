<?php

namespace MasterStudy\Lms\Http\Controllers\Student;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\CourseRepository;
use MasterStudy\Lms\Repositories\StudentsRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class GetCourseStudentsStatsController {

	public function __invoke( WP_REST_Request $request, $course_id ): WP_REST_Response {
		$validator = new Validator(
			array(
				'course_id' => $course_id,
			),
			array(
				'course_id' => 'required|integer',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$params    = $validator->get_validated();
		$course_id = (int) $params['course_id'];
		$user_id   = get_current_user_id();

		if ( ! ( new CourseRepository() )->exists( $course_id ) ) {
			return WpResponseFactory::not_found();
		}

		if ( ! \STM_LMS_Course::check_course_author( $course_id, $user_id ) ) {
			return WpResponseFactory::forbidden();
		}

		return new WP_REST_Response( ( new StudentsRepository() )->get_course_students_stats( $course_id ) );
	}
}
