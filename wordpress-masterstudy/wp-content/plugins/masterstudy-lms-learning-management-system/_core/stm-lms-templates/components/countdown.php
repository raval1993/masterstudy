<?php

/**
 * @var string $id
 * @var int $start_time
 * @var string $style
 * @var boolean $dark_mode
 *
 * masterstudy-countdown_dark-mode- for dark mode
 */

wp_enqueue_style( 'masterstudy-countdown' );
wp_enqueue_script( 'masterstudy-countdown' );

$dark_mode = isset( $dark_mode ) ? $dark_mode : false;
?>

<div class="masterstudy-countdown <?php echo esc_attr( $dark_mode ? 'masterstudy-countdown_dark-mode' : '' ); ?> <?php echo esc_attr( 'masterstudy-countdown_style-' . $style ); ?>"
	data-timer="<?php echo esc_attr( $start_time ); ?>"
	id="<?php echo esc_attr( $id ); ?>">
</div>
