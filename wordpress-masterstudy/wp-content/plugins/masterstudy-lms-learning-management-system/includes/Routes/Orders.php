<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

/**
 * Order routes
 */
$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Routing\Middleware\Administrator::class,
		),
	),
	function ( \MasterStudy\Lms\Routing\Router $router ) {
		$router->get(
			'/all-orders',
			\MasterStudy\Lms\Http\Controllers\Order\GetOrdersController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Order\GetOrders::class
		);
		$router->get(
			'/orders/{order_id}',
			\MasterStudy\Lms\Http\Controllers\Order\GetOrderController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Order\GetOrder::class
		);
		$router->post(
			'/orders-bulk-update',
			\MasterStudy\Lms\Http\Controllers\Order\BulkUpdateOrdersController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Order\BulkUpdateOrder::class
		);
	}
);
