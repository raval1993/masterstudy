<?php
/**
 * @var string $container_class
 * @var string $input_class
 * @var string $value
 * @var array $input_attrs
 */

$container_class = $container_class ?? '';
$input_class     = $input_class ?? '';
$value           = $value ?? '';
$input_attrs_str = '';
if ( ! empty( $input_attrs ) ) {
	foreach ( $input_attrs as $key => $value ) {
		if ( null === $value ) {
			continue;
		}
		$input_attrs_str .= " $key=$value";
	}
}

wp_enqueue_style( 'masterstudy-checkbox-component' );
?>
<div class="masterstudy-checkbox-component <?php echo esc_attr( $container_class ); ?>">
	<input class="<?php echo esc_attr( $input_class ); ?>" type="checkbox" <?php echo esc_attr( $input_attrs_str ); ?> />
</div>
