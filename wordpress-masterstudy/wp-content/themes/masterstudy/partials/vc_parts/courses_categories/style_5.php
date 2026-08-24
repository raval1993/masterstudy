<?php
stm_module_styles('course_category', 'style_5');
$css_class = (isset($css_class) && !empty(trim($css_class))) ? $css_class : '';
if (empty($taxonomy)) $taxonomy = 'get_default';

if (!empty($taxonomy)):
    if ($taxonomy === 'get_default') {
        $terms = array();
        $terms_all = stm_lms_get_terms_with_meta(null, null, array('number' => 6));
        if (!empty($terms_all)) {
            foreach ($terms_all as $term) {
                $meta_value = get_term_meta($term->term_id, 'course_image', true);
                $terms[] = $term->term_id;
            }
        }
    } else {
        $terms = explode(',', str_replace(' ', '', $taxonomy) );
    }


    if (!empty($terms) and is_array($terms)): ?>
        <div class="stm_lms_courses_categories <?php echo esc_attr($style . $css_class); ?>">

            <?php foreach ($terms as $key => $term):
                $term = get_term_by('id', $term, 'stm_lms_course_taxonomy');
                if (empty($term) or is_wp_error($term)) continue;
                $term_image = get_term_meta($term->term_id, 'course_image', true);
                $term_image = wp_get_attachment_image($term_image);
                if(empty($term_image)) {
                    $image_url = get_template_directory_uri() . '/assets/img/category_placeholder.png';
                    $term_image = "<img src='{$image_url}'>";
                }
                ?>
                <div class="stm_lms_courses_category">
                    <a href="<?php echo esc_url(get_term_link($term, 'stm_lms_course_taxonomy')); ?>"
                       title="<?php echo esc_attr($term->name); ?>"
                       class="no_deco">
                        <?php echo wp_kses_post($term_image); ?>
                        <h4><?php echo esc_attr($term->name); ?></h4>
                    </a>
                </div>

            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif;
