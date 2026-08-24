<?php
/**
 * @var object $course
 * @var array $course_preview
 * @var boolean $full
 */

$full  = isset( $full ) ? $full : false;
$image = $full ? $course->full_image : $course->thumbnail;
if ( ! empty( $image ) && ( empty( $course_preview['video_type'] ) || 'none' === $course_preview['video_type'] ) || $full ) { ?>
	<img class="masterstudy-single-course-thumbnail" src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_html( $image['title'] ); ?>">
<?php } elseif ( ! empty( $course_preview['video_type'] ) ) {
	STM_LMS_Templates::show_lms_template(
		'components/course/video',
		array(
			'course'    => (array) $course_preview ?? '',
			'course_id' => $course->id,
			'mode'      => true,
		)
	);
}?>
