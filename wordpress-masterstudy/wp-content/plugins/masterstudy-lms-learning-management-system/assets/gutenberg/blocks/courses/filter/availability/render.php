<?php
$block_wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'archive-courses-filter-availability archive-courses-filter-item' . ( $block->context['masterstudy/hideDefault'] ? ' hide-filter' : '' ),
	)
);

$availabilities = array(
	'all'           => 'All',
	'available_now' => 'Available Now',
	'coming_soon'   => 'Upcoming',
);
?>
<div <?php echo wp_kses_data( $block_wrapper_attributes ); ?>>
	<div class="lms-courses-filter-option-title">
		<?php echo esc_html__( 'Availability', 'masterstudy-lms-learning-management-system' ); ?>
		<div class="lms-courses-filter-option-switcher"></div>
	</div>
	<div class="lms-courses-filter-option-collapse">
		<ul class="lms-courses-filter-option-list">
			<?php foreach ( $availabilities as $key => $availability ) : ?>
			<li class="lms-courses-filter-option-item">
				<label class="lms-courses-filter-radio">
					<input type="radio" value="<?php echo esc_attr( $key ); ?>" name="availability">
					<span class="lms-courses-filter-radio-label">
						<?php echo esc_attr( $availability ); ?>
					</span>
				</label>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
