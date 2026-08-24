<?php

namespace MasterStudy\Lms\Http\Controllers\AdminInstructor;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\AdminInstructorRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class UpdateInstructorBanController {
	public function __invoke( int $user_id, WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_json_params(),
			array(
				'banned' => 'required|boolean',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$banned = rest_sanitize_boolean( $validator->get_validated()['banned'] );
		$saved  = ( new AdminInstructorRepository() )->update_ban( $user_id, $banned );

		if ( ! $saved ) {
			return WpResponseFactory::not_found();
		}

		return WpResponseFactory::ok_with_data(
			array(
				'banned' => $banned,
			)
		);
	}
}
