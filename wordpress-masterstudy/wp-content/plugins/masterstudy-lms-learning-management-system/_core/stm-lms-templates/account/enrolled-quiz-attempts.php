<?php
/**
 * @var int $quiz_id
 * @var int $course_id
 * */

use MasterStudy\Lms\Repositories\CoursePlayerRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lms_current_user = STM_LMS_User::get_current_user( '', true, true );

do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $lms_current_user );
wp_enqueue_style( 'masterstudy-account-main' );

wp_enqueue_script( 'masterstudy-enrolled-quizzes' );
wp_enqueue_style( 'masterstudy-account-enrolled-quiz-attempts' );
wp_enqueue_style( 'masterstudy-pagination' );
wp_enqueue_style( 'masterstudy-loader' );
wp_localize_script(
	'masterstudy-enrolled-quizzes',
	'masterstudy_quiz_attempts',
	array(
		'quiz_id'   => $quiz_id,
		'course_id' => $course_id,
	)
);

$quiz_data = ( new CoursePlayerRepository() )->get_quiz_data( $quiz_id );
?>

<div class="masterstudy-account">
	<?php do_action( 'stm_lms_admin_after_wrapper_start', $lms_current_user ); ?>
	<div class="masterstudy-account-sidebar">
		<div class="masterstudy-account-sidebar__wrapper">
			<?php do_action( 'masterstudy_account_sidebar', $lms_current_user ); ?>
		</div>
	</div>
	<div class="masterstudy-account-container">
		<div class="masterstudy-account-enrolled-quiz-attempts">
			<div class="masterstudy-account-enrolled-quiz-attempts__top-bar">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'title'         => '',
						'link'          => ms_plugin_user_account_url( 'enrolled-quizzes' ),
						'style'         => 'secondary',
						'size'          => 'sm',
						'icon_position' => 'left',
						'icon_name'     => 'arrow-left',
					)
				);
				?>
				<div class="masterstudy-account-enrolled-quiz-attempts__details">
					<?php echo esc_html( get_the_title( $quiz_id ) ); ?>
					<span>
							<?php printf( /* translators: %s Course name */ esc_html__( 'Course: %s', 'masterstudy-lms-learning-management-system' ), esc_html( get_the_title( $course_id ) ) ); ?>
					</span>
				</div>
			</div>

			<div
				class="masterstudy-account-enrolled-quiz-attempts-container<?php echo $quiz_data['has_h5p_shortcode'] ? ' masterstudy-account-enrolled-quiz-attempts-h5p-exists' : ''; ?>">
				<div class="masterstudy-account-enrolled-quiz-attempts__header">
					<div
						class="masterstudy-account-enrolled-quiz-attempts-header__column masterstudy-account-enrolled-quiz-attempts-header__number">
						<?php echo esc_html__( '№', 'masterstudy-lms-learning-management-system' ); ?>
					</div>
					<div
						class="masterstudy-account-enrolled-quiz-attempts-header__column masterstudy-account-enrolled-quiz-attempts-header__attempt">
						<?php echo esc_html__( 'Attempt:', 'masterstudy-lms-learning-management-system' ); ?>
					</div>
					<div
						class="masterstudy-account-enrolled-quiz-attempts-header__column masterstudy-account-enrolled-quiz-attempts-header__questions">
						<?php echo esc_html__( 'Questions', 'masterstudy-lms-learning-management-system' ); ?>
					</div>
					<div
						class="masterstudy-account-enrolled-quiz-attempts-header__column masterstudy-account-enrolled-quiz-attempts-header__correct">
						<?php echo esc_html__( 'Correct', 'masterstudy-lms-learning-management-system' ); ?>
					</div>
					<div
						class="masterstudy-account-enrolled-quiz-attempts-header__column masterstudy-account-enrolled-quiz-attempts-header__incorrect">
						<?php echo esc_html__( 'Incorrect', 'masterstudy-lms-learning-management-system' ); ?>
					</div>
					<div
						class="masterstudy-account-enrolled-quiz-attempts-header__column masterstudy-account-enrolled-quiz-attempts-header__info"></div>
					<div
						class="masterstudy-account-enrolled-quiz-attempts-header__column masterstudy-account-enrolled-quiz-attempts-header__details"></div>
				</div>
				<div class="masterstudy-account-enrolled-quiz-attempts-list">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/loader',
						array(
							'dark_mode' => false,
							'is_local'  => true,
						)
					);
					?>
					<template id="masterstudy-account-enrolled-quiz-attempts-template">
						<div class="masterstudy-account-enrolled-quiz-attempts-item">
							<div class="masterstudy-account-enrolled-quiz-attempts-item__number"></div>
							<div class="masterstudy-account-enrolled-quiz-attempts-item__date"
								data-header="
								<?php
								echo /* translators: %s attempt number */
									esc_attr__( 'Attempt %s', 'masterstudy-lms-learning-management-system' );
								?>
								">
								<span class="masterstudy-account-enrolled-quiz-attempts-item__date--value"></span>
								<span class="masterstudy-account-enrolled-quiz-attempts-item__date--time"></span>
							</div>
							<div class="masterstudy-account-enrolled-quiz-attempts-item__questions"
								data-header="<?php echo esc_attr__( 'Questions:', 'masterstudy-lms-learning-management-system' ); ?>"></div>
							<div class="masterstudy-account-enrolled-quiz-attempts-item__correct"
								data-header="<?php echo esc_attr__( 'Correct:', 'masterstudy-lms-learning-management-system' ); ?>"></div>
							<div class="masterstudy-account-enrolled-quiz-attempts-item__incorrect"
								data-header="<?php echo esc_attr__( 'Incorrect:', 'masterstudy-lms-learning-management-system' ); ?>"></div>
							<div class="masterstudy-account-enrolled-quiz-attempts-item__info">
								<div class="masterstudy-account-enrolled-quiz-attempts-item__progress-wrapper">
									<div class="masterstudy-account-enrolled-quiz-attempts-item__progress"
										data-attempt-progress>
											<span
												class="masterstudy-account-enrolled-quiz-attempts-item__progress--bar">
												<span
													class="masterstudy-account-enrolled-quiz-attempts-item__progress--filled"></span>
											</span>
									</div>
									<div class="masterstudy-account-enrolled-quiz-attempts-item__grade"></div>
									<div class="masterstudy-account-enrolled-quiz-attempts-item__status"
										data-quiz-status></div>
								</div>
							</div>
							<a href="#" class="masterstudy-account-enrolled-quiz-attempts-item__details">
								<?php echo esc_html__( 'Details', 'masterstudy-lms-learning-management-system' ); ?>
							</a>
						</div>
					</template>
					<template id="masterstudy-account-enrolled-quiz-attempts-no-found-template">
						<div class="masterstudy-account-enrolled-quiz-attempts-no-found__info">
							<div class="masterstudy-account-enrolled-quiz-attempts-no-found__info-icon"><span
									class="stmlms-order"></span></div>
							<div class="masterstudy-account-enrolled-quiz-attempts-no-found__info-title">
								<?php echo esc_html__( 'No enrolled quiz attempts yet', 'masterstudy-lms-learning-management-system' ); ?>
							</div>
							<div class="masterstudy-account-enrolled-quiz-attempts-no-found__info-description">
								<?php echo esc_html__( 'All information about your enrolled quiz attempts will be displayed here', 'masterstudy-lms-learning-management-system' ); ?>
							</div>
						</div>
					</template>
				</div>
			</div>

			<div class="masterstudy-account-enrolled-quiz-attempts-navigation">
				<div class="masterstudy-account-enrolled-quiz-attempts-navigation__pagination"></div>
				<div class="masterstudy-account-enrolled-quiz-attempts-navigation__per-page">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/select',
						array(
							'select_id'    => 'quiz-attempts-per-page',
							'select_width' => '170px',
							'select_name'  => 'per_page',
							'placeholder'  => esc_html__( '10 per page', 'masterstudy-lms-learning-management-system' ),
							'default'      => 10,
							'is_queryable' => false,
							'options'      => array(
								'25'  => esc_html__( '25 per page', 'masterstudy-lms-learning-management-system' ),
								'50'  => esc_html__( '50 per page', 'masterstudy-lms-learning-management-system' ),
								'75'  => esc_html__( '75 per page', 'masterstudy-lms-learning-management-system' ),
								'100' => esc_html__( '100 per page', 'masterstudy-lms-learning-management-system' ),
							),
						)
					);
					?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php do_action( 'masterstudy_after_account', $lms_current_user ); ?>
