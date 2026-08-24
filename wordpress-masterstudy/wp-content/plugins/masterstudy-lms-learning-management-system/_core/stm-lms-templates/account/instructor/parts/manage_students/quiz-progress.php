<?php
/**
 * @var $material
 * @var $student_id
 * @var $course_id
 * */

use MasterStudy\Lms\Pro\AddonsPlus\Grades\Services\GradeCalculator;
use MasterStudy\Lms\Repositories\CoursePlayerRepository;

if ( 'quiz' === $material['type'] ) {
	do_action( 'masterstudy_lms_course_player_register_assets' );
	$quiz_id     = $material['post_id'];
	$quiz_data   = ( new CoursePlayerRepository() )->get_quiz_data( $quiz_id, $student_id );
	$is_answered = ! empty( $quiz_data['last_answers'] );
	?>

	<div class="masterstudy-student-progress-list__item-content<?php echo esc_attr( ! empty( $quiz_data ) && $is_answered ? ' masterstudy-student-progress-list__item-content_completed' : '' ); ?>">
		<?php
		$quiz_data['show_answers'] = true;
		$passing_grade             = intval( $data['passing_grade'] ?? 0 );
		$grade                     = is_ms_lms_addon_enabled( 'grades' ) ? GradeCalculator::get_instance()->get_passing_grade( $passing_grade ) : round( $passing_grade, 1 ) . '%';
		?>
		<div class="masterstudy-student-progress__quiz">
			<div class="masterstudy-student-progress-list__content" style="display: block;">
				<div class="masterstudy-student-progress-list__item-content_result<?php echo esc_attr( $is_answered ? ' masterstudy-student-progress-list__item_hidden' : '' ); ?>">
					<?php
					STM_LMS_Templates::show_lms_template(
						'course-player/content/quiz/result',
						array(
							'progress'           => 100,
							'passing_grade'      => $passing_grade,
							'passing_grade_text' => $grade,
							'questions_quantity' => intval( $quiz_data['questions_quantity'] ?? 0 ),
							'correct_answers'    => 0,
							'incorrect_answers'  => 0,
							'show_emoji'         => $quiz_data['show_emoji'],
							'emoji_name'         => STM_LMS_Options::get_option( 'assignments_quiz_passed_emoji' ),
							'created_at'         => $quiz_data['created_at'] ?? null,
							'is_retakable'       => $quiz_data['is_retakable'],
							'attempts_left'      => $quiz_data['attempts_left'] ?? 0,
							'quiz_attempts'      => $data['quiz_attempts'] ?? false,
							'course_id'          => $course_id,
							'quiz_id'            => $quiz_id,
							'attempts'           => array(),
							'quiz_data'          => $quiz_data,
						)
					);
					?>
				</div>
				<div class="masterstudy-student-progress-list__item-quiz<?php echo esc_attr( ! $is_answered ? ' masterstudy-student-progress-list__item_hidden' : '' ); ?>">
					<?php
					STM_LMS_Templates::show_lms_template(
						'course-player/content/quiz/main',
						array(
							'dark_mode'   => false,
							'post_id'     => $course_id,
							'data'        => $quiz_data,
							'item_id'     => $quiz_id,
							'lesson_type' => $material['lesson_type'],
						)
					);
					?>
				</div>
				<div class="masterstudy-student-progress-list__item-no-answer<?php echo esc_attr( $is_answered ? ' masterstudy-student-progress-list__item_hidden' : '' ); ?>">
					<?php esc_html_e( 'Quiz has been completed by instructor.', 'masterstudy-lms-learning-management-system' ); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="masterstudy-student-progress-list__item-content_empty">
		<?php esc_html_e( 'No quizzes yet...', 'masterstudy-lms-learning-management-system' ); ?>
	</div>
	<?php
}
