<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Question;

use MasterStudy\Lms\Routing\Swagger\Fields\QuestionType;
use MasterStudy\Lms\Routing\Swagger\Fields\QuestionView;
use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class BulkCreate extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'questions' => array(
				'type'     => 'array',
				'required' => true,
				'items'    => array(
					'type'       => 'object',
					'properties' => array(
						'answers'     => array(
							'type'     => 'array',
							'required' => true,
						),
						'categories'  => array(
							'type'        => 'array',
							'description' => 'Question or Question Bank categories',
							'items'       => array(
								'type' => 'integer',
							),
						),
						'explanation' => array(
							'type' => 'string',
						),
						'hint'        => array(
							'type' => 'string',
						),
						'image'       => array(
							'type'       => 'object',
							'properties' => array(
								'id'  => array(
									'type' => 'integer',
								),
								'url' => array(
									'type' => 'string',
								),
							),
						),
						'question'    => array(
							'type'     => 'string',
							'required' => true,
						),
						'type'        => QuestionType::as_response(),
						'view_type'   => QuestionView::as_response(),
					),
				),
			),
		);
	}

	public function response(): array {
		return array(
			'question_ids' => array(
				'type'     => 'array',
				'required' => true,
				'items'    => array(
					'type' => 'integer',
				),
			),
		);
	}

	public function get_summary(): string {
		return 'Bulk create questions';
	}

	public function get_description(): string {
		return 'Create multiple questions at once';
	}
}
