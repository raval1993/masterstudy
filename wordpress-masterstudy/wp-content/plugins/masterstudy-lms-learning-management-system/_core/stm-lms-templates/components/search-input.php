<?php
/**
 * @var string $classes_wrapper
 * @var string $classes_input
 * @var string $placeholder
 * @var string $icon
 * */
?>

<form class="<?php echo esc_attr( $classes_wrapper ?? '' ); ?>">
	<input type="text" placeholder="<?php echo esc_attr( $placeholder ?? '' ); ?>" class="<?php echo esc_attr( $classes_input ?? '' ); ?>" name="search">
	<span class="<?php echo esc_attr( $icon ?? 'stmlms-search' ); ?>"></span>
</form>
