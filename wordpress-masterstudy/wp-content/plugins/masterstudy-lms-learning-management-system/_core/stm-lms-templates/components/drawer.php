<?php
/**
 * Drawer component
 * @var string $default_slot - required - template id of default slot content
 * @var string $drawer_class - required
 *
 * masterstudy-drawer-component_open - insert this class to open the drawer
 * @package masterstudy
 */

wp_enqueue_style( 'masterstudy-drawer-component' );

wp_enqueue_script( 'masterstudy-slots-utils' );
wp_enqueue_script( 'masterstudy-drawer-component' );
?>

<!--display: none to fix first page load-->
<div style="display: none" class="masterstudy-drawer-component <?php echo esc_attr( $drawer_class ); ?>" data-masterstudy-drawer-slot-id="<?php echo esc_attr( $default_slot ); ?>">
	<div class="masterstudy-drawer-component__content" data-masterstudy-slot-id="<?php echo esc_attr( $default_slot ); ?>">
	</div>
</div>
