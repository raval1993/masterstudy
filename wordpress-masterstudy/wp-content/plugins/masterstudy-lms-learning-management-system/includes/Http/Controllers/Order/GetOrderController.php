<?php

namespace MasterStudy\Lms\Http\Controllers\Order;

use MasterStudy\Lms\Http\Serializers\OrderSerializer;
use WP_REST_Response;

final class GetOrderController {
	public function __invoke( int $order_id ): WP_REST_Response {
		$data = \STM_LMS_Order::get_order_info( $order_id );

		return new WP_REST_Response( ( new OrderSerializer() )->toArray( $data ) );
	}
}
