<?php

namespace MasterStudy\Lms\Http\Controllers\Course;

use MasterStudy\Lms\Http\Serializers\CourseCategorySerializer;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\CourseCategoryRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;

class GetCategoriesListController {
	public function __invoke( WP_REST_Request $request ): \WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'page'     => 'nullable|integer',
				'per_page' => 'nullable|integer',
				'search'   => 'nullable|string',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$categories = ( new CourseCategoryRepository() )->list( $validator->get_validated() );

		return new \WP_REST_Response(
			array(
				'categories' => ( new CourseCategorySerializer() )->collectionToArray( $categories['items'] ),
				'total'      => $categories['total'],
			)
		);
	}
}
