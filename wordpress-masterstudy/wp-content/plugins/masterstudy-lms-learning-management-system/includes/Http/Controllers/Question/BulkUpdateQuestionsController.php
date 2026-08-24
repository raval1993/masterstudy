<?php

namespace MasterStudy\Lms\Http\Controllers\Question;

use MasterStudy\Lms\Enums\BulkQuestionAction;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\QuestionRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class BulkUpdateQuestionsController {
	private const MAX_BULK_QUESTIONS = 100;

	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'action'    => 'required|string|contains_list,' . implode( ';', BulkQuestionAction::cases() ),
				'questions' => 'required|array',
				'status'    => 'nullable|string|contains_list,publish;pending;draft;trash;private',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$params    = $validator->get_validated();
		$action    = (string) $params['action'];
		$questions = (array) $params['questions'];
		$status    = $params['status'] ?? null;
		$statuses  = array( 'publish', 'pending', 'draft', 'trash', 'private' );

		if ( count( $questions ) > self::MAX_BULK_QUESTIONS ) {
			return WpResponseFactory::bad_request(
				sprintf(
					/* translators: %d: maximum number of questions allowed in one bulk request. */
					esc_html__( 'Too many questions in one request. Maximum allowed is %d.', 'masterstudy-lms-learning-management-system' ),
					self::MAX_BULK_QUESTIONS
				)
			);
		}

		if (
			BulkQuestionAction::UPDATE_STATUS === $action &&
			empty( $status ) &&
			! empty( $questions ) &&
			is_array( $questions[0] ) &&
			! empty( $questions[0]['status'] )
		) {
			$status = (string) $questions[0]['status'];
		}

		if (
			BulkQuestionAction::UPDATE_STATUS === $action &&
			! empty( $status ) &&
			! in_array( (string) $status, $statuses, true )
		) {
			return WpResponseFactory::bad_request(
				esc_html__( 'Invalid status provided for update_status action.', 'masterstudy-lms-learning-management-system' )
			);
		}

		if ( BulkQuestionAction::UPDATE_STATUS === $action && empty( $status ) ) {
			return WpResponseFactory::bad_request(
				esc_html__( 'Status is required for update_status action.', 'masterstudy-lms-learning-management-system' )
			);
		}

		try {
			( new QuestionRepository() )->bulk_update( $action, $questions, $status );
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
			)
		);
	}
}
