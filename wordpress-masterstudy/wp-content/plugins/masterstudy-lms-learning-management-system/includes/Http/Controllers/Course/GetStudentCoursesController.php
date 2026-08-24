<?php

namespace MasterStudy\Lms\Http\Controllers\Course;

use MasterStudy\Lms\Repositories\CourseRepository;
use MasterStudy\Lms\Http\Serializers\StudentCoursesSerializer;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

class GetStudentCoursesController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'page'   => 'required|integer',
				'pp'     => 'required|integer',
				'user'   => 'required|integer',
				'status' => 'nullable|string',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$courses = ( new CourseRepository() )->student_courses( $validator->get_validated() );

		return new WP_REST_Response(
			( new StudentCoursesSerializer() )->toArray( $courses )
		);
	}
}
