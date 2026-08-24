<?php
$block_wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'archive-courses-filter-category archive-courses-filter-item' . ( $block->context['masterstudy/hideDefault'] ? ' hide-filter' : '' ),
	)
);

$filter_categories = get_terms(
	array(
		'hide_empty' => false,
		'taxonomy'   => 'stm_lms_course_taxonomy',
	)
);
?>
<div <?php echo wp_kses_data( $block_wrapper_attributes ); ?>>
	<div class="lms-courses-filter-option-title">
		<?php echo esc_html__( 'Category', 'masterstudy-lms-learning-management-system' ); ?>
		<div class="lms-courses-filter-option-switcher"></div>
	</div>
	<div class="lms-courses-filter-option-collapse">
		<ul class="lms-courses-filter-option-list">
			<?php foreach ( $filter_categories as $category ) : ?>
			<li class="lms-courses-filter-option-item">
				<label class="lms-courses-filter-checkbox">
					<input type="checkbox" value="<?php echo esc_attr( $category->term_id ); ?>" name="terms" />
					<span class="lms-courses-filter-checkbox-label">
						<?php echo esc_html( $category->name ); ?>
					</span>
				</label>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
