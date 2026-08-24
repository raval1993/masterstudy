<?php

namespace MasterStudy\Lms\Http\Controllers\Lesson;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\LessonAdminRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class UpdateAuthorController {
	public function __invoke( int $lesson_id, WP_REST_Request $request ): WP_REST_Response {
		$payload = $request->get_json_params();

		$validator = new Validator(
			is_array( $payload ) ? $payload : $request->get_params(),
			array(
				'author_id' => 'required|integer',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$author_id = (int) $validator->get_validated()['author_id'];

		try {
			( new LessonAdminRepository() )->update_author( $lesson_id, $author_id );
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
				'success'   => true,
				'id'        => $lesson_id,
				'author_id' => $author_id,
			)
		);
	}
}
