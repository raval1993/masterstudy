<?php
/**
 * @var object $course
 * @var string $style
 */

$style = isset( $style ) ? $style : 'default';
$stars = range( 1, 5 );
$rate  = $course->is_udemy_course ? $course->udemy_rate : $course->rate['average'];

if ( $course->is_udemy_course ) {
	foreach ( $course->udemy_rating_distribution as $index => $review ) {
		$marks_array[ $review['rating'] ] = $review['count'];
	}
	$marks = array_sum( $marks_array );
} else {
	$marks = count( $course->marks );
}
?>

<div class="masterstudy-single-course-rating masterstudy-single-course-rating_<?php echo esc_attr( $style ); ?>">
	<div class="masterstudy-single-course-rating__wrapper">
		<div class="masterstudy-single-course-rating__star-wrapper">
			<?php foreach ( $stars as $star ) { ?>
				<span class="masterstudy-single-course-rating__star <?php echo esc_attr( $star <= floor( $rate ) ? 'masterstudy-single-course-rating__star_filled ' : '' ); ?>"></span>
			<?php } ?>
		</div>
		<div class="masterstudy-single-course-rating__count">
			<?php echo (float) $rate === (int) $rate ? (int) $rate : esc_html( $rate ); ?>
		</div>
		<?php if ( 'accent' === $style ) { ?>
			<span class="masterstudy-single-course-rating__one-star"></span>
		<?php } ?>
	</div>
	<div class="masterstudy-single-course-rating__quantity">
		<?php
		printf(
			esc_html(
				/* translators: %d integer marks */
				_n(
					'%s review',
					'%s reviews',
					$marks,
					'masterstudy-lms-learning-management-system'
				)
			),
			esc_html( $marks )
		);
		?>
	</div>
</div>
