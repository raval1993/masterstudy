<?php
namespace MasterStudy\Lms\Http\Controllers\Quiz;

use MasterStudy\Lms\Repositories\EnrolledQuizzesRepository;
use WP_REST_Request;
use WP_REST_Response;
use MasterStudy\Lms\Validation\Validator;
use MasterStudy\Lms\Http\WpResponseFactory;

final class GetQuizAttemptController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'attempt_id'   => 'nullable|integer',
				'course_id'    => 'nullable|integer',
				'quiz_id'      => 'nullable|integer',
				'dark_mode'    => 'nullable|boolean',
				'show_answers' => 'nullable|boolean',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		return new WP_REST_Response( ( new EnrolledQuizzesRepository() )->get_single_attempt( $validator->get_validated() ) );
	}
}
