<?php
/**
 * @var array $user_id
 * @var array $settings
 * @var int $course_id
 * @var boolean $elementor_widget
 * @var boolean $lesson_completed
 * @var boolean $block_enabled
 * @var boolean $dark_mode
 */

use MasterStudy\Lms\Pro\addons\certificate_builder\CertificateRepository;

$total_progress     = STM_LMS_Lesson::get_total_progress( $user_id ?? null, $course_id );
$course_passed      = false;
$dark_mode          = isset( $dark_mode ) ? $dark_mode : false;
$lesson_completed   = isset( $lesson_completed ) ? $lesson_completed : false;
$block_enabled      = isset( $block_enabled ) ? $block_enabled : false;
$elementor_widget   = isset( $elementor_widget ) ? $elementor_widget : false;
$elementor_editor   = isset( $elementor_editor ) ? $elementor_editor : false;
$stars              = range( 1, 5 );
$is_reviews_allowed = STM_LMS_Options::get_option( 'course_tab_reviews', true );

if ( $is_reviews_allowed ) {
	$has_review = STM_LMS_Reviews::get_user_review_on_course( $course_id, $user_id )->found_posts;
}


if ( ! empty( $total_progress['course']['progress_percent'] ) ) {
	$course_passed = $total_progress['course']['progress_percent'] >= ( $settings['certificate_threshold'] ?? 70 );
}

$course_completed_message     = apply_filters( 'masterstudy_lms_course_completed_message', esc_html__( 'You have successfully completed the course', 'masterstudy-lms-learning-management-system' ), $course_id, $course_passed );
$course_not_completed_message = apply_filters( 'masterstudy_lms_course_not_completed_message', esc_html__( 'You have NOT completed the course', 'masterstudy-lms-learning-management-system' ), $course_id, $course_passed );

wp_enqueue_style( 'masterstudy-single-course-complete' );
wp_enqueue_script( 'masterstudy-single-course-complete' );
wp_localize_script(
	'masterstudy-single-course-complete',
	'course_completed',
	array(
		'elementor_widget' => $elementor_widget,
		'course_id'        => $course_id,
		'user_id'          => ! empty( $user_id ),
		'completed'        => $lesson_completed,
		'block_enabled'    => $block_enabled,
		'nonce'            => wp_create_nonce( 'stm_lms_total_progress' ),
		'ajax_url'         => admin_url( 'admin-ajax.php' ),
	)
);

if ( is_ms_lms_addon_enabled( 'certificate_builder' ) ) {
	wp_register_script( 'jspdf', STM_LMS_PRO_URL . '/assets/js/certificate-builder/jspdf.umd.js', array(), STM_LMS_PRO_VERSION, false );
	wp_register_script( 'qrcode', STM_LMS_PRO_URL . '/assets/js/certificate-builder/qrcode.min.js', array(), STM_LMS_PRO_VERSION, false );
	wp_register_script( 'html2canvas', STM_LMS_PRO_URL . '/assets/js/certificate-builder/html2canvas.min.js', array(), STM_LMS_PRO_VERSION, true );
	wp_enqueue_script( 'masterstudy_generate_certificate', STM_LMS_URL . 'assets/js/course-player/generate-certificate.js', array( 'jspdf', 'qrcode', 'html2canvas' ), MS_LMS_VERSION, true );

	if ( is_ms_lms_addon_enabled( 'grades' ) ) {
		wp_enqueue_style( 'masterstudy-grades-certificate' );
	}

	$shapes = method_exists( CertificateRepository::class, 'get_shapes' ) ? ( new CertificateRepository() )->get_shapes() : array();

	wp_localize_script(
		'masterstudy_generate_certificate',
		'course_certificate',
		array(
			'nonce'       => wp_create_nonce( 'stm_get_certificate' ),
			'ajax_url'    => admin_url( 'admin-ajax.php' ),
			'shapes'      => $shapes,
			'googleFonts' => STM_LMS_PRO_URL . 'assets/js/certificate-builder/google-fonts.json',
		)
	);
}

if ( ! empty( $total_progress ) ) {
	$percent = is_rtl() ? "%{$total_progress['course']['progress_percent']}" : "{$total_progress['course']['progress_percent']}%";
}

if ( ! empty( $total_progress ) && $total_progress['course_completed'] && $block_enabled && $course_passed ) {
	?>
	<div class="masterstudy-single-course-complete-block">
		<span class="masterstudy-single-course-complete-block__icon"></span>
		<div class="masterstudy-single-course-complete-block__content">
			<span class="masterstudy-single-course-complete-block__title">
				<?php echo esc_html__( 'Course complete', 'masterstudy-lms-learning-management-system' ); ?>
			</span>
			<span class="masterstudy-single-course-complete-block__score">
				<span class="masterstudy-single-course-complete-block__score-value">
					<?php echo esc_html__( 'Score:', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
				<strong><?php echo esc_html( $percent ); ?></strong>
			</span>
		</div>
		<span class="masterstudy-single-course-complete-block__details">
			<?php echo esc_html__( 'Details', 'masterstudy-lms-learning-management-system' ); ?>
		</span>
	</div>
	<?php
} elseif ( ! empty( $total_progress ) && $total_progress['course']['progress_percent'] > 0 && $block_enabled ) {
	?>
	<div class="masterstudy-single-course-complete-block masterstudy-single-course-complete-block_in-progress">
		<div class="masterstudy-single-course-complete-block__content">
			<span class="masterstudy-single-course-complete-block__score">
				<span class="masterstudy-single-course-complete-block__score-value">
					<?php echo esc_html__( 'Score:', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
				<strong><?php echo esc_html( $percent ); ?></strong>
			</span>
			<div class="masterstudy-single-course-complete__bars">
				<span class="masterstudy-single-course-complete__bar-empty"></span>
				<span class="masterstudy-single-course-complete__bar-filled" style="width:<?php echo esc_html( $total_progress['course']['progress_percent'] ); ?>%"></span>
			</div>
		</div>
	</div>
	<?php
} elseif ( $elementor_widget && $elementor_editor ) {
	echo esc_html__( 'You must start taking the course for the data to be displayed on the page.', 'masterstudy-lms-learning-management-system' );
}

if ( ! empty( $user_id ) ) {
	?>
	<div id="masterstudy-single-course-complete" class="masterstudy-single-course-complete" data-course-id="<?php echo esc_attr( $course_id ); ?>" style="display: none;">
		<div class="masterstudy-single-course-complete__wrapper">
			<div class="masterstudy-single-course-complete__container">
				<span class="masterstudy-single-course-complete__close"></span>
				<div class="masterstudy-single-course-complete__loading">
					<?php echo esc_html__( 'Loading your statistics', 'masterstudy-lms-learning-management-system' ); ?>
				</div>
				<div class="masterstudy-single-course-complete__success">
					<div class="masterstudy-single-course-complete__opportunities">
						<?php
						if ( ! STM_LMS_Options::get_option( 'finish_popup_image_disable', false ) ) {
							$failed_image  = STM_LMS_URL . 'assets/icons/lessons/course-completed-negative.png';
							$success_image = STM_LMS_URL . 'assets/icons/lessons/course-completed-positive.svg';

							if ( ! empty( $settings['finish_popup_image_failed'] ) ) {
								$custom_failed_image_url = wp_get_attachment_image_url( $settings['finish_popup_image_failed'] );
								if ( ! empty( $custom_failed_image_url ) ) {
									$failed_image = $custom_failed_image_url;
								}
							}

							if ( ! empty( $settings['finish_popup_image_success'] ) ) {
								$custom_success_image_url = wp_get_attachment_image_url( $settings['finish_popup_image_success'] );
								if ( ! empty( $custom_success_image_url ) ) {
									$success_image = $custom_success_image_url;
								}
							}
							?>
							<div class="masterstudy-single-course-complete__opportunities-icon">
								<?php if ( $course_passed ) { ?>
									<img src="<?php echo esc_url( $success_image ); ?>" width="80" height="80" alt="<?php echo esc_html__( 'You have successfully completed the course', 'masterstudy-lms-learning-management-system' ); ?>">
								<?php } else { ?>
									<img src="<?php echo esc_url( $failed_image ); ?>" width="80" height="80" alt="<?php echo esc_html__( 'You have NOT completed the course', 'masterstudy-lms-learning-management-system' ); ?>">
								<?php } ?>
							</div>
						<?php } ?>
						<div class="masterstudy-single-course-complete__opportunities-statistic">
							<span class="masterstudy-single-course-complete__opportunities-label"><?php echo esc_html__( 'Your score', 'masterstudy-lms-learning-management-system' ); ?></span>
							<span class="masterstudy-single-course-complete__opportunities-percent"></span>
						</div>
					</div>
					<div class="masterstudy-single-course-complete__message">
						<?php echo $course_passed ? wp_kses_post( $course_completed_message ) : wp_kses_post( $course_not_completed_message ); ?>
					</div>
					<h2 class="masterstudy-single-course-complete__title"></h2>
					<div class="masterstudy-single-course-complete__curiculum-statistic">
						<?php
						$curriculums = ms_plugin_curriculum_list();
						foreach ( $curriculums as $curriculum ) {
							?>
							<div class="masterstudy-single-course-complete__curiculum-statistic-item masterstudy-single-course-complete__curiculum-statistic-item_type-<?php echo esc_attr( $curriculum['type'] ); ?>">
								<img src="<?php echo esc_url( STM_LMS_URL . 'assets/icons/lessons/' . $curriculum['icon'] . '.svg' ); ?>" width="<?php echo esc_attr( $curriculum['icon_width'] ); ?>" height="<?php echo esc_attr( $curriculum['icon_height'] ); ?>">
								<span>
									<?php echo esc_html( $curriculum['label'] ); ?>
									<strong>
										<span class="masterstudy-single-course-complete__curiculum-statistic-item_completed"></span>/<span class="masterstudy-single-course-complete__curiculum-statistic-item_total"></span>
									</strong>
								</span>
							</div>
						<?php } ?>
					</div>
					<div class="masterstudy-single-course-complete__review-success-container">
						<div class="masterstudy-single-course-complete__review-success-icon"></div>
						<div class="masterstudy-single-course-complete__review-success-msg"></div>
						<span class="stmlms-close"></span>
					</div>
					<div class="masterstudy-single-course-complete__buttons">
						<?php
						if ( is_ms_lms_addon_enabled( 'certificate_builder' ) && ( $lesson_completed || $block_enabled ) && $course_passed && masterstudy_lms_course_has_certificate( $course_id ) ) {
							STM_LMS_Templates::show_lms_template(
								'components/button',
								array(
									'title'         => __( 'Certificate', 'masterstudy-lms-learning-management-system' ),
									'type'          => '',
									'link'          => '#',
									'style'         => 'primary',
									'size'          => 'md',
									'id'            => $course_id,
									'icon_position' => '',
									'icon_name'     => '',
									'class'         => 'masterstudy-certificate-btn',
								)
							);
						}
						STM_LMS_Templates::show_lms_template(
							'components/button',
							array(
								'title'         => $block_enabled ? __( 'Got it', 'masterstudy-lms-learning-management-system' ) : __( 'View course', 'masterstudy-lms-learning-management-system' ),
								'type'          => '',
								'link'          => $block_enabled ? '#' : get_post_permalink( $course_id ),
								'style'         => 'tertiary',
								'size'          => 'md',
								'data'          => array(),
								'id'            => 'gotit',
								'icon_position' => '',
								'icon_name'     => '',
							)
						);

						if ( $is_reviews_allowed && empty( $has_review ) ) {
							STM_LMS_Templates::show_lms_template(
								'components/button',
								array(
									'title'         => __( 'Leave review', 'masterstudy-lms-learning-management-system' ),
									'type'          => '',
									'style'         => 'tertiary',
									'size'          => 'md',
									'link'          => '#',
									'data'          => array(),
									'id'            => 'leave_review',
									'icon_position' => '',
									'icon_name'     => '',
									'class'         => 'masterstudy-single-course-complete__review-btn',
								)
							);
						}
						?>
					</div>
				</div>
			</div>
		</div>
		<?php if ( $is_reviews_allowed && empty( $has_review ) ) : ?>
			<div class="masterstudy-single-course-complete__wrapper masterstudy-single-course-complete__review-form">
				<div class="masterstudy-single-course-complete__review-header">
					<span class="masterstudy-single-course-complete__review-title">
						<?php echo esc_html__( 'Leave your review', 'masterstudy-lms-learning-management-system' ); ?>
					</span>
					<span class="masterstudy-single-course-complete__close"></span>
				</div>
				<div class="masterstudy-single-course-complete__review-rating">
					<?php foreach ( $stars as $star ) { ?>
						<span class="stmlms-star"></span>
					<?php } ?>
				</div>
				<div class="masterstudy-single-course-complete__review-form-editor">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/wp-editor',
						array(
							'id'        => 'editor_add_review_complete_popup',
							'content'   => '',
							'dark_mode' => $dark_mode ?? false,
							'settings'  => array(
								'quicktags'     => false,
								'media_buttons' => false,
								'teeny'         => false,
								'textarea_rows' => 13,
								'tinymce'       => array(
									'toolbar1' => 'bold italic underline strikethrough bullist numlist link',
									'toolbar2' => false,
									'plugins'  => 'lists,link,wordpress',
								),
							),
						)
					);
					?>
				</div>
				<div class="masterstudy-single-course-complete__review-error-container">
					<div class="masterstudy-single-course-complete__review-error-icon"></div>
					<div class="masterstudy-single-course-complete__review-error-msg"></div>
					<span class="stmlms-close"></span>
				</div>
				<div class="masterstudy-single-course-complete__review-form-actions">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/button',
						array(
							'id'    => 'masterstudy-single-course-complete__review-back',
							'title' => esc_html__( 'Back', 'masterstudy-lms-learning-management-system' ),
							'link'  => '#',
							'style' => 'tertiary',
							'size'  => 'sm',
						)
					);
					?>
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/button',
						array(
							'id'    => 'masterstudy-single-course-complete__review-submit',
							'title' => esc_html__( 'Submit review', 'masterstudy-lms-learning-management-system' ),
							'link'  => '#',
							'style' => 'primary',
							'size'  => 'sm',
						)
					);
					?>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php
}
