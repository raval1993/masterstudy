<?php
/**
 * @var $current_user
 */

$has_avatar = ! $current_user['no_avatar'];
?>

<div class="masterstudy-account-settings__field masterstudy-account-settings__field_centered">
	<div class="masterstudy-account-settings__avatar <?php echo $has_avatar ? 'masterstudy-account-settings__avatar_available' : 'masterstudy-account-settings__avatar_no'; ?>">
		<input
			class="masterstudy-account-settings__avatar-input"
			type="file"
			accept="image/*"
		/>

		<span class="masterstudy-account-settings__avatar-delete"></span>
		<span class="masterstudy-account-settings__avatar-camera"></span>
		<div class="masterstudy-account-settings__avatar-img">
			<?php echo wp_kses_post( $current_user['avatar'] ); ?>
		</div>
	</div>
</div>
