<?php

namespace MasterStudy\Lms\Http\Controllers\Review;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\AdminReviewRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class BulkDeleteReviewsController {
	private const MAX_BULK_SIZE = 100;

	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'ids' => 'required|array',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$data       = $validator->get_validated();
		$ids        = array_map( 'absint', $data['ids'] );
		$ids        = array_filter( $ids );
		$ids        = array_slice( $ids, 0, self::MAX_BULK_SIZE );
		$repository = new AdminReviewRepository();

		foreach ( $ids as $id ) {
			$repository->delete_review( $id );
		}

		return WpResponseFactory::ok();
	}
}
