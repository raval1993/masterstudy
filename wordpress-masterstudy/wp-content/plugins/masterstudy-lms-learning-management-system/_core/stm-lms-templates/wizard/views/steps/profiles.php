<div class="stm_lms_splash_wizard__content_tab"
	v-if="active_step === 'profiles'">
	<h4>
		<?php esc_html_e( 'Profiles', 'masterstudy-lms-learning-management-system' ); ?>
	</h4>
	<div class="stm_lms_splash_wizard__field stm_lms_splash_wizard__field_switch"
		v-bind:class="{'active' : wizard.register_as_instructor}">
		<?php
		STM_LMS_Templates::show_lms_template(
			'wizard/views/field_data',
			array(
				'title' => esc_html__( 'Instructor registration', 'masterstudy-lms-learning-management-system' ),
			)
		);
		?>
		<div class="stm_lms_splash_wizard__field_input">
			<?php
			STM_LMS_Templates::show_lms_template(
				'wizard/fields/switcher',
				array(
					'model' => 'wizard.register_as_instructor',
					'desc'  => esc_html__( 'Turn this on if you want teachers to be able to sign up themselves', 'masterstudy-lms-learning-management-system' ),
				)
			);
			?>
		</div>
	</div>
	<hr v-if="isMarketPlace() && wizard.register_as_instructor"/>
	<div class="stm_lms_splash_wizard__field stm_lms_splash_wizard__field_switch"
		v-if="isMarketPlace() && wizard.register_as_instructor"
		v-bind:class="{'active' : wizard.instructor_premoderation}">
		<?php
		STM_LMS_Templates::show_lms_template(
			'wizard/views/field_data',
			array(
				'title' => esc_html__( 'Premoderation', 'masterstudy-lms-learning-management-system' ),
			)
		);
		?>
		<div class="stm_lms_splash_wizard__field_input">
			<?php
			STM_LMS_Templates::show_lms_template(
				'wizard/fields/switcher',
				array(
					'model' => 'wizard.instructor_premoderation',
					'desc'  => esc_html__( 'Turn this on if users need admin approval to become an instructor', 'masterstudy-lms-learning-management-system' ),
				)
			);
			?>
		</div>
	</div>
	<hr v-if="wizard.register_as_instructor"/>
	<div v-if="wizard.register_as_instructor" class="stm_lms_splash_wizard__field stm_lms_splash_wizard__field_switch" v-bind:class="{'active' : wizard.separate_instructor_registration}">
		<?php
		STM_LMS_Templates::show_lms_template(
			'wizard/views/field_data',
			array(
				'title' => esc_html__( 'Sign up form on a separate page', 'masterstudy-lms-learning-management-system' ),
			)
		);
		?>
		<div class="stm_lms_splash_wizard__field_input">
			<?php
			STM_LMS_Templates::show_lms_template(
				'wizard/fields/switcher',
				array(
					'model' => 'wizard.separate_instructor_registration',
				)
			);
			?>
		</div>
	</div>
	<div v-if="wizard.separate_instructor_registration && wizard.register_as_instructor" class="stm_lms_splash_wizard__field stm_lms_splash_wizard__field_pages stm_lms_splash_wizard__field_pages_instructor_register">
		<?php
		STM_LMS_Templates::show_lms_template(
			'wizard/views/field_data',
			array(
				'title' => esc_html__( 'Create a page ', 'masterstudy-lms-learning-management-system' ),
			)
		);
		?>
		<div class="stm_lms_splash_wizard__field_input">
			<?php
			STM_LMS_Templates::show_lms_template(
				'wizard/fields/pages',
				array(
					'pages'           => array(
						'instructor_registration_page' => esc_html__( 'Instructor Registration Page', 'masterstudy-lms-learning-management-system' ),
					),
					'btn_title'       => esc_html__( 'Generate a page', 'masterstudy-lms-learning-management-system' ),
					'instructor_step' => true,
				)
			);
			?>
		</div>
	</div>
	<hr/>
	<div class="stm_lms_splash_wizard__field stm_lms_splash_wizard__field_switch"
		v-bind:class="{'active' : wizard.instructor_public_profile}">
		<?php
		STM_LMS_Templates::show_lms_template(
			'wizard/views/field_data',
			array(
				'title' => esc_html__( 'Instructor public profile', 'masterstudy-lms-learning-management-system' ),
			)
		);
		?>
		<div class="stm_lms_splash_wizard__field_input">
			<?php
			STM_LMS_Templates::show_lms_template(
				'wizard/fields/switcher',
				array(
					'model' => 'wizard.instructor_public_profile',
					'desc'  => esc_html__( "Display the instructor's public profile with bio, courses, and achievements", 'masterstudy-lms-learning-management-system' ),
				)
			);
			?>
		</div>
	</div>
	<hr/>
	<div class="stm_lms_splash_wizard__field stm_lms_splash_wizard__field_switch"
		v-bind:class="{'active' : wizard.student_public_profile}">
		<?php
		STM_LMS_Templates::show_lms_template(
			'wizard/views/field_data',
			array(
				'title' => esc_html__( 'Student public profile', 'masterstudy-lms-learning-management-system' ),
			)
		);
		?>
		<div class="stm_lms_splash_wizard__field_input">
			<?php
			STM_LMS_Templates::show_lms_template(
				'wizard/fields/switcher',
				array(
					'model' => 'wizard.student_public_profile',
					'desc'  => esc_html__( 'Show courses, certificates, quizzes, and other data', 'masterstudy-lms-learning-management-system' ),
				)
			);
			?>
		</div>
	</div>
	<hr v-if="wizard.instructor_public_profile"/>
	<div v-if="wizard.instructor_public_profile" class="stm_lms_splash_wizard__field stm_lms_splash_wizard__field_image_radio instructor_public_profile_style">
		<?php
		STM_LMS_Templates::show_lms_template(
			'wizard/views/field_data',
			array(
				'title' => esc_html__( 'Instructor profile public page layout', 'masterstudy-lms-learning-management-system' ),
				'desc'  => esc_html__( 'Choose a layout for the instructor profile', 'masterstudy-lms-learning-management-system' ),
			)
		);
		?>
		<div class="stm_lms_splash_wizard__field_input">
			<?php
			STM_LMS_Templates::show_lms_template(
				'wizard/fields/radio_image',
				array(
					'model' => 'wizard.instructor_public_profile_style',
					'value' => 'compact',
					'image' => 'assets/img/instructor-compact.png',
					'label' => esc_html__( 'Compact', 'masterstudy-lms-learning-management-system' ),
				)
			);
			STM_LMS_Templates::show_lms_template(
				'wizard/fields/radio_image',
				array(
					'model' => 'wizard.instructor_public_profile_style',
					'value' => 'extended',
					'image' => 'assets/img/instructor-expanded.png',
					'label' => esc_html__( 'Extended', 'masterstudy-lms-learning-management-system' ),
				)
			);
			?>
		</div>
	</div>
	<hr v-if="wizard.student_public_profile"/>
	<div v-if="wizard.student_public_profile" class="stm_lms_splash_wizard__field stm_lms_splash_wizard__field_image_radio student_public_profile_style">
		<?php
		STM_LMS_Templates::show_lms_template(
			'wizard/views/field_data',
			array(
				'title' => esc_html__( 'Student profile public page layout', 'masterstudy-lms-learning-management-system' ),
				'desc'  => esc_html__( 'Choose a layout for the student profile', 'masterstudy-lms-learning-management-system' ),
			)
		);
		?>
		<div class="stm_lms_splash_wizard__field_input">
			<?php
			STM_LMS_Templates::show_lms_template(
				'wizard/fields/radio_image',
				array(
					'model' => 'wizard.student_public_profile_style',
					'value' => 'compact',
					'image' => 'assets/img/student-compact.png',
					'label' => esc_html__( 'Compact', 'masterstudy-lms-learning-management-system' ),
				)
			);
			STM_LMS_Templates::show_lms_template(
				'wizard/fields/radio_image',
				array(
					'model' => 'wizard.student_public_profile_style',
					'value' => 'extended',
					'image' => 'assets/img/student-expanded.png',
					'label' => esc_html__( 'Extended', 'masterstudy-lms-learning-management-system' ),
				)
			);
			?>
		</div>
	</div>
	<?php
	if ( ! STM_LMS_Helpers::is_theme_activated() ) {
		STM_LMS_Templates::show_lms_template(
			'premium-templates/banners/banner-templates',
			array(
				'custom_class' => 'masterstudy-templates-banner-small',
			)
		);
	}
	?>
</div>
