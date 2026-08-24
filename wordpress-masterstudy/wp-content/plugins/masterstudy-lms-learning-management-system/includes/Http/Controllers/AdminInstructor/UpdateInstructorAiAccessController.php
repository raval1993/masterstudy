<?php

namespace MasterStudy\Lms\Http\Controllers\AdminInstructor;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\AdminInstructorRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class UpdateInstructorAiAccessController {
	public function __invoke( int $user_id, WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_json_params(),
			array(
				'ai_enabled' => 'required|boolean',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$repository = new AdminInstructorRepository();

		if ( ! $repository->is_ai_lab_available() ) {
			return WpResponseFactory::forbidden();
		}

		$ai_enabled = rest_sanitize_boolean( $validator->get_validated()['ai_enabled'] );
		$saved      = $repository->update_ai_access( $user_id, $ai_enabled );

		if ( ! $saved ) {
			return WpResponseFactory::not_found();
		}

		return WpResponseFactory::ok_with_data(
			array(
				'ai_enabled' => $ai_enabled,
			)
		);
	}
}
