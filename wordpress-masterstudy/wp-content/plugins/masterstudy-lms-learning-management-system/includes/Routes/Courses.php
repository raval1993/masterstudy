<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

/**
 * Course routes
 */
$router->get(
	'/courses/new',
	\MasterStudy\Lms\Http\Controllers\Course\AddNewController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\AddNew::class
);

$router->get(
	'/instructor-courses',
	\MasterStudy\Lms\Http\Controllers\Course\GetInstructorCoursesController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\GetInstructorCourses::class
);

$router->post(
	'/courses/create',
	\MasterStudy\Lms\Http\Controllers\Course\CreateController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\Create::class
);

$router->post(
	'/courses/category',
	\MasterStudy\Lms\Http\Controllers\Course\CreateCategoryController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\CreateCategory::class
);

$router->get(
	'/courses/categories/list',
	\MasterStudy\Lms\Http\Controllers\Course\GetCategoriesListController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\GetCategoriesList::class
);

$router->get(
	'/courses/{course_id}/edit',
	\MasterStudy\Lms\Http\Controllers\Course\EditController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\Edit::class
);

$router->get(
	'/courses/{course_id}/settings',
	\MasterStudy\Lms\Http\Controllers\Course\GetSettingsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\GetSettings::class
);

$router->put(
	'/courses/{course_id}/settings',
	\MasterStudy\Lms\Http\Controllers\Course\UpdateSettingsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\UpdateSettings::class
);

$router->get(
	'/courses/{course_id}/settings/faq',
	\MasterStudy\Lms\Http\Controllers\Course\GetFaqSettingsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\GetFaqSettings::class
);

$router->put(
	'/courses/{course_id}/settings/faq',
	\MasterStudy\Lms\Http\Controllers\Course\UpdateFaqSettingsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\UpdateFaqSettings::class
);

$router->put(
	'/courses/{course_id}/settings/certificate',
	\MasterStudy\Lms\Http\Controllers\Course\UpdateCertificateSettingsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\UpdateCertificateSettings::class
);

$router->put(
	'/courses/{course_id}/settings/course-page-style',
	\MasterStudy\Lms\Http\Controllers\Course\UpdatePageStyleSettingsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\UpdatePageStyleSettings::class
);

$router->get(
	'/courses/{course_id}/settings/pricing',
	\MasterStudy\Lms\Http\Controllers\Course\GetPricingSettingsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\GetPricingSettings::class
);

$router->put(
	'/courses/{course_id}/settings/pricing',
	\MasterStudy\Lms\Http\Controllers\Course\UpdatePricingSettingsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\UpdatePricingSettings::class
);

$router->put(
	'/courses/{course_id}/settings/files',
	\MasterStudy\Lms\Http\Controllers\Course\UpdateFilesSettingsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\UpdateFilesSettings::class
);

$router->put(
	'/courses/{course_id}/settings/access',
	\MasterStudy\Lms\Http\Controllers\Course\UpdateAccessSettingsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\UpdateAccessSettings::class
);

$router->put(
	'/courses/{course_id}/status',
	\MasterStudy\Lms\Http\Controllers\Course\UpdateStatusController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\UpdateStatus::class
);

$router->get(
	'/courses/{course_id}/curriculum',
	\MasterStudy\Lms\Http\Controllers\Course\Curriculum\GetCurriculumController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\Curriculum\GetCurriculum::class
);

$router->post(
	'/courses/{course_id}/curriculum/section',
	\MasterStudy\Lms\Http\Controllers\Course\Curriculum\CreateSectionController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\Curriculum\CreateSection::class
);

$router->put(
	'/courses/{course_id}/curriculum/section',
	\MasterStudy\Lms\Http\Controllers\Course\Curriculum\UpdateSectionController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\Curriculum\UpdateSection::class
);

$router->delete(
	'/courses/{course_id}/curriculum/section/{section_id}',
	\MasterStudy\Lms\Http\Controllers\Course\Curriculum\DeleteSectionController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\Curriculum\DeleteSection::class
);

$router->post(
	'/courses/{course_id}/curriculum/material',
	\MasterStudy\Lms\Http\Controllers\Course\Curriculum\CreateMaterialController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\Curriculum\CreateMaterial::class
);

$router->put(
	'/courses/{course_id}/curriculum/material',
	\MasterStudy\Lms\Http\Controllers\Course\Curriculum\UpdateMaterialController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\Curriculum\UpdateMaterial::class
);

$router->delete(
	'/courses/{course_id}/curriculum/material/{material_id}',
	\MasterStudy\Lms\Http\Controllers\Course\Curriculum\DeleteMaterialController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\Curriculum\DeleteMaterial::class
);

$router->get(
	'/courses/{course_id}/curriculum/import',
	\MasterStudy\Lms\Http\Controllers\Course\Curriculum\ImportSearchController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\Curriculum\ImportSearch::class
);

$router->post(
	'/courses/{course_id}/curriculum/import',
	\MasterStudy\Lms\Http\Controllers\Course\Curriculum\ImportMaterialsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\Curriculum\ImportMaterials::class
);

$router->get(
	'/courses/{course_id}/announcement',
	\MasterStudy\Lms\Http\Controllers\Course\GetAnnouncementController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\GetAnnouncement::class
);

$router->put(
	'/courses/{course_id}/announcement',
	\MasterStudy\Lms\Http\Controllers\Course\UpdateAnnouncementController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\UpdateAnnouncement::class
);

$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Routing\Middleware\AdminOrInstructor::class,
		),
	),
	function ( \MasterStudy\Lms\Routing\Router $router ) {
		$router->get(
			'/all-courses',
			\MasterStudy\Lms\Http\Controllers\Course\GetAdminCoursesController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Course\GetAdminCourses::class
		);
		$router->patch(
			'/courses/{course_id}/status',
			\MasterStudy\Lms\Http\Controllers\Course\UpdateCourseStatusController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Course\UpdateCourseStatus::class
		);
		$router->post(
			'/courses-bulk-update',
			\MasterStudy\Lms\Http\Controllers\Course\BulkUpdateCoursesController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Course\BulkUpdateCourses::class
		);
	}
);
