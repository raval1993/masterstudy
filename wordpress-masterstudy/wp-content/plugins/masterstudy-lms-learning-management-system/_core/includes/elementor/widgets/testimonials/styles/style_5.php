<div class="testimonials_main_wrapper simple_carousel_wrapper stm-testimonials-carousel-wrapper swiper-container stm-testimonials-carousel-wrapper-style_5
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
	<div class="testimonials_control_bar_top">
		<?php if ( ! empty( $testimonials_title ) ) : ?>
			<div class="pull-left">
				<h2 class="testimonials_main_title"><?php echo esc_html( $testimonials_title ); ?></h2>
			</div>
		<?php endif; ?>

		<div class="pull-right testimonials_control_bar">
			<div class="clearfix">
				<div class="pull-right">
					<div class="btn-carousel-control simple_carousel_prev swiper-button-prev" title="<?php esc_attr_e( 'Scroll Carousel left', 'masterstudy-lms-learning-management-system' ); ?>"></div>
					<div class="btn-carousel-control simple_carousel_next swiper-button-next" title="<?php esc_attr_e( 'Scroll Carousel right', 'masterstudy-lms-learning-management-system' ); ?>"></div>
				</div>
			</div>
		</div>
	</div>

	<div class="testimonials-carousel-unit owl-stage-outer testimonials-carousel-init simple_carousel_init clearfix elementor-testimonials-carousel swiper-wrapper">
		<?php
		foreach ( $testimonials as $testimonial ) {
			$image_html = '';
			if ( ! empty( $testimonial['image'] ) && ! empty( $testimonial['image']['id'] ) ) {
				$image_html = wp_get_attachment_image(
					$testimonial['image']['id'],
					'thumbnail',
					false,
					array(
						'class' => 'testimonial-media-unit-rounded',
						'alt'   => ! empty( $testimonial['author_name'] ) ? $testimonial['author_name'] : '',
					)
				);
			}
			?>
			<div class="col-md-12 col-sm-12 col-xs-12 ms-lms-testimonial-data swiper-slide">
				<div class="testimonial_inner_wrapper">
					<?php if ( empty( $image_html ) ) : ?>
						<?php if ( ! empty( $testimonial['author_name'] ) ) : ?>
							<h4 class="testimonials-inner-title author-name"><?php echo esc_html( $testimonial['author_name'] ); ?></h4>
						<?php endif; ?>
						<?php if ( ! empty( $testimonial['author_position'] ) ) : ?>
							<div class="testimonial_sphere author-position"><?php echo esc_html( $testimonial['author_position'] ); ?></div>
						<?php endif; ?>
					<?php else : ?>
						<div class="media">
							<div class="media-left media-top">
								<div class="testimonial-media-unit">
									<?php echo wp_kses_post( $image_html ); ?>
								</div>
							</div>
							<div class="media-body">
								<?php if ( ! empty( $testimonial['author_name'] ) ) : ?>
									<h4 class="testimonials-inner-title author-name"><?php echo esc_html( $testimonial['author_name'] ); ?></h4>
								<?php endif; ?>
								<?php if ( ! empty( $testimonial['author_position'] ) ) : ?>
									<div class="testimonial_sphere author-position"><?php echo esc_html( $testimonial['author_position'] ); ?></div>
								<?php endif; ?>
								<?php if ( ! $image_html ) : ?>
									<div class="short_separator"></div>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>

					<div class="testimonial_inner_content content">
						<?php echo wp_kses_post( $testimonial['content'] ); ?>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</div>
