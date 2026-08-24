<?php

namespace MasterStudy\Lms\Http\Controllers\Review;

use MasterStudy\Lms\Http\Serializers\AdminReviewSerializer;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\AdminReviewRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class UpdateReviewController {
	public function __invoke( int $review_id, WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'title'      => 'nullable|string',
				'content'    => 'nullable|string',
				'course_id'  => 'nullable|integer',
				'student_id' => 'nullable|integer',
				'mark'       => 'nullable|integer',
				'status'     => 'nullable|string|contains_list,publish;pending;draft',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$repository = new AdminReviewRepository();
		$updated    = $repository->update_review( $review_id, $validator->get_validated() );

		if ( ! $updated ) {
			return WpResponseFactory::not_found();
		}

		$post = $repository->get_review( $review_id );

		return WpResponseFactory::ok_with_data(
			( new AdminReviewSerializer() )->toDetailArray( $post )
		);
	}
}
