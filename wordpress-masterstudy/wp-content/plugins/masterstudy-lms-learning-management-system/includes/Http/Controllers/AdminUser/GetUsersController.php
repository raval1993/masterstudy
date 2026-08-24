<?php

namespace MasterStudy\Lms\Http\Controllers\AdminUser;

use MasterStudy\Lms\Http\Serializers\AdminUserSerializer;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\AdminUserRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class GetUsersController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_query_params(),
			array(
				'page'           => 'nullable|integer',
				'per_page'       => 'nullable|integer',
				'search'         => 'nullable|string',
				'exclude_emails' => 'nullable|string',
				'exclude_roles'  => 'nullable|string',
				'include_roles'  => 'nullable|string',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$result = ( new AdminUserRepository() )->search_users( $validator->get_validated() );

		return WpResponseFactory::ok_with_data(
			array(
				'users' => ( new AdminUserSerializer() )->collectionToArray( $result['items'] ),
				'total' => $result['total'],
				'pages' => $result['pages'],
			)
		);
	}
}
