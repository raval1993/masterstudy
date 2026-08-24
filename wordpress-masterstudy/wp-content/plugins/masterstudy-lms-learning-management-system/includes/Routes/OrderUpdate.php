<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Routing\Middleware\Instructor::class,
		),
	),
	function ( \MasterStudy\Lms\Routing\Router $router ) {
		$router->put(
			'/orders/{order_id}',
			\MasterStudy\Lms\Http\Controllers\Order\UpdateOrderController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Order\UpdateOrder::class
		);
	}
);
