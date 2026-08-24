<?php

namespace MasterStudy\Lms\Http\Controllers\Review;

use MasterStudy\Lms\Http\Serializers\AdminReviewSerializer;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\AdminReviewRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class GetReviewsController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'per_page'   => 'nullable|integer',
				'page'       => 'nullable|integer',
				'search'     => 'nullable|string',
				'status'     => 'nullable|string|contains_list,any;publish;pending;draft',
				'sort'       => 'nullable|string',
				'date_range' => 'nullable|string',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$data            = ( new AdminReviewRepository() )->get_list( $validator->get_validated() );
		$data['reviews'] = ( new AdminReviewSerializer() )->collectionToArray( $data['posts'] );
		unset( $data['posts'] );

		return WpResponseFactory::ok_with_data( $data );
	}
}
