<?php


namespace MasterStudy\Lms\Routing\Swagger\Routes\Order;

use MasterStudy\Lms\Routing\Swagger\Fields\ListOrder;
use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetOrders extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'per_page'   => array(
				'type'        => 'integer',
				'description' => 'Posts per page. Default is 10.',
			),
			'page'       => array(
				'type'        => 'integer',
				'description' => 'Current page. Default is 1.',
			),
			'search'     => array(
				'type'        => 'string',
				'description' => 'Order ID or student name',
			),
			'status'     => array(
				'type'        => 'string',
				'description' => 'Order status',
			),
			'sort'       => array(
				'type'        => 'string',
				'description' => 'Sort courses by',
				'enum'        => array(
					'id',
					'status',
				),
			),
			'date_range' => array(
				'type'        => 'string',
				'description' => 'Date range. Comma-separated.',
			),
		);
	}

	public function response(): array {
		return array(
			'orders'       => ListOrder::as_array(),
			'total_orders' => array(
				'type'        => 'integer',
				'description' => 'Total number of orders.',
			),
			'current_page' => array(
				'type'        => 'integer',
				'description' => 'Current page.',
			),
			'pages'        => array(
				'type'        => 'integer',
				'description' => 'Total number of pages.',
			),
		);
	}

	public function get_summary(): string {
		return 'Get Orders';
	}

	public function get_description(): string {
		return 'Returns a list of orders based on the provided parameters.';
	}
}
