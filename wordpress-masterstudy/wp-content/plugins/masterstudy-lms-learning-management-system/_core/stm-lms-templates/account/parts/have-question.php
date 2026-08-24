<?php
/**
 * @var $current_user
 */

wp_enqueue_style( 'masterstudy-account-have-question' );
?>

<div class="masterstudy-account-have-question__button" data-masterstudy-modal="masterstudy-enterprise-modal">
	<i class="stmlms-menu-have-question"></i>
	<div class="masterstudy-account-have-question__label">
		<?php echo esc_html__( 'Have a question?', 'masterstudy-lms-learning-management-system' ); ?>
	</div>
</div>
<?php
STM_LMS_Templates::show_lms_template(
	'components/modals/enterprise',
	array(
		'dark_mode' => false,
	)
);
