<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Routing\Middleware\Administrator::class,
		),
	),
	function ( Router $router ) {
		$router->get(
			'/admin/statistics/orders',
			\MasterStudy\Lms\Http\Controllers\Statistics\GetStatisticsOrdersController::class
		);

		$router->get(
			'/admin/statistics/summary',
			\MasterStudy\Lms\Http\Controllers\Statistics\GetStatisticsSummaryController::class
		);

		$router->post(
			'/admin/statistics/payouts',
			\MasterStudy\Lms\Http\Controllers\Statistics\CreateStatisticsPayoutController::class
		);
	}
);
