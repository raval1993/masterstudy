<div class="stm_lms_splash_wizard__content_tab"
	v-if="active_step === 'single_course'">
	<h4>
		<?php esc_html_e( 'Course details page', 'masterstudy-lms-learning-management-system' ); ?>
	</h4>
	<hr/>
	<div class="stm_lms_splash_wizard__field stm_lms_splash_wizard__field_switch"
		v-bind:class="{'inactive' : !wizard.redirect_after_purchase}">
		<?php
		STM_LMS_Templates::show_lms_template(
			'wizard/views/field_data',
			array(
				'title' => esc_html__( 'Redirect to Checkout', 'masterstudy-lms-learning-management-system' ),
			)
		);
		?>
		<div class="stm_lms_splash_wizard__field_input">
			<?php
			STM_LMS_Templates::show_lms_template(
				'wizard/fields/switcher',
				array(
					'model' => 'wizard.redirect_after_purchase',
					'desc'  => esc_html__( 'Users will go to the checkout page right after adding a course to the cart', 'masterstudy-lms-learning-management-system' ),
				)
			);
			?>
		</div>
	</div>
	<hr/>
	<div class="stm_lms_splash_wizard__field stm_lms_splash_wizard__field_checkboxes stm_lms_splash_wizard__field_splitted">
		<?php
		STM_LMS_Templates::show_lms_template(
			'wizard/views/field_data',
			array(
				'title' => esc_html__( 'Course tabs', 'masterstudy-lms-learning-management-system' ),
				'desc'  => esc_html__( 'Show tabs for better navigation and extra info on a course page', 'masterstudy-lms-learning-management-system' ),
			)
		);
		?>
		<div class="stm_lms_splash_wizard__field_input">
			<div class="stm_lms_splash_wizard__field_checks">
				<?php
				STM_LMS_Templates::show_lms_template(
					'wizard/fields/checkbox',
					array(
						'model' => 'wizard.course_tab_description',
						'label' => esc_html__( 'Description', 'masterstudy-lms-learning-management-system' ),
					)
				);
				STM_LMS_Templates::show_lms_template(
					'wizard/fields/checkbox',
					array(
						'model' => 'wizard.course_tab_curriculum',
						'label' => esc_html__( 'Curriculum', 'masterstudy-lms-learning-management-system' ),
					)
				);
				STM_LMS_Templates::show_lms_template(
					'wizard/fields/checkbox',
					array(
						'model' => 'wizard.course_tab_faq',
						'label' => esc_html__( 'FAQ', 'masterstudy-lms-learning-management-system' ),
					)
				);
				STM_LMS_Templates::show_lms_template(
					'wizard/fields/checkbox',
					array(
						'model' => 'wizard.course_tab_announcement',
						'label' => esc_html__( 'Notice', 'masterstudy-lms-learning-management-system' ),
					)
				);
				STM_LMS_Templates::show_lms_template(
					'wizard/fields/checkbox',
					array(
						'model' => 'wizard.course_tab_reviews',
						'label' => esc_html__( 'Reviews', 'masterstudy-lms-learning-management-system' ),
					)
				);
				?>
			</div>
		</div>
	</div>
	<hr v-if="isPro()"/>
	<div class="stm_lms_splash_wizard__field stm_lms_splash_wizard__field_image_radio stm_lms_splash_wizard__field_splitted"
		v-if="isPro()">
		<?php
		STM_LMS_Templates::show_lms_template(
			'wizard/views/field_data',
			array(
				'title' => esc_html__( 'Choose a style for your Course page', 'masterstudy-lms-learning-management-system' ),
			)
		);
		?>
		<div class="masterstudy-wizard-single-course-styles">
			<?php
			$course_styles = array(
				array(
					'value' => 'default',
					'label' => esc_html__( 'Default', 'masterstudy-lms-learning-management-system' ),
				),
				array(
					'value' => 'classic',
					'label' => esc_html__( 'Classic', 'masterstudy-lms-learning-management-system' ),
				),
				array(
					'value' => 'modern',
					'label' => esc_html__( 'Industrial', 'masterstudy-lms-learning-management-system' ),
				),
			);

			if ( STM_LMS_Helpers::is_pro_plus() ) {
				$course_styles = array_merge(
					$course_styles,
					array(
						array(
							'value' => 'timeless',
							'label' => esc_html__( 'Timeless', 'masterstudy-lms-learning-management-system' ),
						),
						array(
							'value' => 'sleek-sidebar',
							'label' => esc_html__( 'Sleek with Sidebar', 'masterstudy-lms-learning-management-system' ),
						),
						array(
							'value' => 'minimalistic',
							'label' => esc_html__( 'Minimalistic', 'masterstudy-lms-learning-management-system' ),
						),
						array(
							'value' => 'dynamic',
							'label' => esc_html__( 'Dynamic', 'masterstudy-lms-learning-management-system' ),
						),
						array(
							'value' => 'modern-curriculum',
							'label' => esc_html__( 'Modern with Curriculum', 'masterstudy-lms-learning-management-system' ),
						),
						array(
							'value' => 'dynamic-sidebar',
							'label' => esc_html__( 'Dynamic with Short Sidebar', 'masterstudy-lms-learning-management-system' ),
						),
						array(
							'value' => 'full-width',
							'label' => esc_html__( 'Bold with Full Width Cover', 'masterstudy-lms-learning-management-system' ),
						),
					)
				);
			}

			foreach ( $course_styles as $item ) {
				STM_LMS_Templates::show_lms_template(
					'wizard/fields/course-radio-image',
					array(
						'model' => 'wizard.course_style',
						'value' => $item['value'],
						'label' => $item['label'],
					)
				);
			}
			?>
		</div>
	</div>
</div>
