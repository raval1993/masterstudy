<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */
/**
 * Course Template Routes
 */
$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Routing\Middleware\Instructor::class,
		),
		'prefix'     => '/course-templates',
	),
	function ( \MasterStudy\Lms\Routing\Router $router ) {
		$router->post(
			'/modify-template',
			\MasterStudy\Lms\Http\Controllers\Course\CourseTemplate\ModifyCourseTemplateController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Course\CourseTemplate\Modify::class
		);
		$router->put(
			'/update-template',
			\MasterStudy\Lms\Http\Controllers\Course\CourseTemplate\UpdateCourseTemplateController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Course\CourseTemplate\Update::class
		);
		$router->post(
			'/duplicate-template',
			\MasterStudy\Lms\Http\Controllers\Course\CourseTemplate\DuplicateCourseTemplateController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Course\CourseTemplate\Duplicate::class
		);
		$router->post(
			'/page-to-course-template',
			\MasterStudy\Lms\Http\Controllers\Course\CourseTemplate\SavePageToCourseTemplateController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Course\CourseTemplate\SavePage::class
		);
		$router->post(
			'/assign-category-template',
			\MasterStudy\Lms\Http\Controllers\Course\CourseTemplate\AssignCategoryToTemplateController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Course\CourseTemplate\AssignCategory::class
		);
		$router->delete(
			'/delete-template/{template_id}',
			\MasterStudy\Lms\Http\Controllers\Course\CourseTemplate\DeleteCourseTemplateController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Course\CourseTemplate\Delete::class
		);
		$router->post(
			'/create-template',
			\MasterStudy\Lms\Http\Controllers\Course\CourseTemplate\CreateCourseTemplateController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Course\CourseTemplate\Create::class
		);
	}
);
