<?php

namespace MasterStudy\Lms\Http\Controllers\Quiz;

use MasterStudy\Lms\Repositories\EnrolledQuizzesRepository;
use WP_REST_Request;
use WP_REST_Response;
use MasterStudy\Lms\Validation\Validator;
use MasterStudy\Lms\Http\WpResponseFactory;

final class GetEnrolledQuizzesController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				's'            => 'string',
				'per_page'     => 'nullable|integer',
				'current_page' => 'nullable|integer',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		return new WP_REST_Response( ( new EnrolledQuizzesRepository() )->get_quizzes( $validator->get_validated() ) );
	}
}
