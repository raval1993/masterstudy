<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$personal_options   = masterstudy_lms_personal_data_display_options( get_current_user_id() );
$settings           = get_option( 'stm_lms_settings' );
$has_billing_fields = false;

foreach ( (array) ( $personal_options['personal_fields'] ?? array() ) as $key => $label ) {
	if ( ! empty( $settings[ "personal_data_{$key}" ] ) ) {
		$has_billing_fields = true;
		break;
	}
}

if ( ! $has_billing_fields ) {
	return;
}
?>

<div class="masterstudy-account-settings__billing">
	<h2 class="masterstudy-account-settings__billing-title">
		<?php echo esc_html__( 'Billing information', 'masterstudy-lms-learning-management-system' ); ?>
	</h2>
	<div class="masterstudy-account-settings__billing-list">
		<?php
		foreach ( $personal_options['personal_fields'] as $key => $label ) {
			if ( empty( $settings[ "personal_data_{$key}" ] ) ) {
				continue;
			}
			?>
			<div class="masterstudy-account-settings__field">
				<div class="masterstudy-account-settings__field-wrapper">
					<label class="masterstudy-account-settings__field-label">
						<?php echo esc_html( $label ); ?>
					</label>
					<?php if ( 'country' === $key ) : ?>
						<select
							name="country"
							class="masterstudy-account-settings__select masterstudy-account-settings-country-select"
							data-placeholder="<?php echo esc_attr( $personal_options['country_label'] ); ?>"
						>
							<option value="" disabled selected hidden><?php echo esc_html( $personal_options['country_label'] ); ?></option>
							<?php foreach ( $personal_options['countries'] as $country ) : ?>
								<option value="<?php echo esc_attr( $country['code'] ); ?>">
									<?php echo esc_html( $country['name'] ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					<?php elseif ( 'state' === $key ) : ?>
						<select
							name="state"
							class="masterstudy-account-settings__select masterstudy-account-settings-state-select"
							data-placeholder="<?php echo esc_attr( $personal_options['state_label'] ); ?>"
							<?php echo $personal_options['is_us'] ? '' : 'style="display:none" disabled'; ?>
						>
							<option value="" disabled selected hidden><?php echo esc_html( $personal_options['state_label'] ); ?></option>
							<?php
							foreach ( $personal_options['us_states'] as $st ) :
								$code = (string) ( $st['code'] ?? '' );
								$name = (string) ( $st['name'] ?? '' );
								?>
								<option value="<?php echo esc_attr( $code ); ?>"
									<?php selected( strtoupper( $code ), $personal_options['state_code_selected'] ); ?>>
									<?php echo esc_html( $name ); ?>
								</option>
							<?php endforeach; ?>
						</select>

						<input
							type="text"
							name="state"
							class="masterstudy-account-settings__input masterstudy-account-settings-state-input"
							value="<?php echo esc_attr( $personal_options['state_code_selected'] ? '' : $personal_options['current_state'] ); ?>"
							placeholder="<?php echo esc_attr__( 'Enter your state', 'masterstudy-lms-learning-management-system' ); ?>"
							<?php echo $personal_options['is_us'] ? 'style="display:none" disabled' : ''; ?>
						>

					<?php else : ?>
						<?php
						$dynamic_placeholder = sprintf(
							/* translators: %s name of input */
							__( 'Enter your %s', 'masterstudy-lms-learning-management-system' ),
							strtolower( $label )
						);
						?>
						<input
							name="<?php echo esc_attr( $key ); ?>"
							class="masterstudy-account-settings__input masterstudy-account-settings-<?php echo esc_attr( $key ); ?>-input"
							placeholder="<?php echo esc_attr( $dynamic_placeholder ); ?>"
						>
					<?php endif; ?>
				</div>
			</div>
			<?php
		}
		?>
	</div>
</div>
