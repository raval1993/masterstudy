<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

/**
 * Course Builder routes
 */
$router->get(
	'/healthcheck',
	\MasterStudy\Lms\Http\Controllers\HealthCheckController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\HealthCheck::class,
);

$router->get(
	'/course-builder/settings',
	\MasterStudy\Lms\Http\Controllers\CourseBuilder\GetSettingsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\CourseBuilder\GetSettings::class,
);

$router->put(
	'/course-builder/custom-fields/{post_id}',
	\MasterStudy\Lms\Http\Controllers\CourseBuilder\UpdateCustomFieldsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\CourseBuilder\UpdateCustomFields::class,
);
