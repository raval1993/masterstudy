<?php
/**
 * @var object $course
 * @var array $course_preview
 * @var boolean $with_image
 * @var boolean $mode
 */

$with_image = isset( $with_image ) ? $with_image : false;
$mode       = $mode ?? '';
?>

<div class="masterstudy-single-course-description">
	<?php if ( ! empty( $course->full_image ) && $with_image && ( empty( $course_preview['video_type'] ) || 'none' == $course_preview['video_type'] ) ) { ?>
		<img class="masterstudy-single-course-description__image"
			src="<?php echo esc_url( $course->full_image['url'] ); ?>"
			alt="<?php echo esc_html( $course->full_image['title'] ); ?>">
		<?php
	} elseif ( ! empty( $course_preview['video_type'] ) && $with_image || 'full_width' === $mode ) {
		STM_LMS_Templates::show_lms_template(
			'components/course/video',
			array(
				'course'    => (array) $course_preview ?? '',
				'course_id' => $course->id,
				'mode'      => true,
			)
		);
	}
	?>
	<div class="masterstudy-single-course-description__content">
		<?php
		$post = get_post( $course->id );
		setup_postdata( $post );
		the_content();
		wp_reset_postdata();
		?>
	</div>
	<?php if ( ! empty( $course->attachments ) ) { ?>
		<div class="masterstudy-single-course-description__files">
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/course/materials',
				array(
					'attachments' => $course->attachments,
				)
			);
			?>
		</div>
	<?php } ?>
</div>
