<?php
/**
 * @var string $student_id
 * @var string $course_id
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lms_current_user = (array) STM_LMS_User::get_current_user( '', true, true );

do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $lms_current_user );
wp_enqueue_style( 'masterstudy-account-main' );

$lms_page_path = get_query_var( 'lms_page_path' );
$course_id     = intval( $course_id ?? $_GET['course_id'] ?? 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$student_id    = intval( $student_id ?? $_GET['student_id'] ?? 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

if (
	! is_user_logged_in() ||
	! STM_LMS_Instructor::instructor_show_list_students() && 'enrolled-students' === $lms_page_path ||
	! STM_LMS_Instructor::instructor_can_add_students() && 'enrolled-students' !== $lms_page_path ||
	! STM_LMS_Instructor::is_instructor() ||
	! \STM_LMS_Course::check_course_author( $course_id, get_current_user_id() )
) {
	wp_safe_redirect( STM_LMS_User::login_page_url() );
	die;
}

$course = STM_LMS_Course::get_user_course( $student_id, $course_id );

if ( ! empty( $student_id ) && ( empty( $course ) || intval( $course['course_id'] ) !== $course_id ) ) {
	wp_safe_redirect( STM_LMS_Instructor::instructor_manage_students_url() . "/?course_id=$course_id" );
	die;
}

$style = STM_LMS_Options::get_option( 'profile_style', 'default' );
?>

<div class="masterstudy-account">
	<div class="masterstudy-account-sidebar">
		<div class="masterstudy-account-sidebar__wrapper">
			<?php do_action( 'masterstudy_account_sidebar', $lms_current_user ); ?>
		</div>
	</div>
	<div class="masterstudy-account-container">
		<?php do_action( 'stm_lms_admin_after_wrapper_start', $lms_current_user ); ?>
		<?php
		if ( ! empty( $course_id ) && empty( $student_id ) ) :
			?>
			<div id="stm_lms_instructor_manage_students">
				<?php STM_LMS_Templates::show_lms_template( 'account/instructor/parts/manage_students/main', compact( 'course_id' ) ); ?>
			</div>
		<?php endif; ?>

		<?php
		if ( ! empty( $course_id ) && ! empty( $student_id ) ) :
			?>
			<div id="stm_lms_instructor_manage_students">
				<?php STM_LMS_Templates::show_lms_template( 'account/instructor/parts/manage_students/student-progress', compact( 'course_id', 'student_id' ) ); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php do_action( 'masterstudy_after_account', $lms_current_user ); ?>
