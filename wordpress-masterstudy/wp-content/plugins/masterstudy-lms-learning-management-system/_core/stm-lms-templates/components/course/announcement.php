<?php
/**
 * @var object $course
 */

?>

<div class="masterstudy-single-course-announcement">
	<?php
	if ( ! empty( $course->announcement ) ) {
		echo wp_kses_post( htmlspecialchars_decode( $course->announcement ) );
	} else {
		?>
		<p><?php echo esc_html__( 'No notices at this moment.', 'masterstudy-lms-learning-management-system' ); ?></p>
	<?php } ?>
</div>
