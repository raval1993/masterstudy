<?php
if ( STM_LMS_Helpers::is_pro() ) {
	$text_payment_dynamic = esc_html__( 'Pro Plus Version', 'masterstudy-lms-learning-management-system' );
} else {
	$text_payment_dynamic = esc_html__( 'Pro Version', 'masterstudy-lms-learning-management-system' );
}
?>
<div class="stm-lms-payments">

	<div class="stm-lms-payment_method"
		v-for="(payment_info, payment) in payments"
		:class="{ 'is-pro-locked': isProLocked(payment_info) }">

		<div class="stm-lms-payment_header" @click="togglePayment(payment, event)" :class="{active: payment_info.displayShow}">
			<div class="stm-lms-payment_header_info">
				<div class="stm-lms-payment_header_img">
					<img :src="'<?php echo esc_url( STM_LMS_URL ); ?>/assets/img/payments/' + payment_info.img"
						:alt="payment_info.name"
						v-if="payment_info.img"
						width="40" height="40">
				</div>
				<div class="stm-lms-payment_header_block">
					<div class="stm-lms-payment_header_title">
						{{ payment_info.name }}
					</div>
					<div class="stm-lms-payment_header_block_description" v-if="payment_info.payment_description" >
						<div class="stm-lms-payment_info_block_hint">
							<i class="stmlms-info-circle"></i>
						</div>
						<span v-if="payment_info.payment_description" v-html="payment_info.payment_description"></span>
					</div>
				</div>
			</div>

			<div class="stm-lms-payment_header-toggle">
				<div class="pro-notice" v-if="isProLocked(payment_info)">
					<?php
					printf(
						wp_kses(
							/* translators: %s: pro link. */
							__( 'Available in %s', 'masterstudy-lms-learning-management-system' ),
							array(
								'a' => array(
									'href'   => array(),
									'target' => array(),
								),
							)
						),
						'<a href="https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin-ms&utm_medium=addons&utm_campaign=get-now-addons" target="_blank">' . esc_html( $text_payment_dynamic ) . '</a>'
					);
					?>
				</div>
				<div class="wpcfto-admin-checkbox" @click.stop>
					<label>
						<div class="wpcfto-admin-checkbox-wrapper is_toggle" :class="{active: payment_info.enabled}">
							<div class="wpcfto-checkbox-switcher"></div>
							<input type="checkbox" v-model="payment_info.enabled" :disabled="isProLocked(payment_info)">
						</div>
					</label>
				</div>

				<div class="stm-lms-payment_header-toggle-arrow" :class="{rotate: payment_info.displayShow}">
					<i class="stmlms-chevron_down"></i>
				</div>
			</div>
		</div>

		<transition name="slide-fade">
			<div class="stm-lms-payment_info" v-if="payment_info.displayShow">
				<div class="stm-lms-payment_info_field" v-for="(field_info, field_name) in payment_info.fields">

					<div class="stm-lms-payment_info_block" v-if="field_info.info_title">
						<div class="stm-lms-payment_info_block_title">
							{{ field_info.info_title }}
						</div>
						<div class="stm-lms-payment_info_block_description" v-if="field_info.info_description" >
							<span v-html="field_info.info_description"></span>
						</div>
					</div>

					<textarea v-if="field_info['type'] == 'textarea'"
							v-bind:placeholder="field_info['placeholder']"
							:disabled="isProLocked(payment_info)"
							v-model="payments[payment].fields[field_name].value">
					</textarea>

					<div class="stm-lms-payment_content" v-if="field_info['type'] == 'text'">
						<input type="text"
								v-bind:placeholder="field_info['placeholder']"
								v-model="payment_info.fields[field_name].value"
								:disabled="isProLocked(payment_info)"
								v-bind:readonly="field_info['readonly']"
								v-bind:id="payment + _ +field_name"
								@click="handleInputClick(field_info, payment + _ + field_name)">

						<div v-if="activeTooltip === payment + _ + field_name" class="readonly-tooltip">copied_text</div>
					</div>

					<select v-if="field_info['type'] == 'select'"
							:disabled="isProLocked(payment_info)"
							v-model="payment_info.fields[field_name].value">
						<option v-for="(option_value, option_name) in sources[field_info['source']]" v-bind:value="option_value">
							{{ option_name }}
						</option>
					</select>

				</div>
			</div>
		</transition>

	</div>
</div>
