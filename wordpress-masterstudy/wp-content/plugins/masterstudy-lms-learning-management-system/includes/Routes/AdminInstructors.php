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
			'/admin/instructors',
			\MasterStudy\Lms\Http\Controllers\AdminInstructor\GetInstructorsController::class
		);

		$router->post(
			'/admin/instructors',
			\MasterStudy\Lms\Http\Controllers\AdminInstructor\CreateInstructorController::class
		);

		$router->post(
			'/admin/instructors/{user_id}/status',
			\MasterStudy\Lms\Http\Controllers\AdminInstructor\UpdateInstructorStatusController::class
		);

		$router->put(
			'/admin/instructors/{user_id}/ban',
			\MasterStudy\Lms\Http\Controllers\AdminInstructor\UpdateInstructorBanController::class
		);

		$router->put(
			'/admin/instructors/{user_id}/ai-access',
			\MasterStudy\Lms\Http\Controllers\AdminInstructor\UpdateInstructorAiAccessController::class
		);

		$router->put(
			'/admin/instructors/ai-access',
			\MasterStudy\Lms\Http\Controllers\AdminInstructor\UpdateInstructorsAiAccessController::class
		);
	}
);
