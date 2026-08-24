<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

/**
 * Quiz admin routes (require Authentication + Administrator).
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
			'/admin-quizzes',
			\MasterStudy\Lms\Http\Controllers\Quiz\GetQuizzesController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Quiz\GetAdminQuizzes::class
		);

		$router->patch(
			'/admin-quizzes/{quiz_id}/status',
			\MasterStudy\Lms\Http\Controllers\Quiz\UpdateStatusController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Quiz\UpdateStatus::class
		);

		$router->post(
			'/admin-quizzes/bulk-update',
			\MasterStudy\Lms\Http\Controllers\Quiz\BulkUpdateController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Quiz\BulkUpdateQuizzes::class
		);

		$router->post(
			'/admin-quizzes/bulk-delete',
			\MasterStudy\Lms\Http\Controllers\Quiz\BulkDeleteController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Quiz\BulkDeleteQuizzes::class
		);
	}
);
