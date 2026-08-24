<?php

/**
 * @var $model
 * @var $desc
 * @var $options
 *
 */

?>
<div class="desc-wrapper">
	<?php if ( ! empty( $desc ) ) : ?>
		<div class="desc"><?php echo esc_html( $desc ); ?></div>
	<?php endif; ?>
	<div class="stm_lms_splash_wizard__field_select desc-field">
		<select v-model="<?php echo esc_attr( $model ); ?>">
			<?php foreach ( $options as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>">
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</div>
</div>
