<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Order;

use MasterStudy\Lms\Routing\Swagger\Fields\ListOrder;
use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class BulkUpdateOrder extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'action' => array(
				'type'        => 'string',
				'description' => 'Action to perform on orders',
				'required'    => true,
				'enum'        => array( 'delete' ),
			),
			'orders' => ListOrder::as_array(),
		);
	}

	public function response(): array {
		return array(
			'success' => array(
				'type' => 'boolean',
			),
		);
	}

	public function get_summary(): string {
		return 'Perform bulk update of orders';
	}

	public function get_description(): string {
		return 'Perform bulk update of orders by action.';
	}
}
