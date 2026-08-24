<div class="stm_lms_splash_wizard__content_tab" v-if="active_step === 'business'">
	<h4>
		<?php esc_html_e( 'Choose how you want to use the plugin', 'masterstudy-lms-learning-management-system' ); ?>
	</h4>
	<div class="stm_lms_splash_wizard__business_type">
		<div class="stm_lms_splash_wizard__business_type_one"
			v-bind:class="{'active': business_type === 'individual'}"
			@click="changeType('individual')"
		>
			<div class="stm_lms_splash_wizard__business_type_one__wrapper">
				<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/wizard/individual.png' ); ?>"/>
				<div class="stm_lms_splash_wizard__business_type_label">
				<label class="stm_lms_wizard__radio">
					<?php esc_html_e( 'Individual', 'masterstudy-lms-learning-management-system' ); ?>
					<input type="hidden"
						value="individual"
						name="business_type"
						v-model="business_type"/>
					<div></div>
				</label>
				</div>
				<div class="stm_lms_splash_wizard__business_type_description">
					<?php
					esc_html_e(
						'Make your own LMS with courses and promote yourself as a private teacher',
						'masterstudy-lms-learning-management-system'
					);
					?>
				</div>
				<a
					href="#"
					class="btn-continue"
					@click="nextStep()"
				>
					<?php esc_html_e( 'Continue', 'masterstudy-lms-learning-management-system' ); ?>
				</a>
			</div>
		</div>
		<div class="stm_lms_splash_wizard__business_type_one"
			v-bind:class="{'active': business_type === 'marketplace'}"
			@click="changeType('marketplace')"
		>
			<div class="stm_lms_splash_wizard__business_type_one__wrapper">
				<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/wizard/marketplace.png' ); ?>"/>
				<div class="stm_lms_splash_wizard__business_type_label">
					<label class="stm_lms_wizard__radio">
						<?php esc_html_e( 'Marketplace', 'masterstudy-lms-learning-management-system' ); ?>
						<input type="hidden"
							value="marketplace"
							name="business_type"
							v-model="business_type"/>
						<div></div>
					</label>
				</div>
				<div class="stm_lms_splash_wizard__business_type_description">
					<?php esc_html_e( 'Create a large education platform and bring teachers and students together', 'masterstudy-lms-learning-management-system' ); ?>
				</div>
				<a
					href="#"
					class="btn-continue"
					@click="nextStep('marketplace')"
				>
					<?php esc_html_e( 'Continue', 'masterstudy-lms-learning-management-system' ); ?>
				</a>
			</div>
		</div>
	</div>
	<?php STM_LMS_Templates::show_lms_template( 'wizard/views/skip' ); ?>
</div>
