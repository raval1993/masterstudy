<?php

namespace MasterStudy\Lms\Http\Controllers\CourseCategory;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Http\Serializers\AdminCourseCategorySerializer;
use MasterStudy\Lms\Repositories\AdminCourseCategoryRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class CreateCourseCategoryController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_json_params(),
			array(
				'category'          => 'required|string',
				'description'       => 'nullable|string',
				'parent_category'   => 'nullable|integer',
				'course_page_style' => 'nullable|string',
				'course_image'      => 'nullable|integer',
				'course_icon'       => 'nullable|string',
				'course_color'      => 'nullable|string',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$category = ( new AdminCourseCategoryRepository() )->create( $validator->get_validated() );

		if ( is_wp_error( $category ) ) {
			return WpResponseFactory::error( $category->get_error_message() );
		}

		return new \WP_REST_Response(
			array( 'category' => ( new AdminCourseCategorySerializer() )->toArray( $category ) )
		);
	}
}
