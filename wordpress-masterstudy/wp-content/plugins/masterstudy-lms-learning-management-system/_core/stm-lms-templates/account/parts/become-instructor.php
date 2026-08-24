<?php
/**
 * @var $current_user
 */

wp_enqueue_style( 'masterstudy-account-become-instructor' );
?>

<div class="masterstudy-account-become-instructor">
	<div class="masterstudy-account-become-instructor__button" data-masterstudy-modal="masterstudy-become-instructor-modal">
		<i class="stmlms-menu-become-instructor"></i>
		<div class="masterstudy-account-become-instructor__label">
			<?php echo esc_html__( 'Become an instructor', 'masterstudy-lms-learning-management-system' ); ?>
		</div>
	</div>
	<?php
	STM_LMS_Templates::show_lms_template(
		'components/modals/become-instructor',
		array(
			'dark_mode' => false,
		)
	);
	?>
</div>
