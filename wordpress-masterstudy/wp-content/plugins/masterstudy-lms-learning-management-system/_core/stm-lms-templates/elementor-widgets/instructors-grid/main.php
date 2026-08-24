<?php
$instructor_public = STM_LMS_Options::get_option( 'instructor_public_profile', true );
?>

<div class="ms_lms_instructors_grid">
	<div class="ms_lms_instructors_grid__header <?php echo ( ! empty( $widget_header_presets ) ) ? esc_attr( $widget_header_presets ) : 'style_1'; ?>">
		<?php
		if ( ! empty( $widget_title ) ) {
			?>
			<h2 class="ms_lms_instructors_grid__header_title">
				<?php echo esc_html( $widget_title ); ?>
			</h2>
			<?php
		}
		if ( ! empty( $widget_description ) ) {
			?>
			<p class="ms_lms_instructors_grid__header_description">
				<?php echo esc_html( $widget_description ); ?>
			</p>
			<?php
		}
		if ( ! empty( $show_view_all ) ) {
			?>
			<a class="ms_lms_instructors_grid__header_view_all" href="<?php echo esc_url( STM_LMS_Instructor::get_instructors_url() ); ?>">
				<?php esc_html_e( 'View all', 'masterstudy-lms-learning-management-system' ); ?>
				<i class="stmlms-arrow-right"></i>
			</a>
			<?php
		}
		?>
	</div>
	<?php if ( ! empty( $instructors ) ) { ?>
		<div class="ms_lms_instructors_grid__content">
			<?php
			foreach ( $instructors as $instructor ) {
				$user_profile_url = STM_LMS_User::instructor_public_page_url( $instructor->ID );
				$user             = STM_LMS_User::get_current_user( $instructor->ID, false, true );
				$reviews          = STM_LMS_Options::get_option( 'course_tab_reviews', true );
				$rating           = STM_LMS_Instructor::my_rating_v2( $user );
				?>
				<div class="ms_lms_instructors_grid__item <?php echo ( ! empty( $instructor_card_presets ) ) ? esc_attr( $instructor_card_presets ) : 'style_1'; ?>">
					<div class="ms_lms_instructors_grid__item_wrapper">
						<a
						<?php if ( $instructor_public ) { ?>
							href="<?php echo esc_url( $user_profile_url ); ?>"
						<?php } ?>
							class="ms_lms_instructors_grid__item_link"
						>
						</a>
						<?php if ( ! empty( $show_avatars ) && ! empty( $user['avatar_url'] ) ) { ?>
							<div class="ms_lms_instructors_grid__item_avatar">
								<?php
								if ( ! empty( $show_socials ) && ! empty( $instructor_card_presets ) && 'style_5' === $instructor_card_presets ) {
									STM_LMS_Templates::show_lms_template(
										'elementor-widgets/instructors-grid/instructor/socials-inside',
										array(
											'show_socials' => $show_socials,
											'instructor_card_presets' => $instructor_card_presets,
											'socials_presets' => $socials_presets,
											'user' => $user,
										)
									);
								}
								?>
								<a href="<?php echo esc_url( $user_profile_url ); ?>" class="ms_lms_instructors_grid__item_avatar_link">
									<img src="<?php echo esc_url( $user['avatar_url'] ); ?>" class="ms_lms_instructors_grid__item_avatar_img">
								</a>
							</div>
						<?php } ?>
						<a href="<?php echo esc_url( $user_profile_url ); ?>" class="ms_lms_instructors_grid__item_info">
							<h3 class="ms_lms_instructors_grid__item_title"><?php echo esc_attr( $user['login'] ); ?></h3>
							<?php
							if ( ! empty( $show_instructor_position ) && ! empty( $user['meta']['position'] ) ) {
								STM_LMS_Templates::show_lms_template(
									'elementor-widgets/instructors-grid/instructor/position',
									array(
										'show_instructor_position' => $show_instructor_position,
										'user' => $user,
									)
								);
							}
							if ( ! empty( $show_instructor_course_quantity ) && ! empty( $instructor->course_quantity ) ) {
								STM_LMS_Templates::show_lms_template(
									'elementor-widgets/instructors-grid/instructor/courses',
									array(
										'show_instructor_course_quantity' => $show_instructor_position,
										'instructor' => $instructor,
									)
								);
							}
							if ( ! empty( $show_reviews ) && ! empty( $rating['total'] ) && $reviews ) {
								STM_LMS_Templates::show_lms_template(
									'elementor-widgets/instructors-grid/instructor/reviews',
									array(
										'show_reviews' => $show_reviews,
										'rating'       => $rating,
										'show_reviews_count' => $show_reviews_count,
									)
								);
							}
							?>
						</a>
						<?php
						if ( ! empty( $show_socials ) && ! empty( $instructor_card_presets ) && 'style_5' !== $instructor_card_presets ) {
							STM_LMS_Templates::show_lms_template(
								'elementor-widgets/instructors-grid/instructor/socials',
								array(
									'show_socials'    => $show_socials,
									'instructor_card_presets' => $instructor_card_presets,
									'socials_presets' => $socials_presets,
									'user'            => $user,
								)
							);
						}
						?>
					</div>
				</div>
			<?php } ?>
		</div>
	<?php } else { ?>
		<p class="ms_lms_instructors_grid__no_results"><?php echo esc_html_e( 'No instructors found', 'masterstudy-lms-learning-management-system' ); ?></p>
	<?php } ?>
</div>
