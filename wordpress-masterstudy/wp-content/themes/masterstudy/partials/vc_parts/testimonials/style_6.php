<?php
$testimonials = new WP_Query(array('post_type' => 'testimonial', 'posts_per_page' => $testimonials_max_num));

$slide_col = 12 / $testimonials_slides_per_row;

wp_enqueue_script('owl.carousel');
wp_enqueue_style('owl.carousel');
stm_module_scripts('testimonials', $style, null, 'js', true);
stm_module_styles('testimonials', $style);

?>

<?php if (!empty($testimonials_title)): ?>
    <h2 class="testimonials_main_title testimonials_main_title_6 text-center">
        <?php echo esc_attr($testimonials_title); ?>
        <i class="fa fa-quote-right"></i>
    </h2>
<?php endif; ?>

<?php if ($testimonials->have_posts()):
    $testimonials_data = array();
    while ($testimonials->have_posts()): $testimonials->the_post();
        $testimonials_data[] = array(
            'id' => get_the_ID(),
            'title' => get_the_title(),
            'excerpt' => get_the_excerpt(),
            'image' => stm_get_VC_attachment_img_safe(get_post_thumbnail_id(), '60x60', 'thumbnail', true),
        );
    endwhile; ?>

    <div class="testimonials_main_wrapper simple_carousel_wrapper simple_carousel_wrapper_style_6">

        <div class="navs">
            <div class="testimonials_navigation">
                <div class="simple_carousel_prev"><i class="lnr lnr-arrow-left"></i></div>
                <div class="simple_carousel_next"><i class="lnr lnr-arrow-right"></i></div>
            </div>

            <ul id="carousel-custom-dots">
                <?php foreach ($testimonials_data as $testimonial): ?>
                    <li class="testinomials_dots_image">
                        <img src="<?php echo esc_url($testimonial['image']) ?>"/>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="stm_testimonials_wrapper_style_6">
            <?php foreach ($testimonials_data as $testimonial): ?>

                <?php
                $sphere = get_post_meta($testimonial['id'], 'testimonial_profession', true);
                $testimonial_user = get_post_meta($testimonial['id'], 'testimonial_user', true);

                ?>

                <div class="stm_testimonials_single">

                    <?php if(!empty($testimonial_user)): ?>
                        <div class="testimonials_title h3">
                            <?php echo sanitize_text_field($testimonial_user); ?>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($sphere)): ?>
                        <div class="sphere">
                            <?php echo esc_html($sphere); ?>
                        </div>
                    <?php endif; ?>

                    <div class="testimonials_excerpt">
                        <?php echo wp_kses_post($testimonial['excerpt']); ?>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>

    </div>

    <?php wp_reset_postdata(); ?>

<?php endif; ?>
