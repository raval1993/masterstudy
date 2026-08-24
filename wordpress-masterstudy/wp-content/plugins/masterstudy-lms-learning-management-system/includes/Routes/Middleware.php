<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

/**
 * Middlewares for all routes
 */
$router->middleware(
	apply_filters(
		'masterstudy_lms_routes_middleware',
		array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Routing\Middleware\Instructor::class,
			\MasterStudy\Lms\Routing\Middleware\PostGuard::class,
		)
	)
);
