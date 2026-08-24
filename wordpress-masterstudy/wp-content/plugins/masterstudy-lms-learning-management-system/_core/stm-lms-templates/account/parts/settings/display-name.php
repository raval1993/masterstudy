<?php
$user                  = wp_get_current_user();
$display_name_options  = array_unique(
	array_filter(
		array_map(
			'trim',
			array(
				$user->user_nicename,
				$user->user_login,
				$user->first_name . ' ' . $user->last_name,
				$user->last_name . ' ' . $user->first_name,
				$user->first_name,
				$user->last_name,
				$user->display_name,
			)
		),
		'strlen'
	)
);
$selected_display_name = $user->display_name;
?>

<script>
	let displayNameOptions  = <?php echo wp_json_encode( $display_name_options ); ?>;
	let selectedDisplayName = '<?php echo esc_js( $selected_display_name ); ?>';
</script>

<div class="masterstudy-account-settings__field masterstudy-account-settings__field_full">
	<div class="masterstudy-account-settings__field-wrapper">
		<label class="masterstudy-account-settings__field-label">
			<?php echo esc_html__( 'Display name publicly as:', 'masterstudy-lms-learning-management-system' ); ?>
		</label>
		<div class="masterstudy-account-settings__field-select2">
			<select name="display_name" id="display_name" class="masterstudy-account-settings__select masterstudy-account-settings-display-name-options">
				<?php foreach ( $display_name_options as $option ) : ?>
					<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $user->display_name, $option ); ?>>
						<?php echo esc_html( $option ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		<p class="masterstudy-account-settings__field-desc">
			<?php echo esc_html__( 'The display name is shown in all public fields, such as the author name, instructor name, student name', 'masterstudy-lms-learning-management-system' ); ?>
		</p>
	</div>
</div>
