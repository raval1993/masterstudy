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
			'/admin-reviews',
			\MasterStudy\Lms\Http\Controllers\Review\GetReviewsController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Review\GetAdminReviews::class
		);

		$router->get(
			'/admin-reviews/{review_id}',
			\MasterStudy\Lms\Http\Controllers\Review\GetReviewController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Review\GetAdminReview::class
		);

		$router->post(
			'/admin-reviews',
			\MasterStudy\Lms\Http\Controllers\Review\CreateReviewController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Review\CreateAdminReview::class
		);

		$router->put(
			'/admin-reviews/{review_id}',
			\MasterStudy\Lms\Http\Controllers\Review\UpdateReviewController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Review\UpdateAdminReview::class
		);

		$router->delete(
			'/admin-reviews/{review_id}',
			\MasterStudy\Lms\Http\Controllers\Review\DeleteReviewController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Review\DeleteAdminReview::class
		);

		$router->post(
			'/admin-reviews/bulk-delete',
			\MasterStudy\Lms\Http\Controllers\Review\BulkDeleteReviewsController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Review\BulkDeleteAdminReviews::class
		);

		$router->get(
			'/admin-reviews/course-students/{course_id}',
			\MasterStudy\Lms\Http\Controllers\Review\GetCourseStudentsController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Review\GetCourseStudents::class
		);
	}
);
