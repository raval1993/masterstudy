<?php

namespace MasterStudy\Lms\Repositories;

use MasterStudy\Lms\Enums\LessonType;
use MasterStudy\Lms\Enums\QuestionType;
use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Plugin\Addons;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Services\CourseService;
use function Patchwork\CodeManipulation\prime;

final class CoursePlayerRepository {
	public array $data = array();

	public const CONTENT_TYPES = array(
		'stm-lessons'      => 'lesson',
		'stm-quizzes'      => 'quiz',
		'stm-assignments'  => 'assignments',
		'stm-google-meets' => 'google_meet',
	);

	private const NOT_RANDOMIZING_ANSWERS_TYPES = array(
		QuestionType::FILL_THE_GAP,
		QuestionType::KEYWORDS,
		QuestionType::SORTABLE,
	);

	public function get_main_data( string $page_path, int $lesson_id ): array {
		$course           = get_page_by_path( $page_path, OBJECT, PostType::COURSE );
		$post_id          = apply_filters( 'wpml_object_id', $course->ID, 'post' ) ?? $course->ID;
		$user_id          = get_current_user_id();
		$settings         = get_option( 'stm_lms_settings' );
		$lesson_post_type = get_post_type( $lesson_id );
		$lesson_files     = get_post_meta( $lesson_id, 'lesson_files', true );
		$lesson           = get_post_meta( $lesson_id );
		$attachments      = $this->get_only_attachments( is_array( $lesson ) ? $lesson : array(), $lesson_files );
		$curriculum       = ( new CurriculumRepository() )->get_curriculum( $post_id, true );
		$course_materials = array_reduce(
			$curriculum,
			function ( $carry, $section ) {
				return array_merge( $carry, $section['materials'] ?? array() );
			},
			array()
		);
		$material_ids     = array_column( $course_materials, 'post_id' );

		$this->data = array(
			'post_id'                  => $post_id,
			'item_id'                  => $lesson_id,
			'curriculum'               => $curriculum,
			'material_ids'             => $material_ids,
			'lesson_post_type'         => $lesson_post_type,
			'content_type'             => self::CONTENT_TYPES[ $lesson_post_type ] ?? $lesson_post_type,
			'stm_lms_question_sidebar' => apply_filters( 'stm_lms_show_question_sidebar', true ),
			'course_title'             => $course->post_title,
			'user_id'                  => $user_id,
			'has_access'               => \STM_LMS_User::has_course_access( $post_id, $lesson_id ),
			'has_preview'              => \STM_LMS_Lesson::lesson_has_preview( $lesson_id ),
			'is_trial_course'          => get_post_meta( $post_id, 'shareware', true ),
			'lesson_attachments'       => $attachments,
			'trial_lesson_count'       => 0,
			'has_trial_access'         => false,
			'is_enrolled'              => false,
			'user_page_url'            => \STM_LMS_User::user_page_url(),
			'course_url'               => get_permalink( $post_id ),
			'lesson_completed'         => false,
			'lesson_lock_before_start' => false,
			'lesson_locked_by_drip'    => false,
			'is_scorm_course'          => false,
			'last_lesson'              => ! empty( $material_ids ) ? end( $material_ids ) : 0,
			'settings'                 => $settings,
			'theme_fonts'              => $settings['course_player_theme_fonts'] ?? false,
			'discussions_sidebar'      => $settings['course_player_discussions_sidebar'] ?? true,
			'dark_mode'                => $settings['course_player_theme_mode'] ?? false,
			'show_attempts_history'    => $settings['course_player_theme_mode'] ?? false,
		);

		$lesson_types_labels       = $this->get_lesson_labels();
		$this->data['lesson_type'] = 'lesson' === $this->data['content_type']
			? get_post_meta( $lesson_id, 'type', true )
			: $this->data['content_type'];

		if ( 'lesson' === $this->data['content_type'] && empty( $this->data['lesson_type'] ) ) {
			$this->data['lesson_type'] = 'text';
		}

		$this->data['lesson_type_label'] = $lesson_types_labels[ $this->data['lesson_type'] ] ?? '';

		if ( LessonType::VIDEO === $this->data['lesson_type'] ) {
			$this->data['video_type']              = get_post_meta( $lesson_id, 'video_type', true );
			$this->data['video_required_progress'] = get_post_meta( $lesson_id, 'video_required_progress', true );
			$this->data['video_questions_stats']   = array(
				'answered'  => 0,
				'completed' => 0,
				'total'     => 0,
			);

			if ( \STM_LMS_Helpers::is_pro_plus() ) {
				$this->data['video_questions']       = \STM_LMS_Lesson::get_lesson_video_questions( $user_id, $lesson_id );
				$this->data['video_questions_stats'] = array_reduce(
					$this->data['video_questions'],
					function ( $stats, $question ) {
						if ( ! empty( $question['is_answered'] ) ) {
							$stats['answered']++;
							$stats['completed']++;
						}
						if ( ! empty( $question['type'] ) ) {
							$stats['total']++;
						}

						return $stats;
					},
					array(
						'answered'  => 0,
						'completed' => 0,
						'total'     => 0,
					)
				);
			}
		} elseif ( 'audio' === $this->data['lesson_type'] ) {
			$this->data['audio_type']              = get_post_meta( $lesson_id, 'audio_type', true );
			$this->data['audio_required_progress'] = get_post_meta( $lesson_id, 'audio_required_progress', true );
		} elseif ( 'pdf' === $this->data['lesson_type'] ) {
			$this->data['pdf_file']     = get_post_meta( $lesson_id, 'pdf_file', true );
			$this->data['bookmarks']    = get_post_meta( $lesson_id, 'bookmarks', true );
			$this->data['pdf_read_all'] = get_post_meta( $lesson_id, 'pdf_read_all', true );
		}

		if ( is_user_logged_in() ) {
			$user_mode = get_user_meta( $user_id, 'masterstudy_course_player_theme_mode', true );
			if ( ! empty( $user_mode ) ) {
				$this->data['dark_mode'] = $user_mode;
			}

			$this->data['user_course'] = \STM_LMS_Course::get_user_course( $user_id, $post_id );
			$this->data['is_enrolled'] = ! empty( $this->data['user_course'] );

			if ( PostType::QUIZ === $lesson_post_type ) {
				$this->data['last_quiz']        = stm_lms_get_user_last_quiz( $this->data['user_id'], $lesson_id, array(), $post_id );
				$passing_grade                  = get_post_meta( $lesson_id, 'passing_grade', true );
				$this->data['lesson_completed'] = ( ! empty( $this->data['last_quiz']['progress'] ) && $this->data['last_quiz']['progress'] >= ( $passing_grade ?? 0 ) ) && ! \STM_LMS_Options::get_option( 'retry_after_passing', false ) ? 'completed' : '';
			} else {
				$this->data['lesson_completed'] = \STM_LMS_Lesson::is_lesson_completed( $user_id, $post_id, $lesson_id ) ? 'completed' : '';
			}
		}

		if ( ! empty( $this->data['is_trial_course'] ) && 'on' === $this->data['is_trial_course'] ) {
			$this->data['course_materials']   = $course_materials;
			$this->data['shareware_settings'] = get_option( 'stm_lms_shareware_settings' );
			$this->data['trial_lesson_count'] = $this->data['shareware_settings']['shareware_count'] ?? 0;
			$this->data['trial_lessons']      = array_filter(
				$this->data['course_materials'],
				function ( $lesson ) {
					return ( $this->data['trial_lesson_count'] >= $lesson['order'] && $lesson['post_id'] === $this->data['item_id'] );
				}
			);

			if ( ! empty( $this->data['trial_lessons'] ) ) {
				$this->data['has_trial_access'] = true;
			}
		}

		return apply_filters( 'masterstudy_lms_course_player_data', $this->data );
	}

	public function hydrate_materials( $materials, $course_id = null, $user_id = null ): array {
		$lesson_types_labels = $this->get_lesson_labels();

		if ( ! empty( $materials ) ) {
			return array_map(
				function ( $material ) use ( $lesson_types_labels, $course_id, $user_id ) {
					$material['post_id']                  = apply_filters( 'wpml_object_id', $material['post_id'], 'post' ) ?? $material['post_id'];
					$material['lesson_type']              = ! empty( $material['lesson_type'] ) ? $material['lesson_type'] : 'text';
					$material['lesson_lock_before_start'] = false;
					$material['lesson_locked_by_drip']    = false;

					if ( PostType::QUIZ === $material['post_type'] ) {
						$material['icon']                      = 'quiz';
						$material['questions']                 = get_post_meta( $material['post_id'], 'questions', true );
						$material['questions_array']           = ! empty( $material['questions'] ) ? explode( ',', $material['questions'] ) : '';
						$material['question_bank_total_items'] = $this->masterstudy_lms_get_question_bank_total_items( $material['post_id'] );
						$material['label']                     = $lesson_types_labels[ self::CONTENT_TYPES[ $material['post_type'] ] ];
					} else {
						$material['icon']     = $material['lesson_type'];
						$material['progress'] = '';
						$material['duration'] = get_post_meta( $material['post_id'], 'duration', true );
						$material['label']    = $lesson_types_labels[ $material['lesson_type'] ];
						if ( ! empty( $course_id ) && ! empty( $user_id ) ) {
							$user_progress        = masterstudy_lms_get_user_lesson_progress( $user_id, $course_id, $material['post_id'] ) ?? 0;
							$material['progress'] = $user_progress > 0 ? $user_progress . '% ' . esc_html__( 'completed', 'masterstudy-lms-learning-management-system' ) : '';
						}
						if ( PostType::ASSIGNMENT === $material['post_type'] ) {
							$material['icon']     = 'assignments';
							$material['progress'] = '';
							$material['label']    = $lesson_types_labels[ self::CONTENT_TYPES[ $material['post_type'] ] ];
						} elseif ( PostType::GOOGLE_MEET === $material['post_type'] ) {
							$material['icon']  = 'google-meet';
							$material['label'] = $lesson_types_labels[ self::CONTENT_TYPES[ $material['post_type'] ] ];
						}
					}

					return $material;
				},
				$materials
			);
		}

		return array();
	}

	public function get_lesson_labels(): array {
		return array(
			'text'            => esc_html__( 'Text lesson', 'masterstudy-lms-learning-management-system' ),
			'audio'           => esc_html__( 'Audio lesson', 'masterstudy-lms-learning-management-system' ),
			'video'           => esc_html__( 'Video lesson', 'masterstudy-lms-learning-management-system' ),
			'pdf'             => esc_html__( 'PDF lesson', 'masterstudy-lms-learning-management-system' ),
			'quiz'            => esc_html__( 'Quiz', 'masterstudy-lms-learning-management-system' ),
			'assignments'     => esc_html__( 'Assignment', 'masterstudy-lms-learning-management-system' ),
			'stream'          => esc_html__( 'Stream lesson', 'masterstudy-lms-learning-management-system' ),
			'zoom_conference' => esc_html__( 'Zoom lesson', 'masterstudy-lms-learning-management-system' ),
			'google_meet'     => esc_html__( 'Google Meet webinar', 'masterstudy-lms-learning-management-system' ),
		);
	}

	/**
	 * Get total number of question-bank items.
	 *
	 * @param int $quiz_id Quiz post ID.
	 *
	 * @return int
	 */
	public static function masterstudy_lms_get_question_bank_total_items( $quiz_id ) {
		static $cache = array();

		if ( isset( $cache[ $quiz_id ] ) ) {
			return $cache[ $quiz_id ];
		}

		$total_questions = 0;

		$quiz_meta    = \STM_LMS_Helpers::parse_meta_field( $quiz_id );
		$question_ids = array_filter( explode( ',', $quiz_meta['questions'] ?? '' ) );

		$bank_ids = array();
		foreach ( $question_ids as $qid ) {
			$type = get_post_meta( $qid, 'type', true );

			if ( 'question_bank' === $type ) {
				$bank_ids[] = $qid;
			} else {
				++$total_questions;
			}
		}

		foreach ( $bank_ids as $bank_qid ) {
			$q_meta  = \STM_LMS_Helpers::parse_meta_field( $bank_qid );
			$answers = $q_meta['answers'] ?? array();

			if ( empty( $answers[0]['categories'] ) || empty( $answers[0]['number'] ) ) {
				continue;
			}

			$number     = absint( $answers[0]['number'] );
			$categories = wp_list_pluck( $answers[0]['categories'], 'slug' );

			$bank_query = new \WP_Query(
				array(
					'post_type'      => \MasterStudy\Lms\Plugin\PostType::QUESTION,
					'posts_per_page' => $number,
					'fields'         => 'ids',
					'no_found_rows'  => true,
					'post__not_in'   => $question_ids,
					'tax_query'      => array(
						array(
							'taxonomy' => \MasterStudy\lms\Plugin\Taxonomy::QUESTION_CATEGORY,
							'field'    => 'slug',
							'terms'    => $categories,
						),
					),
				)
			);

			$total_questions += count( $bank_query->posts );
			wp_reset_postdata();
		}

		$cache[ $quiz_id ] = $total_questions;

		return $total_questions;
	}

	public function get_quiz_data( int $quiz_id, int $user_id = 0, int $course_id = 0 ): array {
		$quiz = ( new QuizRepository() )->get( $quiz_id );

		if ( ! $quiz ) {
			return array();
		}

		ob_start();
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo apply_filters( 'the_content', $quiz['content'] );
		$content = str_replace( '../../', site_url() . '/', ob_get_clean() );

		$quiz_data = array_merge(
			$quiz,
			array(
				'content'               => $content,
				'question_banks'        => array(),
				'quiz_style'            => \STM_LMS_Quiz::get_style( $quiz_id ),
				'duration'              => \STM_LMS_Quiz::get_quiz_duration( $quiz_id ),
				'duration_value'        => $quiz['duration'],
				'quiz_attempts'         => empty( $quiz['quiz_attempts'] ) ? \STM_LMS_Options::get_option( 'quiz_attempts' ) : $quiz['quiz_attempts'],
				'retry_after_passing'   => isset( $quiz['retry_after_passing'] ) ? $quiz['retry_after_passing'] : \STM_LMS_Options::get_option( 'retry_after_passing' ),
				'show_attempts_history' => empty( $quiz['show_attempts_history'] ) ? \STM_LMS_Options::get_option( 'show_attempts_history' ) : $quiz['show_attempts_history'],
				'is_retakable'          => true,
				'show_attempts'         => true,
			)
		);

		if ( empty( $this->data ) ) {
			$user_id    = apply_filters( 'user_answers__user_id', ! empty( $user_id ) ? $user_id : get_current_user_id() );
			$last_quiz  = stm_lms_get_user_last_quiz( $user_id, $quiz_id, array(), $course_id );
			$this->data = array(
				'user_id'          => $user_id,
				'last_quiz'        => $last_quiz,
				'lesson_completed' => ! empty( $last_quiz['progress'] ) && $last_quiz['progress'] >= ( $quiz_data['passing_grade'] ?? 0 ) ? 'completed' : '',
			);
		}

		if ( is_user_logged_in() && ! empty( $this->data['post_id'] ) && ! empty( $quiz_data['attempts'] ) && 'limited' === $quiz_data['quiz_attempts'] ) {
			$total_attempts = \STM_LMS_Helpers::simplify_db_array(
				stm_lms_get_user_all_course_quizzes( $this->data['user_id'], $this->data['post_id'], $quiz_id, array(), true )
			);

			$quiz_data['is_retakable']  = ( $total_attempts['COUNT(*)'] ?? 0 ) < $quiz_data['attempts'];
			$quiz_data['attempts_left'] = intval( $quiz_data['attempts'] ) - intval( $total_attempts['COUNT(*)'] ?? 0 );
		}

		$quiz_data['last_quiz'] = $this->data['last_quiz'] ?? array();
		$created_at             = isset( $quiz_data['last_quiz']['created_at'] ) ? \STM_LMS_Helpers::format_date( $quiz_data['last_quiz']['created_at'] ) : null;

		if ( empty( $created_at ) ) {
			$created_at['date'] = sprintf( '№ %d', esc_html( $quiz_data['last_quiz']['attempt_number'] ?? 0 ) );
		}

		$quiz_data['progress']     = $quiz_data['last_quiz']['progress'] ?? 0;
		$quiz_data['created_at']   = $created_at;
		$quiz_data['passed']       = $quiz_data['progress'] >= $quiz_data['passing_grade'] && ! empty( $quiz_data['progress'] );
		$quiz_data['emoji_type']   = $quiz_data['progress'] < $quiz_data['passing_grade'] ? 'assignments_quiz_failed_emoji' : 'assignments_quiz_passed_emoji';
		$quiz_data['show_emoji']   = \STM_LMS_Options::get_option( 'assignments_quiz_result_emoji_show', true ) ?? false;
		$quiz_data['emoji_name']   = \STM_LMS_Options::get_option( $quiz_data['emoji_type'] );
		$quiz_data['show_answers'] = ( $this->data['lesson_completed'] ?? false ) || ( ! empty( $quiz_data['last_quiz'] ) && $quiz['correct_answer'] ) || ! $quiz_data['is_retakable'];

		if ( ! empty( $quiz['questions'] ) ) {
			if ( ! empty( $quiz_data['random_questions'] ) ) {
				shuffle( $quiz['questions'] );
			}

			$quiz_data['questions'] = ( new QuestionRepository() )->get_all( $quiz['questions'] );

			if ( ! empty( $quiz_data['questions'] ) ) {
				$quiz_data['questions_quantity'] = count( $quiz_data['questions'] );
				$quiz_data['questions_for_nav']  = count( $quiz_data['questions'] );
				$sequence                        = ! empty( $quiz_data['last_quiz'] ) ? json_decode( $quiz_data['last_quiz']['sequency'], true ) : array();

				foreach ( $quiz_data['questions'] as &$question ) {
					$question['title']   = $question['question'];
					$question['content'] = str_replace( '../../', site_url() . '/', stm_lms_filtered_output( $question['content'] ) );

					if ( 'question_bank' === $question['type'] ) {
						if ( ! empty( $question['answers'][0]['categories'] ) && ! empty( $question['answers'][0]['number'] ) ) {
							$bank_args = array(
								'post_type'      => 'stm-questions',
								'posts_per_page' => $question['answers'][0]['number'],
								'post__not_in'   => $quiz['questions'],
								'meta_query'     => array(
									array(
										'key'     => 'type',
										'value'   => 'question_bank',
										'compare' => '!=',
									),
								),
								'tax_query'      => array(
									array(
										'taxonomy' => 'stm_lms_question_taxonomy',
										'field'    => 'slug',
										'terms'    => wp_list_pluck( $question['answers'][0]['categories'], 'slug' ),
									),
								),
							);

							if ( ! empty( $quiz['random_questions'] ) ) {
								$bank_args['orderby'] = 'rand';
							}

							if ( ! empty( $sequence ) && is_array( $sequence ) && isset( $sequence[ $question['id'] ] ) ) {
								$bank_args = array(
									'post_type'      => 'stm-questions',
									'post__in'       => $sequence[ $question['id'] ],
									'posts_per_page' => - 1,
									'orderby'        => 'post__in',
								);
							}

							$bank_data = new \WP_Query( $bank_args );
						}

						$quiz_data['question_banks'][ $question['id'] ] = $bank_data ?? array();

						if ( ! empty( $quiz_data['question_banks'] ) ) {
							$quiz_data['questions_for_nav'] += $quiz_data['question_banks'][ $question['id'] ]->found_posts > $question['answers'][0]['number']
								? $question['answers'][0]['number'] - 1
								: $quiz_data['question_banks'][ $question['id'] ]->found_posts - 1;
						}
					}

					if ( ! empty( $quiz_data['random_answers'] ) && 'completed' !== $this->data['lesson_completed'] &&
						$this->can_answers_be_randomized( $question['type'] )
					) {
						shuffle( $question['answers'] );
					}
				}

				if ( ! empty( $sequence ) && is_array( $sequence ) ) {
					foreach ( $sequence as $sequence_question ) {
						if ( is_array( $sequence_question ) ) {
							$quiz_data['questions_quantity'] += count( $sequence_question );
						}
					}

					$quiz_data['questions_quantity'] -= count( $sequence );
				}

				$quiz_data['last_answers'] = \STM_LMS_Helpers::set_value_as_key(
					stm_lms_get_quiz_latest_answers(
						$this->data['user_id'],
						$quiz_id,
						array(
							'question_id',
							'user_answer',
							'correct_answer',
							'questions_order',
						)
					),
					'question_id'
				);

			}
		}

		$quiz_data['has_attempts']      = stm_lms_attempts_exists( $course_id, $quiz_id, $user_id );
		$quiz_data['has_h5p_shortcode'] = isset( $quiz['content'] ) && preg_match( '/\[h5p id="\d+"\]/', $quiz['content'] );
		$quiz_data['show_history']      =
			$quiz_data['show_attempts_history']
			&& ! empty( $quiz_data['questions'] )
			&& ! $quiz_data['has_h5p_shortcode'];

		return $quiz_data;
	}

	public function get_student_all_quizes( int $student_id, int $course_id, int $quiz_id ) {
		$quizzes   = stm_lms_get_user_all_course_quizzes( $student_id, $course_id, $quiz_id );
		$quiz_data = $this->get_quiz_data( $quiz_id, $student_id );
		$output    = array();

		foreach ( $quizzes as $attempt => $quiz ) {
			++$attempt;
			$quiz_data['attempt']      = $attempt;
			$quiz_data['progress']     = $quiz['progress'];
			$quiz_data['created_at']   = \STM_LMS_Helpers::format_date( $quiz['created_at'] );
			$quiz_data['passed']       = $quiz['progress'] >= $quiz_data['passing_grade'];
			$quiz_data['emoji_type']   = $quiz['progress'] < $quiz_data['passing_grade'] ? 'assignments_quiz_failed_emoji' : 'assignments_quiz_passed_emoji';
			$quiz_data['emoji_name']   = \STM_LMS_Options::get_option( $quiz_data['emoji_type'] );
			$quiz_data['last_answers'] = \STM_LMS_Helpers::set_value_as_key(
				stm_lms_get_quiz_attempt_answers(
					$student_id,
					$quiz_id,
					array(
						'question_id',
						'user_answer',
						'correct_answer',
					),
					$attempt
				),
				'question_id'
			);

			$output[] = $quiz_data;
		}

		return $output;
	}

	/**
	 * @param $lesson array lesson meta
	 * @param $lesson_files string lesson files
	 *
	 * @return array
	 */
	private function get_only_attachments( array $lesson, string $lesson_files ): array {
		$repository_files = ( new FileMaterialRepository() )->get_files( $lesson_files );

		$exclude_ids = array_merge(
			! empty( $lesson['video_captions_ids'] ) ? maybe_unserialize( $lesson['video_captions_ids'][0] ) : array(),
			! empty( $lesson['pdf_file_ids'] ) ? maybe_unserialize( $lesson['pdf_file_ids'][0] ) : array()
		);
		$exclude_ids = array_flip( $exclude_ids );

		return array_values(
			array_filter(
				$repository_files,
				static fn( $file ) => ! isset( $exclude_ids[ $file->ID ] )
			)
		);
	}

	private function can_answers_be_randomized( string $type ): bool {
		return ! in_array( $type, self::NOT_RANDOMIZING_ANSWERS_TYPES, true );
	}
}
