<?php
/**
 * @var string $title_items - required
 * @var string $title_search - optional
 * @var string $icon - required
 * @var string $container_class - required
 * @var string $default_slot - optional - template id of default slot content
 */

wp_enqueue_style( 'masterstudy-no-records' );
if ( ! empty( $default_slot ) ) {
	wp_enqueue_script( 'masterstudy-slots-utils' );
	wp_enqueue_script( 'masterstudy-no-records' );
	wp_localize_script(
		'masterstudy-no-records',
		'no_records_data',
		array(
			'default_slot' => $default_slot,
		)
	);
}
?>

<div class="masterstudy-no-records__container <?php echo esc_attr( $container_class ); ?>">
	<div class="masterstudy-no-records__icon"><span class="<?php echo esc_html( $icon ); ?>"></span></div>
	<div class="masterstudy-no-records__no-items">
		<?php echo esc_html( $title_items ); ?>
	</div>
	<?php if ( ! empty( $title_search ) ) : ?>
		<div class="masterstudy-no-records__no-search">
			<?php echo esc_html( $title_search ); ?>
		</div>
	<?php endif; ?>
	<?php if ( ! empty( $default_slot ) ) : ?>
	<div data-masterstudy-slot-id="<?php echo esc_attr( $default_slot ); ?>"></div>
	<?php endif; ?>
</div>
