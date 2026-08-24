<?php

namespace MasterStudy\Lms\Http\Controllers\Question;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\QuestionCategoryRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class BulkDeleteCategoriesController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'ids' => 'required',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$ids  = array_map( 'intval', (array) $request->get_json_params()['ids'] );
		$repo = new QuestionCategoryRepository();

		foreach ( $ids as $id ) {
			$repo->delete( $id );
		}

		return new \WP_REST_Response( array( 'success' => true ) );
	}
}
