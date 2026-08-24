<?php

/**
 * @var array $items
 * @var string $style
 * @var string $class
 * @var int $active_tab_index
 * @var boolean $dark_mode
 *
 * masterstudy-tabs_dark-mode - for dark mode
 * masterstudy-tabs__item_active - for item active state
 * masterstudy-tabs_style-default|nav-sm|nav-md|buttons - for tabs style change
 */

wp_enqueue_style( 'masterstudy-tabs' );

$tabs_classes  = 'masterstudy-tabs';
$tabs_classes .= $dark_mode ? ' masterstudy-tabs_dark-mode' : '';
$tabs_classes .= ! empty( $class ) ? " $class" : '';
$tabs_classes .= " masterstudy-tabs_style-$style";
?>

<ul class="<?php echo esc_attr( $tabs_classes ); ?>">
	<?php foreach ( $items as $index => $item ) { ?>
		<li class="masterstudy-tabs__item <?php echo $active_tab_index === $index ? 'masterstudy-tabs__item_active' : ''; ?>" data-id="<?php echo esc_attr( $item['id'] ); ?>">
			<?php
			echo esc_html( $item['title'] );
			if ( isset( $item['hint'] ) ) {
				?>
				<span class="masterstudy-tabs__item-hint"><?php echo esc_html( $item['hint'] ); ?></span>
				<?php
			}
			?>
		</li>
	<?php } ?>
</ul>
