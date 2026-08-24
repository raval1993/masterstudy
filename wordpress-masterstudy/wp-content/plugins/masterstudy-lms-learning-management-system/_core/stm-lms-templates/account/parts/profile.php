<?php
/**
 * @var $current_user
 */

wp_enqueue_style( 'masterstudy-account-profile' );

if ( STM_LMS_Instructor::is_instructor( $current_user['id'] ) ) {
	$public_page_url = STM_LMS_Options::get_option( 'instructor_public_profile' )
		? esc_url( STM_LMS_User::instructor_public_page_url( $current_user['id'] ) )
		: '';
} else {
	$public_page_url = STM_LMS_Options::get_option( 'student_public_profile' )
		? esc_url( STM_LMS_User::student_public_page_url( $current_user['id'] ) )
		: '';
}
?>

<div class="masterstudy-account-profile">
	<div class="masterstudy-account-profile__avatar">
		<?php if ( ! empty( $current_user['avatar_url'] ) ) { ?>
			<img src="<?php echo esc_url( $current_user['avatar_url'] ); ?>">
		<?php } ?>
	</div>
	<div class="masterstudy-account-profile__info">
		<div class="masterstudy-account-profile__name">
			<?php echo esc_html( $current_user['login'] ); ?>
		</div>
		<?php if ( ! empty( $public_page_url ) ) { ?>
			<a href="<?php echo esc_url( $public_page_url ); ?>" class="masterstudy-account-profile__link" target="_blank">
				<?php echo esc_html__( 'Visit Public Profile', 'masterstudy-lms-learning-management-system' ); ?>
			</a>
		<?php } ?>
	</div>
</div>
