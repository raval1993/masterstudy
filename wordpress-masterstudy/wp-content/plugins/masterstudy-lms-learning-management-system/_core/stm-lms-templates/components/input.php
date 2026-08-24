<?php
/**
 * Input component
 *
 * @var string  $input_name    - input attribute `name`.
 * @var string  $input_class - input attribute `class`.
 * @var string  $input_id      - input attribute `id`.
 * @var string  $input_value   - input attribute `value`.
 * @var string  $placeholder   - input placeholder.
 * @var string  $input_type    - input type (text, email, password, etc.).
 * @var string  $icon_class    - optional icon class to display inside input.
 * @var boolean $dark_mode     - if $dark_mode is true then add class
 * `masterstudy-input_dark-mode` to class `masterstudy-input`
 * @var array   $attributes    - additional HTML attributes for input element.
 *
 * @package masterstudy
 */

$input_name  = $input_name ?? '';
$input_class = $input_class ?? '';
$input_id    = $input_id ?? '';
$input_value = $input_value ?? '';
$placeholder = $placeholder ?? '';
$input_type  = $input_type ?? 'text';
$icon_class  = $icon_class ?? '';
$dark_mode   = $dark_mode ?? false;
$attributes  = $attributes ?? array();

$input_wrapper_class  = ( $dark_mode ) ? ' masterstudy-input_dark-mode' : '';
$input_wrapper_class .= ( ! empty( $icon_class ) ) ? ' masterstudy-input_has-icon' : '';

// Build additional attributes string.
$additional_attributes = '';
if ( ! empty( $attributes ) && is_array( $attributes ) ) {
	foreach ( $attributes as $attr_key => $attr_value ) {
		$additional_attributes .= ' ' . esc_attr( $attr_key ) . '="' . esc_attr( $attr_value ) . '"';
	}
}

wp_enqueue_style( 'masterstudy-input' );
?>
<div class="masterstudy-input<?php echo esc_attr( $input_wrapper_class ); ?>">
	<input
		class="masterstudy-input__field <?php echo esc_attr( $input_class ); ?>"
		type="<?php echo esc_attr( $input_type ); ?>"
		<?php echo ! empty( $input_name ) ? 'name="' . esc_attr( $input_name ) . '"' : ''; ?>
		<?php echo ! empty( $input_id ) ? 'id="' . esc_attr( $input_id ) . '"' : ''; ?>
		<?php echo ! empty( $input_value ) ? 'value="' . esc_attr( $input_value ) . '"' : ''; ?>
		<?php echo ! empty( $placeholder ) ? 'placeholder="' . esc_attr( $placeholder ) . '"' : ''; ?>
		<?php echo esc_attr( $additional_attributes ); ?>
	>
	<?php if ( ! empty( $icon_class ) ) : ?>
		<label class="masterstudy-input__icon-wrapper">
			<span class="masterstudy-input__icon <?php echo esc_attr( $icon_class ); ?>"></span>
		</label>
	<?php endif; ?>
</div>
