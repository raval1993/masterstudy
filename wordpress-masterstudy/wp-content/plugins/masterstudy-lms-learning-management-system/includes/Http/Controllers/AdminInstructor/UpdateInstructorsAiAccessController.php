<?php

namespace MasterStudy\Lms\Http\Controllers\AdminInstructor;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\AdminInstructorRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class UpdateInstructorsAiAccessController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
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
		$repository->update_all_ai_access( $ai_enabled );

		return WpResponseFactory::ok_with_data(
			array(
				'ai_enabled_for_all' => $repository->is_ai_enabled_for_all(),
			)
		);
	}
}
