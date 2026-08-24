<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

/**
 * Question routes
 */
$router->get(
	'/questions/categories',
	\MasterStudy\Lms\Http\Controllers\Question\GetCategoriesController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Question\GetCategories::class
);

$router->get(
	'/questions/categories/list',
	\MasterStudy\Lms\Http\Controllers\Question\GetCategoriesListController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Question\GetCategoriesList::class
);

$router->post(
	'/questions/category',
	\MasterStudy\Lms\Http\Controllers\Question\CreateCategoryController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Question\CreateCategory::class
);

$router->put(
	'/questions/categories/{category_id}',
	\MasterStudy\Lms\Http\Controllers\Question\UpdateCategoryController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Question\UpdateCategory::class
);

$router->delete(
	'/questions/categories/{category_id}',
	\MasterStudy\Lms\Http\Controllers\Question\DeleteCategoryController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Question\DeleteCategory::class
);

$router->post(
	'/questions/categories/bulk-delete',
	\MasterStudy\Lms\Http\Controllers\Question\BulkDeleteCategoriesController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Question\BulkDeleteCategories::class
);

$router->post(
	'/questions',
	\MasterStudy\Lms\Http\Controllers\Question\CreateController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Question\Create::class
);

$router->post(
	'/questions/bulk',
	\MasterStudy\Lms\Http\Controllers\Question\BulkCreateController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Question\BulkCreate::class
);

$router->get(
	'/questions/{question_id}',
	\MasterStudy\Lms\Http\Controllers\Question\GetController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Question\Get::class
);

$router->put(
	'/questions/{question_id}',
	\MasterStudy\Lms\Http\Controllers\Question\UpdateController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Question\Update::class
);

$router->delete(
	'/questions/{question_id}',
	\MasterStudy\Lms\Http\Controllers\Question\DeleteController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Question\Delete::class
);

/**
 * Question admin routes (require Authentication + Administrator)
 */
$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Routing\Middleware\AdminOrInstructor::class,
		),
	),
	function ( \MasterStudy\Lms\Routing\Router $router ) {
		$router->get(
			'/all-questions',
			\MasterStudy\Lms\Http\Controllers\Question\GetQuestionsController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Question\GetQuestions::class
		);
		$router->patch(
			'/questions/{question_id}/status',
			\MasterStudy\Lms\Http\Controllers\Question\UpdateStatusController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Question\UpdateStatus::class
		);
		$router->post(
			'/questions-bulk-update',
			\MasterStudy\Lms\Http\Controllers\Question\BulkUpdateQuestionsController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Question\BulkUpdateQuestions::class
		);
	}
);
