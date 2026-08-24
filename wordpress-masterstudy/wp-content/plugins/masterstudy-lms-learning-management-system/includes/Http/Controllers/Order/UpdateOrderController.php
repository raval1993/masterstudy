<?php

namespace MasterStudy\Lms\Http\Controllers\Order;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\OrderRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class UpdateOrderController {
	public function __invoke( int $order_id, WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'status' => 'required|string',
				'note'   => 'nullable|string',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$success = ( new OrderRepository() )->update_order( $order_id, $validator->get_validated() );

		return new WP_REST_Response(
			array(
				'success' => $success,
			)
		);
	}
}
