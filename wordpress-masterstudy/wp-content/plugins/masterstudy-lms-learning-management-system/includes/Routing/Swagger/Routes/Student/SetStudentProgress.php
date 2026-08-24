<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Student;

use MasterStudy\Lms\Routing\Swagger\Fields\CurriculumMaterial;
use MasterStudy\Lms\Routing\Swagger\Fields\CurriculumSection;
use MasterStudy\Lms\Routing\Swagger\Route;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\RequestInterface;

class SetStudentProgress extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'course_id'  => array(
				'type'     => 'integer',
				'required' => true,
			),
			'item_id'    => array(
				'type'     => 'integer',
				'required' => true,
			),
			'student_id' => array(
				'type'     => 'integer',
				'required' => true,
			),
			'completed'  => array(
				'type'     => 'boolean',
				'required' => true,
			),
		);
	}

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
		return 'Set student progress to course';
	}

	public function get_description(): string {
		return 'Set student progress to course.';
	}
}
