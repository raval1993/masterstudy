<?php
/**
 * @var boolean $course_id
 * @var string $content
 * @var string $title
 */

if ( ! empty( $content ) ) {
	?>
	<div class="masterstudy-single-course-info">
		<span class="masterstudy-single-course-info__title">
			<?php echo esc_html( $title ); ?>
		</span>
		<div class="masterstudy-single-course-info__content">
			<?php echo wp_kses_post( $content ); ?>
		</div>
	</div>
	<?php
}
