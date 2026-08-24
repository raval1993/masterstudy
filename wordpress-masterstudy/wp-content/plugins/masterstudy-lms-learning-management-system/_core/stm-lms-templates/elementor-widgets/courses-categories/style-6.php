<?php
/**
 * @var array $taxonomy
 * @var array $terms
 */
if ( ! is_array( $terms ) || is_wp_error( $terms ) ) {
	$terms = array();
}
?>
<div class="masterstudy-courses-category-widget masterstudy-courses-category-widget__style-6">
	<?php foreach ( $terms as $_term ) : ?>
		<?php
		$image_id      = get_term_meta( $_term->term_id, 'course_image', true );
		$image_url     = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';
		$color         = get_term_meta( $_term->term_id, 'course_color', true );
		$courses_count = 0;
		if ( class_exists( 'STM_LMS_Courses' ) && method_exists( 'STM_LMS_Courses', 'get_children_terms_count' ) ) {
			$courses_count = STM_LMS_Courses::get_children_terms_count( (int) $_term->term_id );
		}
		?>
		<a
			href="<?php echo esc_url( STM_LMS_Course::courses_page_url() . '?terms[]=' . $_term->term_id . '&category[]=' . $_term->term_id ); ?>"
			title="<?php echo esc_attr( $_term->name ); ?>"
			class="masterstudy-courses-category__category"
			<?php if ( ! empty( $color ) ) : ?>
				style="--masterstudy-style-6-accent: <?php echo esc_attr( $color ); ?>; --masterstudy-style-6-card-bg: <?php echo esc_attr( $color ); ?>;"
			<?php endif; ?>
		>
			<div class="masterstudy-courses-category__category-content">
				<div class="masterstudy-courses-category__category-headings">
					<h4 class="masterstudy-courses-category__category-title"><?php echo esc_html( $_term->name ); ?></h4>
				</div>
				<span class="masterstudy-courses-category__category-link">
					<?php if ( ! empty( $button_icon ) && ! empty( $button_icon['value'] ) ) : ?>
						<span class="masterstudy-courses-category__category-link-icon-wrap">
							<?php
								\Elementor\Icons_Manager::render_icon(
									$button_icon,
									array(
										'aria-hidden' => 'true',
										'class'       => 'masterstudy-courses-category__category-link-icon',
									)
								);
							?>
						</span>
					<?php endif; ?>
					<?php echo esc_html( isset( $link_text ) ? $link_text : __( 'Learn more', 'masterstudy-lms-learning-management-system' ) ); ?>
				</span>
			</div>
			<div class="masterstudy-courses-category__category-image-wrap">
				<?php if ( empty( $image_url ) ) : ?>
					<div class="masterstudy-courses-category__category-image-fallback"></div>
				<?php else : ?>
					<img class="masterstudy-courses-category__category-image" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $_term->name ); ?>" loading="lazy" />
				<?php endif; ?>
			</div>
		</a>
	<?php endforeach; ?>
</div>
