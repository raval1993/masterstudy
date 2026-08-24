<?php

namespace MasterStudy\Lms\Http\Controllers\Order;

use MasterStudy\Lms\Http\Serializers\OrderListSerializer;
use WP_REST_Request;
use WP_REST_Response;
use MasterStudy\Lms\Repositories\OrderRepository;
use MasterStudy\Lms\Validation\Validator;
use MasterStudy\Lms\Http\WpResponseFactory;

final class GetUserOrdersController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'per_page'     => 'nullable|integer',
				'current_page' => 'nullable|integer',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$data = ( new OrderRepository() )->get_all_user_orders( $validator->get_validated() );

		return new WP_REST_Response( $data );
	}
}
