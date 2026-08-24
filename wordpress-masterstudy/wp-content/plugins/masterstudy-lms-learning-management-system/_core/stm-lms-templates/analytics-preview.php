<?php
wp_enqueue_style( 'masterstudy-analytics-preview-page' );
wp_enqueue_script( 'masterstudy-analytics-preview-page' );

$text              = esc_html__( 'Upgrade to PRO', 'masterstudy-lms-learning-management-system' );
$pro_plus_icon_url = STM_LMS_URL . 'assets/img/pro-features/unlock-pro-logo.svg';

if ( STM_LMS_Helpers::is_pro() && ! STM_LMS_Helpers::is_pro_plus() ) {
	$text              = esc_html__( 'Upgrade to PRO PLUS', 'masterstudy-lms-learning-management-system' );
	$pro_plus_icon_url = STM_LMS_URL . 'assets/img/pro-features/pro_plus.svg';
}

$purchase_link = STM_LMS_Helpers::is_pro() && ! STM_LMS_Helpers::is_pro_plus() ? 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=msadmin&utm_medium=reportsanalytics' : admin_url( 'admin.php?page=stm-lms-go-pro&source=button-analytics-settings' );
?>

<div class="masterstudy-analytics-preview-page__popup">
	<div class="masterstudy-analytics-preview-page__popup-video-wrapper">
		<div class="masterstudy-analytics-preview-page__popup-video">
			<iframe id="masterstudy-analytics-preview-video" frameborder="0" allowfullscreen="" data-original-src="https://www.youtube.com/embed/7NqPcDGVOZM?rel=0&amp;autoplay=1"></iframe>
		</div>
	</div>
</div>
<div class="masterstudy-analytics-preview-page">
	<div class="masterstudy-analytics-preview-page__wrapper">
		<div class="masterstudy-analytics-preview-page__content">
			<h2><?php echo esc_html__( 'Unlock', 'masterstudy-lms-learning-management-system' ); ?>
				<span class="masterstudy-analytics-preview-page__addon">
					<?php echo esc_html__( 'Reports & Analytics', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
				<?php echo esc_html__( 'with', 'masterstudy-lms-learning-management-system' ); ?>
				<div class="masterstudy-analytics-preview-page__logo">
					<span class="masterstudy-analytics-preview-page__logo-title">
						<?php echo esc_html__( 'MasterStudy', 'masterstudy-lms-learning-management-system' ); ?>
					</span>
					<img src="<?php echo esc_url( $pro_plus_icon_url ); ?>">
				</div>
			</h2>
			<p><?php echo esc_html__( 'Track your success with Reports and Statistics! See your earnings, courses, students, and certificates in one place. Students can also see their progress, course bundles, group courses, reviews, certificates and points.', 'masterstudy-lms-learning-management-system' ); ?> </p>
			<div class="masterstudy-analytics-preview-page__actions">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'title'  => $text,
						'link'   => $purchase_link,
						'style'  => 'primary',
						'size'   => 'sm',
						'id'     => 'analytics-upgrade-pro',
						'target' => '_blank',
					)
				);
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'title'  => esc_html__( 'Learn more', 'masterstudy-lms-learning-management-system' ),
						'link'   => 'https://stylemixthemes.com/wordpress-lms-plugin/reports-and-analytics/?utm_source=msadmin&utm_medium=reportsanalytics',
						'style'  => 'tertiary',
						'size'   => 'sm',
						'id'     => 'analytics-learn-more',
						'target' => '_blank',
					)
				);
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'title' => esc_html__( 'Watch video', 'masterstudy-lms-learning-management-system' ),
						'link'  => '#',
						'style' => 'outline',
						'size'  => 'sm',
						'id'    => 'analytics-watch-video',
					)
				);
				?>
			</div>
		</div>
		<div class="masterstudy-analytics-preview-page__image">
			<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/pro-features/analytics.png' ); ?>" alt="">
		</div>
	</div>
</div>
