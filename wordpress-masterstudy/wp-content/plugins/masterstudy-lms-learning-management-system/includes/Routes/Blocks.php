<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

/**
 * Gutenberg Blocks routes
 */
$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Routing\Middleware\PostGuard::class,
		),
		'prefix'     => '/blocks',
	),
	function ( \MasterStudy\Lms\Routing\Router $router ) {
		$router->get(
			'/course-levels',
			\MasterStudy\Lms\Http\Controllers\Blocks\Course\GetLevelsController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Blocks\Course\GetLevels::class,
		);
		$router->get(
			'/course-statuses',
			\MasterStudy\Lms\Http\Controllers\Blocks\Course\GetStatusesController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Blocks\Course\GetStatuses::class,
		);
		$router->get(
			'/settings',
			\MasterStudy\Lms\Http\Controllers\Blocks\GetSettingsController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Blocks\GetSettings::class,
		);
	}
);
