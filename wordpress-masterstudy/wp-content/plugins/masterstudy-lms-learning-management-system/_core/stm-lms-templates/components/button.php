<?php

/**
 * @var int $id
 * @var string $class
 * @var string $title
 * @var string $link
 * @var string $icon_name
 * @var string $icon_position
 * @var string $style
 * @var string $size
 * @var string $target
 * @var string $download
 * @var bool $login
 *
 * masterstudy-button_icon-left|right - for icon direction
 * masterstudy-button_style-primary|secondary|tertiary|outline|danger|primary-light|danger-light - for style change
 * masterstudy-button_size-sm|md - for size change
 * masterstudy-button_loading - for loading animation
 * masterstudy-button_disabled - for "disabled" style
 */

wp_enqueue_style( 'masterstudy-button' );

$data        = isset( $id ) ? ' data-id=' . $id : '';
$icon_class  = isset( $icon_position ) ? ' masterstudy-button_icon-' . $icon_position : '';
$icon_class .= isset( $icon_name ) ? ' masterstudy-button_icon-' . $icon_name : '';
$link        = isset( $link ) ? $link : '#';
$target      = isset( $target ) && ! empty( $target ) ? 'target=' . $target : '';
$download    = isset( $download ) && ! empty( $download ) ? 'download=' . $download : '';
$login       = isset( $login ) ? 'register' === $login ? 'data-authorization-modal=register' : 'data-authorization-modal=login' : '';
$class_attr  = 'masterstudy-button_style-' . $style . ' masterstudy-button_size-' . $size . $icon_class;

if ( isset( $class ) && ! empty( $class ) ) {
	$class_attr .= ' ' . $class;
}

if ( ! empty( $login ) ) {
	wp_enqueue_script( 'vue-resource.js' );
	stm_lms_register_style( 'login' );
	stm_lms_register_style( 'register' );
	enqueue_login_script();
	enqueue_register_script();
}
?>

<a
	href="<?php echo esc_url( $link ); ?>"
	<?php echo esc_attr( $target ); ?>
	<?php echo esc_attr( $download ); ?>
	class="masterstudy-button <?php echo esc_attr( $class_attr ); ?>"
	<?php echo esc_attr( $login . $data ); ?>
>
	<span class="masterstudy-button__title"><?php echo esc_html( $title ); ?></span>
</a>
