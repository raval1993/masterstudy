<?php
wp_enqueue_style( 'masterstudy-personal-info' );
wp_enqueue_style( 'masterstudy-select2' );
wp_enqueue_script( 'masterstudy-personal-info' );
wp_localize_script(
	'masterstudy-personal-info',
	'personal_info_data',
	array(
		'placeholder' => esc_html__( 'Search', 'masterstudy-lms-learning-management-system' ),
	)
);

$user_id       = get_current_user_id();
$countries     = masterstudy_lms_get_countries( false );
$personal_data = get_user_meta( $user_id, 'masterstudy_personal_data', true );
$settings      = get_option( 'stm_lms_settings' );
$enter_label   = esc_html__( 'Enter', 'masterstudy-lms-learning-management-system' );
$state_label   = esc_html__( 'Select state', 'masterstudy-lms-learning-management-system' );
$country_label = esc_html__( 'Select your country', 'masterstudy-lms-learning-management-system' );

$fields = array(
	'country'   => esc_html__( 'country', 'masterstudy-lms-learning-management-system' ),
	'post_code' => esc_html__( 'post code', 'masterstudy-lms-learning-management-system' ),
	'state'     => esc_html__( 'state', 'masterstudy-lms-learning-management-system' ),
	'city'      => esc_html__( 'town/city', 'masterstudy-lms-learning-management-system' ),
	'company'   => esc_html__( 'company name', 'masterstudy-lms-learning-management-system' ),
	'phone'     => esc_html__( 'phone number', 'masterstudy-lms-learning-management-system' ),
);

$has_enabled_fields = false;

foreach ( $fields as $key => $_label ) {
	if ( ! empty( $settings[ "personal_data_{$key}" ] ) ) {
		$has_enabled_fields = true;
		break;
	}
}

if ( ! $has_enabled_fields ) {
	return;
}

$current_country     = strtoupper( (string) ( $personal_data['country'] ?? '' ) );
$current_state       = (string) ( $personal_data['state'] ?? '' );
$us_states           = masterstudy_lms_get_us_states( true );
$state_code_selected = '';

if ( $current_state && ! empty( $us_states ) ) {
	$cur_up = strtoupper( $current_state );

	foreach ( $us_states as $st ) {
		if ( strtoupper( (string) ( $st['code'] ?? '' ) ) === $cur_up ) {
			$state_code_selected = $cur_up;
			break;
		}
	}
}
?>

<div class="masterstudy-personal-info">
	<div class="masterstudy-personal-info__header">
		<?php echo esc_html__( 'Personal Information', 'masterstudy-lms-learning-management-system' ); ?>
	</div>
	<div class="masterstudy-personal-info__content">
		<?php
		foreach ( $fields as $key => $label ) :
			if ( empty( $settings[ "personal_data_{$key}" ] ) ) {
				continue;
			}
			$value = (string) ( $personal_data[ $key ] ?? '' );
			?>
			<div class="masterstudy-personal-info__block">
				<span class="masterstudy-personal-info__label"><?php echo esc_html( $label ); ?></span>
				<?php if ( 'country' === $key ) : ?>
					<select
						name="country"
						class="masterstudy-personal-info__select"
						data-placeholder="<?php echo esc_attr( $country_label ); ?>"
					>
						<option value="" disabled selected hidden><?php echo esc_html( $country_label ); ?></option>
						<?php foreach ( $countries as $country ) : ?>
							<option value="<?php echo esc_attr( (string) $country['code'] ); ?>" <?php selected( $value, (string) $country['code'] ); ?>>
								<?php echo esc_html( (string) $country['name'] ); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<?php
				elseif ( 'state' === $key ) :
					$is_us = ( 'US' === $current_country );
					?>
					<select
						name="state"
						class="masterstudy-personal-info__select masterstudy-personal-info__state-select"
						data-placeholder="<?php echo esc_attr( $state_label ); ?>"
						<?php echo $is_us ? '' : 'style="display:none" disabled'; ?>
					>
						<option value="" disabled selected hidden><?php echo esc_html( $state_label ); ?></option>
						<?php
						foreach ( $us_states as $st ) :
							$code = (string) ( $st['code'] ?? '' );
							$name = (string) ( $st['name'] ?? '' );
							?>
							<option value="<?php echo esc_attr( $code ); ?>" <?php selected( strtoupper( $code ), $state_code_selected ); ?>>
								<?php echo esc_html( $name ); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<input
						type="text"
						name="state"
						class="masterstudy-personal-info__input masterstudy-personal-info__state-input"
						value="<?php echo esc_attr( $state_code_selected ? '' : $current_state ); ?>"
						placeholder="<?php echo esc_attr( $enter_label . ' ' . $fields['state'] ); ?>"
						<?php echo $is_us ? 'style="display:none" disabled' : ''; ?>
					/>
				<?php elseif ( 'phone' === $key ) : ?>
					<input
						type="tel"
						name="<?php echo esc_attr( $key ); ?>"
						class="masterstudy-personal-info__input"
						value="<?php echo esc_attr( $value ); ?>"
						placeholder="<?php echo esc_attr( $enter_label . ' ' . $label ); ?>"
						oninput="this.value = this.value.replace(/[^0-9+()-]/g, '');"
					/>
				<?php else : ?>
					<input
						type="text"
						name="<?php echo esc_attr( $key ); ?>"
						class="masterstudy-personal-info__input"
						value="<?php echo esc_attr( $value ); ?>"
						placeholder="<?php echo esc_attr( $enter_label . ' ' . $label ); ?>"
					/>
				<?php endif; ?>
				<span class="masterstudy-personal-info__error">
					<?php echo esc_html__( 'This field is required', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
			</div>
		<?php endforeach; ?>
	</div>
</div>
