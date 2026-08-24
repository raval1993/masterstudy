<?php
/**
 * @var string $type
 * @var array $answers
 * @var array $user_answer
 * @var string $question
 * @var string $question_explanation
 * @var string $question_hint
 * @var string $random_answers
 */

stm_lms_register_style( 'sortable_question_admin' );

$user_answers = array();
if ( ! empty( $user_answer['user_answer'] ) ) {
	$user_answers = explode( '[stm_lms_sep]', str_replace( '[stm_lms_sortable]', '', $user_answer['user_answer'] ) );
}
?>

<div class="stm_lms_question_sortable stm_lms_question_sortable_not-drag">
	<div class="stm_lms_question_sortable__answer">
	<?php
	foreach ( $answers as $i => $answer ) :
		$user_answer = $user_answers[ $i ] ?? null;
		$answer_text = $answer['text'];

		if ( ! empty( $user_answer ) ) {
			$user_answer_idx = array_search( $user_answer, array_column( $answers, 'text' ), true );
			if ( false !== $user_answer_idx ) {
				$user_answer         = $answers[ $user_answer_idx ];
				$user_answer['_pos'] = $user_answer_idx + 1;
			}
			$answer = $user_answer;
		}

		$correct_answer_text = trim( str_replace( array( '\\(', '\\)' ), '', (string) $answer_text ) );
		$user_answer_text    = trim( str_replace( array( '\\(', '\\)' ), '', (string) $user_answer['text'] ) );

		$data['correctly'] = ! empty( $user_answer ) && $user_answer_text === $correct_answer_text;
		$data['wrongly']   = empty( $user_answer ) || $user_answer_text !== $correct_answer_text;

		$data['answer_class'] = implode(
			' ',
			array_filter(
				array(
					$data['correctly'] ? 'stm_lms_question_sortable__answer-item_correct' : '',
					$data['wrongly'] ? 'stm_lms_question_sortable__answer-item_wrong' : '',
				)
			)
		);
		?>
		<div class="stm_lms_question_sortable__answer-item <?php echo esc_html( $data['answer_class'] ); ?>">
			<div class="stm_lms_question_sortable__answer-item-wrapper">
				<div class="stm_lms_question_sortable__answer-item-checked">
					<span class="stmlms-check-3"></span>
				</div>
				<div class="stm_lms_question_sortable__answer-item-content">
					<?php echo esc_html( trim( $answer['text'] ) ); ?>
				</div>
				<div class="stm_lms_question_sortable__answer-item-actions">
					<?php if ( ! empty( $answer['explain'] ) ) : ?>
						<div class="stm-lms-single-answer__hint">
							<i class="fa fa-info"></i>
							<div class="stm-lms-single-answer__hint_text">
								<div class="inner">
									<?php echo wp_kses_post( $answer['explain'] ); ?>
								</div>
							</div>
						</div>
					<?php endif ?>

				</div>
			</div>
		</div>
	<?php endforeach; ?>
	</div>
</div>
