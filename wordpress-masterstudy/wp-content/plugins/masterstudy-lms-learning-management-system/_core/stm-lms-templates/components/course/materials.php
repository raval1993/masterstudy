<?php
/**
 * @var array $attachments
 */

?>

<div class="masterstudy-single-course-materials">
	<span class="masterstudy-single-course-materials__title">
		<?php echo esc_html__( 'Course materials', 'masterstudy-lms-learning-management-system' ); ?>
	</span>
	<?php
	STM_LMS_Templates::show_lms_template(
		'components/file-attachment',
		array(
			'attachments' => $attachments,
		)
	);
	if ( count( $attachments ) > 1 ) {
		?>
		<div class="masterstudy-single-course-materials__download-all">
			<span class="masterstudy-single-course-materials__quantity">
				<?php
				/* translators: %d number */
				echo sprintf( esc_html__( '%d items', 'masterstudy-lms-learning-management-system' ), count( $attachments ) );
				?>
			</span>
			<span class="masterstudy-single-course-materials__link">
				<?php echo esc_html__( 'Download all', 'masterstudy-lms-learning-management-system' ); ?>
			</span>
		</div>
	<?php } ?>
</div>
