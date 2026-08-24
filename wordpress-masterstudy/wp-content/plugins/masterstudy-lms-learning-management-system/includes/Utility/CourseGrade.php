<?php

namespace MasterStudy\Lms\Utility;

use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Repositories\CurriculumMaterialRepository;

final class CourseGrade {
	public static function update_user_course_grade( int $user_id, int $course_id, ?int $user_course_id = null ): void {
		global $wpdb;

		// Database table names
		$assignments_table  = $wpdb->prefix . 'stm_lms_user_assignments';
		$user_courses_table = $wpdb->prefix . 'stm_lms_user_courses';
		$user_quizzes_table = $wpdb->prefix . 'stm_lms_user_quizzes';

		// Get & Filter Course Materials to only include quizzes and assignments
		$course_materials = ( new CurriculumMaterialRepository() )->get_course_materials( $course_id, false );
		$quiz_ids         = array();
		$assignment_ids   = array();
		$materials_count  = 0;
		$quiz_progress    = 0;
		$assignment_grade = 0;

		foreach ( $course_materials as $material ) {
			if ( PostType::QUIZ === $material['post_type'] ) {
				$quiz_ids[] = $material['post_id'];
				$materials_count++;
			} elseif ( PostType::ASSIGNMENT === $material['post_type'] ) {
				$assignment_ids[] = $material['post_id'];
				$materials_count++;
			}
		}

		if ( 1 > $materials_count ) {
			return;
		}

		if ( ! $user_course_id ) {
			$user_course_id = $wpdb->get_var(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"SELECT user_course_id FROM $user_courses_table WHERE user_id = %d AND course_id = %d ORDER BY user_course_id DESC LIMIT 1",
					$user_id,
					$course_id
				)
			);
		}

		// Get SUM of Quizzes progress for this user-course pair
		if ( ! empty( $quiz_ids ) ) {
			$quiz_ids_placeholder = implode( ',', array_fill( 0, count( $quiz_ids ), '%d' ) );
			$quiz_progress        = $wpdb->get_results(
				$wpdb->prepare(
					// phpcs:disable
					"SELECT progress FROM $user_quizzes_table AS quizzes
					WHERE user_id = %d AND quiz_id IN ($quiz_ids_placeholder)
					AND user_quiz_id = (
						SELECT MAX(user_quiz_id) FROM $user_quizzes_table AS sub_quizzes
						WHERE sub_quizzes.user_id = quizzes.user_id AND sub_quizzes.quiz_id = quizzes.quiz_id
					) ORDER BY user_quiz_id DESC",
					// phpcs:enable
					array_merge(
						array( $user_id ),
						$quiz_ids
					)
				),
				ARRAY_A
			);

			$quiz_progress = array_sum( array_column( $quiz_progress ?? array(), 'progress' ) );
		}

		// Get SUM of Assignments progress for this user-course pair
		if ( ! empty( $assignment_ids ) ) {
			$assignment_ids_placeholder = implode( ',', array_fill( 0, count( $assignment_ids ), '%d' ) );
			$assignment_grade           = $wpdb->get_results(
				// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
				$wpdb->prepare(
					// phpcs:disable
					"SELECT grade FROM $assignments_table AS assignments
					WHERE user_id = %d AND course_id = %d AND assignment_id IN ($assignment_ids_placeholder) AND (status = 'passed' OR status = 'not_passed')
					AND updated_at = (
						SELECT MAX(updated_at) FROM $assignments_table
						WHERE user_id = assignments.user_id AND course_id = assignments.course_id
						AND assignment_id = assignments.assignment_id AND (status = 'passed' OR status = 'not_passed')
					) ORDER BY updated_at DESC",
					// phpcs:enable
					array_merge(
						array( $user_id, $course_id ),
						$assignment_ids
					)
				),
				ARRAY_A
			);

			$assignment_grade = array_sum( array_column( $assignment_grade ?? array(), 'grade' ) );
		}

		$final_grade = ( $quiz_progress + $assignment_grade ) / $materials_count;
		$final_grade = min( 100, max( 0, ceil( $final_grade ) ) );

		// Update final grade for this user-course pair
		$wpdb->update(
			$user_courses_table,
			array(
				'final_grade' => (int) $final_grade,
				'is_gradable' => 1,
			),
			array( 'user_course_id' => $user_course_id ),
			array( '%d', '%d' ),
			array( '%d' )
		);
	}
}
