<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Student;

use MasterStudy\Lms\Routing\Swagger\Fields\CurriculumMaterial;
use MasterStudy\Lms\Routing\Swagger\Fields\CurriculumSection;
use MasterStudy\Lms\Routing\Swagger\Route;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;

class ResetStudentProgress extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'course_title'      => array(
				'type' => 'string',
			),
			'current_lesson_id' => array(
				'type' => 'integer',
			),
			'progress_percent'  => array(
				'type' => 'integer',
			),
			'lesson_type'       => array(
				'type' => 'string',
			),
			'materials'         => CurriculumMaterial::as_array(),
			'sections'          => CurriculumSection::as_array(),
			'user'              => array(
				'type' => 'array',
			),
		);
	}

	public function get_summary(): string {
		return 'Reset student progress to course';
	}

	public function get_description(): string {
		return 'Reset student progress to course.';
	}
}
