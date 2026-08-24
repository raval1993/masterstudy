<?php
/**
 * @var array $course
 */

if ( empty( $course['start_time'] ) ) {
	return;
}
?>
<div class="masterstudy-course-card__start-time">
	<?php echo esc_html( $course['start_time'] ); ?>
</div>
