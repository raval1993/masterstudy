<?php

namespace MasterStudy\Lms\Http\Controllers\Student;

use WP_REST_Request;
use WP_REST_Response;
use MasterStudy\Lms\Validation\Validator;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\CourseRepository;
use MasterStudy\Lms\Repositories\StudentsRepository;

class SetStudentProgressController {
	public function __invoke( $course_id, $student_id, WP_REST_Request $request ) {
		$course_id  = (int) $course_id;
		$student_id = (int) $student_id;

		if ( ! ( new CourseRepository() )->exists( $course_id ) ) {
			return WpResponseFactory::not_found();
		}

		if ( ! \STM_LMS_Course::check_course_author( $course_id, get_current_user_id() ) ) {
			return WpResponseFactory::forbidden();
		}

		if ( ! ( new StudentsRepository() )->is_student_enrolled_in_course( $course_id, $student_id ) ) {
			return WpResponseFactory::not_found();
		}

		$validator = new Validator(
			$request->get_params(),
			array(
				'item_id'   => 'required|integer',
				'completed' => 'required|boolean',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$data = $validator->get_validated();

		return new WP_REST_Response( ( new StudentsRepository() )->set_student_progress( $course_id, $student_id, $data ) );
	}
}
