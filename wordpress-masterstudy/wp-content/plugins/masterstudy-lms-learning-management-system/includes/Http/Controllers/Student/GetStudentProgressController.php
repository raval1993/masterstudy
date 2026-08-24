<?php

namespace MasterStudy\Lms\Http\Controllers\Student;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\CourseRepository;
use MasterStudy\Lms\Repositories\StudentProgressRepository;
use MasterStudy\Lms\Repositories\StudentsRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class GetStudentProgressController {
	public function __invoke( WP_REST_Request $request, $course_id, $student_id ): WP_REST_Response {
		$validator = new Validator(
			array(
				'course_id'  => $course_id,
				'student_id' => $student_id,
			),
			array(
				'course_id'  => 'required|integer',
				'student_id' => 'required|integer',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$params     = $validator->get_validated();
		$course_id  = (int) $params['course_id'];
		$student_id = (int) $params['student_id'];
		$user_id    = get_current_user_id();
		$repo       = new StudentsRepository();
		$progress   = new StudentProgressRepository();

		if ( ! ( new CourseRepository() )->exists( $course_id ) ) {
			return WpResponseFactory::not_found();
		}

		if ( ! \STM_LMS_Course::check_course_author( $course_id, $user_id ) ) {
			return WpResponseFactory::forbidden();
		}

		if ( ! $repo->is_student_enrolled_in_course( $course_id, $student_id ) ) {
			return WpResponseFactory::not_found();
		}

		return new WP_REST_Response( $progress->get_student_progress( $course_id, $student_id ) );
	}
}
