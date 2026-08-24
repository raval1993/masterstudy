<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

/**
 * Lesson admin routes (require Authentication + Administrator).
 */
$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Routing\Middleware\AdminOrInstructor::class,
		),
	),
	function ( Router $router ) {
		$router->get(
			'/admin-lessons',
			\MasterStudy\Lms\Http\Controllers\Lesson\GetLessonsController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Lesson\GetAdminLessons::class
		);

		$router->patch(
			'/admin-lessons/{lesson_id}/status',
			\MasterStudy\Lms\Http\Controllers\Lesson\UpdateStatusController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Lesson\UpdateStatus::class
		);

		$router->post(
			'/admin-lessons/bulk-update',
			\MasterStudy\Lms\Http\Controllers\Lesson\BulkUpdateController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Lesson\BulkUpdateLessons::class
		);

		$router->post(
			'/admin-lessons/bulk-delete',
			\MasterStudy\Lms\Http\Controllers\Lesson\BulkDeleteController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Lesson\BulkDeleteLessons::class
		);
	}
);
