<?php
/**
 * @var $current_user
 */

$is_instructor = STM_LMS_Instructor::is_instructor();

wp_enqueue_style( 'masterstudy-select2' );
wp_enqueue_script( 'masterstudy-personal-info' );
wp_enqueue_style( 'masterstudy-account-settings' );
wp_enqueue_script( 'masterstudy-account-settings' );

$personal_data = get_user_meta( $current_user['id'], 'masterstudy_personal_data', true );

if ( ! is_array( $personal_data ) ) {
	$personal_data = array();
}

$session_management_enabled = masterstudy_lms_can_manage_user_sessions();

wp_localize_script(
	'masterstudy-account-settings',
	'masterstudy_account_settings_data',
	array(
		'account_info'  => $current_user,
		'personal_data' => $personal_data,
		'sessions'      => $session_management_enabled ? STM_LMS_User_Sessions::get_current_user_sessions_payload() : array(),
		'i18n'          => array(
			'empty_sessions'    => esc_html__( 'No active sessions found.', 'masterstudy-lms-learning-management-system' ),
			'sign_out_error'    => esc_html__( 'Unable to sign out this session.', 'masterstudy-lms-learning-management-system' ),
			'clear_all_error'   => esc_html__( 'Unable to clear active sessions.', 'masterstudy-lms-learning-management-system' ),
			'sign_out_confirm'  => esc_html__( 'Are you sure you want to sign out this session?', 'masterstudy-lms-learning-management-system' ),
			'clear_all_confirm' => esc_html__( 'Are you sure you want to clear all other active sessions?', 'masterstudy-lms-learning-management-system' ),
		),
	)
);
?>

<div class="masterstudy-account-settings <?php echo esc_attr( $is_instructor ? 'masterstudy-account-settings_instructor' : '' ); ?>" id="masterstudy-account-settings">
	<h1 class="masterstudy-account-settings__title">
		<?php echo esc_html__( 'Profile', 'masterstudy-lms-learning-management-system' ); ?>
	</h1>
	<?php STM_LMS_Templates::show_lms_template( 'account/parts/settings/become-instructor-info', array( 'current_user' => $current_user ) ); ?>
	<div class="masterstudy-account-settings__fields">
		<?php
		if ( $is_instructor ) {
			STM_LMS_Templates::show_lms_template( 'account/parts/settings/profile-cover' );
		}

		STM_LMS_Templates::show_lms_template( 'account/parts/settings/avatar', array( 'current_user' => $current_user ) );
		STM_LMS_Templates::show_lms_template( 'account/parts/settings/name' );

		if ( $is_instructor ) {
			STM_LMS_Templates::show_lms_template( 'account/parts/settings/position' );
			STM_LMS_Templates::show_lms_template( 'account/parts/settings/bio' );
		}

		STM_LMS_Templates::show_lms_template( 'account/parts/settings/display-name' );
		STM_LMS_Templates::show_lms_template( 'account/parts/settings/custom-fields' );
		?>
	</div>
	<?php
	$email_settings    = get_option( 'stm_lms_email_manager_settings' );
	$student_digest    = $email_settings['stm_lms_reports_student_checked_enable'] ?? false;
	$instructor_digest = $email_settings['stm_lms_reports_instructor_checked_enable'] ?? false;
	$admin_digest      = $email_settings['stm_lms_reports_admin_checked_enable'] ?? true;

	if ( is_ms_lms_addon_enabled( 'email_manager' ) && STM_LMS_Helpers::is_pro_plus() ) {

		if ( isset( $current_user['roles'] ) && is_array( $current_user['roles'] ) && ! empty( $current_user['roles'][0] ) ) {
			$user_role = $current_user['roles'][0] ?? '';

			if ( in_array( $user_role, array( 'administrator', 'stm_lms_instructor', 'subscriber' ), true ) ) {

				if (
					( 'administrator' === $user_role && $admin_digest ) ||
					( 'stm_lms_instructor' === $user_role && $instructor_digest ) ||
					( 'subscriber' === $user_role && $student_digest )
				) {
					STM_LMS_Templates::show_lms_template( 'account/parts/settings/email-notifications', array( 'current_user' => $current_user ) );
				}
			}
		}
	}

	STM_LMS_Templates::show_lms_template( 'account/parts/settings/billing' );

	if ( $is_instructor ) {
		STM_LMS_Templates::show_lms_template( 'account/parts/settings/socials' );
	}

	if ( $session_management_enabled ) {
		STM_LMS_Templates::show_lms_template( 'account/parts/settings/login-sessions', array( 'current_user' => $current_user ) );
	}
	STM_LMS_Templates::show_lms_template( 'account/parts/settings/change-password' );
	?>

	<div class="masterstudy-account-settings__actions">
		<?php
		STM_LMS_Templates::show_lms_template(
			'components/button',
			array(
				'title' => esc_html__( 'Save changes', 'masterstudy-lms-learning-management-system' ),
				'link'  => '#',
				'style' => 'primary',
				'size'  => 'sm',
				'id'    => 'masterstudy-account-settings-save',
			)
		);
		?>
		<div class="masterstudy-account-settings__message masterstudy-account-settings__message_hidden">
		</div>
	</div>
</div>
