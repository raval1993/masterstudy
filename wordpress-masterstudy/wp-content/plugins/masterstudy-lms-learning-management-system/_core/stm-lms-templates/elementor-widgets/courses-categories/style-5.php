<?php
/**
 * @var array $taxonomy
 * @var array $terms
 * */

?>
<div class="masterstudy-courses-category-widget masterstudy-courses-category-widget__style-5">
	<?php foreach ( $terms as $_term ) : ?>
		<?php
		$image_id  = get_term_meta( $_term->term_id, 'course_image', true );
		$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';
		?>
		<a
			href="<?php echo esc_url( STM_LMS_Course::courses_page_url() . '?terms[]=' . $_term->term_id . '&category[]=' . $_term->term_id ); ?>"
			title="<?php echo esc_attr( $_term->name ); ?>"
			class="masterstudy-courses-category__category"
		>
			<div class="masterstudy-courses-category__category-image-container">
			<?php if ( empty( $image_url ) ) : ?>
				<div class="masterstudy-courses-category__category-image-fallback"></div>
			<?php else : ?>
				<img class="masterstudy-courses-category__category-image" src="<?php echo esc_url( $image_url ); ?>"
					alt="category image"/>
			<?php endif ?>
			</div>
			<div class="masterstudy-courses-category__category-text-container">
				<span class="masterstudy-courses-category__category-title"><?php echo esc_attr( $_term->name ); ?></span>
				<span class="masterstudy-courses-category__category-subtitle">
					<?php
					printf(
					/* translators: %s: number */
						esc_html__( '%s Courses', 'masterstudy-lms-learning-management-system' ),
						esc_html( STM_LMS_Courses::get_children_terms_count( $_term->term_id ) )
					);
					?>
				</span>
			</div>
		</a>
	<?php endforeach; ?>
</div>
