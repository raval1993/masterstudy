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
			'/admin/users/search',
			\MasterStudy\Lms\Http\Controllers\AdminUser\GetUsersController::class
		);
	}
);
