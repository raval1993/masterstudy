<?php
$block_wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'archive-courses-filter-statuses archive-courses-filter-item' . ( ( $block->context['masterstudy/hideDefault'] ?? false ) ? ' hide-filter' : '' ),
	)
);

$filter_statuses = STM_LMS_Helpers::get_course_statuses();
?>
<div <?php echo wp_kses_data( $block_wrapper_attributes ); ?>>
	<div class="lms-courses-filter-option-title">
		<?php echo esc_html__( 'Status', 'masterstudy-lms-learning-management-system' ); ?>
		<div class="lms-courses-filter-option-switcher"></div>
	</div>
	<div class="lms-courses-filter-option-collapse">
		<ul class="lms-courses-filter-option-list">
			<?php foreach ( $filter_statuses as $status_slug => $_status ) : ?>
			<li class="lms-courses-filter-option-item">
				<label class="lms-courses-filter-checkbox">
					<input type="checkbox" value="<?php echo esc_attr( $status_slug ); ?>" name="status" />
					<span class="lms-courses-filter-checkbox-label">
					<?php echo esc_html( $_status['label'] ); ?>
				</span>
				</label>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
