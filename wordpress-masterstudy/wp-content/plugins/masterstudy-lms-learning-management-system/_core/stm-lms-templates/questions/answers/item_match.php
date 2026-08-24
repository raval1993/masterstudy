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

use MasterStudy\Lms\Utility\Question;

$question_id = get_the_ID();

stm_lms_register_style( 'item_match_question' );

if ( ! empty( $user_answer['questions_order'] ) && 'on' === $random_answers ) {
	$answers = Question::sort_answers_by_order( $answers, $user_answer['questions_order'], $type );
}

$user_answers = array();
if ( ! empty( $user_answer['user_answer'] ) ) {
	$user_answers = explode( '[stm_lms_sep]', str_replace( '[stm_lms_item_match]', '', $user_answer['user_answer'] ) );
}

?>
<div class="stm_lms_question_item_match">
	<div class="stm_lms_question_item_match_row">
		<div class="stm_lms_question_item_match_col">
			<div class="stm_lms_question_item_match__questions">
				<?php foreach ( $answers as $answer ) : ?>
					<div class="stm_lms_question_item_match__single">
						<?php echo wp_kses_post( trim( str_replace( array( '\\(', '\\)' ), '', (string) ( $answer['question'] ?? '' ) ) ) ); ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="stm_lms_question_item_match_col">
			<div class="stm_lms_question_item_match__answers">
				<?php
				foreach ( $answers as $i => $correct_answer ) :
					if ( empty( $user_answers[ $i ] ) ) {
						?>
						<div class="stm_lms_question_item_match__answer incorrect">
							<div class="stm_lms_question_item_match__match"></div>
							<?php if ( ! empty( $correct_answer['explain'] ) ) : ?>
								<div class="stm-lms-single-answer__hint">
									<i class="stmlms-info"></i>
									<div class="stm-lms-single-answer__hint_text">
										<div class="inner">
											<?php echo wp_kses_post( $correct_answer['explain'] ); ?>
										</div>
									</div>
								</div>
							<?php endif; ?>
						</div>
						<?php
						continue;
					}

					$user_answer_text    = trim( str_replace( array( '\\(', '\\)' ), '', (string) $user_answers[ $i ] ) );
					$correct_answer_text = trim( str_replace( array( '\\(', '\\)' ), '', (string) $correct_answer['text'] ) );
					$is_correct          = ( strtolower( $user_answer_text ) === strtolower( $correct_answer_text ) ) ? 'correct' : 'incorrect';
					?>
					<div class="stm_lms_question_item_match__answer <?php echo esc_attr( $is_correct ); ?>">
						<?php if ( ! empty( $user_answers[ $i ] ) ) : ?>
							<div class="stm_lms_question_item_match__match">
								<?php echo esc_html( stripslashes( $user_answers[ $i ] ) ); ?>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $correct_answer['explain'] ) ) : ?>
							<div class="stm-lms-single-answer__hint">
								<i class="stmlms-info"></i>
								<div class="stm-lms-single-answer__hint_text">
									<div class="inner">
										<?php echo wp_kses_post( $correct_answer['explain'] ); ?>
									</div>
								</div>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>
