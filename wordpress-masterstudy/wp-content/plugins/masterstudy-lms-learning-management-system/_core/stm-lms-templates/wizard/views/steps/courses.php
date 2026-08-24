<div class="stm_lms_splash_wizard__content_tab"
	v-if="active_step === 'courses'">
	<h4>
		<?php esc_html_e( 'Courses list', 'masterstudy-lms-learning-management-system' ); ?>
	</h4>
	<hr/>
	<div class="stm_lms_splash_wizard__field stm_lms_splash_wizard__field_pages stm_lms_splash_wizard__field_pages_courses">
		<?php
		STM_LMS_Templates::show_lms_template(
			'wizard/views/field_data',
			array(
				'title' => esc_html__( 'Create a Courses list page', 'masterstudy-lms-learning-management-system' ),
				'desc'  => esc_html__( 'A page with the list of courses will be automatically generated', 'masterstudy-lms-learning-management-system' ),
			)
		);
		?>
		<div class="stm_lms_splash_wizard__field_input">
			<?php
			STM_LMS_Templates::show_lms_template(
				'wizard/fields/pages',
				array(
					'pages'        => array(
						'courses_page' => esc_html__( 'Courses Page', 'masterstudy-lms-learning-management-system' ),
					),
					'btn_title'    => esc_html__( 'Generate a page', 'masterstudy-lms-learning-management-system' ),
					'courses_step' => true,
				)
			);
			?>
		</div>
	</div>
	<hr/>
	<div class="stm_lms_splash_wizard__field stm_lms_splash_wizard__field_image_radio">
		<?php
		STM_LMS_Templates::show_lms_template(
			'wizard/views/field_data',
			array(
				'title' => esc_html__( 'Page Layout', 'masterstudy-lms-learning-management-system' ),
				'desc'  => esc_html__( 'Choose how to display courses as a grid or list', 'masterstudy-lms-learning-management-system' ),
			)
		);
		?>
		<div class="stm_lms_splash_wizard__field_input">
			<?php
			STM_LMS_Templates::show_lms_template(
				'wizard/fields/radio_image',
				array(
					'model' => 'wizard.courses_view',
					'value' => 'grid',
					'image' => 'assets/img/wizard/grid.svg',
					'label' => esc_html__( 'Grid View', 'masterstudy-lms-learning-management-system' ),
				)
			);
			STM_LMS_Templates::show_lms_template(
				'wizard/fields/radio_image',
				array(
					'model' => 'wizard.courses_view',
					'value' => 'list',
					'image' => 'assets/img/wizard/list.svg',
					'label' => esc_html__( 'List View', 'masterstudy-lms-learning-management-system' ),
				)
			);
			?>
		</div>
	</div>
	<hr/>
	<div class="stm_lms_splash_wizard__field stm_lms_splash_wizard__field_range_slider">
		<?php
		STM_LMS_Templates::show_lms_template(
			'wizard/views/field_data',
			array(
				'title' => esc_html__( 'Courses per page', 'masterstudy-lms-learning-management-system' ),
				'desc'  => esc_html__( 'Choose how many courses to show on one page', 'masterstudy-lms-learning-management-system' ),
			)
		);
		?>
		<div class="stm_lms_splash_wizard__field_input">
			<div class="stm_lms_splash_wizard_range_slider">
				<span
					class="stm_lms_splash_wizard_range_slider__pin"
					v-html="wizard.courses_per_page"
					v-bind:style="rangeStyles(wizard.courses_per_page, 1, 15)">
				</span>
				<range-slider
						class="slider"
						min="1"
						max="15"
						step="1"
						v-model="wizard.courses_per_page">
				</range-slider>
			</div>
		</div>
	</div>
	<hr/>
	<div class="stm_lms_splash_wizard__field stm_lms_splash_wizard__field_image_radio stm_lms_splash_wizard__field_image_radio_4">
		<?php
		STM_LMS_Templates::show_lms_template(
			'wizard/views/field_data',
			array(
				'title' => esc_html__( 'Courses per row', 'masterstudy-lms-learning-management-system' ),
				'desc'  => esc_html__( 'Choose how many courses to show in a row', 'masterstudy-lms-learning-management-system' ),
			)
		);
		?>
		<div class="stm_lms_splash_wizard__field_input bottom_view">
			<?php
			STM_LMS_Templates::show_lms_template(
				'wizard/fields/radio_image',
				array(
					'model' => 'wizard.courses_per_row',
					'value' => '2',
					'image' => 'assets/img/wizard/cols/2.svg',
					'label' => '2',
				)
			);
			STM_LMS_Templates::show_lms_template(
				'wizard/fields/radio_image',
				array(
					'model' => 'wizard.courses_per_row',
					'value' => '3',
					'image' => 'assets/img/wizard/cols/3.svg',
					'label' => '3',
				)
			);
			STM_LMS_Templates::show_lms_template(
				'wizard/fields/radio_image',
				array(
					'model' => 'wizard.courses_per_row',
					'value' => '4',
					'image' => 'assets/img/wizard/cols/4.svg',
					'label' => '4',
				)
			);
			STM_LMS_Templates::show_lms_template(
				'wizard/fields/radio_image',
				array(
					'model' => 'wizard.courses_per_row',
					'value' => '6',
					'image' => 'assets/img/wizard/cols/6.svg',
					'label' => '6',
				)
			);
			?>
		</div>
	</div>
	<hr/>
	<div class="stm_lms_splash_wizard__field stm_lms_splash_wizard__field_switch"
		v-bind:class="{'inactive' : !wizard.enable_courses_filter}">
		<?php
		STM_LMS_Templates::show_lms_template(
			'wizard/views/field_data',
			array(
				'title' => esc_html__( 'Add a Filter', 'masterstudy-lms-learning-management-system' ),
			)
		);
		?>
		<div class="stm_lms_splash_wizard__field_input">
			<?php
			STM_LMS_Templates::show_lms_template(
				'wizard/fields/switcher',
				array(
					'model' => 'wizard.enable_courses_filter',
					'desc'  => esc_html__( 'Allow filtering courses by category, level, price and more', 'masterstudy-lms-learning-management-system' ),
				)
			);
			?>
		</div>
	</div>
	<hr v-if="isPro()"/>
	<div class="stm_lms_splash_wizard__field stm_lms_splash_wizard__field_switch" v-if="isPro()" v-bind:class="{'inactive' : wizard.ecommerce_engine !== 'woocommerce'}">
		<?php
		STM_LMS_Templates::show_lms_template(
			'wizard/views/field_data',
			array(
				'title' => esc_html__( 'Select eCommerce Engine', 'masterstudy-lms-learning-management-system' ),
			)
		);
		?>
		<div class="stm_lms_splash_wizard__field_input">
			<?php
			STM_LMS_Templates::show_lms_template(
				'wizard/fields/select',
				array(
					'model'    => 'wizard.ecommerce_engine',
					'options'  => array(
						'native'      => esc_html__( 'Native', 'masterstudy-lms-learning-management-system' ),
						'woocommerce' => esc_html__( 'WooCommerce Checkout', 'masterstudy-lms-learning-management-system' ),
					),
					'desc'     => esc_html__( 'Choose how you want to process course payments and orders.', 'masterstudy-lms-learning-management-system' ),
				)
			);
			?>
		</div>
	</div>
	<hr/>
	<div class="stm_lms_splash_wizard__field stm_lms_splash_wizard__field_switch" v-bind:class="{'inactive' : !wizard.guest_checkout}">
		<?php
		STM_LMS_Templates::show_lms_template(
			'wizard/views/field_data',
			array(
				'title' => esc_html__( 'Enable guest Checkout', 'masterstudy-lms-learning-management-system' ),
			)
		);
		?>
		<div class="stm_lms_splash_wizard__field_input">
			<?php
			STM_LMS_Templates::show_lms_template(
				'wizard/fields/switcher',
				array(
					'model' => 'wizard.guest_checkout',
					'desc'  => esc_html__( 'Let users buy without registering by just entering their email', 'masterstudy-lms-learning-management-system' ),
				)
			);
			?>
		</div>
	</div>
	<hr v-if="isMarketPlace() && isPro()"/>
	<div class="stm_lms_splash_wizard__field stm_lms_splash_wizard__field_number admin_comission" v-if="isMarketPlace() && isPro()">
		<?php
		STM_LMS_Templates::show_lms_template(
			'wizard/views/field_data',
			array(
				'title' => esc_html__( 'Admin commission', 'masterstudy-lms-learning-management-system' ),
			)
		);
		?>
		<div class="stm_lms_splash_wizard__field_input">
			<?php
			STM_LMS_Templates::show_lms_template(
				'wizard/fields/number',
				array(
					'model' => 'wizard.author_fee',
					'desc'  => esc_html__( 'Specify the % that you’ll get from instructors’ sales.', 'masterstudy-lms-learning-management-system' ),
				)
			);
			?>
		</div>
	</div>
</div>
