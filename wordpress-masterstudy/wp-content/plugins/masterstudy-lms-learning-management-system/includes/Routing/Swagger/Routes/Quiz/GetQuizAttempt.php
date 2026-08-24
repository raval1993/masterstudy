<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Quiz;

use MasterStudy\Lms\Routing\Swagger\Fields\Post;
use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetQuizAttempt extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'attempt_id' => array(
				'type'        => 'integer',
				'description' => 'Attempt ID.',
			),
			'course_id'  => array(
				'type'        => 'integer',
				'description' => 'Course ID.',
			),
			'quiz_id'    => array(
				'type'        => 'integer',
				'description' => 'Quiz ID.',
			),
			'dark_mode'  => array(
				'type'        => 'boolean',
				'description' => 'Dark mode.',
			),
		);
	}

	public function response(): array {
		return array(
			'date'           => array(
				'type'        => 'string',
				'description' => 'Dates of attempt creation.',
			),
			'time'           => array(
				'type'        => 'string',
				'description' => 'Time to create attempts.',
			),
			'progress'       => array(
				'type'        => 'string',
				'description' => 'Total number of quiz progress.',
			),
			'questions'      => array(
				'type'        => 'integer',
				'description' => 'Total number of quiz questions.',
			),
			'correct'        => array(
				'type'        => 'integer',
				'description' => 'Total number of quiz correct.',
			),
			'incorrect'      => array(
				'type'        => 'integer',
				'description' => 'Total number of quiz incorrect.',
			),
			'answers'        => array(
				'type'        => 'string',
				'description' => 'Number of answered questions.',
			),
			'passed'         => array(
				'type'        => 'string',
				'description' => 'Attempt passed.',
			),
			'questions_html' => array(
				'type'        => 'string',
				'description' => 'Questions html.',
			),
		);
	}

	public function get_summary(): string {
		return 'Get Attempt';
	}

	public function get_description(): string {
		return 'Returns quiz attempt based on the provided parameters.';
	}
}
