<?php
/**
 * @var $course
 */

$current_status = get_post_status( $course['id'] );

$course_statuses = array(
	'publish'  => esc_html__( 'Published', 'masterstudy-lms-learning-management-system' ),
	'draft'    => esc_html__( 'In draft', 'masterstudy-lms-learning-management-system' ),
	'rejected' => esc_html__( 'Rejected', 'masterstudy-lms-learning-management-system' ),
	'pending'  => esc_html__( 'Pending', 'masterstudy-lms-learning-management-system' ),
	'private'  => esc_html__( 'Private', 'masterstudy-lms-learning-management-system' ),
);
?>

<div class="masterstudy-instructor-course-actions">
	<div class="masterstudy-instructor-course-actions__content">
		<div class="masterstudy-instructor-course-actions__column">
			<div class="masterstudy-instructor-course-actions__item">
				<span class="masterstudy-instructor-course-actions__title">
					<?php echo esc_html__( 'Course status', 'masterstudy-lms-learning-management-system' ); ?>:
				</span>
				<span class="masterstudy-instructor-course-actions__status masterstudy-instructor-course-actions__status_<?php echo esc_attr( $current_status ); ?>">
					<?php echo esc_html( $course_statuses[ $current_status ] ?? ucfirst( $current_status ) ); ?>
				</span>
			</div>
			<div class="masterstudy-instructor-course-actions__item">
				<span class="masterstudy-instructor-course-actions__title">
					<?php echo esc_html__( 'Last updated', 'masterstudy-lms-learning-management-system' ); ?>:
				</span>
				<span class="masterstudy-instructor-course-actions__value">
					<?php echo esc_html( $course['updated'] ); ?>
				</span>
			</div>
		</div>
		<div class="masterstudy-instructor-course-actions__column">
			<span class="masterstudy-instructor-course-actions__modal-btn">
				<i class="stmlms-course-modal-menu"></i>
			</span>
		</div>
	</div>
</div>
