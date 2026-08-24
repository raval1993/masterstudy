<?php
/**
 * @var $lms_current_user
 */

$lms_current_user = STM_LMS_User::get_current_user( '', true, true );

wp_enqueue_style( 'masterstudy-account-main' );

do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $lms_current_user );
?>

<div class="masterstudy-account">
	<?php do_action( 'stm_lms_admin_after_wrapper_start', $lms_current_user ); ?>
	<div class="masterstudy-account-sidebar">
		<div class="masterstudy-account-sidebar__wrapper">
			<?php do_action( 'masterstudy_account_sidebar', $lms_current_user ); ?>
		</div>
	</div>
	<div class="masterstudy-account-container">
		<?php
		STM_LMS_Templates::show_lms_template( 'account/parts/settings/main', array( 'current_user' => $lms_current_user ) );

		do_action( 'stm_lms_before_profile_buttons_all', $lms_current_user );
		?>
	</div>
</div>
<?php do_action( 'masterstudy_after_account', $lms_current_user ); ?>
