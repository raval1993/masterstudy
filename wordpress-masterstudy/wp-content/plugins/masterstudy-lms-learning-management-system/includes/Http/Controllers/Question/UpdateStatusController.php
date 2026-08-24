<?php

namespace MasterStudy\Lms\Http\Controllers\Question;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\QuestionRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class UpdateStatusController {
	public function __invoke( int $question_id, WP_REST_Request $request ): WP_REST_Response {
		$repository = new QuestionRepository();
		if ( ! $repository->exists( $question_id ) ) {
			return WpResponseFactory::not_found();
		}

		if ( ! current_user_can( 'edit_post', $question_id ) ) {
			return WpResponseFactory::forbidden();
		}

		$json_params = $request->get_json_params();

		$validator = new Validator(
			! empty( $json_params ) ? $json_params : $request->get_params(),
			array(
				'status' => 'required|string|contains_list,publish;pending;draft;trash;private',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$status = (string) $validator->get_validated()['status'];

		if ( 'trash' === $status ) {
			if ( ! current_user_can( 'delete_post', $question_id ) ) {
				return WpResponseFactory::forbidden();
			}
		}

		try {
			$repository->update_status( $question_id, $status );
		} catch ( \RuntimeException $e ) {
			if ( 400 === $e->getCode() ) {
				return WpResponseFactory::bad_request( $e->getMessage() );
			}

			return WpResponseFactory::error( $e->getMessage() );
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'id'      => $question_id,
				'status'  => $status,
			)
		);
	}
}
