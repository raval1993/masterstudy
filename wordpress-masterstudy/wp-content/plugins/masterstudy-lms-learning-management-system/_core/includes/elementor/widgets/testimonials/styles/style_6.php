<div class="stm-testimonials-carousel-wrapper swiper-container stm-testimonials-carousel-wrapper-style_6
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
		<?php if ( ! empty( $testimonials_title ) ) : ?>
			<h2 class="testimonials_main_title testimonials_main_title_6"><?php echo esc_html( $testimonials_title ); ?></h2>
		<?php endif; ?>
		<div class="testimonials_style_6_quote"><i class="fa fa-quote-right"></i></div>
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
				<div class="content">
					<?php echo wp_kses_post( $testimonial['content'] ); ?>
				</div>
				<div class="author-name"><?php echo esc_html( $testimonial['author_name'] ); ?></div>
				<?php if ( ! empty( $testimonial['author_position'] ) ) : ?>
					<div class="author-position"><?php echo esc_html( $testimonial['author_position'] ); ?></div>
				<?php endif; ?>
			</div>
		<?php } ?>
	</div>
	<div class="ms-lms-elementor-testimonials-swiper-pagination"></div>
</div>
