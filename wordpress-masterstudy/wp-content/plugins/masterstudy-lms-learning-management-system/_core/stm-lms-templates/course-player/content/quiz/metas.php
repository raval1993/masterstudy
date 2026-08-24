<?php
/**
 * @var int $duration_value
 * @var string $duration_measure
 * @var int $passing_grade
 * @var string $passing_grade_text
 * @var int $questions_quantity
 * @var int $allowed_attempts
 * @var string $quiz_attempts
 * @var boolean $show_result
 * @var boolean $has_attempts
 * @var boolean $show_history
 */
?>

<ul class="masterstudy-course-player-quiz__content-meta">
	<?php if ( ( ! $show_history || ! $show_result ) && $questions_quantity > 0 ) { ?>
		<li class="masterstudy-course-player-quiz__content-meta-item masterstudy-course-player-quiz__content-meta-item_questions">
			<?php echo esc_html__( 'Questions count', 'masterstudy-lms-learning-management-system' ); ?>:
			<span class="masterstudy-course-player-quiz__content-meta-item-title">
				<?php echo esc_html( $questions_quantity ); ?>
			</span>
		</li>
		<?php
	}
	if ( ( ! $show_history || ! $show_result ) && $passing_grade > 0 ) {
		?>
		<li class="masterstudy-course-player-quiz__content-meta-item masterstudy-course-player-quiz__content-meta-item_grade">
		<?php echo esc_html__( 'Passing grade', 'masterstudy-lms-learning-management-system' ); ?>:
			<span class="masterstudy-course-player-quiz__content-meta-item-title">
				<?php echo esc_html( $passing_grade_text ); ?>
			</span>
		</li>
		<?php
	}
	if ( $duration_value > 0 ) {
		?>
		<li class="masterstudy-course-player-quiz__content-meta-item masterstudy-course-player-quiz__content-meta-item_duration">
		<?php echo esc_html__( 'Time limit', 'masterstudy-lms-learning-management-system' ); ?>:
			<span class="masterstudy-course-player-quiz__content-meta-item-title">
				<?php echo esc_html( masterstudy_lms_time_elapsed_string_e( $duration_value, $duration_measure ) ); ?>
			</span>
		</li>
		<?php
	}
	if ( $allowed_attempts > 0 && 'limited' === $quiz_attempts && ( ! $show_history || ! $has_attempts ) ) {
		?>
		<li class="masterstudy-course-player-quiz__content-meta-item masterstudy-course-player-quiz__content-meta-item_allowed_attempts">
			<?php echo esc_html__( 'Number of allowed attempts', 'masterstudy-lms-learning-management-system' ); ?>:
			<span class="masterstudy-course-player-quiz__content-meta-item-title">
				<?php echo esc_html( $allowed_attempts ); ?>
			</span>
		</li>
	<?php } ?>
</ul>
