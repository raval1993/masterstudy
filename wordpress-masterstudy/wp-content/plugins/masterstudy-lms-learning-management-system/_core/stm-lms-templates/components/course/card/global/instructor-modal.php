<?php
/**
 * @var $course
 */

$current_status = get_post_status( $course['id'] );

$edit_link = function_exists( 'ms_plugin_manage_course_url' )
	? ms_plugin_manage_course_url( $course['id'] )
	: get_edit_post_link( $course['id'], '' );

$can_add_students     = STM_LMS_Instructor::instructor_can_add_students();
$manage_students_link = STM_LMS_Instructor::instructor_manage_students_url() . "/?course_id={$course['id']}";
$is_featured_enabled  = STM_LMS_Options::get_option( 'enable_featured_courses', true );
$is_featured          = get_post_meta( $course['id'], 'featured', true );

$analytics_link = '';
$grades_link    = '';

if ( STM_LMS_Helpers::is_pro_plus() && STM_LMS_Options::get_option( 'instructors_reports', true ) ) {
	$tmp = apply_filters( 'masterstudy_add_analytics_link', array( 'id' => $course['id'] ), $course['id'] );
	if ( ! empty( $tmp['analytics_link'] ) ) {
		$analytics_link = $tmp['analytics_link'];
	}
}

if ( STM_LMS_Helpers::is_pro_plus() && is_ms_lms_addon_enabled( 'grades' ) ) {
	$tmp = apply_filters( 'masterstudy_add_grades_link', array( 'id' => $course['id'] ), $course['id'] );
	if ( ! empty( $tmp['grades_link'] ) ) {
		$grades_link = $tmp['grades_link'];
	}
}
?>
<div class="masterstudy-instructor-course-actions__modal">
	<div class="masterstudy-instructor-course-actions__modal-top">
		<div class="masterstudy-instructor-course-actions__modal-status" data-status="<?php echo esc_attr( $current_status ); ?>">
			<?php
			echo esc_html(
				( 'publish' === $current_status )
					? __( 'Move to drafts', 'masterstudy-lms-learning-management-system' )
					: __( 'Publish', 'masterstudy-lms-learning-management-system' )
			);
			?>
		</div>
	</div>
	<span class="masterstudy-instructor-course-actions__modal-divider"></span>
	<div class="masterstudy-instructor-course-actions__modal-list">
		<a class="masterstudy-instructor-course-actions__modal-link" href="<?php echo esc_url( $edit_link ); ?>" target="_blank" rel="noopener noreferrer">
			<i class="stmlms-course-modal-edit"></i>
			<?php echo esc_html__( 'Edit', 'masterstudy-lms-learning-management-system' ); ?>
		</a>

		<?php if ( $is_featured_enabled ) : ?>
			<div class="masterstudy-instructor-course-actions__modal-featured <?php echo esc_attr( 'on' === $is_featured ? 'masterstudy-instructor-course-actions__modal-featured_on' : '' ); ?>" data-featured="<?php echo esc_attr( 'on' === $is_featured ? 'featured' : 'not featured' ); ?>">
				<?php
				echo esc_html(
					( 'on' === $is_featured )
						? __( 'Remove from Featured', 'masterstudy-lms-learning-management-system' )
						: __( 'Make Featured', 'masterstudy-lms-learning-management-system' )
				);
				?>
			</div>
		<?php endif; ?>

		<?php if ( $can_add_students ) : ?>
			<a class="masterstudy-instructor-course-actions__modal-link" href="<?php echo esc_url( $manage_students_link ); ?>" target="_blank" rel="noopener noreferrer">
				<i class="stmlms-menu-students"></i>
				<?php echo esc_html__( 'Manage Students', 'masterstudy-lms-learning-management-system' ); ?>
			</a>
		<?php endif; ?>

		<?php if ( ! empty( $analytics_link ) ) : ?>
			<a class="masterstudy-instructor-course-actions__modal-link" href="<?php echo esc_url( $analytics_link ); ?>" target="_blank" rel="noopener noreferrer">
				<i class="stmlms-course-modal-analytics"></i>
				<?php echo esc_html__( 'Analytics', 'masterstudy-lms-learning-management-system' ); ?>
			</a>
		<?php endif; ?>

		<?php if ( ! empty( $grades_link ) ) : ?>
			<a class="masterstudy-instructor-course-actions__modal-link" href="<?php echo esc_url( $grades_link ); ?>" target="_blank" rel="noopener noreferrer">
				<i class="stmlms-course-modal-grades"></i>
				<?php echo esc_html__( 'Grades', 'masterstudy-lms-learning-management-system' ); ?>
			</a>
		<?php endif; ?>
	</div>
</div>
