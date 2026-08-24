<?php

namespace MasterStudy\Lms\Http\Controllers\Question;

use MasterStudy\Lms\Enums\QuestionType;
use MasterStudy\Lms\Enums\QuestionView;
use MasterStudy\Lms\Repositories\QuestionRepository;
use MasterStudy\Lms\Validation\Validator;
use MasterStudy\Lms\Http\WpResponseFactory;
use WP_REST_Request;

final class BulkCreateController {
	public function __invoke( WP_REST_Request $request ) {
		$validator = new Validator(
			$request->get_params(),
			array(
				'questions' => 'required|array',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$data    = $validator->get_validated();
		$results = array();
		$rules   = array(
			'answers'     => 'required|array',
			'categories'  => 'array',
			'explanation' => 'string',
			'hint'        => 'string',
			'image'       => 'array',
			'image.id'    => 'integer',
			'image.url'   => 'string',
			'question'    => 'required|string',
			'type'        => 'required|string|contains_list,' . implode( ';', QuestionType::cases() ),
			'view_type'   => 'string|contains_list,' . implode( ';', QuestionView::cases() ),
		);

		foreach ( $data['questions'] as $question_data ) {
			$validator = new Validator(
				$question_data,
				apply_filters( 'masterstudy_lms_question_validation_rules', $rules )
			);

			if ( $validator->fails() ) {
				return WpResponseFactory::validation_failed( $validator->get_errors_array() );
			}

			$results[] = ( new QuestionRepository() )->create( $question_data );
		}

		return WpResponseFactory::created(
			array( 'question_ids' => $results )
		);
	}
}
