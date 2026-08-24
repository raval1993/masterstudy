<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

/**
 * Media routes
 */
$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Routing\Middleware\PostGuard::class,
		),
	),
	function ( \MasterStudy\Lms\Routing\Router $router ) {
		$router->post(
			'/media',
			\MasterStudy\Lms\Http\Controllers\Media\UploadController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Media\Upload::class
		);

		$router->delete(
			'/media/{media_id}',
			\MasterStudy\Lms\Http\Controllers\Media\DeleteController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Media\Delete::class
		);

		$router->post(
			'/media/from-url',
			\MasterStudy\Lms\Http\Controllers\Media\UploadFromUrlController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Media\UploadFromUrl::class,
		);
	}
);
