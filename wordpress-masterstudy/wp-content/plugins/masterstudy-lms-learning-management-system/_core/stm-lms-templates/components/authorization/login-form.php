<?php
$session_limit_enabled = masterstudy_lms_user_sessions_limit_enabled();
?>
<div id="masterstudy-authorization-form-login" class="masterstudy-authorization__form">
	<div class="masterstudy-authorization__form-wrapper">
		<div class="masterstudy-authorization__form-field">
			<input type="text" name="user_login" class="masterstudy-authorization__form-input" placeholder="<?php echo esc_html__( 'Enter email or username', 'masterstudy-lms-learning-management-system' ); ?>">
		</div>
		<div class="masterstudy-authorization__form-field">
			<input type="password" name="user_password" class="masterstudy-authorization__form-input masterstudy-authorization__form-input_pass" placeholder="<?php echo esc_html__( 'Enter password', 'masterstudy-lms-learning-management-system' ); ?>">
			<span class="masterstudy-authorization__form-show-pass"></span>
		</div>
	</div>
	<?php if ( $session_limit_enabled ) : ?>
		<?php STM_LMS_Templates::show_lms_template( 'components/authorization/session-notice' ); ?>
	<?php endif; ?>
</div>
