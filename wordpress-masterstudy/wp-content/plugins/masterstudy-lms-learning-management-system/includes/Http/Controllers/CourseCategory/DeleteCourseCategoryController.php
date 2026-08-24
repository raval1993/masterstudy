<?php

namespace MasterStudy\Lms\Http\Controllers\CourseCategory;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\AdminCourseCategoryRepository;
use WP_REST_Request;
use WP_REST_Response;

final class DeleteCourseCategoryController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$id   = (int) $request->get_param( 'category_id' );
		$repo = new AdminCourseCategoryRepository();

		$existing = $repo->get( $id );

		if ( ! $existing || is_wp_error( $existing ) ) {
			return WpResponseFactory::bad_request( 'Category not found.' );
		}

		$result = $repo->delete( $id );

		if ( is_wp_error( $result ) ) {
			return WpResponseFactory::error( $result->get_error_message() );
		}

		return new WP_REST_Response( array( 'success' => true ) );
	}
}
