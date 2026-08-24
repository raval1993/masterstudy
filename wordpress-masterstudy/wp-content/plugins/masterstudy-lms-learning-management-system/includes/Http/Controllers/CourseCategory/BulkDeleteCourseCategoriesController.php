<?php

namespace MasterStudy\Lms\Http\Controllers\CourseCategory;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\AdminCourseCategoryRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class BulkDeleteCourseCategoriesController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$data = $request->get_json_params();

		$validator = new Validator(
			$data,
			array(
				'ids' => 'required',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$ids  = array_map( 'intval', (array) ( $data['ids'] ?? array() ) );
		$repo = new AdminCourseCategoryRepository();

		foreach ( $ids as $id ) {
			$repo->delete( $id );
		}

		return new WP_REST_Response( array( 'success' => true ) );
	}
}
