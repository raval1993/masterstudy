<?php
/**
 * @var object $course
 */
?>

<div class="masterstudy-single-course-description-single">
	<?php echo wp_kses( $course->content, stm_lms_allowed_html() ); ?>
</div>
