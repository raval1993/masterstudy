<?php
/**
 * @var bool $is_retakable
 * @var int $progress
 * @var int $passing_grade
 * @var string $passing_grade_text
 * @var int $questions_quantity
 * @var int $correct_answers
 * @var int $incorrect_answers
 * @var bool $show_emoji
 * @var string $emoji_name
 * @var int $attempts_left
 * @var string $quiz_attempts
 * @var array $created_at
 * @var array $attempts
 * @var int $course_id
 * @var int $quiz_id
 * @var array $quiz_data
 */

use MasterStudy\Lms\Pro\AddonsPlus\Grades\Services\GradeDisplay;
?>
<div class="masterstudy-course-player-quiz__result-container">
	<div class="masterstudy-course-player-quiz__result-inner <?php echo esc_attr( ! empty( $quiz_data['show_history'] ) ? 'masterstudy-course-player-quiz__result-inner-history' : '' ); ?>">
		<?php
		if ( ! empty( $quiz_data['show_history'] ) ) :
			$attempt_count = count( $attempts );
			$show_attempts = $attempt_count > 1;
			?>
			<div class="masterstudy-course-player-quiz__attempt-info <?php echo esc_attr( $show_attempts ? 'masterstudy-course-player-quiz__attempt-info-center' : '' ); ?>">
				<div class="masterstudy-course-player-quiz__attempt-column">
					<div class="masterstudy-course-player-quiz__attempt-title">
					<?php esc_html_e( 'Attempt:', 'masterstudy-lms-learning-management-system' ); ?>
					</div>
					<div class="masterstudy-course-player-quiz__attempt-date">
						<span class="masterstudy-course-player-quiz__attempt-date--value">
							<?php echo ! empty( $created_at['date'] ) ? esc_html( $created_at['date'] ) : ''; ?>
						</span>
						<span class="masterstudy-course-player-quiz__attempt-date--time">
							<?php echo ! empty( $created_at['time'] ) ? esc_html( $created_at['time'] ) : ''; ?>
						</span>
					</div>
				</div>
				<div class="masterstudy-course-player-quiz__attempt-column masterstudy-course-player-quiz__attempt-info-column">
					<span class="masterstudy-course-player-quiz__attempt-item masterstudy-course-player-quiz__attempt-item-questions">
					<?php printf( '%s: <span>%s</span>', esc_html__( 'Questions', 'masterstudy-lms-learning-management-system' ), esc_html( $questions_quantity ) ); ?>
					</span>
					<span class="masterstudy-course-player-quiz__attempt-item masterstudy-course-player-quiz__attempt-item-correct">
					<?php printf( '%s: <span>%s</span>', esc_html__( 'Correct', 'masterstudy-lms-learning-management-system' ), esc_html( $correct_answers ) ); ?>
					</span>
					<div class="masterstudy-course-player-quiz__attempt-item masterstudy-course-player-quiz__attempt-item-incorrect">
					<?php printf( '%s: <span>%s</span>', esc_html__( 'Incorrect', 'masterstudy-lms-learning-management-system' ), esc_html( $incorrect_answers ) ); ?>
					</div>
				</div>
			<?php if ( $show_attempts ) : ?>
				<div class="masterstudy-course-player-quiz__attempt-column masterstudy-course-player-quiz__attempt-select-column">
					<span class="masterstudy-course-player-quiz__attempt-select-wrapper" data-default="<?php echo esc_attr( reset( $attempts )['user_quiz_id'] ); ?>" data-course_id="<?php echo esc_attr( $course_id ); ?>" data-quiz_id="<?php echo esc_attr( $quiz_id ); ?>">
						<?php
						$retry_reduction = $attempt_count;

						STM_LMS_Templates::show_lms_template(
							'components/select',
							array(
								'select_id'    => 'lms-attempt',
								'select_width' => '120px',
								'select_name'  => 'lms-attempt',
								'placeholder'  => sprintf(
									/* translators: %d attempt index */
									esc_html__( 'Attempt %d', 'masterstudy-lms-learning-management-system' ),
									$retry_reduction
								),
								'default'      => reset( $attempts )['user_quiz_id'],
								'is_queryable' => false,
								'options'      => array_combine(
									array_column( $attempts, 'user_quiz_id' ),
									array_map(
										function () use ( &$retry_reduction ) {
											return sprintf(
												/* translators: %d attempt index */
												esc_html__( 'Attempt %d', 'masterstudy-lms-learning-management-system' ),
												esc_html( $retry_reduction-- )
											);
										},
										$attempts
									)
								),
							)
						);
						?>
					</span>
				</div>
			<?php endif; ?>
			</div>
		<?php endif; ?>
		<div class="masterstudy-course-player-quiz__result <?php echo esc_attr( $progress < $passing_grade ? 'masterstudy-course-player-quiz__result_failed' : '' ); ?>">
			<?php if ( ! ! empty( $quiz_data['show_history'] ) ) : ?>
				<h2 class="masterstudy-course-player-quiz__result-title"><?php esc_html_e( 'Result', 'masterstudy-lms-learning-management-system' ); ?></h2>
			<?php endif; ?>
			<div class="masterstudy-course-player-quiz__result-wrapper">
				<span class="masterstudy-course-player-quiz__result-progress">
					<?php
					if ( is_ms_lms_addon_enabled( 'grades' ) ) {
						echo esc_html( GradeDisplay::get_instance()->simple_render( $progress, true ) );
					} else {
						echo esc_html( round( $progress, 1 ) . '%' );
					}
					?>
				</span>
				<?php if ( $show_emoji && ! empty( $emoji_name ) ) { ?>
					<p class="masterstudy-course-player-quiz__emoji"><?php echo esc_html( $emoji_name ); ?></p>
				<?php } ?>
				<div class="masterstudy-course-player-quiz__result-info">
					<span class="masterstudy-course-player-quiz__result-answers">
						<?php
						if ( $questions_quantity > 0 ) {
							if ( is_rtl() ) {
								/* translators: %d: number */
								printf( wp_kses_post( __( '<strong>%2$d</strong> out of <strong>%1$d</strong> questions answered correctly', 'masterstudy-lms-learning-management-system' ) ), esc_html( $questions_quantity ), esc_html( $correct_answers ) );
							} else {
								/* translators: %d: number */
								printf( wp_kses_post( __( '<strong>%1$d</strong> out of <strong>%2$d</strong> questions answered correctly', 'masterstudy-lms-learning-management-system' ) ), esc_html( $correct_answers ), esc_html( $questions_quantity ) );
							}
						}
						?>
					</span>
					<div>
						<?php if ( ! empty( $quiz_data['show_history'] ) && ! empty( $passing_grade ) ) { ?>
							<span class="masterstudy-course-player-quiz__result-minimum-passing-grade">
								<?php printf( /* translators: %d: number */ wp_kses_post( __( 'Passing grade <strong>%s</strong>', 'masterstudy-lms-learning-management-system' ) ), esc_html( $passing_grade_text ) ); ?>
							</span>
						<?php } ?>
						<?php if ( $attempts_left >= 0 && 'limited' === $quiz_attempts && $progress < $passing_grade ) { ?>
							<span class="masterstudy-course-player-quiz__result-attempts-left">
								<?php
								printf(
								/* translators: %d: number */
									wp_kses_post( _n( '<strong>%d</strong> attempt left', '<strong>%d</strong> attempts left', $attempts_left, 'masterstudy-lms-learning-management-system' ) ),
									esc_html( $attempts_left )
								);
								?>
							</span>
						<?php } ?>
					</div>
				</div>
				<?php if ( empty( $quiz_data['has_h5p_shortcode'] ) && $is_retakable && ( $progress < $passing_grade || $quiz_data['retry_after_passing'] ) ) : ?>
					<div class="masterstudy-course-player-quiz__result-retake">
						<?php
						STM_LMS_Templates::show_lms_template(
							'components/button',
							array(
								'id'            => 'quiz-result-retake',
								'title'         => __( 'Retake', 'masterstudy-lms-learning-management-system' ),
								'link'          => '#retake',
								'style'         => 'primary',
								'size'          => 'sm',
								'icon_position' => '',
								'icon_name'     => '',
							)
						);
						?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
