<?php
/**
 * @var $course
 */
?>

<div class="masterstudy-course-card__meta-block">
	<i class="stmlms-cats"></i>
	<span>
		<?php
		printf(
			/* translators: %s: number */
			esc_html( _n( '%s Lecture', '%s Lectures', $course['lectures']['lessons'], 'masterstudy-lms-learning-management-system' ) ),
			esc_html( $course['lectures']['lessons'] )
		);
		?>
	</span>
</div>
