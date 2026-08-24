<?php

namespace MasterStudy\Lms\Http\Controllers\AdminInstructor;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\AdminInstructorRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class UpdateInstructorStatusController {
	public function __invoke( int $user_id, WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_json_params(),
			array(
				'status'  => 'required|string|contains_list,approved;rejected',
				'message' => 'nullable|string',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$data   = $validator->get_validated();
		$result = ( new AdminInstructorRepository() )->update_status(
			$user_id,
			(string) $data['status'],
			(string) ( $data['message'] ?? '' )
		);

		if ( null === $result ) {
			return WpResponseFactory::not_found();
		}

		return WpResponseFactory::ok_with_data( $result );
	}
}
