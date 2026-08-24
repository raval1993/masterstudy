<?php

namespace MasterStudy\Lms\Http\Controllers\Review;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\AdminReviewRepository;
use WP_REST_Response;

final class DeleteReviewController {
	public function __invoke( int $review_id ): WP_REST_Response {
		$deleted = ( new AdminReviewRepository() )->delete_review( $review_id );

		if ( ! $deleted ) {
			return WpResponseFactory::not_found();
		}

		return WpResponseFactory::ok();
	}
}
