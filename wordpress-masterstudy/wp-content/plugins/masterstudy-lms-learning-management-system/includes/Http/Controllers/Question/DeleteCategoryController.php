<?php

namespace MasterStudy\Lms\Http\Controllers\Question;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\QuestionCategoryRepository;
use WP_REST_Request;
use WP_REST_Response;

final class DeleteCategoryController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$id     = (int) $request->get_param( 'category_id' );
		$result = ( new QuestionCategoryRepository() )->delete( $id );

		if ( is_wp_error( $result ) ) {
			return WpResponseFactory::error( $result->get_error_message() );
		}

		return new \WP_REST_Response( array( 'success' => true ) );
	}
}
