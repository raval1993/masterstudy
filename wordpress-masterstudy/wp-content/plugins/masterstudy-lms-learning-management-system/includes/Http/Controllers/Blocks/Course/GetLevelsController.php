<?php

namespace MasterStudy\Lms\Http\Controllers\Blocks\Course;

use MasterStudy\Lms\Http\Serializers\CourseLevelSerializer;
use WP_REST_Request;

class GetLevelsController {
	public function __invoke( WP_REST_Request $request ): \WP_REST_Response {
		return new \WP_REST_Response(
			array(
				'levels' => ( new CourseLevelSerializer() )->collectionToArray( \STM_LMS_Helpers::get_course_levels() ),
			)
		);
	}
}
