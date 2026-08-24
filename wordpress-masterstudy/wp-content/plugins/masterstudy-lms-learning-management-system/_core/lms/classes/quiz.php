<?php

use MasterStudy\Lms\Pro\AddonsPlus\Grades\Services\GradeCalculator;
use MasterStudy\Lms\Repositories\CoursePlayerRepository;
use MasterStudy\Lms\Utility\Question;

STM_LMS_Quiz::init();

class STM_LMS_Quiz {
	public static function init() {
		add_action( 'wp_ajax_stm_lms_start_quiz', 'STM_LMS_Quiz::start_quiz' );
		add_action( 'wp_ajax_nopriv_stm_lms_start_quiz', 'STM_LMS_Quiz::start_quiz' );
		add_action( 'wp_ajax_stm_lms_user_answers', 'STM_LMS_Quiz::user_answers' );
		add_action( 'wp_ajax_nopriv_stm_lms_user_answers', 'STM_LMS_Quiz::user_answers' );
		add_action( 'wp_ajax_stm_lms_add_h5p_result', 'STM_LMS_Quiz::h5p_results' );
		add_action( 'wp_ajax_nopriv_stm_lms_add_h5p_result', 'STM_LMS_Quiz::h5p_results' );
	}

	public static function get_quiz_end_time( $quiz_id ) {
		$user = STM_LMS_User::get_current_user();
		if ( empty( $user['id'] ) ) {
			die;
		}

		return STM_LMS_Helpers::simplify_db_array( stm_lms_get_user_quizzes_time( $user['id'], $quiz_id, array( 'end_time' ) ) );
	}

	public static function is_quiz_failed( $item_id, $course_id ) {
		$duration = self::get_quiz_duration( $item_id );

		if ( ! empty( $duration ) ) {
			$already_started = self::get_quiz_end_time( $item_id );
			if ( ! empty( $already_started['end_time'] ) ) {
				$end_time = $already_started['end_time'];
				/*Quiz failed*/
				if ( time() > $end_time ) {
					self::quiz_failed( $item_id, $course_id );
				}
			}
		}
	}

	public static function quiz_failed( $quiz_id, $course_id ) {
		$user = STM_LMS_User::get_current_user();
		if ( empty( $user['id'] ) ) {
			die;
		}
		$user_id = $user['id'];

		$progress = 0;
		$status   = 'failed';

		$user_quiz = compact( 'user_id', 'course_id', 'quiz_id', 'progress', 'status' );
		stm_lms_add_user_quiz( $user_quiz );

		/*REMOVE TIMER*/
		stm_lms_get_delete_user_quiz_time( $user_id, $quiz_id );
	}

	public static function start_quiz() {
		check_ajax_referer( 'start_quiz', 'nonce' );

		if ( empty( $_GET['quiz_id'] ) ) {
			die;
		}

		$quiz_id         = intval( $_GET['quiz_id'] );
		$user            = STM_LMS_User::get_current_user();
		$user_id         = $user['id'] ?? null;
		$duration        = self::get_quiz_duration( $quiz_id );
		$already_started = STM_LMS_Helpers::simplify_db_array( stm_lms_get_user_quizzes_time( $user['id'], $quiz_id, array( 'end_time' ) ) );
		$count_to        = ! empty( $duration ) ? time() + $duration : 0;

		if ( empty( $already_started ) ) {
			stm_lms_add_user_quiz_time(
				array(
					'user_id'    => $user_id,
					'quiz_id'    => $quiz_id,
					'start_time' => time(),
					'end_time'   => $count_to,
				)
			);
		} else {
			$count_to = $already_started['end_time'];
		}

		if ( time() - $count_to > 0 ) {
			/*REMOVE TIMER*/
			stm_lms_get_delete_user_quiz_time( $user_id, $quiz_id );
			/*Set NEW*/
			$count_to = time() + $duration;
			stm_lms_add_user_quiz_time(
				array(
					'user_id'    => $user_id,
					'quiz_id'    => $quiz_id,
					'start_time' => time(),
					'end_time'   => $count_to,
				)
			);
		}
		wp_send_json( $count_to );
	}

	public static function user_answers() {
		check_ajax_referer( 'user_answers', 'nonce' );

		$source    = intval( $_POST['source'] ?? 0 );
		$sequency  = wp_json_encode( $_POST['questions_sequency'] ?? array() );
		$user      = STM_LMS_User::get_current_user();
		$user_id   = $user['id'] ?? null;
		$course_id = apply_filters( 'user_answers__course_id', intval( $_POST['course_id'] ?? 0 ), $source );
		$quiz_id   = intval( $_POST['quiz_id'] ?? 0 );

		if ( empty( $course_id ) || empty( $quiz_id ) ) {
			wp_die();
		}

		$total_questions = CoursePlayerRepository::masterstudy_lms_get_question_bank_total_items( $quiz_id );

		$score_per_question = 100 / $total_questions;
		$re_take_cut        = (float) get_post_meta( $quiz_id, 're_take_cut', true );
		$cutting_rate       = ! empty( $re_take_cut ) ? ( 100 - $re_take_cut ) / 100 : 1;
		$passing_grade      = (float) get_post_meta( $quiz_id, 'passing_grade', true );
		$user_answer_id     = 0;
		$attempt_number     = stm_lms_get_user_quizzes( $user_id, $quiz_id, $course_id, array(), true ) + 1;

		$progress = 0;

		foreach ( $_POST as $question_id => $value ) {
			if ( ! is_numeric( $question_id ) ) {
				continue;
			}

			$question_id     = intval( $question_id );
			$type            = get_post_meta( $question_id, 'type', true );
			$questions_order = $_POST[ "order_$question_id" ] ?? '';

			switch ( $type ) {
				case 'fill_the_gap':
				case 'item_match':
				case 'single_choice':
				case 'multi_choice':
				case 'keywords':
				case 'sortable':
					$answer = is_array( $value ) ? array_map( 'trim', $value ) : trim( (string) $value );
					$answer = self::deslash( $answer );
					break;

				default:
					$answer = is_array( $value ) ? self::sanitize_answers( $value ) : sanitize_text_field( self::deslash( $value ) );
					break;
			}

			$user_answer    = is_array( $answer ) ? implode( ',', $answer ) : $answer;
			$correct_answer = self::check_answer( $question_id, $answer, array(), $questions_order );
			$progress      += $correct_answer ? $score_per_question : 0;
			$user_answer_id = stm_lms_add_user_answer( compact( 'user_id', 'course_id', 'quiz_id', 'question_id', 'attempt_number', 'user_answer', 'correct_answer', 'questions_order' ) );
		}

		$progress = 1 === $attempt_number ? round( $progress ) : round( $progress * pow( $cutting_rate, $attempt_number - 1 ) );
		$status   = $progress < $passing_grade ? 'failed' : 'passed';

		$user_quiz = compact( 'user_id', 'course_id', 'quiz_id', 'progress', 'status', 'sequency' );
		stm_lms_add_user_quiz( $user_quiz );

		stm_lms_get_delete_user_quiz_time( $user_id, $quiz_id );
		STM_LMS_Course::update_course_progress( $user_id, $course_id );

		if ( 'passed' === $status ) {
			$user_login   = $user['login'];
			$course_title = get_the_title( $course_id );
			$quiz_name    = get_the_title( $quiz_id );

			$progress        = is_ms_lms_addon_enabled( 'grades' ) ? GradeCalculator::get_instance()->get_passing_grade( $progress ) : round( $progress, 1 ) . '%';
			$passing_grade   = is_ms_lms_addon_enabled( 'grades' ) ? GradeCalculator::get_instance()->get_passing_grade( $passing_grade ) : round( $passing_grade, 1 ) . '%';
			$attempt_details = stm_lms_get_quiz_last_attempt( $user_id, $course_id, $quiz_id );

			$email_data_quiz_completed = array(
				'user_login'           => STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $user_id ),
				'quiz_name'            => $quiz_name,
				'course_title'         => $course_title,
				'quiz_result'          => $progress,
				'quiz_passing_grade'   => $passing_grade,
				'quiz_completion_date' => current_time( 'mysql' ),
				'blog_name'            => STM_LMS_Helpers::masterstudy_lms_get_site_name(),
				'site_url'             => \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_site_url() ),
				'quiz_url'             => \MS_LMS_Email_Template_Helpers::link( STM_LMS_Lesson::get_lesson_url( $course_id, $quiz_id ) ),
				'attempt_url'          => \MS_LMS_Email_Template_Helpers::link( ms_plugin_user_account_url() . 'enrolled-quiz-attempts/' . $course_id . '/' . $quiz_id . '/' . $attempt_details['user_quiz_id'] . '/' ),
				'attempt_number'       => $attempt_details['attempt_number'],
			);

			$template = wp_kses_post(
				'Hi {{user_login}}, <br>
				You’ve just completed the quiz "{{quiz_name}}" in the course "{{course_title}}". Great work!<br>
				<b>Here’s a summary of your attempt:</b><br>
				<ul style="text-align: left;">
					<li> <b>Course:</b> {{course_title}}  </li>
					<li> <b>Quiz:</b> {{quiz_name}}  </li>
					<li> <b>Your Result:</b> {{quiz_result}}  </li>
					<li> <b>Passing Grade:</b> {{quiz_passing_grade}}  </li>
					<li> <b>Completion Date:</b> {{quiz_completion_date}}  </li>
				</ul>
				Keep it up - each step brings you closer to your learning goals!'
			);

			$message = \MS_LMS_Email_Template_Helpers::render( $template, $email_data_quiz_completed );
			$subject = esc_html__( 'You’ve Completed the Quiz in {{course_title}}', 'masterstudy-lms-learning-management-system' );

			if ( class_exists( 'STM_LMS_Email_Manager' ) ) {
				$email_manager = STM_LMS_Email_Manager::stm_lms_get_settings();
				$subject       = $email_manager['stm_lms_course_quiz_completed_for_user_subject'] ?? $subject;
			}

			$subject = \MS_LMS_Email_Template_Helpers::render( $subject, $email_data_quiz_completed );

			STM_LMS_Helpers::send_email( $user['email'], $subject, $message, 'stm_lms_course_quiz_completed_for_user', $email_data_quiz_completed );

			//email about quiz completed to instructor by student
			$email_data_quiz_completed_instructor = array(
				'user_login'           => STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $user_id ),
				'quiz_name'            => $quiz_name,
				'course_title'         => $course_title,
				'quiz_result'          => $progress,
				'quiz_completion_date' => current_time( 'mysql' ),
				'blog_name'            => STM_LMS_Helpers::masterstudy_lms_get_site_name(),
				'site_url'             => \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_site_url() ),
				'quiz_url'             => \MS_LMS_Email_Template_Helpers::link( STM_LMS_Lesson::get_lesson_url( $course_id, $quiz_id ) ),
			);
			$template                             = wp_kses_post(
				'We\'re pleased to inform you that {{user_login}} has completed the quiz "{{quiz_name}}" in the course {{course_title}}.<br>
			Quiz Result: {{quiz_result}}<br>
			Completion Date: {{quiz_completion_date}} <br>'
			);

			$message = \MS_LMS_Email_Template_Helpers::render( $template, $email_data_quiz_completed_instructor );
			$subject = esc_html__( '{{user_login}} has completed the quiz {{quiz_name}} in {{course_title}}', 'masterstudy-lms-learning-management-system' );

			if ( class_exists( 'STM_LMS_Email_Manager' ) ) {
				$email_manager = STM_LMS_Email_Manager::stm_lms_get_settings();
				$subject       = $email_manager['stm_lms_course_quiz_completed_for_instructor_subject'] ?? $subject;
			}
			$subject = \MS_LMS_Email_Template_Helpers::render( $subject, $email_data_quiz_completed_instructor );

			STM_LMS_Helpers::send_email( \STM_LMS_Helpers::masterstudy_lms_get_post_author_email_by_post_id( $course_id ), $subject, $message, 'stm_lms_course_quiz_completed_for_instructor', $email_data_quiz_completed );
		}

		$user_quiz['user_answer_id'] = $user_answer_id;
		$user_quiz['passed']         = $progress >= $passing_grade;
		$user_quiz['progress']       = $progress;
		$user_quiz['url']            = apply_filters( 'user_answers__course_url', '<a class="btn btn-default btn-close-quiz-modal-results" href="' . apply_filters( 'stm_lms_item_url_quiz_ended', STM_LMS_Lesson::get_lesson_url( $course_id, $quiz_id ) ) . '">' . esc_html__( 'Close', 'masterstudy-lms-learning-management-system' ) . '</a>', $source );

		do_action( 'stm_lms_quiz_' . $status, $user_id, $quiz_id, $user_quiz['progress'], $course_id );

		wp_send_json( $user_quiz );
	}

	public static function h5p_results() {
		check_ajax_referer( 'stm_lms_add_h5p_result', 'nonce' );

		$res = array(
			'completed' => false,
		);

		$course_id = intval( $_POST['sources']['post_id'] );
		$quiz_id   = intval( $_POST['sources']['item_id'] );
		$user_id   = get_current_user_id();
		$last_quiz = stm_lms_get_user_last_quiz( $user_id, $quiz_id, array( 'progress', 'status' ) );

		if ( ! empty( $last_quiz ) && ! empty( $last_quiz['status'] ) && 'passed' === $last_quiz['status'] ) {
			wp_send_json( $res );
		}

		$status = ( ! empty( $_POST['success'] ) ) ? sanitize_text_field( $_POST['success'] ) : 'failed';
		$status = ( ! empty( $status ) && 'true' === $status ) ? 'passed' : 'failed';

		stm_lms_get_delete_user_quiz_time( $user_id, $quiz_id );

		$progress = ( isset( $_POST['score']['scaled'] ) ) ? intval( $_POST['score']['scaled'] * 100 ) : 0;

		/*We have no success, but we have progress now!*/
		if ( ! isset( $_POST['success'] ) ) {
			if ( 100 === $progress ) {
				$status = 'passed';
			}
		}

		$sequency = '';

		$res['completed'] = ( 'passed' === $status );
		$res['progress']  = $progress;
		$res['status']    = $status;

		$user_quiz = compact( 'user_id', 'course_id', 'quiz_id', 'progress', 'status', 'sequency' );
		stm_lms_add_user_quiz( $user_quiz );

		if ( 'passed' === $status ) {
			STM_LMS_Course::update_course_progress( $user_id, $course_id );
		}

		wp_send_json( $res );
	}

	public static function deslash( $content ) {
		$content = preg_replace( "/\\\+'/", "'", $content );

		/*
		 * Replace one or more backslashes followed by a double quote with
		 * a double quote.
		 */
		$content = preg_replace( '/\\\+"/', '"', $content );

		// Replace one or more backslashes with one backslash.
		return preg_replace( '/\\\+/', '\\', $content );
	}

	public static function encode_answers( $answers ) {
		if ( is_array( $answers ) ) {
			foreach ( $answers as &$answer ) {
				$answer = wp_kses_post( rawurlencode( $answer ) );
			}
		} else {
			$answers = wp_kses_post( rawurlencode( $answers ) );
		}
		return $answers;
	}

	public static function sanitize_answers( $answers ) {
		$new_answers = array();
		foreach ( $answers as $answer ) {
			$new_answers[] = sanitize_text_field( self::deslash( $answer ) );
		}

		return $new_answers;
	}

	public static function check_answer( $question_id, $answer, $answers = array(), $order = '' ) {
		$correct = false;
		$answers = ! empty( $answers ) ? $answers : get_post_meta( $question_id, 'answers', true );

		if ( empty( $answers ) ) {
			return false;
		}

		$type             = get_post_meta( $question_id, 'type', true );
		$has_wrong_answer = false;

		foreach ( $answers as $stored_answer ) {
			switch ( $type ) {
				case 'single_choice':
					$answer      = wp_unslash( $answer );
					$full_answer = $stored_answer['text'];
					if ( ! empty( $stored_answer['text_image']['url'] ) ) {
						$full_answer .= '|' . esc_url( $stored_answer['text_image']['url'] );
					}

					$answer_to_decode    = is_array( $answer ) ? implode( '', $answer ) : $answer;
					$answer_decoded      = str_replace( '\\', '', stripslashes( htmlspecialchars_decode( rawurldecode( $answer_to_decode ) ) ) );
					$full_answer_decoded = str_replace( '\\', '', stripslashes( htmlspecialchars_decode( rawurldecode( $full_answer ) ) ) );

					if ( $answer_decoded === $full_answer_decoded && $stored_answer['isTrue'] ) {
						$correct = true;
					}
					break;
				case 'multi_choice':
					$answer      = wp_unslash( $answer );
					$full_answer = $stored_answer['text'];

					if ( ! empty( $stored_answer['text_image']['url'] ) ) {
						$full_answer .= '|' . esc_url( $stored_answer['text_image']['url'] );
					}

					$answer = array_map(
						function ( $answers ) {
							return str_replace( '\\', '', html_entity_decode( stripslashes( rawurldecode( $answers ) ), ENT_QUOTES ) );
						},
						$answer
					);

					$full_answer          = str_replace( '\\', '', html_entity_decode( stripslashes( $full_answer ), ENT_QUOTES ) );
					$contains_full_answer = in_array( $full_answer, $answer, true );
					$is_true              = $stored_answer['isTrue'];

					if ( $contains_full_answer && $is_true ) {
						$correct = true;
					} elseif ( ! $contains_full_answer && $is_true ) {
						$correct          = false;
						$has_wrong_answer = true;
					} elseif ( $contains_full_answer && ! $is_true ) {
						$correct          = false;
						$has_wrong_answer = true;
					}

					$correct = $has_wrong_answer ? false : $correct;

					break;
				case 'item_match':
					$answer = array_map(
						function ( $item ) {
							return stripslashes( htmlspecialchars_decode( rawurldecode( $item ) ) );
						},
						explode( '[stm_lms_sep]', str_replace( '[stm_lms_item_match]', '', $answer ) )
					);

					if ( ! empty( $order ) ) {
						$answers = Question::sort_answers_by_order( $answers, $order, $type );
					}

					foreach ( $answers as $i => $correct_answer ) {
						$correct = true;

						$processed_correct_answer = str_replace( '\\', '', html_entity_decode( wp_unslash( strtolower( $correct_answer['text'] ) ), ENT_QUOTES ) );
						$processed_answer         = str_replace( '\\', '', html_entity_decode( wp_unslash( strtolower( $answer[ $i ] ) ), ENT_QUOTES ) );

						if ( preg_replace( '/^\(|\)$/', '', $processed_correct_answer ) !== preg_replace( '/^\(|\)$/', '', $processed_answer ) ) {
							$correct = false;
							break;
						}
					}

					return $correct;
				case 'image_match':
					$answer = explode( '[stm_lms_sep]', str_replace( '[stm_lms_image_match]', '', $answer ) );

					if ( ! empty( $order ) ) {
						$answers = Question::sort_answers_by_order( $answers, $order, $type );
					}

					foreach ( $answers as $i => $correct_answer ) {
						$correct     = true;
						$correct_url = ( ! empty( $correct_answer['text_image']['url'] ) ) ? '|' . esc_url( $correct_answer['text_image']['url'] ) : '';
						if ( strtolower( $correct_answer['text'] . $correct_url ) !== strtolower( $answer[ $i ] ) ) {
							$correct = false;
							break;
						}
					}

					return $correct;
				case 'keywords':
					$answer = explode( '[stm_lms_sep]', str_replace( '[stm_lms_keywords]', '', $answer ) );

					foreach ( $answers as $i => $correct_answer ) {
						$correct = true;
						if ( strtolower( $correct_answer['text'] ) !== strtolower( $answer[ $i ] ) ) {
							$correct = false;
							break;
						}
					}

					return $correct;
				case 'fill_the_gap':
					if ( ! empty( $answers[0] ) && ! empty( $answers[0]['text'] ) ) {
						$text    = $answers[0]['text'];
						$matches = stm_lms_get_string_between( $text, '|', '|' );

						foreach ( $matches as $i => $correct_answer ) {
							$correct = true;
							if ( ! isset( $answer[ $i ] ) || ! isset( $correct_answer['answer'] ) ) {
								$correct = false;
								break;
							}

							$user_ans    = trim( strtolower( stripslashes( rawurldecode( $answer[ $i ] ) ) ) );
							$correct_ans = trim( strtolower( stripslashes( rawurldecode( html_entity_decode( $correct_answer['answer'], ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) ) ) );

							if ( $correct_ans !== $user_ans ) {
								$correct = false;
								break;
							}
						}

						return $correct;
					}

					break;
				case 'sortable':
					$answer = array_map(
						function ( $item ) {
							return stripslashes( htmlspecialchars_decode( rawurldecode( $item ) ) );
						},
						explode( '[stm_lms_sep]', str_replace( '[stm_lms_sortable]', '', $answer ) )
					);

					foreach ( $answers as $i => $correct_answer ) {
						$correct = true;

						$processed_correct_answer = str_replace( '\\', '', html_entity_decode( wp_unslash( strtolower( $correct_answer['text'] ) ), ENT_QUOTES ) );
						$processed_answer         = str_replace( '\\', '', html_entity_decode( wp_unslash( strtolower( $answer[ $i ] ) ), ENT_QUOTES ) );
						if ( preg_replace( '/^\(|\)$/', '', $processed_correct_answer ) !== preg_replace( '/^\(|\)$/', '', $processed_answer ) ) {
							$correct = false;
							break;
						}
					}

					return $correct;
				default:
					$answer = wp_unslash( $answer );

					// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
					if ( $answer == $stored_answer['text'] && $stored_answer['isTrue'] ) {
						$correct = true;
					}
			}
		}

		return $correct;
	}

	public static function passing_grade( $meta ) {
		return ( ! empty( $meta['passing_grade'] ) ) ? $meta['passing_grade'] : 0;
	}

	public static function quiz_passed( $quiz_id, $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user = STM_LMS_User::get_current_user();
			if ( empty( $user['id'] ) ) {
				return false;
			}

			$user_id = $user['id'];
		}

		$last_quiz = stm_lms_get_user_last_quiz( $user_id, $quiz_id, array( 'progress' ) );
		if ( empty( $last_quiz ) ) {
			return false;
		}
		$passing_grade = self::passing_grade( STM_LMS_Helpers::parse_meta_field( $quiz_id ) );

		return $last_quiz['progress'] >= $passing_grade;
	}

	public static function can_watch_answers( $quiz_id ) {
		$show_answers = get_post_meta( $quiz_id, 'correct_answer', true );
		if ( ! empty( $show_answers ) && 'on' === $show_answers ) {
			return true;
		}

		return self::quiz_passed( $quiz_id );
	}

	public static function answers_url() {
		return add_query_arg( 'show_answers', '1', STM_LMS_Helpers::get_current_url() );
	}

	public static function show_answers( $quiz_id, $admin = false ) {
		if ( ! self::can_watch_answers( $quiz_id ) && ! $admin ) {
			return false;
		}
		if ( self::quiz_passed( $quiz_id ) || $admin ) {
			return true;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return ! empty( $_GET['show_answers'] );
	}

	public static function get_quiz_duration( $quiz_id ) {
		$duration = get_post_meta( $quiz_id, 'duration', true );
		if ( empty( $duration ) ) {
			return 0;
		}

		$duration_measure = get_post_meta( $quiz_id, 'duration_measure', true );
		switch ( $duration_measure ) {
			case 'hours':
				$multiple = 60 * 60;
				break;
			case 'days':
				$multiple = 24 * 60 * 60;
				break;
			default:
				$multiple = 60;
		}

		return $duration * $multiple;
	}

	public static function get_style( $quiz_id ) {
		$quiz_style = get_post_meta( $quiz_id, 'quiz_style', true );

		if ( ! empty( $quiz_style ) && 'global' !== $quiz_style ) {
			return $quiz_style;
		}

		return STM_LMS_Options::get_option( 'quiz_style', 'default' );
	}

	public static function get_passed_quizzes( $user_id, $course_id ) {
		$quizzes = stm_lms_get_user_course_quizzes(
			$user_id,
			$course_id,
			array( 'quiz_id' ),
			'passed'
		);

		if ( empty( $quizzes ) ) {
			return array();
		}

		return array_fill_keys(
			array_column( $quizzes, 'quiz_id' ),
			true
		);
	}
}
