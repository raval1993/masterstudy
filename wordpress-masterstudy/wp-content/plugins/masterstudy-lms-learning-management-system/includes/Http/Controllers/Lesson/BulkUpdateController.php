<?php

namespace MasterStudy\Lms\Http\Controllers\Lesson;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\LessonAdminRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class BulkUpdateController {
	private const MAX_BULK_LESSONS = 100;

	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'action'  => 'required|string|contains_list,publish;draft',
				'lessons' => 'required|array',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$params  = $validator->get_validated();
		$action  = (string) $params['action'];
		$lessons = (array) $params['lessons'];

		if ( count( $lessons ) > self::MAX_BULK_LESSONS ) {
			return WpResponseFactory::bad_request(
				sprintf(
					/* translators: %d: maximum number of lessons allowed in one bulk request. */
					esc_html__( 'Too many lessons in one request. Maximum allowed is %d.', 'masterstudy-lms-learning-management-system' ),
					self::MAX_BULK_LESSONS
				)
			);
		}

		try {
			( new LessonAdminRepository() )->bulk_update_status( $lessons, $action );
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
