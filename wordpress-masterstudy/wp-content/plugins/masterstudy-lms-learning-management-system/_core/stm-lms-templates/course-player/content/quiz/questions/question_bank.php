<?php
/**
 * @var array $data
 * @var array $last_answers
 * @var array $last_quiz
 * @var boolean $show_answers
 * @var string $quiz_style
 * @var array $question_banks
 * @var int $item_id
 * @var boolean $dark_mode
 */

use MasterStudy\Lms\Enums\QuestionType;
use MasterStudy\Lms\Utility\Question;

$question_bank = $question_banks[ $data['id'] ] ?? null;

$random_answers = get_post_meta( $item_id, 'random_answers', true );

if ( ! empty( $question_bank ) && $question_bank->have_posts() ) { ?>
	<div class="masterstudy-course-player-question-bank">
		<?php
		while ( $question_bank->have_posts() ) {
			$question_bank->the_post();

			$question_data         = array(
				'id'      => get_the_ID(),
				'title'   => get_the_title(),
				'content' => str_replace( '../../', site_url() . '/', stm_lms_filtered_output( get_the_content() ) ),
			);
			$question              = array_merge( $question_data, STM_LMS_Helpers::parse_meta_field( $question_data['id'] ) );
			$question['view_type'] = $question['question_view_type'] ?? '';

			if ( 'on' === $random_answers && ! $show_answers &&
				! in_array( $question['type'], array( QuestionType::FILL_THE_GAP, QuestionType::KEYWORDS ), true )
			) {
				shuffle( $question['answers'] );
			}

			wp_reset_postdata();
			?>
			<input type="hidden" name="questions_sequency[<?php echo esc_attr( $data['id'] ); ?>][]" value="<?php echo esc_attr( $question['id'] ); ?>" />
			<?php
				$sorted_ids = Question::get_sorted_answers_ids( $question['type'], $question['answers'] );
			?>
			<?php if ( '' !== $sorted_ids ) : ?>
				<input type="hidden" name="order_<?php echo esc_attr( $question['id'] ); ?>" value="<?php echo esc_attr( $sorted_ids ); ?>" />
			<?php endif; ?>

			<?php
			if ( ! empty( $question['type'] ) && ! empty( $question['answers'] ) ) {
				STM_LMS_Templates::show_lms_template(
					'course-player/content/quiz/questions/main',
					array(
						'data'         => $question,
						'last_answers' => $last_answers,
						'show_answers' => $show_answers,
						'quiz_style'   => $quiz_style,
						'item_id'      => $item_id,
						'dark_mode'    => $dark_mode,
						'last_quiz'    => $last_quiz,
					)
				);
			}
		}
		?>
	</div>
	<?php
	global $ms_question_number;
	--$ms_question_number;
}
