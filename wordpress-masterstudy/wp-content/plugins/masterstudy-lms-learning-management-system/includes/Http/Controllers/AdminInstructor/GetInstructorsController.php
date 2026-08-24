<?php

namespace MasterStudy\Lms\Http\Controllers\AdminInstructor;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\AdminInstructorRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class GetInstructorsController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_query_params(),
			array(
				'page'     => 'nullable|integer',
				'per_page' => 'nullable|integer',
				'order'    => 'nullable|string|contains_list,asc;desc;ASC;DESC',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		return WpResponseFactory::ok_with_data(
			( new AdminInstructorRepository() )->get_instructors( $validator->get_validated() )
		);
	}
}
