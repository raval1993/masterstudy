<?php

namespace MasterStudy\Lms\Http\Controllers\Blocks\Course;

use MasterStudy\Lms\Http\Serializers\CourseCategorySerializer;
use MasterStudy\Lms\Plugin\Taxonomy;
use WP_REST_Request;

class GetCategoriesController {
	public function __invoke( WP_REST_Request $request ): \WP_REST_Response {
		return new \WP_REST_Response(
			array(
				'categories'        => ( new CourseCategorySerializer() )->collectionToArray( Taxonomy::all_categories( $request ) ),
				'course_url'        => \STM_LMS_Course::courses_page_url(),
				'rating_visibility' => \STM_LMS_Options::get_option( 'course_tab_reviews', true ),
			)
		);
	}
}
