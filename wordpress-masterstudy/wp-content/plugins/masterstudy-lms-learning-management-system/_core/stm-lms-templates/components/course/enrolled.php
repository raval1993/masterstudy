<?php
/**
 * @var object $course
 * @var int $user_id
 */

if ( empty( $course->id ) || empty( $user_id ) ) {
	return;
}

$start_time = masterstudy_lms_get_user_enrolled_date( $course->id, $user_id );

if ( empty( $start_time ) ) {
	$enrolled_date = __( 'You are not enrolled', 'masterstudy-lms-learning-management-system' );
} else {
	$enrolled_date = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), intval( $start_time ) );
}
?>

<div class="masterstudy-single-course-enrolled">
	<div class="masterstudy-single-course-enrolled__container">
		<span class="masterstudy-single-course-enrolled__icon"></span>
		<div class="masterstudy-single-course-enrolled__list">
			<span class="masterstudy-single-course-enrolled__title">
				<?php
				echo esc_html__( 'Enrolled', 'masterstudy-lms-learning-management-system' );
				echo ':';
				?>
			</span>
			<div class="masterstudy-single-course-enrolled__item">
				<?php echo esc_html( $enrolled_date ); ?>
			</div>
		</div>
	</div>
</div>
