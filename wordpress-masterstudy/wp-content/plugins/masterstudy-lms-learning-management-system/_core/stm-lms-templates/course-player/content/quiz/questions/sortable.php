<?php

/**
 * @var array $data
 * @var boolean $show_answers
 * @var array $last_quiz
 * @var int $item_id
 * @var boolean $dark_mode
 */

use MasterStudy\Lms\Utility\Question;

if ( ! empty( $data['last_answers']['user_answer'] ) ) {
	$user_answers = Question::get_last_sortable_answers( $data );
} elseif ( empty( $data['last_answers'] ) && $data['is_correct'] ) {
	$user_answers = $data['answers'];
}

$show_correct_answer = ! empty( $data['show_correct_answer'] );
$already_answered    = ! empty( $data['last_answers']['user_answer'] ) || $data['is_correct'];
?>

<div class="masterstudy-course-player-sortable <?php echo esc_attr( ( ! $show_answers && $already_answered ) ? 'masterstudy-course-player-sortable_hide' : '' ); ?> <?php echo esc_attr( $already_answered ? 'masterstudy-course-player-sortable_not-drag' : '' ); ?>">
	<input type="text" class="masterstudy-course-player-sortable__input" name="<?php echo esc_attr( $data['id'] ); ?>"/>
	<div class="masterstudy-course-player-sortable__answer">
	<?php
	foreach ( $data['answers'] as $i => $answer ) :
		$user_answer = $user_answers[ $i ] ?? null;
		$answer_text = $answer['text'];

		if ( ! empty( $user_answer['text'] ) ) {
			$user_answer_idx = array_search( $user_answer['text'], array_column( $data['answers'], 'text' ), true );
			if ( false !== $user_answer_idx ) {
				$user_answer         = $data['answers'][ $user_answer_idx ];
				$user_answer['_pos'] = $user_answer_idx + 1;
				$answer              = $user_answer;
			}
		}

		if ( $show_answers ) {
			$correct_answer_text = trim( str_replace( array( '\\(', '\\)' ), '', (string) $answer_text ) );
			$user_answer_text    = trim( str_replace( array( '\\(', '\\)' ), '', (string) ( $user_answer['text'] ?? '' ) ) );

			$data['correctly'] = ! empty( $user_answer ) && $user_answer_text === $correct_answer_text;
			$data['wrongly']   = empty( $user_answer ) || $user_answer_text !== $correct_answer_text;

			$data['answer_class'] = implode(
				' ',
				array_filter(
					array(
						$data['correctly'] ? 'masterstudy-course-player-sortable__answer-item_correct' : '',
						$data['wrongly'] ? 'masterstudy-course-player-sortable__answer-item_wrong' : '',
					)
				)
			);
		} else {
			$data['answer_class'] = '';
		}
		?>
		<div class="masterstudy-course-player-sortable__answer-item <?php echo esc_html( $data['answer_class'] ); ?>">
			<div class="masterstudy-course-player-sortable__answer-item-wrapper">
				<div class="masterstudy-course-player-sortable__answer-item-drag"></div>
				<?php if ( ! empty( $answer['_pos'] ) && $show_correct_answer ) : ?>
					<div class="masterstudy-course-player-sortable__answer-item-number"><?php echo esc_html( $answer['_pos'] ); ?></div>
				<?php endif; ?>

				<div class="masterstudy-course-player-sortable__answer-item-content">
					<?php echo esc_html( trim( $answer['text'] ) ); ?>
				</div>
				<div class="masterstudy-course-player-sortable__answer-item-actions">
					<?php if ( ! empty( $answer['explain'] ) && $show_answers && ! empty( $last_quiz ) ) : ?>
						<div class="masterstudy-course-player-sortable__answer-item-hint">
							<?php
							STM_LMS_Templates::show_lms_template(
								'components/hint',
								array(
									'content'   => $answer['explain'],
									'side'      => 'right',
									'dark_mode' => $dark_mode,
								)
							);
							?>
						</div>
					<?php endif ?>

					<?php if ( ! empty( $data['correctly'] ) ) : ?>
					<div class="masterstudy-course-player-answer__status-correct">
						<span class="masterstudy-correctly"></span>
					</div>
					<?php elseif ( ! empty( $data['wrongly'] ) ) : ?>
					<div class="masterstudy-course-player-answer__status-wrong">
						<span class="masterstudy-wrongly"></span>
					</div>
					<?php endif ?>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
	</div>
</div>
