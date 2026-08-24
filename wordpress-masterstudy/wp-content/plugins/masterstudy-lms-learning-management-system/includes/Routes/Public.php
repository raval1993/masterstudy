<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

/**
 * Public routes
 */
$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Guest::class,
		),
	),
	function ( \MasterStudy\Lms\Routing\Router $router ) {
		$router->get(
			'/courses',
			\MasterStudy\Lms\Http\Controllers\Course\GetCoursesController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Course\GetCourses::class
		);
		$router->get(
			'/course-categories',
			\MasterStudy\Lms\Http\Controllers\Blocks\Course\GetCategoriesController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Blocks\Course\GetCategories::class,
		);
		$router->get(
			'/users',
			'\MasterStudy\Lms\Http\Controllers\User\UserController@search',
		);
		$router->get(
			'/orders',
			\MasterStudy\Lms\Http\Controllers\Order\GetUserOrdersController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Order\GetUserOrders::class
		);
		$router->get(
			'/enrolled-quizzes',
			\MasterStudy\Lms\Http\Controllers\Quiz\GetEnrolledQuizzesController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Quiz\GetEnrolledQuizzes::class
		);
		$router->get(
			'/quiz/attempts',
			\MasterStudy\Lms\Http\Controllers\Quiz\GetQuizAttemptsController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Quiz\GetQuizAttempts::class
		);
		$router->get(
			'/quiz/attempt',
			\MasterStudy\Lms\Http\Controllers\Quiz\GetQuizAttemptController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Quiz\GetQuizAttempt::class
		);
		$router->get(
			'/instructor-public-courses',
			\MasterStudy\Lms\Http\Controllers\Course\GetInstructorPublicCoursesController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Course\GetInstructorPublicCourses::class
		);
		$router->get(
			'/instructor-reviews',
			\MasterStudy\Lms\Http\Controllers\Review\GetInstructorReviewsController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Review\GetInstructorReviews::class
		);
		$router->get(
			'/student-courses',
			\MasterStudy\Lms\Http\Controllers\Course\GetStudentCoursesController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Course\GetStudentCourses::class
		);
		$router->get(
			'/student/stats/{student_id}',
			\MasterStudy\Lms\Http\Controllers\Student\GetStudentStatsController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Student\GetStudentStats::class
		);
	}
);
