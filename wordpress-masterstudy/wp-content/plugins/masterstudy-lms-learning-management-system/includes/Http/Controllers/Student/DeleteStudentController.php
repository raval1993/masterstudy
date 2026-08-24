<?php

namespace MasterStudy\Lms\Http\Controllers\Student;

use WP_REST_Request;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\CourseRepository;
use MasterStudy\Lms\Repositories\StudentsRepository;

final class DeleteStudentController {

	public function __invoke( WP_REST_Request $request, $course_id, $student_id ) {
		$repo             = new CourseRepository();
		$subscribed_email = $request->get_param( 'subscribed_email' ) ?? null;

		if ( ! $repo->exists( $course_id ) ) {
			return WpResponseFactory::not_found();
		}

		if ( ! \STM_LMS_Course::check_course_author( $course_id, get_current_user_id() ) ) {
			return WpResponseFactory::forbidden();
		}

		( new StudentsRepository() )->delete_student_by_course( $course_id, $student_id, $subscribed_email );

		return WpResponseFactory::ok();
	}
}
