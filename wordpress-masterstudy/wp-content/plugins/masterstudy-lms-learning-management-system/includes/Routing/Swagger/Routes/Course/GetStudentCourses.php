<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Course;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetStudentCourses extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'page'   => array(
				'type'        => 'integer',
				'description' => 'Number of page to offset in the query.',
			),
			'user'   => array(
				'type'        => 'integer',
				'description' => 'Filter courses by user ID.',
			),
			'pp'     => array(
				'type'        => 'integer',
				'description' => 'Posts per page.',
			),
			'status' => array(
				'type'        => 'string',
				'description' => 'Course status.',
			),
		);
	}

	public function response(): array {
		return array(
			'courses'     => array(
				'type'        => 'array',
				'description' => 'List of course templates.',
			),
			'pagination'  => array(
				'type'        => 'string',
				'description' => 'HTML representation of the pagination.',
			),
			'total_pages' => array(
				'type'        => 'integer',
				'description' => 'Total number of pages.',
			),
			'total_posts' => array(
				'type'        => 'integer',
				'description' => 'Total number of courses.',
			),
		);
	}

	public function get_summary(): string {
		return "Get Student's Courses";
	}

	public function get_description(): string {
		return "Returns a list of student's courses based on the provided parameters.";
	}
}
