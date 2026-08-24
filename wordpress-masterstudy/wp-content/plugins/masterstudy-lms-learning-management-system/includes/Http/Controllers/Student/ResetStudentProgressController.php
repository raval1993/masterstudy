<?php

namespace MasterStudy\Lms\Http\Controllers\Student;

use WP_REST_Response;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\CourseRepository;
use MasterStudy\Lms\Repositories\StudentsRepository;

class ResetStudentProgressController {
	public function __invoke( $course_id, $student_id ) {
		$course_id     = (int) $course_id;
		$student_id    = (int) $student_id;
		$students_repo = new StudentsRepository();
		$course_repo   = new CourseRepository();

		if ( ! $course_repo->exists( $course_id ) ) {
			return WpResponseFactory::not_found();
		}

		if ( ! \STM_LMS_Course::check_course_author( $course_id, get_current_user_id() ) ) {
			return WpResponseFactory::forbidden();
		}

		if ( ! $students_repo->is_student_enrolled_in_course( $course_id, $student_id ) ) {
			return WpResponseFactory::not_found();
		}

		return new WP_REST_Response( $students_repo->reset_student_progress( $course_id, $student_id ) );
	}
}
