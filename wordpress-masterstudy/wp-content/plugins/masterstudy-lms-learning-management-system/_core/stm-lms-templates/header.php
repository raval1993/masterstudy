<?php
if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
	wp_head();
	$allowed_tags = wp_kses_allowed_html( 'post' );

	$allowed_tags['svg'] = array(
		'xmlns'       => true,
		'viewBox'     => true,
		'class'       => true,
		'aria-hidden' => true,
		'role'        => true,
		'focusable'   => true,
	);

	$allowed_tags['path'] = array(
		'fill'      => true,
		'fill-rule' => true,
		'clip-rule' => true,
		'd'         => true,
	);

	$allowed_tags['circle'] = array(
		'cx'           => true,
		'cy'           => true,
		'r'            => true,
		'stroke'       => true,
		'stroke-width' => true,
		'fill'         => true,
	);

	echo wp_kses( do_blocks( '<!-- wp:template-part {"slug":"header"} /-->' ), $allowed_tags );
} else {
	get_header();
}
