<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

/**
 * Quiz routes
 */
$router->post(
	'/quizzes',
	\MasterStudy\Lms\Http\Controllers\Quiz\CreateController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Quiz\Create::class
);

$router->get(
	'/quizzes/{quiz_id}',
	\MasterStudy\Lms\Http\Controllers\Quiz\GetController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Quiz\Get::class
);

$router->put(
	'/quizzes/{quiz_id}',
	\MasterStudy\Lms\Http\Controllers\Quiz\UpdateController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Quiz\Update::class
);

$router->delete(
	'/quizzes/{quiz_id}',
	\MasterStudy\Lms\Http\Controllers\Quiz\DeleteController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Quiz\Delete::class
);

$router->put(
	'/quizzes/{quiz_id}/questions',
	\MasterStudy\Lms\Http\Controllers\Quiz\UpdateQuestionsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Quiz\UpdateQuestions::class
);
