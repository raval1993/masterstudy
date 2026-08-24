<?php
if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
	wp_footer();
	echo wp_kses_post( do_blocks( '<!-- wp:template-part {"slug":"footer"} /-->' ) );
} else {
	get_footer();
}
