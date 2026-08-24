<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Order;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class UpdateOrder extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'status' => array(
				'type'        => 'string',
				'description' => 'Order status',
				'required'    => true,
			),
			'note'   => array(
				'type'        => 'string',
				'description' => 'Order note',
				'required'    => false,
			),
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
		return 'Update order by ID';
	}

	public function get_description(): string {
		return 'Update order status and note based on ID.';
	}
}
