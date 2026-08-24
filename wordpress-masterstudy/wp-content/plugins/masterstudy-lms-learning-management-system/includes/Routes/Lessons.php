<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

/**
 * Lesson routes
 */
$router->post(
	'/lessons',
	\MasterStudy\Lms\Http\Controllers\Lesson\CreateController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Lesson\Create::class
);

$router->put(
	'/lessons/{lesson_id}',
	\MasterStudy\Lms\Http\Controllers\Lesson\UpdateController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Lesson\Update::class
);

$router->get(
	'/lessons/{lesson_id}',
	\MasterStudy\Lms\Http\Controllers\Lesson\GetController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Lesson\Get::class
);
