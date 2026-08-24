<?php
/**
 * @var $course
 */

$stars = range( 1, 5 );
?>

<div class="masterstudy-course-card__rating">
	<?php foreach ( $stars as $star ) { ?>
		<span class="masterstudy-course-card__rating-star <?php echo esc_attr( $star <= floor( $course['rating']['average'] ) ? 'masterstudy-course-card__rating-star_filled ' : '' ); ?>"></span>
	<?php } ?>
	<div class="masterstudy-course-card__rating-count">
		<?php echo number_format( $course['rating']['average'], 1, '.', '' ); ?>
	</div>
</div>
