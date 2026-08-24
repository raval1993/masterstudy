<?php

namespace MasterStudy\Lms\Http\Controllers\Review;

use MasterStudy\Lms\Repositories\ReviewRepository;
use MasterStudy\Lms\Http\Serializers\ReviewsSerializer;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

class GetInstructorReviewsController {
	public function __invoke( WP_REST_Request $request ): \WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'page'   => 'required|integer',
				'pp'     => 'required|integer',
				'user'   => 'required|integer',
				'rating' => 'nullable|integer',
				'course' => 'nullable|string',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$reviews = ( new ReviewRepository() )->get_instructor_reviews( $validator->get_validated() );

		return new WP_REST_Response(
			( new ReviewsSerializer() )->toArray( $reviews )
		);
	}
}
