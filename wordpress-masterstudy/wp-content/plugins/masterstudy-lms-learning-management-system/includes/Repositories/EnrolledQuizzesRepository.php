<?php
namespace MasterStudy\Lms\Repositories;

use MasterStudy\Lms\Pro\AddonsPlus\Grades\Services\GradeCalculator;
use STM_LMS_Helpers;
use STM_LMS_Lesson;
use STM_LMS_Templates;

final class EnrolledQuizzesRepository {

	/**
	 * @var CoursePlayerRepository|null
	 */
	private $course_player = null;

	/**
	 * @param array $request
	 *
	 * @return array
	 */
	public function get_quizzes( array $request = array() ): array {
		$user     = get_current_user_id();
		$per_page = $request['per_page'] ?? get_option( 'posts_per_page', 10 );
		$search   = $request['s'] ?? '';
		$page     = $request['current_page'] ?? 1;
		$offset   = ( $page - 1 ) * $per_page;

		$quizzes_data = stm_lms_get_course_all_quizzes( $user, $search, $per_page, $offset );
		$total        = stm_lms_get_course_all_quizzes( $user, $search, '', '', true );

		$courses = array();

		foreach ( $quizzes_data as $item ) {
			$course_id = (int) $item['course_id'];

			$quizzes_array = isset( $item['quizzes'] ) && is_array( $item['quizzes'] )
				? $item['quizzes']
				: array();

			if ( empty( $quizzes_array ) ) {
				continue;
			}

			$course_title = ! empty( get_post_status( $course_id ) )
				? wp_specialchars_decode( get_the_title( $course_id ), ENT_QUOTES )
				: esc_html__( 'Course Deleted', 'masterstudy-lms-learning-management-system' );

			if ( ! isset( $courses[ $course_id ] ) ) {
				$courses[ $course_id ] = array(
					'title'   => $course_title,
					'url'     => get_the_permalink( $course_id ),
					'quizzes' => array(),
				);
			}

			foreach ( $quizzes_array as $quiz_item ) {
				$user_quiz_id    = isset( $quiz_item['user_quiz_id'] ) ? (int) $quiz_item['user_quiz_id'] : 0;
				$quiz_id         = isset( $quiz_item['quiz_id'] ) ? (int) $quiz_item['quiz_id'] : 0;
				$quiz_status     = $quiz_item['quiz_status'] ?? '';
				$progress        = isset( $quiz_item['progress'] ) ? (int) $quiz_item['progress'] : 0;
				$attempts_count  = isset( $quiz_item['attempts_count'] ) ? (int) $quiz_item['attempts_count'] : 0;
				$questions_count = isset( $quiz_item['questions_count'] ) ? (int) $quiz_item['questions_count'] : 0;

				$status      = $this->format_status( $quiz_status );
				$grade       = $this->format_grade( $progress );
				$grade_point = $this->get_grade_point( $progress );

				$courses[ $course_id ]['quizzes'][] = array(
					'user_quiz_id' => esc_html( $user_quiz_id ),
					'quiz_status'  => $quiz_status,
					'title'        => esc_html( get_the_title( $quiz_id ) ),
					'url'          => esc_url( STM_LMS_Lesson::get_lesson_url( $course_id, $quiz_id ) ),
					'grade'        => esc_html( $grade ),
					'grade_point'  => esc_html( $grade_point ),
					'progress'     => esc_html( $progress ),
					'attempts'     => array(
						'url'   => esc_url( ms_plugin_user_account_url( 'enrolled-quiz-attempts/' . $course_id . '/' . $quiz_id ) ),
						'count' => sprintf(
							/* translators: %d count attempts */
							esc_html__( '%d attempt(s)', 'masterstudy-lms-learning-management-system' ),
							$attempts_count
						),
					),
					'questions'    => $this->get_questions_count(
						array(
							'user_quiz_id'    => $user_quiz_id,
							'quiz_id'         => $quiz_id,
							'quiz_status'     => $quiz_status,
							'progress'        => $progress,
							'attempts_count'  => $attempts_count,
							'questions_count' => $questions_count,
						)
					),
					'status'       => $status,
				);
			}
		}

		return array(
			'courses'       => array_values( $courses ),
			'pages'         => (int) ceil( $total / $per_page ),
			'current_page'  => (int) $page,
			'total_quizzes' => (int) $total,
			'total'         => ( $total <= $offset + $per_page ),
		);
	}

	/**
	 * @param array $request
	 *
	 * @return array
	 */
	public function get_attempts( array $request = array() ): array {
		$user_id   = get_current_user_id();
		$course_id = $request['course_id'] ?? null;
		$quiz_id   = $request['quiz_id'] ?? null;
		$per_page  = $request['per_page'] ?? get_option( 'posts_per_page', 10 );
		$page      = $request['current_page'] ?? 1;
		$offset    = ( $page - 1 ) * $per_page;

		$quiz_attempts = stm_lms_get_quiz_all_attempts( $user_id, $course_id, $quiz_id, $per_page, $offset );
		$total         = stm_lms_get_quiz_all_attempts( $user_id, $course_id, $quiz_id, $per_page, $offset, true );

		$attempts = array();

		foreach ( $quiz_attempts as $quiz_attempt ) {
			$progress = $quiz_attempt['progress'] ?? 0;

			$attempts[] = array(
				'number'     => sprintf( '№%d', esc_html( $quiz_attempt['attempt_number'] ) ),
				'url'        => esc_url( ms_plugin_user_account_url( 'enrolled-quiz-attempt/' . $quiz_attempt['course_id'] . '/' . $quiz_attempt['quiz_id'] . '/' . $quiz_attempt['user_quiz_id'] ) ),
				'progress'   => esc_html( $progress ),
				'grade'      => esc_html( $this->format_grade( $progress ) ),
				'created_at' => $this->format_created_at( $quiz_attempt['created_at'], esc_html__( 'N/A', 'masterstudy-lms-learning-management-system' ) ),
				'attempts'   => array(
					'correct'   => absint( $quiz_attempt['correct'] ),
					'incorrect' => absint( $quiz_attempt['incorrect'] ),
				),
				'questions'  => absint( $quiz_attempt['correct'] ) + absint( $quiz_attempt['incorrect'] ),
				'status'     => $this->format_status( $quiz_attempt['status'] ),
			);
		}

		return array(
			'attempts'     => array_values( $attempts ),
			'pages'        => (int) ceil( $total / $per_page ),
			'current_page' => (int) $page,
			'total'        => ( $total <= $offset + $per_page ),
		);
	}

	/**
	 * @param array $request
	 *
	 * @return array
	 */
	public function get_attempt( array $request = array() ): array {
		$user_id    = get_current_user_id();
		$attempt_id = $request['attempt_id'] ?? null;
		$course_id  = $request['course_id'] ?? null;
		$quiz_id    = $request['quiz_id'] ?? null;

		$attempt = stm_lms_get_attempt( $attempt_id, $user_id, $quiz_id, $course_id );

		if ( empty( $attempt ) ) {
			return array();
		}

		$quiz_data  = ( new CoursePlayerRepository() )->get_quiz_data( $quiz_id, $user_id, $course_id );
		$emoji_type = $attempt['progress'] < $quiz_data['passing_grade'] ? 'assignments_quiz_failed_emoji' : 'assignments_quiz_passed_emoji';

		return wp_parse_args(
			array(
				'created_at'    => $this->format_created_at( $attempt['created_at'], sprintf( '№ %d', esc_html( $attempt['attempt_number'] ) ) ),
				'last_answers'  => $attempt['answers'] ?? array(),
				'last_quiz'     => $attempt,
				'progress'      => $attempt['progress'] ?? 0,
				'passed'        => ! empty( $attempt['progress'] ) && $attempt['progress'] >= $quiz_data['passing_grade'],
				'show_attempts' => false,
				'is_retakable'  => false,
				'content'       => '',
				'show_answers'  => true,
				'lesson_type'   => get_post_meta( $quiz_id, 'type', true ),
				'has_attempts'  => $quiz_data['has_attempts'] ?? false,
				'emoji_type'    => $emoji_type,
				'emoji_name'    => $quiz_data['show_emoji'] ? \STM_LMS_Options::get_option( $emoji_type ) : '',
			),
			$quiz_data
		);
	}

	/**
	 * @param array $request
	 *
	 * @return array
	 */
	public function get_single_attempt( array $request = array() ): array {
		$user_id      = get_current_user_id();
		$attempt_id   = $request['attempt_id'] ?? null;
		$course_id    = $request['course_id'] ?? null;
		$quiz_id      = $request['quiz_id'] ?? null;
		$dark_mode    = $request['dark_mode'] ?? false;
		$show_answers = $request['show_answers'] ?? false;

		$attempt = stm_lms_get_attempt( $attempt_id, $user_id, $quiz_id, $course_id );

		if ( empty( $attempt ) ) {
			return array();
		}

		if ( is_null( $this->course_player ) ) {
			$this->course_player = new CoursePlayerRepository();
		}

		$quiz_data          = $this->course_player->get_quiz_data( $quiz_id );
		$questions_quantity = count( $attempt['answers'] );
		$correct_answers    = count( array_filter( $attempt['answers'], fn( $item ) => isset( $item['correct_answer'] ) && '1' === $item['correct_answer'] ) );
		$incorrect_answers  = count( array_filter( $attempt['answers'], fn( $item ) => isset( $item['correct_answer'] ) && '0' === $item['correct_answer'] ) );
		$created_at         = $this->format_created_at( $attempt['created_at'], sprintf( '№ %d', esc_html( $attempt['attempt_number'] ) ) );
		$progress           = intval( $attempt['progress'] ?? 0 );

		$answers = '';
		if ( $questions_quantity > 0 ) {
			$template = is_rtl()
				/* translators: %1$d count answers , %2$d count questions */
				? __( '<strong>%2$d</strong> out of <strong>%1$d</strong> questions answered correctly', 'masterstudy-lms-learning-management-system' )
				/* translators: %1$d count answers , %2$d count questions */
				: __( '<strong>%1$d</strong> out of <strong>%2$d</strong> questions answered correctly', 'masterstudy-lms-learning-management-system' );
			$answers = sprintf( $template, $correct_answers, $questions_quantity );
		}

		$quiz_data['last_quiz']    = $attempt;
		$quiz_data['last_answers'] = $attempt['answers'];
		$quiz_data['show_answers'] = $show_answers || $quiz_data['show_answers'] || ! $quiz_data['retry_after_passing'];
		$quiz_data['emoji_type']   = $progress < $quiz_data['passing_grade'] ? 'assignments_quiz_failed_emoji' : 'assignments_quiz_passed_emoji';
		$quiz_data['emoji_name']   = $quiz_data['show_emoji'] ? \STM_LMS_Options::get_option( $quiz_data['emoji_type'] ) : '';

		return array(
			'date'           => $created_at['date'],
			'time'           => $created_at['time'],
			'grade'          => esc_html( $this->format_grade( $progress ) ),
			'questions'      => esc_html( $questions_quantity ),
			'correct'        => $correct_answers,
			'incorrect'      => $incorrect_answers,
			'answers'        => $answers,
			'emoji_name'     => $quiz_data['emoji_name'],
			'passed'         => ! ( $progress < intval( $quiz_data['passing_grade'] ?? 0 ) ),
			'questions_html' => STM_LMS_Templates::load_lms_template(
				'course-player/content/quiz/questions',
				array(
					'dark_mode' => $dark_mode,
					'quiz_data' => $quiz_data,
					'quiz_id'   => $quiz_id,
				)
			),
		);
	}

	private function format_created_at( $datetime, $default_value = '' ): array {
		$formatted = STM_LMS_Helpers::format_date( $datetime );
		return array(
			'date' => $formatted['date'] ?? $default_value,
			'time' => $formatted['time'] ?? '',
		);
	}

	private function get_questions_count( array $quiz_data ): string {
		$quiz_repo = ( new QuizRepository() )->get( $quiz_data['quiz_id'] );
		return isset( $quiz_repo['content'] ) && preg_match( '/\[h5p id="\d+"\]/', $quiz_repo['content'] ) ? '' :
			esc_html(
				sprintf(
					/* translators: %s: count of questions */
					_n(
						'%s question',
						'%s questions',
						$quiz_data['questions_count'] ?? 0,
						'masterstudy-lms-learning-management-system'
					),
					$quiz_data['questions_count'] ?? 0
				)
			);
	}

	private function format_status( string $status ): array {
		$label = 'passed' === $status
			? esc_html__( 'Passed', 'masterstudy-lms-learning-management-system' )
			: esc_html__( 'Failed', 'masterstudy-lms-learning-management-system' );

		return array(
			'label' => $label,
			'value' => esc_html( $status ),
		);
	}

	private function format_grade( int $progress ) {
		return is_ms_lms_addon_enabled( 'grades' ) ? GradeCalculator::get_instance()->get_passing_grade( $progress ) : round( $progress, 1 ) . '%';
	}

	private function get_grade_point( int $progress ) {
		return is_ms_lms_addon_enabled( 'grades' ) ? GradeCalculator::get_instance()->calculate( $progress )['point'] : '';
	}
}
