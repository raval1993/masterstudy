<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lms_current_user = STM_LMS_User::get_current_user( '', true, true );

do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $lms_current_user );

wp_enqueue_style( 'masterstudy-account-main' );
wp_enqueue_style( 'masterstudy-account-messages' );
wp_enqueue_script( 'masterstudy-account-messages' );
wp_localize_script(
	'masterstudy-account-messages',
	'chat_data',
	array(
		'instructor_public' => STM_LMS_Options::get_option( 'instructor_public_profile', true ),
		'student_public'    => STM_LMS_Options::get_option( 'student_public_profile', true ),
		'user_id'           => $lms_current_user['id'],
		'you'               => esc_html__( 'You', 'masterstudy-lms-learning-management-system' ),
	)
);
?>

<div class="masterstudy-account">
	<?php do_action( 'stm_lms_admin_after_wrapper_start', $lms_current_user ); ?>

	<div class="masterstudy-account-sidebar">
		<div class="masterstudy-account-sidebar__wrapper">
			<?php do_action( 'masterstudy_account_sidebar', $lms_current_user ); ?>
		</div>
	</div>

	<div class="masterstudy-account-container">
		<div id="masterstudy-account-messages" class="masterstudy-account-messages">
			<div class="masterstudy-account-messages__header">
				<h1 class="masterstudy-account-messages__title">
					<?php echo esc_html__( 'Messages', 'masterstudy-lms-learning-management-system' ); ?>
				</h1>
			</div>

			<div class="masterstudy-account-messages__layout">
				<div class="masterstudy-account-messages__empty">
					<div class="masterstudy-account-messages__empty-block">
						<span class="masterstudy-account-messages__empty-icon"></span>
						<span class="masterstudy-account-messages__empty-text">
							<?php echo esc_html__( 'No messages yet', 'masterstudy-lms-learning-management-system' ); ?>
						</span>
					</div>
				</div>

				<div class="masterstudy-account-messages__loader">
					<div class="masterstudy-account-messages__loader-body"></div>
				</div>

				<!-- LEFT: conversations -->
				<div class="masterstudy-account-messages__list is-hidden">
					<div class="masterstudy-account-messages__list-inner masterstudy-account-messages__conversations">
					</div>
				</div>

				<!-- RIGHT: chat panel -->
				<div class="masterstudy-account-messages__panel masterstudy-account-messages__chat-container is-hidden">
					<div class="masterstudy-account-messages__panel-card">
						<div class="masterstudy-account-messages__panel-header">
							<?php
							STM_LMS_Templates::show_lms_template(
								'components/back-link',
								array(
									'id'  => 'masterstudy-account-messages-back',
									'url' => '#',
								)
							);
							?>
							<div class="masterstudy-account-messages__panel-user">
								<div class="masterstudy-account-messages__panel-avatar"></div>
								<div class="masterstudy-account-messages__panel-meta">
									<a href="#" class="masterstudy-account-messages__panel-name"></a>
									<div class="masterstudy-account-messages__panel-time"></div>
								</div>
							</div>

							<div class="masterstudy-account-messages__panel-actions">
								<a href="#" class="masterstudy-account-messages__panel-action masterstudy-account-messages__panel-action--refresh" aria-label="<?php esc_attr_e( 'Refresh', 'masterstudy-lms-learning-management-system' ); ?>">
									<i class="stmlms-sync"></i>
								</a>

								<a href="#" class="masterstudy-account-messages__panel-action masterstudy-account-messages__panel-action--profile" target="_blank" rel="noopener">
									<?php echo esc_html__( 'Profile', 'masterstudy-lms-learning-management-system' ); ?>
								</a>
							</div>
						</div>

						<div class="masterstudy-account-messages__thread masterstudy-account-messages_chat" id="masterstudy-account-messages_chat"></div>

						<div class="masterstudy-account-messages__composer">
							<textarea class="masterstudy-account-messages__input masterstudy-account-messages_chat__send-message" placeholder="<?php echo esc_html__( 'Write a message...', 'masterstudy-lms-learning-management-system' ); ?>" rows="1"></textarea>
							<span class="masterstudy-account-messages__send masterstudy-account-messages_chat__send-btn">
								<i class="stmlms-send"></i>
							</span>
						</div>

						<p class="masterstudy-account-messages__error masterstudy-account-messages_chat__send-response"></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php do_action( 'masterstudy_after_account', $lms_current_user ); ?>
