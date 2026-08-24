<?php
/**
 * @var integer $current_students
 * @var boolean $with_icon
 */

$with_icon = isset( $with_icon ) ? $with_icon : false;

if ( ! empty( $current_students ) ) {
	?>
	<div class="masterstudy-single-course-current-students <?php echo $with_icon ? 'masterstudy-single-course-current-students_icon-style' : ''; ?>">
		<?php if ( $with_icon ) { ?>
			<span class="masterstudy-single-course-current-students__icon"></span>
		<?php } ?>
		<div class="masterstudy-single-course-current-students__wrapper">
			<span class="masterstudy-single-course-current-students__count">
				<?php echo esc_html( number_format_i18n( $current_students ) ); ?>
			</span>
			<span class="masterstudy-single-course-current-students__title">
				<span>
					<?php echo 1 === $current_students ? esc_html__( 'Student', 'masterstudy-lms-learning-management-system' ) : esc_html__( 'Students', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
					<?php echo esc_html__( 'enrolled', 'masterstudy-lms-learning-management-system' ); ?>
			</span>
		</div>
	</div>
	<?php
}
