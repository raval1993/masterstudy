<?php
/**
 * Block render callback.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content Block default content.
 * @param WP_Block $block The block instance.
 */

$block_wrapper_attributes = '';
$block_wrapper_attributes = get_block_wrapper_attributes();

?>
<div <?php echo wp_kses_data( $block_wrapper_attributes ); ?>>
	<div class="courses-load-more">
		<button type="button" class="courses-load-more__button"><?php echo esc_html__( 'Load more', 'masterstudy-lms-learning-management-system' ); ?></button>
	</div>
</div>
