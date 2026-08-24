<?php

$testimonials = new WP_Query(
	array(
		'post_type'      => 'testimonial',
		'posts_per_page' => $testimonials_max_num,
	)
);

$slide_col = 12 / $testimonials_slides_per_row;

wp_enqueue_script( 'owl.carousel' );
wp_enqueue_style( 'owl.carousel' );
stm_module_scripts( 'testimonials', $style, null, 'js', true );
stm_module_styles( 'testimonials', $style );

?>

<?php if ( $testimonials->have_posts() ) : ?>
	<div class="testimonials_main_wrapper simple_carousel_wrapper stm_testimonials_wrapper_style_7">
		<div class="testimonials_control_bar_top">

			<?php if ( ! empty( $testimonials_title ) ) : ?>
				<div class="pull-left">
					<h2 class="testimonials_main_title"><?php echo esc_html( $testimonials_title ); ?></h2>
				</div>
			<?php endif; ?>

			<div class="testimonials_control_bar">
				<a href="#" class="btn-carousel-control simple_carousel_prev" title="<?php esc_attr_e( 'Scroll Carousel left', 'masterstudy' ); ?>">
					<i class="fa-icon-stm_icon_chevron_left"></i>
				</a>
				<a href="#" class="btn-carousel-control simple_carousel_next" title="<?php esc_attr_e( 'Scroll Carousel right', 'masterstudy' ); ?>">
					<i class="fa-icon-stm_icon_chevron_right"></i>
				</a>
			</div>

		</div>
		<div class="testimonials-carousel-unit">
			<div class="testimonials-carousel-init simple_carousel_init" data-items="<?php echo esc_attr( $testimonials_slides_per_row ); ?>">
				<?php
				while ( $testimonials->have_posts() ) :
						$testimonials->the_post();
					?>
					<?php $sphere = get_post_meta( get_the_id(), 'testimonial_profession', true ); ?>
					<div class="col-md-<?php echo esc_attr( $slide_col ); ?> col-sm-12 col-xs-12">
						<div class="testimonial_inner_wrapper">
							<div class="testimonial_inner_content" style="color:<?php echo esc_attr( $testimonials_text_color ); ?>"><?php echo wp_kses_post( masterstudy_substr_text( get_the_excerpt(), $number_of_letters ) ); ?></div>
							<h4 class="testimonials-inner-title" style="color:<?php echo esc_attr( $testimonials_text_color ); ?>"><?php the_title(); ?></h4>
							<?php if ( ! has_post_thumbnail() ) : ?>
								<?php if ( ! empty( $sphere ) ) : ?>
									<div class="testimonial_sphere"><?php echo esc_html( $sphere ); ?></div>
								<?php endif; ?>
							<?php else : ?>
								<div class="media">
									<?php $image = stm_get_VC_attachment_img_safe( get_post_thumbnail_id(), '560x560', 'thumbnail', true ); ?>
									<img src="<?php echo esc_url( $image ); ?>" title="<?php the_title(); ?>" alt="<?php the_title(); ?>" />
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endwhile; ?>
			</div>
		</div>
	</div>

	<?php wp_reset_postdata(); ?>

<?php endif; ?>
