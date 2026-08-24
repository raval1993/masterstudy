<?php
/**
 * @var string $gdpr_page
 * @var string $gdpr_warning
 */
?>

<div class="masterstudy-checkout-gdpr">
	<div class="masterstudy-checkout-gdpr__checkbox">
		<input type="checkbox" name="privacy_policy" id="masterstudy-checkout-gdpr" v-bind:checked="agree_with_policy"/>
		<span @click.prevent="toggle_policy()" class="masterstudy-checkout-gdpr__checkbox-wrapper" v-bind:class="{'masterstudy-checkout-gdpr__checkbox-wrapper_checked': agree_with_policy}"></span>
	</div>
	<span class="masterstudy-checkout-gdpr__text">
		<?php echo esc_html( $gdpr_warning ); ?>
		<a href="<?php echo esc_url( get_the_permalink( $gdpr_page ) ); ?>" target="_blank" class="masterstudy-checkout-gdpr__link">
			<?php echo esc_html__( 'Privacy Policy', 'masterstudy-lms-learning-management-system' ); ?>
		</a>
	</span>
</div>
