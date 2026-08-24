<?php

namespace MasterStudy\Lms\Http\Controllers\Blocks\Course;

use MasterStudy\Lms\Http\Serializers\CourseStatusSerializer;

class GetStatusesController {
	public function __invoke(): \WP_REST_Response {
		return new \WP_REST_Response(
			array(
				'statuses' => ( new CourseStatusSerializer() )->collectionToArray( \STM_LMS_Helpers::get_course_statuses() ),
			)
		);
	}
}
