<?php
defined( 'ABSPATH' ) || exit;

add_action( 'rest_api_init', function () {
	register_rest_route( 'mls/v1', '/evidence/(?P<lesson>\d+)', array( 'methods' => 'POST', 'permission_callback' => function ( $request ) { $context = mls_context( absint( $request['lesson'] ) ); return $context && mls_access( $context ); }, 'callback' => 'mls_evidence_upload' ) );
} );
add_action( 'admin_post_mls_evidence', 'mls_evidence_download' );

function mls_private_dir(): string {
	$path = defined( 'MLS_PRIVATE_DIR' ) ? realpath( MLS_PRIVATE_DIR ) : false;
	$root = realpath( ABSPATH );
	if ( ! $path || ! $root || ! is_writable( $path ) || str_starts_with( strtolower( str_replace( '\\', '/', $path ) ) . '/', strtolower( str_replace( '\\', '/', $root ) ) . '/' ) ) { throw new RuntimeException( 'Private storage must be configured outside the website root.' ); }
	return $path;
}

function mls_evidence_allowed( WP_Post $post ): bool {
	if ( $post->post_type !== 'mls_evidence' || ! is_user_logged_in() ) { return false; }
	$course = (int) get_post_meta( $post->ID, '_mls_course', true );
	return (int) $post->post_author === get_current_user_id() || mls_manage( $course );
}

function mls_evidence_upload( WP_REST_Request $request ) {
	$context = mls_context( absint( $request['lesson'] ) );
	$attempt = get_post( absint( $request->get_param( 'attempt' ) ) );
	if ( ! $context || ! mls_access( $context ) || ! $attempt || $attempt->post_type !== 'mls_attempt' || (int) $attempt->post_author !== get_current_user_id() || (int) get_post_meta( $attempt->ID, '_mls_lesson', true ) !== $context['lesson_id'] || get_post_meta( $attempt->ID, '_mls_release', true ) !== $context['release'] || ! in_array( get_post_meta( $attempt->ID, '_mls_status', true ), array( 'in_progress', 'pending_review', 'revision_required' ), true ) ) { return mls_error( 'Evidence can only be added to your active or reviewable submission.', 403 ); }
	$description = sanitize_textarea_field( $request->get_param( 'description' ) ?? '' );
	if ( strlen( $description ) < 10 || strlen( $description ) > 2000 ) { return mls_error( 'Describe the evidence and the skill it demonstrates (10-2000 characters).' ); }
	$files = $request->get_file_params(); $file = $files['file'] ?? null;
	if ( ! is_array( $file ) || (int) $file['error'] !== UPLOAD_ERR_OK || ! is_uploaded_file( $file['tmp_name'] ) ) { return mls_error( 'Upload one PDF, PNG, JPEG or plain-text file.' ); }
	$size = filesize( $file['tmp_name'] );
	if ( ! $size || $size > 10 * 1024 * 1024 ) { return mls_error( 'Maximum evidence size is 10 MB.' ); }
	$types = array( 'pdf' => 'application/pdf', 'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'txt' => 'text/plain' );
	$ext = strtolower( pathinfo( sanitize_file_name( $file['name'] ), PATHINFO_EXTENSION ) );
	$mime = ( new finfo( FILEINFO_MIME_TYPE ) )->file( $file['tmp_name'] );
	if ( ! isset( $types[ $ext ] ) || $types[ $ext ] !== $mime ) { return mls_error( 'The file type does not match an allowed document or image.' ); }
	global $wpdb; $lock = 'mls-submit-' . $attempt->ID;
	if ( (int) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s,2)', $lock ) ) !== 1 ) { return mls_error( 'Please retry shortly.', 409 ); }
	try {
		clean_post_cache( $attempt->ID );
		if ( ! in_array( get_post_meta( $attempt->ID, '_mls_status', true ), array( 'in_progress', 'pending_review', 'revision_required' ), true ) ) { return mls_error( 'This submission has already been finalized.', 409 ); }
		$data = json_decode( get_post_field( 'post_content', $attempt->ID ), true );
		if ( get_post_meta( $attempt->ID, '_mls_status', true ) === 'in_progress' && ( $data['bank']['minutes'] ?? 0 ) && time() > $data['started_at'] + $data['bank']['minutes'] * 60 ) { return mls_error( 'The attempt time limit has expired.', 409 ); }
		$count = new WP_Query( array( 'post_type' => 'mls_evidence', 'post_status' => 'private', 'posts_per_page' => 1, 'meta_key' => '_mls_attempt', 'meta_value' => $attempt->ID ) );
		if ( $count->found_posts >= 5 ) { return mls_error( 'Maximum five evidence files per submission.' ); }
		$dir = mls_private_dir(); $name = bin2hex( random_bytes( 24 ) ) . '.' . $ext;
		if ( ! move_uploaded_file( $file['tmp_name'], $dir . '/' . $name ) ) { throw new RuntimeException( 'Could not store evidence privately.' ); }
		chmod( $dir . '/' . $name, 0600 );
		$record = array( 'storage' => $name, 'name' => sanitize_file_name( $file['name'] ), 'mime' => $mime, 'bytes' => $size, 'sha256' => hash_file( 'sha256', $dir . '/' . $name ), 'description' => $description, 'uploaded_at' => gmdate( 'c' ) );
		$id = wp_insert_post( array( 'post_type' => 'mls_evidence', 'post_status' => 'private', 'post_author' => get_current_user_id(), 'post_title' => $record['name'], 'post_content' => wp_slash( wp_json_encode( $record ) ) ), true );
		if ( is_wp_error( $id ) ) { unlink( $dir . '/' . $name ); return $id; }
		foreach ( array( '_mls_course' => $context['course_id'], '_mls_lesson' => $context['lesson_id'], '_mls_release' => $context['release'], '_mls_attempt' => $attempt->ID ) as $key => $value ) { update_post_meta( $id, $key, $value ); }
		update_post_meta( $attempt->ID, '_mls_evidence_version', $id );
		mls_event( $context['course_id'], $context['lesson_id'], 'evidence_uploaded', array( 'attempt' => $attempt->ID, 'evidence' => $id, 'sha256' => $record['sha256'] ) );
		return mls_private_response( array( 'saved' => true, 'id' => $id, 'name' => $record['name'] ), 201 );
	} catch ( Throwable $error ) { return mls_error( $error->getMessage(), 503 ); }
	finally { $wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock ) ); }
}

function mls_evidence_url( int $id ): string {
	return add_query_arg( '_wpnonce', wp_create_nonce( 'mls_evidence_' . $id ), admin_url( 'admin-post.php?action=mls_evidence&id=' . $id ) );
}

function mls_evidence_download(): void {
	$id = absint( $_GET['id'] ?? 0 ); check_admin_referer( 'mls_evidence_' . $id ); $post = get_post( $id );
	if ( ! $post || ! mls_evidence_allowed( $post ) ) { wp_die( 'Evidence unavailable.', '', array( 'response' => 403 ) ); }
	try {
		$data = json_decode( $post->post_content, true );
		if ( ! preg_match( '/^[a-f0-9]{48}\.(pdf|png|jpe?g|txt)$/', $data['storage'] ?? '' ) ) { throw new RuntimeException( 'Invalid evidence record.' ); }
		$path = mls_private_dir() . '/' . $data['storage'];
		if ( ! is_file( $path ) || ! hash_equals( $data['sha256'], hash_file( 'sha256', $path ) ) ) { throw new RuntimeException( 'Evidence integrity check failed.' ); }
		nocache_headers(); header( 'Content-Type: application/octet-stream' ); header( 'X-Content-Type-Options: nosniff' ); header( 'Content-Security-Policy: sandbox' );
		header( 'Content-Disposition: attachment; filename="evidence-' . $id . '.' . pathinfo( $path, PATHINFO_EXTENSION ) . '"' ); header( 'Content-Length: ' . filesize( $path ) ); readfile( $path ); exit;
	} catch ( Throwable $error ) { wp_die( esc_html( $error->getMessage() ), '', array( 'response' => 503 ) ); }
}

function mls_evidence_list( int $attempt ): string {
	$items = get_posts( array( 'post_type' => 'mls_evidence', 'post_status' => 'private', 'posts_per_page' => 5, 'meta_key' => '_mls_attempt', 'meta_value' => $attempt ) ); $html = '';
	foreach ( $items as $item ) { if ( ! mls_evidence_allowed( $item ) ) { continue; } $data = json_decode( $item->post_content, true ); $html .= '<p><a href="' . esc_url( mls_evidence_url( $item->ID ) ) . '">' . esc_html( $item->post_title ) . '</a><br>' . esc_html( $data['description'] ?? '' ) . '</p>'; }
	return $html;
}
