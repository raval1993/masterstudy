<?php

namespace MasterStudy\Lms\Http\Controllers\Review;

use MasterStudy\Lms\Http\Serializers\AdminReviewSerializer;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\AdminReviewRepository;
use WP_REST_Response;

final class GetReviewController {
	public function __invoke( int $review_id ): WP_REST_Response {
		$post = ( new AdminReviewRepository() )->get_review( $review_id );

		if ( ! $post ) {
			return WpResponseFactory::not_found();
		}

		return WpResponseFactory::ok_with_data(
			( new AdminReviewSerializer() )->toDetailArray( $post )
		);
	}
}
