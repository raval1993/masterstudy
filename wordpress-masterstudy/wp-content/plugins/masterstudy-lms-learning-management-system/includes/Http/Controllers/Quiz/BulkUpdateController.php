<?php

namespace MasterStudy\Lms\Http\Controllers\Quiz;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\QuizAdminRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class BulkUpdateController {
	private const MAX_BULK_QUIZZES = 100;

	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'action'  => 'required|string|contains_list,publish;draft',
				'quizzes' => 'required|array',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$params  = $validator->get_validated();
		$action  = (string) $params['action'];
		$quizzes = (array) $params['quizzes'];

		if ( count( $quizzes ) > self::MAX_BULK_QUIZZES ) {
			return WpResponseFactory::bad_request(
				sprintf(
					/* translators: %d: maximum number of quizzes allowed in one bulk request. */
					esc_html__( 'Too many quizzes in one request. Maximum allowed is %d.', 'masterstudy-lms-learning-management-system' ),
					self::MAX_BULK_QUIZZES
				)
			);
		}

		try {
			( new QuizAdminRepository() )->bulk_update_status( $quizzes, $action );
		} catch ( \RuntimeException $e ) {
			if ( 403 === $e->getCode() ) {
				return WpResponseFactory::forbidden();
			}

			if ( 400 === $e->getCode() ) {
				return WpResponseFactory::bad_request( $e->getMessage() );
			}

			return WpResponseFactory::error( $e->getMessage() );
		}

		return WpResponseFactory::ok_with_data(
			array(
				'success' => true,
			)
		);
	}
}
