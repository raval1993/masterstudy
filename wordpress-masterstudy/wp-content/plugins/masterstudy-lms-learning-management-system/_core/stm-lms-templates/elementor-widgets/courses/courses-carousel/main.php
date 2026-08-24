<div class="ms_lms_courses_carousel">
	<?php if ( ! empty( $show_header ) ) { ?>
		<div class="ms_lms_courses_carousel__title <?php echo esc_attr( ! empty( $header_presets ) ? $header_presets : '' ); ?>">
			<h2>
				<?php echo esc_html( $title_text ); ?>
			</h2>
			<?php
			if ( ! empty( $show_sorting ) && ! empty( $sorting_data['sort_options'] ) ) {
				STM_LMS_Templates::show_lms_template(
					"elementor-widgets/courses/courses-carousel/sorting/{$sort_presets}",
					array(
						'sorting_data' => $sorting_data,
					)
				);
			}
			if ( ! empty( $show_navigation ) && 'top' === $navigation_position && empty( $show_sorting ) ) {
				$prev_icon = $navigation_arrows_prev_icon ?? array();
				$next_icon = $navigation_arrows_next_icon ?? array();
				?>
				<div class="ms_lms_courses_carousel__navigation">
					<button class="ms_lms_courses_carousel__navigation_prev <?php echo ( ! empty( $navigation_presets ) ) ? esc_attr( $navigation_presets ) : 'style_1'; ?>">
						<?php
						if ( ! empty( $prev_icon['value'] ) ) {
							\Elementor\Icons_Manager::render_icon( $prev_icon, array( 'aria-hidden' => 'true' ) );
						} else {
							echo '<i class="stmlms-chevron-left" aria-hidden="true"></i>';
						}
						?>
					</button>
					<button class="ms_lms_courses_carousel__navigation_next <?php echo ( ! empty( $navigation_presets ) ) ? esc_attr( $navigation_presets ) : 'style_1'; ?>">
						<?php
						if ( ! empty( $next_icon['value'] ) ) {
							\Elementor\Icons_Manager::render_icon( $next_icon, array( 'aria-hidden' => 'true' ) );
						} else {
							echo '<i class="stmlms-chevron-right" aria-hidden="true"></i>';
						}
						?>
					</button>
				</div>
			<?php } ?>
		</div>
	<?php } ?>
	<div class="ms_lms_courses_carousel__content <?php echo esc_attr( ( ! empty( $header_presets ) ) ? " title_{$header_presets}" : '' ); ?>">
		<?php
		if ( ! empty( $show_navigation ) && 'side' === $navigation_position ) {
			$prev_icon_side = $navigation_arrows_prev_icon ?? array();
			?>
			<button class="ms_lms_courses_carousel__navigation_prev side-nav <?php echo ( ! empty( $navigation_presets ) ) ? esc_attr( $navigation_presets ) : 'style_1'; ?>">
				<?php
				if ( ! empty( $prev_icon_side['value'] ) ) {
					\Elementor\Icons_Manager::render_icon( $prev_icon_side, array( 'aria-hidden' => 'true' ) );
				} else {
					echo '<i class="stmlms-chevron-left" aria-hidden="true"></i>';
				}
				?>
			</button>
		<?php } ?>
		<div class="ms_lms_courses_card_wrapper swiper-container">
			<div class="ms_lms_courses_card swiper-wrapper <?php echo ( ! empty( $course_card_presets ) ) ? esc_attr( $course_card_presets ) : ''; ?> <?php echo esc_attr( ( ! empty( $navigation_position && 'top' === $navigation_position ) ) ? 'order-bottom' : '' ); ?>">
				<?php
				if ( ! empty( $featured_courses ) && is_array( $featured_courses ) ) {
					STM_LMS_Templates::show_lms_template(
						"elementor-widgets/courses/card/{$course_card_presets}/main",
						array(
							'courses'             => $featured_courses,
							'course_image_size'   => $course_image_size,
							'meta_slots'          => $meta_slots,
							'card_data'           => $card_data,
							'popup_data'          => $popup_data,
							'course_card_presets' => $course_card_presets,
							'featured'            => 'featured',
							'widget_type'         => $widget_type,
						)
					);
				}
				if ( ! empty( $courses ) && is_array( $courses ) ) {
					STM_LMS_Templates::show_lms_template(
						"elementor-widgets/courses/card/{$course_card_presets}/main",
						array(
							'courses'             => $courses,
							'course_image_size'   => $course_image_size,
							'meta_slots'          => $meta_slots,
							'card_data'           => $card_data,
							'popup_data'          => $popup_data,
							'course_card_presets' => $course_card_presets,
							'widget_type'         => $widget_type,
						)
					);
				}
				?>
			</div>
			<?php
			if ( ( ! empty( $show_navigation ) && 'side' !== $navigation_position ) && ! ( empty( $show_sorting ) && 'top' === $navigation_position ) ) {
				$prev_icon_bottom = $navigation_arrows_prev_icon ?? array();
				$next_icon_bottom = $navigation_arrows_next_icon ?? array();
				?>
				<div class="ms_lms_courses_carousel__navigation">
					<button class="ms_lms_courses_carousel__navigation_prev <?php echo ( ! empty( $navigation_presets ) ) ? esc_attr( $navigation_presets ) : 'style_1'; ?>">
						<?php
						if ( ! empty( $prev_icon_bottom['value'] ) ) {
							\Elementor\Icons_Manager::render_icon( $prev_icon_bottom, array( 'aria-hidden' => 'true' ) );
						} else {
							echo '<i class="stmlms-chevron-left" aria-hidden="true"></i>';
						}
						?>
					</button>
					<button class="ms_lms_courses_carousel__navigation_next <?php echo ( ! empty( $navigation_presets ) ) ? esc_attr( $navigation_presets ) : 'style_1'; ?>">
						<?php
						if ( ! empty( $next_icon_bottom['value'] ) ) {
							\Elementor\Icons_Manager::render_icon( $next_icon_bottom, array( 'aria-hidden' => 'true' ) );
						} else {
							echo '<i class="stmlms-chevron-right" aria-hidden="true"></i>';
						}
						?>
					</button>
				</div>
			<?php } ?>
		</div>
		<?php
		if ( ! empty( $show_navigation ) && 'side' === $navigation_position ) {
			$next_icon_side = $navigation_arrows_next_icon ?? array();
			?>
			<button class="ms_lms_courses_carousel__navigation_next side-nav <?php echo ( ! empty( $navigation_presets ) ) ? esc_attr( $navigation_presets ) : 'style_1'; ?>">
				<?php
				if ( ! empty( $next_icon_side['value'] ) ) {
					\Elementor\Icons_Manager::render_icon( $next_icon_side, array( 'aria-hidden' => 'true' ) );
				} else {
					echo '<i class="stmlms-chevron-right" aria-hidden="true"></i>';
				}
				?>
			</button>
		<?php } ?>
	</div>
</div>
