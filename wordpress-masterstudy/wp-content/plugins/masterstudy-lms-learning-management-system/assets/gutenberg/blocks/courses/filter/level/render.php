<?php
$block_wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'archive-courses-filter-levels archive-courses-filter-item' . ( $block->context['masterstudy/hideDefault'] ? ' hide-filter' : '' ),
	)
);

$filter_levels = STM_LMS_Helpers::get_course_levels();
?>
<div <?php echo wp_kses_data( $block_wrapper_attributes ); ?>>
	<div class="lms-courses-filter-option-title">
		<?php echo esc_html__( 'Level', 'masterstudy-lms-learning-management-system' ); ?>
		<div class="lms-courses-filter-option-switcher"></div>
	</div>
	<div class="lms-courses-filter-option-collapse">
		<ul class="lms-courses-filter-option-list">
			<?php foreach ( $filter_levels as $level_slug => $level_name ) : ?>
			<li class="lms-courses-filter-option-item">
				<label class="lms-courses-filter-checkbox">
					<input type="checkbox" value="<?php echo esc_attr( $level_slug ); ?>" name="level" />
					<span class="lms-courses-filter-checkbox-label">
					<?php echo esc_html( $level_name ); ?>
				</span>
				</label>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
