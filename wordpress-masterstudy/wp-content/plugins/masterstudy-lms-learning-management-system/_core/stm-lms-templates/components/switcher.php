<?php
/**
 * @var string $name
 * @var bool $on
 * @var string $class
 *
 */

wp_enqueue_style( 'masterstudy-switcher' );

$checked = $on ? 'checked' : '';
$class   = $class ?? '';
?>

<label class="masterstudy-switcher <?php echo esc_attr( $class ); ?>">
	<input type="checkbox" name="<?php echo esc_attr( $name ); ?>" <?php echo esc_attr( $checked ); ?>>
	<div class="masterstudy-switcher-background">
		<div class="masterstudy-switcher-handle"></div>
	</div>
</label>
