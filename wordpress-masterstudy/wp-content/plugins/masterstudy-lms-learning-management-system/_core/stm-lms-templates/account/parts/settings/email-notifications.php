<?php
/**
 * @var $current_user
 */

$has_disable_meta                   = metadata_exists( 'user', $current_user['id'], 'disable_report_email_notifications' );
$disable_report_email_notifications = $has_disable_meta ? false : true;
$checked                            = ( false === $disable_report_email_notifications );
?>

<div class="masterstudy-account-settings__notice">
	<h2 class="masterstudy-account-settings__notice-title">
		<?php echo esc_html__( 'Email notification', 'masterstudy-lms-learning-management-system' ); ?>
	</h2>
	<div class="masterstudy-account-settings__notice-content">
		<?php
		STM_LMS_Templates::show_lms_template(
			'components/switcher',
			array(
				'name'  => 'email_notification',
				'class' => 'masterstudy-account-settings-email-notifications',
				'on'    => $checked,
			)
		);
		?>
		<p class="masterstudy-account-settings__notice-text">
			<?php echo esc_html__( 'Send weekly/monthly reports', 'masterstudy-lms-learning-management-system' ); ?>
		</p>
	</div>
</div>
