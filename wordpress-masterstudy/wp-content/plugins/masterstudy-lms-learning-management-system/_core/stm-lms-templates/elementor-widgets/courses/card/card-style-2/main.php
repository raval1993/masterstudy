<?php
$is_featured_enabled = STM_LMS_Options::get_option( 'enable_featured_courses', true );
foreach ( $courses as $course ) {
	$course            = STM_LMS_Courses::get_course_submetas( $course, $course_image_size );
	$status_bg_style   = ! empty( $course['current_status']['bg_color'] ) ? esc_attr( '--status-background: ' . $course['current_status']['bg_color'] . ';' ) : '';
	$status_text_style = ! empty( $course['current_status']['text_color'] ) ? esc_attr( '--status-text-color: ' . $course['current_status']['text_color'] . ';' ) : '';
	?>
	<div class="ms_lms_courses_card_item <?php echo esc_attr( $is_featured_enabled ? $featured ?? '' : '' ); ?> <?php echo esc_attr( ( 'courses-carousel' === $widget_type ) ? 'swiper-slide' : '' ); ?>">
		<div class="ms_lms_courses_card_item_wrapper">
			<?php if ( ! empty( $course['featured'] ) && $is_featured_enabled ) { ?>
				<div class="ms_lms_courses_card_item_featured <?php echo esc_attr( ( ! empty( $card_data['featured_position'] ) ) ? $card_data['featured_position'] : '' ); ?>">
					<span><?php echo esc_html__( 'Featured', 'masterstudy-lms-learning-management-system' ); ?></span>
				</div>
				<?php
			}
			if ( ! empty( $course['current_status'] ) ) {
				?>
				<div
					<?php if ( ! empty( $status_bg_style ) || ! empty( $status_text_style ) ) : ?>
					style="<?php echo esc_attr( $status_bg_style ); ?> <?php echo esc_attr( $status_text_style ); ?>"
					<?php endif ?>
					class="ms_lms_courses_card_item_status <?php echo esc_attr( ( ! empty( $card_data['status_presets'] ) ) ? $card_data['status_presets'] : '' ); ?> <?php echo esc_attr( ( ! empty( $card_data['status_position'] ) ) ? $card_data['status_position'] : '' ); ?> <?php echo esc_attr( ( ! empty( $course['current_status']['status'] ) ) ? $course['current_status']['status'] : '' ); ?>">
					<span><?php echo esc_html( $course['current_status']['label'] ); ?></span>
				</div>
			<?php } ?>
			<div class="ms_lms_courses_card_item_image_wrapper">
				<a href="<?php echo esc_url( $course['url'] ); ?>" class="ms_lms_courses_card_item_image_link">
					<?php echo wp_kses_post( masterstudy_get_image( $course['id'], $course['lazyload'], 'ms_lms_courses_card_item_image', $course['img_width'], $course['img_height'] ) ); ?>
				</a>
				<?php
				if ( $course['availability'] && is_ms_lms_addon_enabled( 'coming_soon' ) ) {
					STM_LMS_Templates::show_lms_template(
						'global/coming_soon',
						array(
							'course_id' => $course['id'],
							'mode'      => 'card',
						),
					);
				} elseif ( ! empty( $card_data['show_price'] ) ) {
					STM_LMS_Templates::show_lms_template(
						'elementor-widgets/courses/card/global/price',
						array(
							'card_data' => $card_data,
							'course'    => $course,
						)
					);
				}
				?>
			</div>
			<div class="ms_lms_courses_card_item_info">
				<?php
				if ( ! empty( $card_data['show_rating'] ) && $course['reviews_show'] ) {
					STM_LMS_Templates::show_lms_template(
						'elementor-widgets/courses/card/global/rating',
						array(
							'card_data' => $card_data,
							'course'    => $course,
						)
					);
				}
				if ( ! empty( $card_data['show_category'] ) && ! empty( $course['terms'] ) ) {
					?>
					<span class="ms_lms_courses_card_item_info_category">
						<a href="<?php echo esc_url( STM_LMS_Course::courses_page_url() . '?terms[]=' . $course['terms']->term_id . '&category[]=' . $course['terms']->term_id ); ?>">
							<?php echo esc_html( $course['terms']->name ); ?>
						</a>
					</span>
				<?php } ?>
					<a href="<?php echo esc_url( $course['url'] ); ?>" class="ms_lms_courses_card_item_info_title">
						<h3><?php echo esc_html( $course['post_title'] ); ?></h3>
					</a>
				<?php
				if ( ! empty( $card_data['show_progress'] && $course['progress'] > 0 ) ) {
					STM_LMS_Templates::show_lms_template(
						'elementor-widgets/courses/card/global/progress-bar',
						array(
							'course'    => $course,
							'card_data' => $card_data,
						)
					);
				}
				if ( ! empty( $card_data['show_slots'] ) && ! ( 'empty' === $meta_slots['card_slot_1'] && 'empty' === $meta_slots['card_slot_2'] ) ) {
					?>
					<div class="ms_lms_courses_card_item_info_meta">
						<?php
						if ( 'empty' !== $meta_slots['card_slot_1'] ) {
							STM_LMS_Templates::show_lms_template(
								'elementor-widgets/courses/card/global/meta-slot/main',
								array(
									'meta_slot' => $meta_slots['card_slot_1'],
									'course'    => $course,
								)
							);
						}
						if ( 'empty' !== $meta_slots['card_slot_2'] ) {
							STM_LMS_Templates::show_lms_template(
								'elementor-widgets/courses/card/global/meta-slot/main',
								array(
									'meta_slot' => $meta_slots['card_slot_2'],
									'course'    => $course,
								)
							);
						}
						?>
					</div>
					<?php
				}
				if ( ! empty( $card_data['show_divider'] ) ) {
					?>
					<span class="ms_lms_courses_card_item_info_divider"></span>
					<?php
				}
				if ( ! empty( $card_data['show_slots'] ) && ! ( 'empty' === $meta_slots['card_slot_3'] && 'empty' === $meta_slots['card_slot_4'] ) ) {
					?>
					<div class="ms_lms_courses_card_item_info_bottom_wrapper">
						<?php
						if ( 'empty' !== $meta_slots['card_slot_3'] ) {
							STM_LMS_Templates::show_lms_template(
								'elementor-widgets/courses/card/global/meta-slot/main',
								array(
									'meta_slot' => $meta_slots['card_slot_3'],
									'course'    => $course,
								)
							);
						}
						if ( 'empty' !== $meta_slots['card_slot_4'] ) {
							STM_LMS_Templates::show_lms_template(
								'elementor-widgets/courses/card/global/meta-slot/main',
								array(
									'meta_slot' => $meta_slots['card_slot_4'],
									'course'    => $course,
								)
							);
						}
						?>
					</div>
				<?php } ?>
			</div>
		</div>
		<?php
		if ( ! empty( $card_data['show_popup'] ) ) {
			STM_LMS_Templates::show_lms_template(
				'elementor-widgets/courses/card/global/popup',
				array(
					'course'              => $course,
					'meta_slots'          => $meta_slots,
					'popup_data'          => $popup_data,
					'course_card_presets' => $course_card_presets,
				)
			);
		}
		?>
	</div>
	<?php
}
