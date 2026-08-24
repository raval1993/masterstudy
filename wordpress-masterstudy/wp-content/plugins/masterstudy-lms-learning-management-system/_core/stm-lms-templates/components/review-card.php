<?php
/**
 * @var object $review
 */

wp_enqueue_style( 'masterstudy-review-card' );

$stars          = range( 1, 5 );
$rating         = get_post_meta( $review->ID, 'review_mark', true );
$user_id        = intval( get_post_meta( $review->ID, 'review_user', true ) );
$course_id      = intval( get_post_meta( $review->ID, 'review_course', true ) );
$course_title   = get_the_title( $course_id );
$course_url     = get_permalink( $course_id );
$student_public = STM_LMS_Options::get_option( 'student_public_profile', true );

if ( $user_id ) {
	$user_info    = get_userdata( $user_id );
	$first_name   = $user_info->first_name;
	$last_name    = $user_info->last_name;
	$username     = $user_info->user_login;
	$display_name = trim( $first_name . ' ' . $last_name );
	if ( empty( $display_name ) ) {
		$display_name = $username;
	}
}
?>

<div class="masterstudy-review-card">
	<div class="masterstudy-review-card__wrapper">
		<div class="masterstudy-review-card__rating">
			<?php foreach ( $stars as $star ) { ?>
				<span class="masterstudy-review-card__rating-star <?php echo esc_attr( $star <= floor( $rating ) ? 'masterstudy-review-card__rating-star_filled ' : '' ); ?>"></span>
			<?php } ?>
			<div class="masterstudy-review-card__rating-count">
				<?php echo number_format( $rating, 1, '.', '' ); ?>
			</div>
		</div>
		<div class="masterstudy-review-card__content">
			<?php echo wp_kses_post( $review->post_content ); ?>
		</div>
		<a
			<?php if ( $student_public ) { ?>
				href="<?php echo esc_url( STM_LMS_User::student_public_page_url( $user_id ) ); ?>"
			<?php } ?>
			class="masterstudy-review-card__author <?php echo ! $student_public ? 'masterstudy-review-card__author_disabled' : ''; ?>"
		>
			<?php echo esc_html( $display_name ); ?>
		</a>
		<div class="masterstudy-review-card__course">
			<?php echo esc_html__( 'In course', 'masterstudy-lms-learning-management-system' ); ?>:
			<a href="<?php echo esc_url( $course_url ); ?>" class="masterstudy-review-card__course-title">
				<?php echo esc_html( $course_title ); ?>
			</a>
		</div>
	</div>
</div>
