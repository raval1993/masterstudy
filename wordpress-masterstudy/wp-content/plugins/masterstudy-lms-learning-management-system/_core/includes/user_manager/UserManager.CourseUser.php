<?php

use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentStudentRepository;
use MasterStudy\Lms\Repositories\CurriculumRepository;

class STM_LMS_User_Manager_Course_User {
	// phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
	public static function _student_progress( $course_id, $student_id ) {
		$curriculum = ( new CurriculumRepository() )->get_curriculum( $course_id );

		foreach ( $curriculum['materials'] as &$material ) {
			$material = array_merge( $material, self::course_material_data( $material, $student_id, $course_id ) );
		}

		$user_stats = STM_LMS_Helpers::simplify_db_array(
			stm_lms_get_user_course(
				$student_id,
				$course_id,
				array(
					'current_lesson_id',
					'progress_percent',
				)
			)
		);
		if ( empty( $user_stats['current_lesson_id'] ) ) {
			$user_stats['current_lesson_id'] = STM_LMS_Lesson::get_first_lesson( $course_id );
		}

		$lesson_type = get_post_meta( $user_stats['current_lesson_id'], 'type', true );
		if ( empty( $lesson_type ) ) {
			$lesson_type = 'text';
		}

		$user_stats['lesson_type'] = $lesson_type;

		$curriculum = array_merge( $user_stats, $curriculum );

		$curriculum['user']         = STM_LMS_User::get_current_user( $student_id );
		$curriculum['course_title'] = get_the_title( $course_id );

		return $curriculum;
	}

	public static function complete_lesson( $user_id, $course_id, $lesson_id ) {
		$user_lesson = stm_lms_get_user_lesson( $user_id, $course_id, $lesson_id );

		if ( ! empty( $user_lesson ) ) {
			stm_lms_delete_user_lesson( $user_id, $course_id, $lesson_id );
		} else {
			$end_time   = time();
			$start_time = get_user_meta( $user_id, "stm_lms_course_started_{$lesson_id}_{$course_id}", true );
			if ( empty( $start_time ) ) {
				$start_time = time();
			}
			stm_lms_add_user_lesson( compact( 'user_id', 'course_id', 'lesson_id', 'start_time', 'end_time' ) );
		}
	}

	public static function complete_assignment( $user_id, $course_id, $lesson_id, $completed ) {
		$user                  = STM_LMS_User::get_current_user( $user_id );
		$assignment_repository = new AssignmentStudentRepository();
		$last_attempt          = $assignment_repository->get_last_attempt( $course_id, $lesson_id, $user_id );
		$status                = $completed ? 'passed' : 'not_passed';

		// Add or Update Student Assignment
		if ( ! empty( $last_attempt ) ) {
			$user_assignment_id = $last_attempt['user_assignment_id'];

			$assignment_repository->update_grade( $user_assignment_id, $completed ? 100 : 0 );
			$assignment_repository->update_status( $user_assignment_id, $status );

			// Update Post Status
			wp_update_post(
				array(
					'ID'          => $user_assignment_id,
					'post_status' => $completed ? 'publish' : 'draft',
				)
			);
		} else {
			$assignment_name    = get_the_title( $lesson_id );
			$new_assignment     = array(
				'post_type'   => 'stm-user-assignment',
				'post_status' => 'publish',
				'post_title'  => "{$user['login']} on \"{$assignment_name}\"",
			);
			$user_assignment_id = wp_insert_post( $new_assignment );
			$assignment_try     = STM_LMS_Assignments::number_of_assignments( $lesson_id ) + 1;

			$assignment_repository->add_assignment(
				$user_id,
				$course_id,
				$lesson_id,
				$user_assignment_id,
				$status,
				100
			);

			update_post_meta( $user_assignment_id, 'try_num', $assignment_try );
			update_post_meta( $user_assignment_id, 'start_time', time() * 1000 );
			update_post_meta( $user_assignment_id, 'assignment_id', $lesson_id );
			update_post_meta( $user_assignment_id, 'student_id', $user_id );
			update_post_meta( $user_assignment_id, 'course_id', $course_id );
		}

		$editor_comment = $completed
			? esc_html__( 'Approved by admin', 'masterstudy-lms-learning-management-system' )
			: esc_html__( 'Declined by admin', 'masterstudy-lms-learning-management-system' );

		update_post_meta( $user_assignment_id, 'editor_comment', $editor_comment );
		update_post_meta( $user_assignment_id, 'status', $status );

		STM_LMS_Course::update_course_progress( $user_id, $course_id );
	}

	public static function complete_quiz( $user_id, $course_id, $quiz_id, $completed ) {
		if ( ! $completed ) {
			$progress = 0;
			$status   = 'failed';
			self::reset_quiz( $user_id, $course_id, $quiz_id );
			stm_lms_reset_user_answers( $course_id, $user_id );
		} else {
			$progress = 100;
			$status   = 'passed';
			stm_lms_add_user_quiz( compact( 'user_id', 'course_id', 'quiz_id', 'progress', 'status' ) );
		}
	}

	public static function course_material_data( $material, $student_id, $course_id ) {
		$previous_completed = ( isset( $completed ) ) ? $completed : 'first';
		$has_preview        = STM_LMS_Lesson::lesson_has_preview( $material['post_id'] );

		$user      = STM_LMS_User::get_current_user( $student_id );
		$user_id   = $user['id'];
		$duration  = '';
		$questions = '';
		$progress  = '';
		$quiz_info = array();

		if ( 'stm-quizzes' === $material['post_type'] ) {
			$type      = 'quiz';
			$quiz_info = STM_LMS_Helpers::simplify_db_array( stm_lms_get_user_quizzes( $user_id, $material['post_id'], $course_id, array( 'progress' ) ) );
			$completed = STM_LMS_Quiz::quiz_passed( $material['post_id'], $user_id );

			$q = get_post_meta( $material['post_id'], 'questions', true );
			if ( ! empty( $q ) ) :
				/* translators: %s: Post Type Label */
				$questions = sprintf(
					/* translators: %s: Count of Questions */
					_n(
						'%s question',
						'%s questions',
						count(
							explode(
								',',
								$q
							)
						),
						'masterstudy-lms-learning-management-system'
					),
					count(
						explode(
							',',
							$q
						)
					)
				);
			endif;

		} elseif ( 'stm-assignments' === $material['post_type'] ) {
			$type      = 'assignment';
			$completed = class_exists( '\MasterStudy\Lms\Pro\addons\assignments\Assignments' )
				&& method_exists( '\MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentStudentRepository', 'has_passed_assignment' )
				&& ( new AssignmentStudentRepository() )->has_passed_assignment( $material['post_id'], $student_id, $course_id );
		} else {
			$completed = STM_LMS_Lesson::is_lesson_completed( $user_id, $course_id, $material['post_id'] );
			$type      = get_post_meta( $material['post_id'], 'type', true );
			$duration  = get_post_meta( $material['post_id'], 'duration', true );
			$progress  = masterstudy_lms_get_user_lesson_progress( $user_id, $course_id, $material['post_id'] ) ?? 0;
		}

		if ( empty( $type ) ) {
			$type = 'lesson';
		}

		if ( empty( $duration ) ) {
			$duration = '';
		}

		$locked = str_replace(
			'prev-status-',
			'',
			apply_filters( 'stm_lms_prev_status', "{$previous_completed}", $course_id, $material['post_id'], $user_id )
		);

		$locked = ( empty( $locked ) );

		return compact( 'type', 'quiz_info', 'locked', 'completed', 'has_preview', 'duration', 'questions', 'progress' );
	}


	/*RESET ITEMS*/
	public static function reset_lesson( $user_id, $course_id, $lesson_id ) {
		stm_lms_delete_user_lesson( $user_id, $course_id, $lesson_id );
	}

	public static function reset_quiz( $user_id, $course_id, $quiz_id ) {
		stm_lms_delete_user_quiz( $user_id, $course_id, $quiz_id );
	}

	public static function reset_assignment( $user_id, $course_id, $assignment_id ) {
		$args = array(
			'posts_per_page' => - 1,
			'post_type'      => 'stm-user-assignment',
			'post_status'    => array(
				'pending',
				'publish',
				'draft',
			),
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'     => 'course_id',
					'value'   => $course_id,
					'compare' => '=',
				),
				array(
					'key'     => 'assignment_id',
					'value'   => $assignment_id,
					'compare' => '=',
				),
				array(
					'key'     => 'student_id',
					'value'   => $user_id,
					'compare' => '=',
				),
			),
		);

		$q = new WP_Query( $args );

		if ( $q->have_posts() ) {
			while ( $q->have_posts() ) {
				$q->the_post();

				wp_delete_post( get_the_ID() );

			}
		}
	}
}
