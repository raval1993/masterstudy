<?php

namespace MasterStudy\Lms\Http\Controllers\CourseCategory;

use MasterStudy\Lms\Http\Serializers\AdminCourseCategorySerializer;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\AdminCourseCategoryRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class GetCourseCategoriesListController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_query_params(),
			array(
				'page'     => 'nullable|integer',
				'per_page' => 'nullable|integer',
				'search'   => 'nullable|string',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$validated = $validator->get_validated();

		$repo   = new AdminCourseCategoryRepository();
		$result = $repo->get_list(
			array(
				'page'     => $validated['page'] ?? 1,
				'per_page' => $validated['per_page'] ?? 10,
				'search'   => $validated['search'] ?? '',
			)
		);

		return new WP_REST_Response(
			array(
				'categories' => ( new AdminCourseCategorySerializer() )->collectionToArray( $result['items'] ),
				'total'      => $result['total'],
				'pages'      => $result['pages'],
			)
		);
	}
}
