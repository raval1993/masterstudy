<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

/**
 * Student routes
 */
$router->get(
	'/students/',
	\MasterStudy\Lms\Http\Controllers\Student\GetStudentsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Student\GetStudents::class
);

$router->delete(
	'/students/delete/',
	\MasterStudy\Lms\Http\Controllers\Student\DeleteStudentsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Student\DeleteStudents::class
);

$router->get(
	'/students/{course_id}',
	\MasterStudy\Lms\Http\Controllers\Student\GetStudentsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Student\GetStudents::class
);

$router->get(
	'/students/{course_id}/stats',
	\MasterStudy\Lms\Http\Controllers\Student\GetCourseStudentsStatsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Student\GetCourseStudentsStats::class
);

$router->get(
	'/export/students/',
	\MasterStudy\Lms\Http\Controllers\Student\ExportStudentsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Student\ExportStudents::class
);

$router->get(
	'/students/export/{course_id}',
	\MasterStudy\Lms\Http\Controllers\Student\ExportStudentsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Student\ExportStudents::class
);

$router->post(
	'/student/{course_id}',
	\MasterStudy\Lms\Http\Controllers\Student\AddStudentController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Student\AddStudent::class
);

$router->post(
	'/student/bulk/{course_id}',
	\MasterStudy\Lms\Http\Controllers\Student\AddStudentsBulkController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Student\AddStudentsBulk::class
);

$router->get(
	'/student/progress/{course_id}/{student_id}',
	\MasterStudy\Lms\Http\Controllers\Student\GetStudentProgressController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Student\GetStudentProgress::class
);

$router->get(
	'/student/progress/{course_id}/{student_id}/material/{material_id}',
	\MasterStudy\Lms\Http\Controllers\Student\GetStudentProgressMaterialDetailsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Student\GetStudentProgressMaterialDetails::class
);

$router->put(
	'/student/progress/{course_id}/{student_id}',
	\MasterStudy\Lms\Http\Controllers\Student\SetStudentProgressController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Student\SetStudentProgress::class
);

$router->delete(
	'/student/progress/{course_id}/{student_id}',
	\MasterStudy\Lms\Http\Controllers\Student\ResetStudentProgressController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Student\ResetStudentProgress::class
);

$router->delete(
	'/student/{course_id}/{student_id}',
	\MasterStudy\Lms\Http\Controllers\Student\DeleteStudentController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Student\DeleteStudent::class
);
