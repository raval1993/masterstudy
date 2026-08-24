<?php
/**
 * @var array $course
 */

if ( $course['membership_expired'] ) {
	$text = __( 'Membership expired', 'masterstudy-lms-learning-management-system' );
} elseif ( $course['membership_inactive'] ) {
	$text = __( 'Membership inactive', 'masterstudy-lms-learning-management-system' );
} else {
	return;
}
?>

<div class="masterstudy-course-card__membership-status">
	<?php echo esc_html( $text ); ?>
</div>
