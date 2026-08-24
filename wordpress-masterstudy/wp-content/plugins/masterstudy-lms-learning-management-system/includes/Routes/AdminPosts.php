<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Routing\Middleware\AdminOrInstructor::class,
		),
	),
	function ( Router $router ) {
		$router->get(
			'/admin-posts/authors',
			\MasterStudy\Lms\Http\Controllers\AdminPost\GetAuthorsController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\AdminPost\GetAuthors::class
		);
	}
);

$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Routing\Middleware\Administrator::class,
		),
	),
	function ( Router $router ) {
		$router->patch(
			'/admin-posts/{post_id}/author',
			\MasterStudy\Lms\Http\Controllers\AdminPost\UpdateAuthorController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\AdminPost\UpdateAuthor::class
		);
	}
);
