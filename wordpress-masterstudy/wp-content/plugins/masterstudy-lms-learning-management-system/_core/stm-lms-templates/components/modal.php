<?php
/**
 * Modal component
 * @var string $default_slot - required - template id of default slot content
 * @var string $modal_class - required
 *
 * masterstudy-modal-component_open - insert this class to open the modal
 * @package masterstudy
 */

wp_enqueue_style( 'masterstudy-modal-component' );

wp_enqueue_script( 'masterstudy-slots-utils' );
wp_enqueue_script( 'masterstudy-modal-component' );
?>

<!--display: none to fix first page load-->
<div style="display: none" class="masterstudy-modal-component <?php echo esc_attr( $modal_class ); ?>" data-masterstudy-modal-slot-id="<?php echo esc_attr( $default_slot ); ?>">
	<div class="masterstudy-modal-component__content" data-masterstudy-slot-id="<?php echo esc_attr( $default_slot ); ?>">
	</div>
</div>
