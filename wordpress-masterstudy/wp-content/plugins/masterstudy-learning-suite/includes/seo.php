<?php
defined( 'ABSPATH' ) || exit;

function mls_course_description( int $id ): string {
	$custom = get_post_meta( $id, '_mls_seo_description', true );
	if ( $custom ) { return mb_substr( wp_strip_all_tags( $custom ), 0, 180 ); }
	$content = get_post_field( 'post_excerpt', $id );
	if ( ! $content ) { $content = get_post_field( 'post_content', $id ); }
	return mb_substr( trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( strip_shortcodes( $content ) ) ) ), 0, 180 );
}

add_action( 'wp_head', function () {
	if ( ! is_singular( 'stm-courses' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' ) ) { return; }
	$id = get_queried_object_id(); if ( get_post_status( $id ) !== 'publish' ) { return; }
	$title = get_the_title( $id ); $description = mls_course_description( $id ); $image = get_the_post_thumbnail_url( $id, 'large' );
	echo '<meta name="description" content="' . esc_attr( $description ) . '"><meta property="og:type" content="website"><meta property="og:title" content="' . esc_attr( $title ) . '"><meta property="og:description" content="' . esc_attr( $description ) . '"><meta property="og:url" content="' . esc_url( get_permalink( $id ) ) . '">';
	if ( $image ) { echo '<meta property="og:image" content="' . esc_url( $image ) . '">'; }
	$schema = array( '@context' => 'https://schema.org', '@type' => 'Course', '@id' => get_permalink( $id ) . '#course', 'name' => $title, 'description' => $description, 'url' => get_permalink( $id ), 'provider' => array( '@type' => 'Organization', 'name' => get_bloginfo( 'name' ), 'url' => home_url( '/' ) ) );
	if ( $image ) { $schema['image'] = $image; }
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . '</script>';
}, 20 );
