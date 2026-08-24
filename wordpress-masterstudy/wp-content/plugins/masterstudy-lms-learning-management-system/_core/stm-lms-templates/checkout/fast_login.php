<?php
wp_enqueue_script( 'jquery.cookie' );
stm_lms_register_script( 'fast_login' );
wp_enqueue_style( 'masterstudy-authorization' );

$restrict_registration          = STM_LMS_Options::get_option( 'restrict_registration', false );
$registration_strength_password = STM_LMS_Options::get_option( 'registration_strength_password', false );

$premoderation          = STM_LMS_Options::get_option( 'user_premoderation', false );
$session_notice_enabled = masterstudy_lms_user_sessions_limit_enabled();

wp_localize_script(
	'stm-lms-fast_login',
	'stm_lms_fast_login',
	array(
		'translations'                   => array(
			'sign_up' => esc_html__( 'Sign Up', 'masterstudy-lms-learning-management-system' ),
			'sign_in' => esc_html__( 'Sign In', 'masterstudy-lms-learning-management-system' ),
		),
		'confirmation_message'           => esc_html__( 'Confirmation link sent. Please follow the instructions sent to your email address.', 'masterstudy-lms-learning-management-system' ),
		'restrict_registration'          => $restrict_registration,
		'registration_strength_password' => $registration_strength_password,
		'user_premoderation'             => (bool) $premoderation,
	)
);

stm_lms_register_style( 'fast_login' );
wp_enqueue_style( 'masterstudy-button' );
?>

<div id="stm_lms_fast_login">
	<div class="stm_lms_fast_login">
		<div class="stm_lms_fast_login__head">
			<h3 class="stm_lms_fast_login__current-method"><?php echo esc_html__( 'Sign Up', 'masterstudy-lms-learning-management-system' ); ?></h3>
			<?php if ( ! $restrict_registration ) : ?>
			<div class="stm_lms_fast_login__switch">
				<div class="stm_lms_fast_login__switch-account">
					<span class="stm_lms_fast_login__switch-account-title">
						<?php echo esc_html__( 'or', 'masterstudy-lms-learning-management-system' ); ?>
					</span>
					<a href="#" class="stm_lms_fast_login__switch-account-link"><?php echo esc_html__( 'Sign In', 'masterstudy-lms-learning-management-system' ); ?></a>
				</div>
			</div>
			<?php endif; ?>
		</div>
		<div class="stm_lms_fast_login__message" role="status" aria-live="polite" style="display:none;">
			<?php if ( $session_notice_enabled ) : ?>
				<?php STM_LMS_Templates::show_lms_template( 'components/authorization/session-notice' ); ?>
			<?php endif; ?>
			<div class="text-message-register" style="display:none;"></div>
		</div>
		<div class="stm_lms_fast_login__body">
			<div class="stm_lms_fast_login__field stm_lms_fast_login__email">
				<input type="email" class="stm_lms_fast_login__input" placeholder="<?php echo esc_html__( 'Enter your email', 'masterstudy-lms-learning-management-system' ); ?>">
			</div>
			<div class="stm_lms_fast_login__field stm_lms_fast_login__password">
				<input type="password" class="stm_lms_fast_login__input stm_lms_fast_login__input_pass" placeholder="<?php echo esc_html__( 'Enter your password', 'masterstudy-lms-learning-management-system' ); ?>">
				<?php if ( ! empty( STM_LMS_Options::get_option( 'registration_strength_password', false ) ) ) : ?>
				<div class="masterstudy-authorization__strength-password stm_lms_fast_login__strength-password">
				</div>
				<span class="masterstudy-authorization__strength-password__label"></span>
				<?php endif; ?>
				<span class="stm_lms_fast_login__input-show-pass"></span>
			</div>
			<div class="stm_lms_fast_login__submit">
				<a href="#" class="masterstudy-button masterstudy-button_style-primary masterstudy-button_size-sm">
					<span class="masterstudy-button__title">
						<?php echo esc_html__( 'Sign up', 'masterstudy-lms-learning-management-system' ); ?>
					</span>
				</a>
			</div>
		</div>
	</div>
</div>
