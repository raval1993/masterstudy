<?php

namespace MasterStudy\Lms\Http\Controllers\Course;

use MasterStudy\Lms\Repositories\CourseRepository;
use MasterStudy\Lms\Http\Serializers\InstructorCoursesSerializer;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

class GetInstructorPublicCoursesController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'page' => 'required|integer',
				'pp'   => 'required|integer',
				'user' => 'required|integer',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$validated = $validator->get_validated();
		$args      = array(
			'posts_per_page' => $validated['pp'],
			'paged'          => $validated['page'],
			'author__in'     => $validated['user'],
			'page'           => $validated['page'],
		);

		$courses = ( new CourseRepository() )->instructor_courses( $args );

		return new WP_REST_Response(
			( new InstructorCoursesSerializer() )->toArray( $courses )
		);
	}
}
