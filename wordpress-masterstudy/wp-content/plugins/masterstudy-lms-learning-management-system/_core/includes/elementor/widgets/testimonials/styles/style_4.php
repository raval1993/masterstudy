<div class="stm-testimonials-carousel-wrapper swiper-container stm-testimonials-carousel-wrapper-style_4
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
	<?php if ( ! empty( $testimonials_title ) ) : ?>
		<div class="ms-lms-testimonials-header">
			<p><?php echo esc_html( $testimonials_title ); ?></p>
		</div>
	<?php endif; ?>

	<div class="elementor-testimonials-carousel swiper-wrapper">
		<?php foreach ( $testimonials as $testimonial ) { ?>
			<div class="ms-lms-testimonial-data swiper-slide">
				<div class="content">
					<?php echo wp_kses_post( $testimonial['content'] ); ?>
				</div>
				<div class="ms-lms-testimonial-author">
					<?php
					if ( ! empty( $testimonial['image'] ) && ! empty( $testimonial['image']['id'] ) ) {
						$image_src = wp_get_attachment_image_src( $testimonial['image']['id'], 'thumbnail' );
						?>
						<div class="ms-lms-testimonial-media">
							<img src="<?php echo esc_url( $image_src[0] ); ?>" alt="<?php echo esc_html( $testimonial['author_name'] ); ?>">
						</div>
					<?php } ?>
					<div class="ms-lms-testimonial-author-data">
						<div class="author-name"><?php echo esc_html( $testimonial['author_name'] ); ?></div>
						<?php if ( ! empty( $testimonial['author_position'] ) ) : ?>
							<div class="author-position"><?php echo esc_html( $testimonial['author_position'] ); ?></div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>

	<?php $has_custom_arrows = ! empty( $navigation_prev_icon['value'] ) || ! empty( $navigation_next_icon['value'] ); ?>
	<div class="swiper-button-prev <?php echo $has_custom_arrows ? 'has-custom-arrow-icon' : ''; ?>">
		<?php if ( ! empty( $navigation_prev_icon['value'] ) ) : ?>
			<span class="ms-lms-testimonials-arrow-icon">
				<i class="<?php echo esc_attr( $navigation_prev_icon['value'] ); ?>" aria-hidden="true"></i>
			</span>
		<?php endif; ?>
	</div>
	<div class="swiper-button-next <?php echo $has_custom_arrows ? 'has-custom-arrow-icon' : ''; ?>">
		<?php if ( ! empty( $navigation_next_icon['value'] ) ) : ?>
			<span class="ms-lms-testimonials-arrow-icon">
				<i class="<?php echo esc_attr( $navigation_next_icon['value'] ); ?>" aria-hidden="true"></i>
			</span>
		<?php endif; ?>
	</div>
</div>

