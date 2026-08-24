<div class="stm-testimonials-carousel-wrapper-style_2
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
	">
	<div class="ms-lms-testimonials-header">
		<p><?php echo esc_html( $testimonials_title ); ?></p>
	</div>
	<div class="stm-testimonials-carousel-shapes">
		<div class="stm-testimonials-carousel-wrapper swiper-container" id="<?php echo esc_attr( $unique_id ); ?>">
			<div class="elementor-testimonials-carousel swiper-wrapper">
				<?php foreach ( $testimonials as $testimonial ) { ?>
				<div class="ms-lms-testimonial-data swiper-slide">
					<div class="content">
						<?php echo wp_kses_post( $testimonial['content'] ); ?>
					</div>
					<?php if ( $testimonial['review_rating'] > 0 ) : ?>
					<div class="ms-lms-testimonial-review-rating">
						<?php echo wp_kses_post( str_repeat( '<i class="stmlms-star-3"></i>', intval( $testimonial['review_rating'] ) ) ); ?>
					</div>
					<?php endif; ?>
					<div class="author-name"><?php echo esc_html( $testimonial['author_name'] ); ?></div>
					<?php
					if ( ! empty( $testimonial['image'] ) && ! empty( $testimonial['image']['id'] ) ) {
						$image_src = wp_get_attachment_image_src( $testimonial['image']['id'], 'full' );
						?>
						<div class="ms-lms-testimonial-media">
							<img src="<?php echo esc_url( $image_src[0] ); ?>" alt="<?php echo esc_html( $testimonial['author_name'] ); ?>" >
						</div>
					<?php } ?>
				</div>
				<?php } ?>
			</div>
			<div class="swiper-button-prev"></div>
			<div class="swiper-button-next"></div>
		</div>
	</div>
</div>
