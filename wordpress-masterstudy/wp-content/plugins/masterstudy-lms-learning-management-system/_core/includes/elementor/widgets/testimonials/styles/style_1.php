<div class="stm-testimonials-carousel-wrapper swiper-container stm-testimonials-carousel-wrapper-style_1
	<?php
		$classes = array_filter(
			array(
				empty( $arrows ) ? 'hide-carousel-arrows' : null,
				empty( $arrows_tablet ) ? 'hide-carousel-arrows-tablet' : null,
				empty( $arrows_mobile ) ? 'hide-carousel-arrows-mobile' : null,
			)
		);

		echo esc_attr( implode( ' ', $classes ) );
		?>
	" id="<?php echo esc_attr( $unique_id ); ?>">
	<div class="ms-lms-testimonials-header">
		<svg xmlns='http://www.w3.org/2000/svg' width='82.722' height='77.014' viewBox='0 0 82.722 77.014' class="ms-lms-testimonials-icon">
			<g id='Group_3160' data-name='Group 3160' transform='translate(-1 -2)'>
			<path id='Path_5183' data-name='Path 5183' d='M76.45,3V56.769H39.225L14.408,69.178V56.769H2V3Z' transform='translate(3.136 3.136)' class="ms-lms-testimonials-icon__fillable" fill='#195ec8'/>
			<path id='Path_5185' data-name='Path 5185' d='M13.408,79.014V64.041H1V2H83.722V64.041H43.354ZM9.272,55.769H21.68v9.844l19.688-9.844H75.45v-45.5H9.272Z' class="ms-lms-testimonials-icon__fillable" transform='translate(0 0)' fill='#195ec8'/>
			<path id='Path_5186' data-name='Path 5186' d='M28.544,6V24.24l-9.472,9.513L13.2,27.88l5.377-5.336H12V6Z' transform='translate(34.497 12.544)' fill='#fff'/>
			<path id='Path_5187' data-name='Path 5187' d='M22.544,6V24.24l-9.472,9.513L7.2,27.88l5.377-5.336H6V6Z' transform='translate(15.68 12.544)' fill='#fff'/>
			</g>
		</svg>
		<p><?php echo esc_html( $testimonials_title ); ?></p>
	</div>
	<div class="elementor-testimonials-carousel swiper-wrapper">
		<?php
		foreach ( $testimonials as $testimonial ) {
			$thumbnail_img = '';
			if ( ! empty( $testimonial['image'] ) && ! empty( $testimonial['image']['id'] ) ) {
				$thumbnail_img = wp_get_attachment_image_src( $testimonial['image']['id'], 'thumbnail' );
			}
			?>
			<div class="ms-lms-testimonial-data swiper-slide"
				data-thumbnail="<?php echo isset( $thumbnail_img[0] ) ? esc_attr( $thumbnail_img[0] ) : ''; ?>">
				<?php if ( $testimonial['review_rating'] > 0 ) : ?>
				<div class="ms-lms-testimonial-review-rating">
					<?php echo wp_kses_post( str_repeat( '<i class="stmlms-star-3"></i>', intval( $testimonial['review_rating'] ) ) ); ?>
				</div>
				<?php endif; ?>
				<div class="author-name"><?php echo esc_html( $testimonial['author_name'] ); ?></div>
				<div class="content">
					<?php echo wp_kses_post( $testimonial['content'] ); ?>
				</div>
			</div>
		<?php } ?>
	</div>
	<div class="ms-lms-elementor-testimonials-swiper-pagination"></div>
	<div class="swiper-button-prev"></div>
	<div class="swiper-button-next"></div>
</div>
