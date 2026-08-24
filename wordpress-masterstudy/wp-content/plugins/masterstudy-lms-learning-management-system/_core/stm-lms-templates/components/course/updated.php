<?php
/**
 * @var object $course
 */

if ( empty( $course->id ) ) {
	return;
}

$post_data = get_post( $course->id );

if ( ! $post_data ) {
	return;
}
$modified           = '0000-00-00 00:00:00' !== $post_data->post_modified ? $post_data->post_modified : $post_data->post_date;
$modified_formatted = mysql2date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $modified );
?>

<div class="masterstudy-single-course-updated">
	<div class="masterstudy-single-course-updated__container">
		<span class="masterstudy-single-course-updated__icon"></span>
		<div class="masterstudy-single-course-updated__list">
			<span class="masterstudy-single-course-updated__title">
				<?php
				echo esc_html__( 'Updated', 'masterstudy-lms-learning-management-system' );
				echo ':';
				?>
			</span>
			<div class="masterstudy-single-course-updated__item">
				<?php echo esc_html( $modified_formatted ); ?>
			</div>
		</div>
	</div>
</div>
