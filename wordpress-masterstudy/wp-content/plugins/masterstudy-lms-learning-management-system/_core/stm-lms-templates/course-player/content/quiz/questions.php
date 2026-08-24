<?php
/**
 * @var array $quiz_data
 * @var int $quiz_id
 * @var boolean $dark_mode
 * */

?>

<div class="masterstudy-course-player-quiz__questions <?php echo esc_attr( 'pagination' === $quiz_data['quiz_style'] ? 'masterstudy-course-player-quiz__questions_pagination' : '' ); ?>">
	<?php
	global $ms_question_number;
	$ms_question_number = 1;
	foreach ( $quiz_data['questions'] as $index => $question ) {
		STM_LMS_Templates::show_lms_template(
			'course-player/content/quiz/questions/main',
			array(
				'data'           => $question,
				'last_answers'   => $quiz_data['last_answers'],
				'show_answers'   => $quiz_data['show_answers'],
				'quiz_style'     => $quiz_data['quiz_style'],
				'last_quiz'      => $quiz_data['last_quiz'],
				'question_banks' => $quiz_data['question_banks'] ?? array(),
				'item_id'        => $quiz_id,
				'dark_mode'      => $dark_mode,
			)
		);
	}
	?>
</div>
