<?php
/**
 * @var array $instructor
 * @var boolean $without_title
 * @var object $course
 */

$without_title     = isset( $without_title ) ? $without_title : false;
$co_instructor     = ! empty( $course->co_instructor ) ? STM_LMS_User::get_current_user( $course->co_instructor->ID ) : false;
$instructor_class  = $without_title ? ' masterstudy-single-course-instructor_no-title' : '';
$instructor_class .= $co_instructor ? ' masterstudy-single-course-instructor_co-instructor' : '';
$instructor_public = STM_LMS_Options::get_option( 'instructor_public_profile', true );
?>

<div class="masterstudy-single-course-instructor <?php echo esc_attr( $instructor_class ); ?>">
	<div class="masterstudy-single-course-instructor__avatar">
		<?php
		if ( $course->is_udemy_course ) {
			?>
			<img src="<?php echo esc_url( $course->udemy_instructor['image_100x100'] ); ?>">
			<?php
		} else {
			echo wp_kses_post( $instructor['avatar'] );
			if ( $co_instructor ) {
				echo wp_kses_post( $co_instructor['avatar'] );
			}
		}
		?>
	</div>
	<div class="masterstudy-single-course-instructor__info">
		<?php if ( ! $without_title ) { ?>
			<div class="masterstudy-single-course-instructor__title">
				<?php
				if ( $co_instructor ) {
					echo esc_html__( 'Instructors', 'masterstudy-lms-learning-management-system' );
				} else {
					echo esc_html__( 'Instructor', 'masterstudy-lms-learning-management-system' );
				}
				?>
			</div>
		<?php } ?>
		<a class="masterstudy-single-course-instructor__name <?php echo ! $instructor_public ? 'masterstudy-single-course-instructor__name_disabled' : ''; ?>"
			<?php if ( $instructor_public ) { ?>
				href="<?php echo $course->is_udemy_course ? esc_url( "https://www.udemy.com{$course->udemy_instructor['url']}" ) : esc_url( STM_LMS_User::instructor_public_page_url( $instructor['id'] ) ); ?>"
			<?php } ?>
			target="_blank"
		>
			<?php
			if ( $course->is_udemy_course ) {
				echo esc_html( $course->udemy_instructor['display_name'] );
			} else {
				echo esc_html( $instructor['login'] );
			}
			?>
		</a>
		<?php if ( ! $course->is_udemy_course && $co_instructor ) { ?>
			<a class="masterstudy-single-course-instructor__co-instructor <?php echo ! $instructor_public ? 'masterstudy-single-course-instructor__co-instructor_disabled' : ''; ?>"
				<?php if ( $instructor_public ) { ?>
					href="<?php echo esc_url( STM_LMS_User::instructor_public_page_url( $co_instructor['id'] ) ); ?>"
				<?php } ?>
				target="_blank"
			>
				<?php echo esc_html( $co_instructor['login'] ); ?>
			</a>
		<?php } ?>
	</div>
</div>
