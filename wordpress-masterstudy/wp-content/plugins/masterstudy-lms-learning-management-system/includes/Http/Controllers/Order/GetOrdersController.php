<?php

namespace MasterStudy\Lms\Http\Controllers\Order;

use MasterStudy\Lms\Enums\OrderStatus;
use MasterStudy\Lms\Http\Serializers\OrderListSerializer;
use MasterStudy\Lms\Http\Serializers\OrderSerializer;
use WP_REST_Request;
use WP_REST_Response;
use MasterStudy\Lms\Repositories\OrderRepository;
use MasterStudy\Lms\Validation\Validator;
use MasterStudy\Lms\Http\WpResponseFactory;

final class GetOrdersController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'per_page'   => 'nullable|integer',
				'page'       => 'nullable|integer',
				'search'     => 'nullable|string',
				'status'     => 'nullable|string|contains_list,' . implode( ';', OrderStatus::cases() ),
				'date_range' => 'nullable|string',
				'sort'       => 'nullable|string',
				'coupon_id'  => 'nullable|integer',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$data           = ( new OrderRepository() )->get_all_orders( $validator->get_validated() );
		$data['orders'] = ( new OrderListSerializer() )->collectionToArray( $data['orders'] );

		return new WP_REST_Response( $data );
	}
}
