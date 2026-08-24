<?php
/**
 * @var object $course
 */


$course_status   = false;
$course_statuses = \STM_LMS_Helpers::get_course_statuses();

if ( ! empty( $course->status ) ) {
	if ( empty( $course->status_date_start ) && empty( $course->status_date_end ) ) {
		$course_status = true;
	} else {
		$current_time = time() * 1000;
		if ( $current_time > intval( $course->status_date_start ) && $current_time < intval( $course->status_date_end ) ) {
			$course_status = true;
		}
	}
}

if ( $course_status && ! empty( $course_statuses[ $course->status ] ) ) {
	$_status = $course_statuses[ $course->status ];
	?>
	<span style="color: <?php echo esc_attr( $_status['text_color'] ); ?>; background-color: <?php echo esc_attr( $_status['bg_color'] ); ?>" class="masterstudy-single-course-status">
		<?php echo esc_attr( $_status['label'] ); ?>
	</span>
	<?php
}
