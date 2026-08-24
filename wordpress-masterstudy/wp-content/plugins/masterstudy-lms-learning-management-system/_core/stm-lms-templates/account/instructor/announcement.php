<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lms_current_user = STM_LMS_User::get_current_user( '', true, true );

do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $lms_current_user );

wp_enqueue_style( 'masterstudy-account-edit-account' );
wp_enqueue_style( 'masterstudy-account-instructor-account' );
wp_enqueue_style( 'masterstudy-account-announcement' );
wp_enqueue_style( 'masterstudy-account-main' );

wp_enqueue_script( 'masterstudy-account-announcement' );

wp_localize_script(
	'masterstudy-account-announcement',
	'announcement_data',
	array(
		'loading_your_courses'          => esc_html__( 'Loading your courses', 'masterstudy-lms-learning-management-system-pro' ),
		'unable_to_load_courses'        => esc_html__( 'Unable to load courses', 'masterstudy-lms-learning-management-system-pro' ),
		'unable_to_create_announcement' => esc_html__( 'Unable to create announcement', 'masterstudy-lms-learning-management-system-pro' ),
	)
);

$is_instructor = STM_LMS_Instructor::is_instructor();
?>

<div class="masterstudy-account">
	<?php do_action( 'stm_lms_admin_after_wrapper_start', $lms_current_user ); ?>
	<div class="masterstudy-account-sidebar">
		<div class="masterstudy-account-sidebar__wrapper">
			<?php do_action( 'masterstudy_account_sidebar', $lms_current_user ); ?>
		</div>
	</div>
	<div class="masterstudy-account-container">
		<div class="masterstudy-account-announcement">
			<?php do_action( 'stm_lms_admin_after_wrapper_start', $lms_current_user ); ?>

			<span class="masterstudy-account-announcement__title"><?php esc_html_e( 'Announcement', 'masterstudy-lms-learning-management-system' ); ?></span>

			<div class="stm_lms_create_announcement">
				<div class="masterstudy-account-announcement__select">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/select',
						array(
							'select_id'    => 'announcement-course-select',
							'select_name'  => 'announcement_course',
							'placeholder'  => esc_html__( '- Choose Course for Announcement -', 'masterstudy-lms-learning-management-system' ),
							'is_queryable' => false,
							'clearable'    => false,
							'options'      => array(),
						)
					);
					?>
				</div>

				<textarea class="masterstudy-account-announcement__message-input" placeholder="<?php esc_attr_e( 'Enter message for students', 'masterstudy-lms-learning-management-system' ); ?>"></textarea>

				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'title' => esc_html__( 'Submit', 'masterstudy-lms-learning-management-system' ),
						'style' => 'primary',
						'size'  => 'sm',
						'class' => 'masterstudy-account-announcement__create-announcement-btn',
					)
				);
				?>

				<div class="masterstudy-account-utility__message masterstudy-account-announcement__response-msg"></div>
			</div>
		</div>
	</div>
</div>

<?php do_action( 'masterstudy_after_account', $lms_current_user ); ?>
