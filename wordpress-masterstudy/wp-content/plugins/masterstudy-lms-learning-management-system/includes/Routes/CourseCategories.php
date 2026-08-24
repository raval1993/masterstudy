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
			'/admin/courses/categories',
			\MasterStudy\Lms\Http\Controllers\CourseCategory\GetCourseCategoriesController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\CourseCategory\GetCategories::class
		);

		$router->get(
			'/admin/courses/categories/list',
			\MasterStudy\Lms\Http\Controllers\CourseCategory\GetCourseCategoriesListController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\CourseCategory\GetCategoriesList::class
		);

		$router->post(
			'/admin/courses/category',
			\MasterStudy\Lms\Http\Controllers\CourseCategory\CreateCourseCategoryController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\CourseCategory\CreateCategory::class
		);

		$router->put(
			'/admin/courses/categories/{category_id}',
			\MasterStudy\Lms\Http\Controllers\CourseCategory\UpdateCourseCategoryController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\CourseCategory\UpdateCategory::class
		);

		$router->delete(
			'/admin/courses/categories/{category_id}',
			\MasterStudy\Lms\Http\Controllers\CourseCategory\DeleteCourseCategoryController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\CourseCategory\DeleteCategory::class
		);

		$router->post(
			'/admin/courses/categories/bulk-delete',
			\MasterStudy\Lms\Http\Controllers\CourseCategory\BulkDeleteCourseCategoriesController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\CourseCategory\BulkDeleteCategories::class
		);

		$router->get(
			'/admin/courses/categories/icons',
			\MasterStudy\Lms\Http\Controllers\CourseCategory\GetIconsController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\CourseCategory\GetIcons::class
		);

		$router->get(
			'/admin/courses/categories/course-templates-modal',
			\MasterStudy\Lms\Http\Controllers\CourseCategory\GetCourseTemplatesModalController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\CourseCategory\GetCourseTemplatesModal::class
		);
	}
);
