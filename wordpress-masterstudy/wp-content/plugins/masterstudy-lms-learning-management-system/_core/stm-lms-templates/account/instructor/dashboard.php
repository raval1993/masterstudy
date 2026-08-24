<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $current_user );

wp_enqueue_style( 'masterstudy-account-main' );
?>

<div class="masterstudy-account">
	<?php do_action( 'stm_lms_admin_after_wrapper_start', $current_user ); ?>
	<div class="masterstudy-account-sidebar">
		<div class="masterstudy-account-sidebar__wrapper">
			<?php do_action( 'masterstudy_account_sidebar', $current_user ); ?>
		</div>
	</div>
	<div class="masterstudy-account-container">
		<?php
		if ( STM_LMS_Options::get_option( 'instructors_reports', true ) ) {
			do_action( 'masterstudy_show_analytics_templates', $current_user );
		}

		STM_LMS_Templates::show_lms_template( 'account/instructor/parts/courses', array( 'current_user' => $current_user ) );
		?>
	</div>
</div>
<?php do_action( 'masterstudy_after_account', $current_user ); ?>
