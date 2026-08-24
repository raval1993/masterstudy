<?php
/**
 * @var $id
 */

$rating  = get_post_meta( $id, 'course_marks', true );
$reviews = STM_LMS_Options::get_option( 'course_tab_reviews', true );

if ( ! empty( $rating ) ) {
	$rates   = STM_LMS_Course::course_average_rate( $rating );
	$average = $rates['average'];
	$percent = $rates['percent'];
	$total   = count( $rating );
}

?>

<?php if ( ! empty( $average ) && $reviews ) : ?>
	<div class="average-rating-stars__top">
		<div class="star-rating">
			<span style="width: <?php echo esc_attr( $percent ); ?>%">
				<strong class="rating"><?php echo wp_kses_post( sanitize_text_field( $average ) ); ?></strong>
			</span>
		</div>
		<div class="average-rating-stars__av heading_font">
			<?php echo wp_kses_post( sanitize_text_field( $average ) ); ?>
		</div>
	</div>
	<?php
else :
	$hours = get_post_meta( $id, 'duration_info', true );
	if ( ! empty( $hours ) ) :
		?>
		<div class="stm_lms_courses__hours">
			<i class="stmlms-lms-clocks"></i>
			<span><?php echo wp_kses_post( $hours ); ?></span>
		</div>
	<?php endif; ?>

	<?php
endif;
