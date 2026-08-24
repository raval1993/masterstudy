<?php

namespace MasterStudy\Lms\Http\Controllers\Quiz;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\QuizAdminRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class UpdateStatusController {
	public function __invoke( int $quiz_id, WP_REST_Request $request ): WP_REST_Response {
		$json_params = $request->get_json_params();

		$validator = new Validator(
			! empty( $json_params ) ? $json_params : $request->get_params(),
			array(
				'status' => 'required|string|contains_list,publish;draft',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$status = (string) $validator->get_validated()['status'];

		try {
			( new QuizAdminRepository() )->update_status( $quiz_id, $status );
		} catch ( \RuntimeException $e ) {
			if ( 403 === $e->getCode() ) {
				return WpResponseFactory::forbidden();
			}

			if ( 400 === $e->getCode() ) {
				return WpResponseFactory::bad_request( $e->getMessage() );
			}

			return WpResponseFactory::error( $e->getMessage() );
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'id'      => $quiz_id,
				'status'  => $status,
			)
		);
	}
}
