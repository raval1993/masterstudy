<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lms_current_user = STM_LMS_User::get_current_user( '', true, true );

do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $lms_current_user );
wp_enqueue_style( 'masterstudy-account-main' );

wp_enqueue_style( 'masterstudy-pagination' );
wp_enqueue_style( 'masterstudy-account-enrolled-quizzes' );
wp_enqueue_style( 'masterstudy-loader' );

wp_enqueue_script( 'masterstudy-enrolled-quizzes' );

wp_localize_script(
	'masterstudy-enrolled-quizzes',
	'enrolled_data',
	array(
		'grades_table'            => STM_LMS_Options::get_option( 'grades_table', null ),
		'is_grades_addon_enabled' => is_ms_lms_addon_enabled( 'grades' ),
	)
)
?>

<div class="masterstudy-account">
	<?php do_action( 'stm_lms_admin_after_wrapper_start', $lms_current_user ); ?>
	<div class="masterstudy-account-sidebar">
		<div class="masterstudy-account-sidebar__wrapper">
			<?php do_action( 'masterstudy_account_sidebar', $lms_current_user ); ?>
		</div>
	</div>
	<div class="masterstudy-account-container">
		<div class="masterstudy-account-enrolled-quizzes">
			<div class="masterstudy-account-enrolled-quizzes__header-title">
				<h3><?php echo esc_html__( 'Enrolled Quizzes', 'masterstudy-lms-learning-management-system' ); ?></h3>
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/search-input',
					array(
						'placeholder'     => __( 'Search course or quiz', 'masterstudy-lms-learning-management-system' ),
						'classes_wrapper' => 'masterstudy-account-enrolled-quizzes-search',
						'classes_input'   => 'masterstudy-account-enrolled-quizzes-search__input',
					)
				);
				?>
			</div>
			<div class="masterstudy-account-enrolled-quizzes-container">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/loader',
					array(
						'dark_mode' => false,
						'is_local'  => true,
					)
				);
				?>
				<template id="masterstudy-account-enrolled-quizzes-template">
					<div class="masterstudy-account-enrolled-quizzes__course">
						<div class="masterstudy-account-enrolled-quizzes__header">
							<div class="masterstudy-account-enrolled-quizzes-course__label">
								<?php echo esc_html__( 'Course:', 'masterstudy-lms-learning-management-system' ); ?>
							</div>
							<div class="masterstudy-account-enrolled-quizzes-course__value">
								<a href="#" class="masterstudy-account-enrolled-quizzes-course__link"></a>
							</div>
						</div>
						<div class="masterstudy-account-enrolled-quizzes-items">
							<div class="masterstudy-account-enrolled-quizzes-item">
								<div class="masterstudy-account-enrolled-quizzes-item__name-container">
									<div class="masterstudy-account-enrolled-quizzes-item__name-container-title">
										<a href="#" class="masterstudy-account-enrolled-quizzes-item__name--link"></a>
										<div class="masterstudy-account-enrolled-quizzes-item__status"
											data-quiz-status></div>
									</div>
									<div class="masterstudy-account-enrolled-quizzes-item__name-container-stats">
										<div class="masterstudy-account-enrolled-quizzes-item__attempts"></div>
										•
										<div class="masterstudy-account-enrolled-quizzes-item__questions"></div>
									</div>
								</div>
								<div class="masterstudy-account-enrolled-quizzes-item__info">
									<div class="masterstudy-account-enrolled-quizzes-item__progress-wrapper">
										<div class="masterstudy-account-enrolled-quizzes-item__progress"
											data-quiz-progress>
											<span
												class="masterstudy-account-enrolled-quizzes-item__progress--grade"></span>
											<span
												class="masterstudy-account-enrolled-quizzes-item__progress--grade-text"></span>
										</div>
										<span
											class="masterstudy-account-enrolled-quizzes-item__progress--percent"></span>
									</div>
									<a href="#" class="masterstudy-account-enrolled-quizzes-item__details">
										<?php echo esc_html__( 'Details', 'masterstudy-lms-learning-management-system' ); ?>
									</a>
								</div>
							</div>
						</div>
					</div>
				</template>
				<template id="masterstudy-account-enrolled-quizzes-no-found-template">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/no-records',
						array(
							'title_items'     => esc_html__( 'No enrolled quizzes yet', 'masterstudy-lms-learning-management-system' ),
							'title_search'    => esc_html__( 'No quizzes match your search', 'masterstudy-lms-learning-management-system' ),
							'container_class' => 'masterstudy-account-enrolled-quizzes-no-found__info',
							'icon'            => 'stmlms-order',
						)
					);
					?>
				</template>
			</div>

			<div class="masterstudy-account-enrolled-quizzes-navigation">
				<div class="masterstudy-account-enrolled-quizzes-navigation__pagination"></div>
				<div class="masterstudy-account-enrolled-quizzes-navigation__per-page">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/select',
						array(
							'select_id'    => 'enrolled-quizzes-per-page',
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
