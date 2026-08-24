<?php

namespace MasterStudy\Lms\Http\Controllers\CourseCategory;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Http\Serializers\AdminCourseCategorySerializer;
use MasterStudy\Lms\Repositories\AdminCourseCategoryRepository;
use WP_REST_Response;

final class GetCourseCategoriesController {
	public function __invoke(): WP_REST_Response {
		$categories = ( new AdminCourseCategoryRepository() )->get_all();

		if ( is_wp_error( $categories ) ) {
			return WpResponseFactory::error( $categories->get_error_message() );
		}

		return new \WP_REST_Response(
			array( 'categories' => ( new AdminCourseCategorySerializer() )->collectionToArray( $categories ) )
		);
	}
}
