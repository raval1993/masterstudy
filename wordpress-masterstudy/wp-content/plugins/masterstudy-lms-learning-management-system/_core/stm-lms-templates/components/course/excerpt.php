<?php
/**
 * @var object $course
 * @var boolean $long
 */

$excerpt    = $course->is_udemy_course ? wp_kses_post( $course->udemy_headline ) : wp_kses_post( $course->excerpt );
$more_title = __( 'Show more', 'masterstudy-lms-learning-management-system' );
$less_title = __( 'Show less', 'masterstudy-lms-learning-management-system' );
$long       = isset( $long ) ? $long : false;

wp_localize_script(
	'masterstudy-single-course-components',
	'excerpt_data',
	array(
		'more_title' => $more_title,
		'less_title' => $less_title,
	)
);
?>

<div class="masterstudy-single-course-excerpt">
	<?php
	$words = explode( ' ', $excerpt );

	if ( count( $words ) > 25 && ! $long ) {
		$visible_content = implode( ' ', array_slice( $words, 0, 20 ) );
		$hidden_content  = implode( ' ', array_slice( $words, 20 ) );
		?>
		<div class="masterstudy-single-course-excerpt__content">
			<div class="masterstudy-single-course-excerpt__visible">
				<?php echo wp_kses_post( $visible_content ); ?>
				<span class="masterstudy-single-course-excerpt__continue">...</span>
				<div class="masterstudy-single-course-excerpt__hidden" style="display: none;">
					<?php echo wp_kses_post( $hidden_content ); ?>
				</div>
				<span class="masterstudy-single-course-excerpt__more">
					<?php echo esc_html( $more_title ); ?>
				</span>
			</div>
		</div>
		<?php
	} else {
		echo wp_kses_post( $excerpt );
	}
	?>
</div>
