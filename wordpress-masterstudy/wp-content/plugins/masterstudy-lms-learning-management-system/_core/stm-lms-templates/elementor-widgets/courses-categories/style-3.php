<?php
/**
 * @var array $taxonomy
 * @var array $terms
 * */
?>
<div class="masterstudy-courses-category-widget masterstudy-courses-category-widget__style-3">
	<?php foreach ( $terms as $_term ) : ?>
		<?php
		$image_id       = get_term_meta( $_term->term_id, 'course_image', true );
		$image_url      = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';
		$image_fallback = STM_LMS_URL . '/assets/icons/global/category_placeholder.png';
		?>
		<a
			href="<?php echo esc_url( STM_LMS_Course::courses_page_url() . '?terms[]=' . $_term->term_id . '&category[]=' . $_term->term_id ); ?>"
			title="<?php echo esc_attr( $_term->name ); ?>"
			class="masterstudy-courses-category__category"
		>
			<img class="masterstudy-courses-category__category-image-icon" src="<?php echo esc_url( empty( $image_url ) ? $image_fallback : $image_url ); ?>" alt="category icon">
			<h4 class="masterstudy-courses-category__category-title"><?php echo esc_attr( $_term->name ); ?></h4>
		</a>
	<?php endforeach; ?>
</div>
