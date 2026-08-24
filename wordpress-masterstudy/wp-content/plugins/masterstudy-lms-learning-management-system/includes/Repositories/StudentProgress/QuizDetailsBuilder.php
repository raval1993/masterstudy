<?php

namespace MasterStudy\Lms\Repositories\StudentProgress;

use MasterStudy\Lms\Enums\QuestionType;
use MasterStudy\Lms\Repositories\QuestionRepository;
use MasterStudy\Lms\Repositories\QuizRepository;
use STM_LMS_Helpers;

final class QuizDetailsBuilder {
	public function build( int $course_id, int $student_id, int $quiz_id ): array {
		$quiz_repository = new QuizRepository();
		$quiz            = $quiz_repository->get( $quiz_id );

		if ( empty( $quiz ) ) {
			return array(
				'summary'  => array(),
				'attempts' => array(),
			);
		}

		$attempt_rows = stm_lms_get_quiz_all_attempts( $student_id, $course_id, $quiz_id );
		$attempts     = array();

		foreach ( $attempt_rows as $attempt_row ) {
			$attempt_id = (int) ( $attempt_row['user_quiz_id'] ?? 0 );
			if ( $attempt_id <= 0 ) {
				continue;
			}

			$attempt = stm_lms_get_attempt( $attempt_id, $student_id, $quiz_id, $course_id );
			if ( empty( $attempt ) ) {
				continue;
			}

			$formatted_date = STM_LMS_Helpers::format_date( $attempt['created_at'] ?? '' );

			$attempts[] = array(
				'id'                => $attempt_id,
				'attempt_number'    => (int) ( $attempt['attempt_number'] ?? 0 ),
				'progress'          => (int) ( $attempt['progress'] ?? 0 ),
				'status'            => (string) ( $attempt['status'] ?? '' ),
				'correct_answers'   => (int) ( $attempt_row['correct'] ?? 0 ),
				'incorrect_answers' => (int) ( $attempt_row['incorrect'] ?? 0 ),
				'created_at'        => array(
					'date' => $formatted_date['date'] ?? '',
					'time' => $formatted_date['time'] ?? '',
				),
				'questions'         => $this->normalize_quiz_attempt_questions( $quiz, $attempt ),
			);
		}

		$latest_attempt = $attempts[0] ?? null;

		return array(
			'summary'  => array(
				'passing_grade'       => (float) ( $quiz['passing_grade'] ?? 0 ),
				'quiz_attempts'       => (string) ( $quiz['quiz_attempts'] ?? '' ),
				'attempt_limit'       => (int) ( $quiz['attempts'] ?? 0 ),
				'show_correct_answer' => rest_sanitize_boolean( $quiz['correct_answer'] ?? false ),
				'latest_attempt_id'   => $latest_attempt['id'] ?? 0,
			),
			'attempts' => $attempts,
		);
	}

	private function normalize_quiz_attempt_questions( array $quiz, array $attempt ): array {
		$question_ids = $this->get_quiz_attempt_question_ids( $quiz, $attempt );
		if ( empty( $question_ids ) ) {
			return array();
		}

		$question_repository = new QuestionRepository();
		$questions           = $question_repository->get_all( $question_ids );
		$show_correct_answer = rest_sanitize_boolean( $quiz['correct_answer'] ?? false );
		$output              = array();

		foreach ( $questions as $question ) {
			$question_id   = (int) ( $question['id'] ?? 0 );
			$last_answers  = $attempt['answers'][ $question_id ] ?? array();
			$question_type = (string) ( $question['type'] ?? QuestionType::SINGLE_CHOICE );
			$answers       = is_array( $question['answers'] ?? null ) ? $question['answers'] : array();

			if ( ! empty( $last_answers['questions_order'] ) && ! empty( $answers ) && ! in_array( $question_type, array( QuestionType::FILL_THE_GAP, QuestionType::KEYWORDS, QuestionType::SORTABLE ), true ) ) {
				$answers = \MasterStudy\Lms\Utility\Question::sort_answers_by_order( $answers, $last_answers['questions_order'], $question_type );
			}

			$normalized_question = array(
				'id'                  => $question_id,
				'type'                => $question_type,
				'title'               => wp_kses_post( (string) ( $question['question'] ?? '' ) ),
				'content'             => wp_kses_post( stm_lms_filtered_output( (string) ( $question['content'] ?? '' ) ) ),
				'fill_the_gap_text'   => '',
				'explanation'         => wp_kses_post( (string) ( $question['explanation'] ?? '' ) ),
				'hint'                => wp_kses_post( (string) ( $question['hint'] ?? '' ) ),
				'view_type'           => (string) ( $question['view_type'] ?? '' ),
				'is_correct'          => ! empty( $last_answers['correct_answer'] ),
				'show_correct_answer' => $show_correct_answer,
				'options'             => array(),
				'gaps'                => array(),
				'pairs'               => array(),
				'sortable'            => array(),
				'keywords'            => array(),
			);

			switch ( $question_type ) {
				case QuestionType::MULTI_CHOICE:
					$normalized_question['options'] = $this->normalize_multiple_choice_answers( $answers, $last_answers, $show_correct_answer );
					break;
				case QuestionType::FILL_THE_GAP:
					$normalized_question['fill_the_gap_text'] = wp_kses_post( (string) ( $answers[0]['text'] ?? '' ) );
					$normalized_question['gaps']              = $this->normalize_fill_the_gap_answers( $answers, $last_answers );
					break;
				case QuestionType::ITEM_MATCH:
					$normalized_question['pairs'] = $this->normalize_item_match_answers( $answers, $last_answers );
					break;
				case QuestionType::IMAGE_MATCH:
					$normalized_question['pairs'] = $this->normalize_image_match_answers( $answers, $last_answers );
					break;
				case QuestionType::SORTABLE:
					$normalized_question['sortable'] = $this->normalize_sortable_answers( $answers, $last_answers );
					break;
				case QuestionType::KEYWORDS:
					$normalized_question['keywords'] = $this->normalize_keyword_answers( $answers, $last_answers );
					break;
				default:
					$normalized_question['options'] = $this->normalize_single_choice_answers( $answers, $last_answers, $show_correct_answer );
					break;
			}

			$output[] = $normalized_question;
		}

		return $output;
	}

	private function get_quiz_attempt_question_ids( array $quiz, array $attempt ): array {
		$sequence            = ! empty( $attempt['sequency'] ) ? json_decode( (string) $attempt['sequency'], true ) : array();
		$question_repository = new QuestionRepository();
		$question_ids        = array();

		foreach ( $quiz['questions'] ?? array() as $question_id ) {
			$question_id = (int) $question_id;
			if ( $question_id <= 0 ) {
				continue;
			}

			$question = $question_repository->get( $question_id );
			if ( empty( $question ) ) {
				continue;
			}

			if ( QuestionType::QUESTION_BANK === ( $question['type'] ?? '' ) ) {
				foreach ( $sequence[ $question_id ] ?? array() as $bank_question_id ) {
					$bank_question_id = (int) $bank_question_id;
					if ( $bank_question_id > 0 ) {
						$question_ids[] = $bank_question_id;
					}
				}

				continue;
			}

			$question_ids[] = $question_id;
		}

		return array_values( array_unique( $question_ids ) );
	}

	private function normalize_single_choice_answers( array $answers, array $last_answers, bool $show_correct_answer ): array {
		$is_correct_attempt = ! empty( $last_answers['correct_answer'] );
		$user_answer        = $is_correct_attempt ? array() : stripcslashes( (string) ( $last_answers['user_answer'] ?? '' ) );
		$normalized         = array();

		foreach ( $answers as $answer ) {
			$is_true     = rest_sanitize_boolean( $answer['isTrue'] ?? false );
			$full_answer = ! empty( $answer['text_image']['url'] )
				? (string) $answer['text'] . '|' . (string) $answer['text_image']['url']
				: (string) $answer['text'];

			if ( $is_correct_attempt && $is_true ) {
				$user_answer = $full_answer;
			}

			$state = 'neutral';
			if ( $full_answer === $user_answer && $is_true ) {
				$state = 'correct';
			} elseif ( $full_answer === $user_answer && ! $is_true ) {
				$state = 'incorrect';
			} elseif ( $full_answer !== $user_answer && $is_true && $show_correct_answer ) {
				$state = 'expected';
			}

			$normalized[] = array(
				'text'           => wp_kses_post( (string) ( $answer['text'] ?? '' ) ),
				'text_image_url' => esc_url_raw( (string) ( $answer['text_image']['url'] ?? '' ) ),
				'explain'        => wp_kses_post( (string) ( $answer['explain'] ?? '' ) ),
				'is_true'        => $is_true,
				'is_selected'    => in_array( $state, array( 'correct', 'incorrect' ), true ),
				'state'          => $state,
			);
		}

		return $normalized;
	}

	private function normalize_multiple_choice_answers( array $answers, array $last_answers, bool $show_correct_answer ): array {
		$user_answers = ! empty( $last_answers['user_answer'] )
			? array_map( 'rawurldecode', explode( ',', (string) $last_answers['user_answer'] ) )
			: array();
		$normalized   = array();

		foreach ( $answers as $answer ) {
			$full_answer = ! empty( $answer['text_image']['url'] )
				? trim( rawurldecode( (string) $answer['text'] ) ) . '|' . (string) $answer['text_image']['url']
				: trim( rawurldecode( (string) $answer['text'] ) );
			$is_true     = rest_sanitize_boolean( $answer['isTrue'] ?? false );
			$is_selected = in_array( $full_answer, $user_answers, true );
			$state       = 'neutral';

			if ( $is_selected && $is_true ) {
				$state = 'correct';
			} elseif ( $is_selected && ! $is_true ) {
				$state = 'incorrect';
			} elseif ( ! $is_selected && $is_true && $show_correct_answer ) {
				$state = 'expected';
			}

			$normalized[] = array(
				'text'           => wp_kses_post( (string) ( $answer['text'] ?? '' ) ),
				'text_image_url' => esc_url_raw( (string) ( $answer['text_image']['url'] ?? '' ) ),
				'explain'        => wp_kses_post( (string) ( $answer['explain'] ?? '' ) ),
				'is_true'        => $is_true,
				'is_selected'    => $is_selected,
				'state'          => $state,
			);
		}

		return $normalized;
	}

	private function normalize_fill_the_gap_answers( array $answers, array $last_answers ): array {
		if ( empty( $answers[0]['text'] ) ) {
			return array();
		}

		$user_answers = ! empty( $last_answers['user_answer'] ) ? explode( ',', (string) $last_answers['user_answer'] ) : array();
		$matches      = stm_lms_get_string_between( (string) $answers[0]['text'], '|', '|' );
		$gaps         = array();

		foreach ( $matches as $index => $match ) {
			$expected = stripslashes( rawurldecode( (string) ( $match['answer'] ?? '' ) ) );
			$actual   = isset( $user_answers[ $index ] ) ? stripslashes( rawurldecode( (string) $user_answers[ $index ] ) ) : '';

			$gaps[] = array(
				'index'      => (int) $index,
				'expected'   => $expected,
				'actual'     => $actual,
				'is_correct' => '' !== $actual && 0 === strcasecmp( trim( $actual ), trim( html_entity_decode( $expected, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) ),
			);
		}

		return $gaps;
	}

	private function normalize_item_match_answers( array $answers, array $last_answers ): array {
		$user_answers = ! empty( $last_answers['user_answer'] )
			? explode( '[stm_lms_sep]', str_replace( '[stm_lms_item_match]', '', (string) $last_answers['user_answer'] ) )
			: array();
		$pairs        = array();

		foreach ( $answers as $index => $answer ) {
			$actual   = isset( $user_answers[ $index ] ) ? stripslashes( (string) $user_answers[ $index ] ) : '';
			$expected = trim( str_replace( array( '\\(', '\\)' ), '', (string) ( $answer['text'] ?? '' ) ) );

			$pairs[] = array(
				'index'      => (int) $index,
				'prompt'     => wp_kses_post( trim( str_replace( array( '\\(', '\\)' ), '', (string) ( $answer['question'] ?? '' ) ) ) ),
				'expected'   => $expected,
				'actual'     => $actual,
				'explain'    => wp_kses_post( (string) ( $answer['explain'] ?? '' ) ),
				'is_correct' => '' !== $actual && 0 === strcasecmp( trim( str_replace( array( '\\(', '\\)' ), '', $actual ) ), $expected ),
			);
		}

		return $pairs;
	}

	private function normalize_image_match_answers( array $answers, array $last_answers ): array {
		$user_answers = ! empty( $last_answers['user_answer'] )
			? explode( '[stm_lms_sep]', str_replace( '[stm_lms_image_match]', '', (string) $last_answers['user_answer'] ) )
			: array();
		$pairs        = array();

		foreach ( $answers as $index => $answer ) {
			$actual_parts = ! empty( $user_answers[ $index ] ) ? explode( '|', (string) $user_answers[ $index ] ) : array();
			$expected_url = ! empty( $answer['text_image']['url'] ) ? '|' . (string) $answer['text_image']['url'] : '';
			$expected     = (string) ( $answer['text'] ?? '' ) . $expected_url;

			$pairs[] = array(
				'index'              => (int) $index,
				'prompt'             => wp_kses_post( (string) ( $answer['question'] ?? '' ) ),
				'prompt_image_url'   => esc_url_raw( (string) ( $answer['question_image']['url'] ?? '' ) ),
				'expected'           => wp_kses_post( (string) ( $answer['text'] ?? '' ) ),
				'expected_image_url' => esc_url_raw( (string) ( $answer['text_image']['url'] ?? '' ) ),
				'actual'             => wp_kses_post( (string) ( $actual_parts[0] ?? '' ) ),
				'actual_image_url'   => esc_url_raw( (string) ( $actual_parts[1] ?? '' ) ),
				'explain'            => wp_kses_post( (string) ( $answer['explain'] ?? '' ) ),
				'is_correct'         => ! empty( $user_answers[ $index ] ) && 0 === strcasecmp( (string) $user_answers[ $index ], $expected ),
			);
		}

		return $pairs;
	}

	private function normalize_sortable_answers( array $answers, array $last_answers ): array {
		$user_answers = ! empty( $last_answers['user_answer'] )
			? explode( '[stm_lms_sep]', str_replace( '[stm_lms_sortable]', '', (string) $last_answers['user_answer'] ) )
			: array();
		$items        = array();

		foreach ( $answers as $index => $answer ) {
			$expected = trim( str_replace( array( '\\(', '\\)' ), '', (string) ( $answer['text'] ?? '' ) ) );
			$actual   = isset( $user_answers[ $index ] ) ? trim( str_replace( array( '\\(', '\\)' ), '', (string) $user_answers[ $index ] ) ) : '';

			$items[] = array(
				'index'      => (int) $index,
				'expected'   => $expected,
				'actual'     => $actual,
				'explain'    => wp_kses_post( (string) ( $answer['explain'] ?? '' ) ),
				'is_correct' => '' !== $actual && $actual === $expected,
			);
		}

		return $items;
	}

	private function normalize_keyword_answers( array $answers, array $last_answers ): array {
		$user_answers = ! empty( $last_answers['user_answer'] )
			? explode( '[stm_lms_sep]', str_replace( '[stm_lms_keywords]', '', (string) $last_answers['user_answer'] ) )
			: array();
		$keywords     = array();

		foreach ( $answers as $index => $answer ) {
			$expected = (string) ( $answer['text'] ?? '' );
			$actual   = (string) ( $user_answers[ $index ] ?? '' );

			$keywords[] = array(
				'index'      => (int) $index,
				'expected'   => $expected,
				'actual'     => $actual,
				'explain'    => wp_kses_post( (string) ( $answer['explain'] ?? '' ) ),
				'is_correct' => '' !== $actual && 0 === strcasecmp( $actual, $expected ),
			);
		}

		return $keywords;
	}
}
