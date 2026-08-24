<?php
/**
 * @var int $post_id
 * @var int $item_id
 * @var bool $is_single_quiz
 * @var string $lesson_type
 * @var array $data
 * @var boolean $dark_mode
 */

$is_single_quiz = $is_single_quiz ?? false;

wp_enqueue_style( 'masterstudy-course-player-quiz-history' );
wp_enqueue_style( 'masterstudy-pagination' );
wp_enqueue_script( 'masterstudy-course-player-quiz-history' );
wp_localize_script(
	'masterstudy-course-player-quiz-history',
	'masterstudy_quiz_attempts',
	array(
		'course_id' => $post_id,
		'quiz_id'   => $item_id,
	)
);
?>
<div class="masterstudy-course-player-quiz-attempts-wrapper masterstudy-course-player-quiz__hide" style="display: none">
	<div class="masterstudy-course-player-quiz-attempts__header">
		<div class="masterstudy-course-player-quiz-header__column masterstudy-course-player-quiz-header__number">
			<?php echo esc_html__( '№', 'masterstudy-lms-learning-management-system' ); ?>
		</div>
		<div class="masterstudy-course-player-quiz-header__column masterstudy-course-player-quiz-header__attempt">
			<?php echo esc_html__( 'Attempt:', 'masterstudy-lms-learning-management-system' ); ?>
		</div>
		<div class="masterstudy-course-player-quiz-header__column masterstudy-course-player-quiz-header__questions">
			<?php echo esc_html__( 'Questions', 'masterstudy-lms-learning-management-system' ); ?>
		</div>
		<div class="masterstudy-course-player-quiz-header__column masterstudy-course-player-quiz-header__correct">
			<?php echo esc_html__( 'Correct', 'masterstudy-lms-learning-management-system' ); ?>
		</div>
		<div class="masterstudy-course-player-quiz-header__column masterstudy-course-player-quiz-header__incorrect">
			<?php echo esc_html__( 'Incorrect', 'masterstudy-lms-learning-management-system' ); ?>
		</div>
		<div class="masterstudy-course-player-quiz-header__column masterstudy-course-player-quiz-header__grade">
			<?php echo esc_html__( 'Grade', 'masterstudy-lms-learning-management-system' ); ?>
		</div>
		<div class="masterstudy-course-player-quiz-header__column masterstudy-course-player-quiz-header__info"></div>
	</div>
	<div class="masterstudy-course-player-quiz-attempts">
		<?php
		STM_LMS_Templates::show_lms_template(
			'components/loader',
			array(
				'is_local'  => true,
				'bordered'  => false,
				'dark_mode' => $dark_mode,
			)
		);
		?>
		<template id="masterstudy-course-player-quiz-attempts-template">
			<div class="masterstudy-course-player-quiz-attempt">
				<div class="masterstudy-course-player-quiz-attempt__number"></div>
				<div class="masterstudy-course-player-quiz-attempt__date" data-header="<?php echo /* translators: %s attempt number */esc_attr__( 'Attempt %s', 'masterstudy-lms-learning-management-system' ); ?>">
					<span class="masterstudy-course-player-quiz-attempt__date--value"></span>
					<span class="masterstudy-course-player-quiz-attempt__date--time"></span>
				</div>
				<div class="masterstudy-course-player-quiz-attempt__questions" data-header="<?php echo esc_attr__( 'Questions:', 'masterstudy-lms-learning-management-system' ); ?>"></div>
				<div class="masterstudy-course-player-quiz-attempt__correct" data-header="<?php echo esc_attr__( 'Correct:', 'masterstudy-lms-learning-management-system' ); ?>"></div>
				<div class="masterstudy-course-player-quiz-attempt__incorrect" data-header="<?php echo esc_attr__( 'Incorrect:', 'masterstudy-lms-learning-management-system' ); ?>"></div>
				<div class="masterstudy-course-player-quiz-attempt__grade" data-header="<?php echo esc_attr__( 'Grade:', 'masterstudy-lms-learning-management-system' ); ?>"></div>
				<div class="masterstudy-course-player-quiz-attempt__info">
					<div class="masterstudy-course-player-quiz-attempt__progress-wrapper">
						<div class="masterstudy-course-player-quiz-attempt__progress" data-attempt-progress>
							<span class="masterstudy-course-player-quiz-attempt__progress--bar">
								<span class="masterstudy-course-player-quiz-attempt__progress--filled"></span>
							</span>
							<span class="masterstudy-course-player-quiz-attempt__progress--status"></span>
						</div>
						<div class="masterstudy-course-player-quiz-attempt__status" data-quiz-status></div>
					</div>
				</div>
				<a href="#" class="masterstudy-course-player-quiz-attempt__details">
					<?php echo esc_html__( 'Details', 'masterstudy-lms-learning-management-system' ); ?>
				</a>
			</div>
		</template>
	</div>
	<div class="masterstudy-course-player-quiz-attempts-navigation">
		<div class="masterstudy-course-player-quiz-attempts-navigation__pagination"></div>
	</div>
</div>
