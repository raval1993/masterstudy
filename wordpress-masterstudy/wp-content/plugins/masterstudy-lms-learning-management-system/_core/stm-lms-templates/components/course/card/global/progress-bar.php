<?php
/**
 * @var $course
 */
?>

<div class="masterstudy-course-card__progress">
	<div class="masterstudy-course-card__progress-bars">
		<span class="masterstudy-course-card__progress-bar_empty"></span>
		<span class="masterstudy-course-card__progress-bar_filled" style="width:<?php echo esc_html( $course['progress'] ); ?>%"></span>
	</div>
	<div class="masterstudy-course-card__progress-title">
		<?php echo esc_html_e( 'Progress', 'masterstudy-lms-learning-management-system' ); ?>:
		<?php echo esc_html( $course['progress'] ); ?>%
	</div>
</div>
