<?php

namespace MasterStudy\Lms\Http\Controllers\Course;

use MasterStudy\Lms\Repositories\CourseRepository;
use WP_REST_Request;

class GetCoursesController {
	public function __invoke( WP_REST_Request $request ): \WP_REST_Response {
		return new \WP_REST_Response(
			( new CourseRepository() )->get_all( $request->get_params() ),
		);
	}
}
