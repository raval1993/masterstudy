<?php

namespace MasterStudy\Lms\Http\Controllers\Quiz;

use MasterStudy\Lms\Enums\DurationMeasure;
use MasterStudy\Lms\Enums\QuizAttempts;
use MasterStudy\Lms\Enums\QuizStyle;
use MasterStudy\Lms\Http\Controllers\CourseBuilder\UpdateCustomFieldsController;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\QuizRepository;
use MasterStudy\Lms\Utility\Question as QuestionUtility;
use MasterStudy\Lms\Validation\Validator;

class UpdateController {

	public function __invoke( int $quiz_id, \WP_REST_Request $request ) {
		$repo = new QuizRepository();

		if ( ! $repo->exists( $quiz_id ) ) {
			return WpResponseFactory::not_found();
		}

		$validator = new Validator(
			$request->get_params(),
			array(
				'content'               => 'nullable|string',
				'correct_answer'        => 'required|boolean',
				'duration'              => 'nullable|integer',
				'duration_measure'      => 'nullable|string|contains_list,' . implode( ';', array_map( 'strval', DurationMeasure::cases() ) ),
				'excerpt'               => 'string',
				'attempts'              => 'nullable|integer',
				'passing_grade'         => 'nullable|integer',
				'random_questions'      => 'required|boolean',
				're_take_cut'           => 'nullable|numeric',
				'style'                 => 'required|contains_list,' . implode( ';', array_map( 'strval', QuizStyle::cases() ) ),
				'title'                 => 'required|string',
				'questions'             => 'array',
				'required_answers_ids'  => 'array',
				'custom_fields'         => 'array',
				'quiz_attempts'         => 'required|string|contains_list,' . implode( ';', array_map( 'strval', QuizAttempts::cases() ) ),
				'show_attempts_history' => 'boolean',
				'retry_after_passing'   => 'boolean',
				'random_answers'        => 'boolean',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$data = $validator->get_validated();

		$repo->update( $quiz_id, $data );

		if ( ! empty( $data['custom_fields'] ) ) {
			$response = ( new UpdateCustomFieldsController() )->update( $quiz_id, $data['custom_fields'] );

			if ( is_array( $response ) ) {
				return WpResponseFactory::validation_failed( $response );
			}
		}

		return WpResponseFactory::ok();
	}
}
