<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

/**
 * Admin React app settings routes.
 */
$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
		),
	),
	function ( Router $router ) {
		$router->get(
			'/admin-app/settings/{app_slug}',
			\MasterStudy\Lms\Http\Controllers\AdminReactApp\GetSettingsController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\AdminReactApp\GetSettings::class
		);
	}
);
