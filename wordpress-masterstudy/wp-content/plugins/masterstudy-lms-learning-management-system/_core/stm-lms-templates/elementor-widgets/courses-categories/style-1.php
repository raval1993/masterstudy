<?php
/**
 * @var array $taxonomy
 * @var array $terms
 * */
?>
<div class="masterstudy-courses-category-widget masterstudy-courses-category-widget__style-1">
	<?php foreach ( $terms as $_term ) : ?>
		<?php
		$icon = get_term_meta( $_term->term_id, 'course_icon', true );
		?>
		<a
			href="<?php echo esc_url( STM_LMS_Course::courses_page_url() . '?terms[]=' . $_term->term_id . '&category[]=' . $_term->term_id ); ?>"
			title="<?php echo esc_attr( $_term->name ); ?>"
			class="masterstudy-courses-category__category"
		>
			<span class="masterstudy-courses-category__category-icon <?php echo esc_attr( empty( $icon ) ? 'stmlms-book3' : $icon ); ?>"></span>
			<h4 class="masterstudy-courses-category__category-title"><?php echo esc_attr( $_term->name ); ?></h4>
		</a>
	<?php endforeach; ?>
</div>
