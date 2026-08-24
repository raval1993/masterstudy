<?php
/**
 * @var int $user_id
 * @var bool $stundent
 */

wp_enqueue_style( 'masterstudy-public-page-block' );

$public_page_url = isset( $student ) ? STM_LMS_User::student_public_page_url( $user_id ) : STM_LMS_User::instructor_public_page_url( $user_id );
?>

<div class="masterstudy-public-page-block">
	<a href="<?php echo esc_url( $public_page_url ); ?>" class="masterstudy-public-page-block-link" target="_blank">
		<?php echo esc_html__( 'View Public Profile', 'masterstudy-lms-learning-management-system' ); ?>
	</a>
	<?php
	STM_LMS_Templates::show_lms_template(
		'components/share',
		array(
			'url'   => $public_page_url,
			'label' => __( 'Share Public Profile', 'masterstudy-lms-learning-management-system' ),
		)
	);
	?>
</div>
