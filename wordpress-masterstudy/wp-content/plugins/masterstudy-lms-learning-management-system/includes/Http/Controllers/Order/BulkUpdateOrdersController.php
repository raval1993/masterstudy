<?php

namespace MasterStudy\Lms\Http\Controllers\Order;

use MasterStudy\Lms\Enums\BulkOrderAction;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\OrderRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class BulkUpdateOrdersController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'action' => 'required|string|contains_list,' . implode( ';', BulkOrderAction::cases() ),
				'orders' => 'required|array',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$params = $validator->get_validated();
		$error  = false;

		if ( BulkOrderAction::DELETE === $params['action'] ) {
			$error = ( new OrderRepository() )->bulk_remove_orders( $params );
		} elseif ( BulkOrderAction::UPDATE_STATUS === $params['action'] ) {
			$error = ( new OrderRepository() )->bulk_update_orders( $params );
		}

		if ( is_array( $error ) ) {
			$id = $error['id'];
			return WpResponseFactory::error( "Unable to perform action on $id" );
		}

		return new WP_REST_Response(
			array(
				'success' => true,
			)
		);
	}
}
