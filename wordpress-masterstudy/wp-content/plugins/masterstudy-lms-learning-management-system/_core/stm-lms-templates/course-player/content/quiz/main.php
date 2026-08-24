<?php
/**
 * @var int $post_id
 * @var int $item_id
 * @var bool $is_single_quiz
 * @var string $lesson_type
 * @var array $data
 * @var boolean $dark_mode
 */

use MasterStudy\Lms\Enums\QuestionType;
use MasterStudy\Lms\Pro\AddonsPlus\Grades\Services\GradeCalculator;
use MasterStudy\Lms\Utility\Question;

$is_single_quiz = $is_single_quiz ?? false;

wp_enqueue_style( 'masterstudy-course-player-quiz' );
wp_enqueue_script( 'masterstudy-course-player-quiz-touch' );
wp_enqueue_script( 'masterstudy-course-player-quiz' );
wp_localize_script(
	'masterstudy-course-player-quiz',
	'quiz_data',
	array(
		'start_nonce'    => wp_create_nonce( 'start_quiz' ),
		'submit_nonce'   => wp_create_nonce( 'user_answers' ),
		'h5p_nonce'      => wp_create_nonce( 'stm_lms_add_h5p_result' ),
		'ajax_url'       => admin_url( 'admin-ajax.php' ),
		'duration'       => intval( $data['duration'] ),
		'is_single_quiz' => $is_single_quiz,
		'quiz_id'        => intval( $item_id ),
		'course_id'      => intval( $post_id ),
		'random_answers' => $data['random_answers'],
		'confirmation'   => esc_html__( 'Once you submit, you will no longer be able to change your answers. Are you sure you want to submit the quiz?', 'masterstudy-lms-learning-management-system' ),
	)
);

STM_LMS_Templates::show_lms_template(
	'components/alert',
	array(
		'id'                  => 'quiz_alert',
		'title'               => esc_html__( 'Submit quiz', 'masterstudy-lms-learning-management-system' ),
		'text'                => esc_html__( 'Once you submit, you will no longer be able to change your answers. Are you sure you want to submit the quiz?', 'masterstudy-lms-learning-management-system' ),
		'submit_button_text'  => esc_html__( 'Submit', 'masterstudy-lms-learning-management-system' ),
		'cancel_button_text'  => esc_html__( 'Cancel', 'masterstudy-lms-learning-management-system' ),
		'submit_button_style' => 'primary',
		'cancel_button_style' => 'tertiary',
		'dark_mode'           => $dark_mode,
	)
);
$passing_grade = intval( $data['passing_grade'] ?? 0 );
$grade         = is_ms_lms_addon_enabled( 'grades' ) ? GradeCalculator::get_instance()->get_passing_grade( $passing_grade ) : round( $passing_grade, 1 ) . '%';

?>

<div
	class="masterstudy-course-player-quiz <?php echo esc_attr( $data['show_answers'] ? 'masterstudy-course-player-quiz_show-answers' : '' ); ?>">
	<?php
	$data['last_answers'] = ! empty( $data['last_answers'] ) ? $data['last_answers'] : array();
	if ( ! empty( $data['last_quiz'] ) ) {
		STM_LMS_Templates::show_lms_template(
			'course-player/content/quiz/result',
			array(
				'is_retakable'       => $data['is_retakable'],
				'progress'           => intval( $data['progress'] ),
				'passing_grade_text' => $grade,
				'passing_grade'      => $passing_grade,
				'questions_quantity' => count( $data['last_answers'] ),
				'correct_answers'    => count( array_filter( $data['last_answers'], fn( $item ) => isset( $item['correct_answer'] ) && '1' === $item['correct_answer'] ) ),
				'incorrect_answers'  => count( array_filter( $data['last_answers'], fn( $item ) => isset( $item['correct_answer'] ) && '0' === $item['correct_answer'] ) ),
				'show_emoji'         => $data['show_emoji'],
				'emoji_name'         => $data['emoji_name'],
				'quiz_attempts'      => $data['quiz_attempts'] ?? false,
				'attempts_left'      => $data['attempts_left'] ?? 0,
				'created_at'         => $data['created_at'] ?? null,
				'course_id'          => $post_id,
				'quiz_id'            => $item_id,
				'quiz_data'          => $data,
				'attempts'           => ! empty( $data['show_attempts'] ) ? stm_lms_get_quiz_all_attempts(
					get_current_user_id(),
					$post_id,
					$item_id
				) : array(),
			)
		);
	}
	if ( ! $data['show_answers'] || ! $data['passed'] ) {
		?>
		<div class="masterstudy-course-player-quiz__content">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo stm_lms_filtered_output( $data['content'] );
			?>
		</div>
		<?php
		if ( ! empty( $data['passing_grade'] ) || ! empty( $data['questions_for_nav'] ) || ! empty( $data['duration_value'] ) ) {
			STM_LMS_Templates::show_lms_template(
				'course-player/content/quiz/metas',
				array(
					'passing_grade_text' => $grade,
					'passing_grade'      => $passing_grade,
					'questions_quantity' => intval( $data['questions_for_nav'] ?? 0 ),
					'duration_value'     => intval( $data['duration_value'] ?? 0 ),
					'duration_measure'   => $data['duration_measure'] ?? '',
					'allowed_attempts'   => $data['attempts'] ?? 0,
					'quiz_attempts'      => $data['quiz_attempts'] ?? 0,
					'show_result'        => ! empty( $data['last_quiz'] ),
					'show_history'       => $data['show_history'],
					'has_attempts'       => $data['has_attempts'] ?? false,
				)
			);
		}
	}
	if ( ! empty( $data['questions'] ) ) {
		if ( empty( $data['last_quiz'] ) ) {
			?>
			<div class="masterstudy-course-player-quiz__start-quiz">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'title'         => __( 'Start Quiz', 'masterstudy-lms-learning-management-system' ),
						'type'          => '',
						'link'          => '#',
						'style'         => 'primary',
						'size'          => 'sm',
						'id'            => 'start-quiz',
						'icon_position' => '',
						'icon_name'     => '',
					)
				);
				?>
			</div>
			<?php
		}
		?>
		<form
			class="masterstudy-course-player-quiz__form <?php echo esc_attr( ! $data['show_answers'] || empty( $data['last_quiz'] ) ? 'masterstudy-course-player-quiz__form_hide' : '' ); ?>">
			<input type="hidden" name="source" value="<?php echo intval( $post_id ); ?>">
			<?php
			STM_LMS_Templates::show_lms_template(
				'course-player/content/quiz/questions',
				array(
					'dark_mode' => $dark_mode,
					'quiz_data' => $data,
					'quiz_id'   => $item_id,
				)
			);
			?>
			<?php if ( 'pagination' === $data['quiz_style'] && $data['questions_for_nav'] > 1 ) { ?>
				<div class="masterstudy-course-player-quiz__pagination">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/pagination',
						array(
							'max_visible_pages' => 5,
							'total_pages'       => $data['questions_for_nav'],
							'current_page'      => 1,
							'done_indicator'    => true,
							'dark_mode'         => $dark_mode,
							'item_width'        => 50,
						)
					);
					?>
				</div>
				<?php
			}
			if ( ! $data['passed'] || $data['retry_after_passing'] || $is_single_quiz ) {
				?>
				<input type="hidden" name="question_ids" value="<?php echo esc_attr( implode( ',', array_column( $data['questions'], 'id' ) ) ); ?>"/>
				<?php if ( ! empty( $data['required_answers_ids'] ) ) : ?>
					<input type="hidden" name="required_answer_ids" value="<?php echo esc_attr( implode( ',', $data['required_answers_ids'] ) ); ?>"/>
				<?php endif; ?>
				<input type="hidden" name="action" value="stm_lms_user_answers"/>
				<input type="hidden" name="quiz_id" value="<?php echo intval( $item_id ); ?>"/>
				<input type="hidden" name="course_id" value="<?php echo intval( $post_id ); ?>"/>
				<?php foreach ( $data['questions'] as $question ) : ?>
					<?php
					$sorted_ids = Question::get_sorted_answers_ids( $question['type'], $question['answers'] );

					if ( '' === $sorted_ids ) {
						continue;
					}
					?>
					<input type="hidden" name="order_<?php echo esc_attr( $question['id'] ); ?>" value="<?php echo esc_attr( $sorted_ids ); ?>" />
				<?php endforeach ?>
			<?php } ?>
		</form>
		<?php
	}
	?>
</div>
