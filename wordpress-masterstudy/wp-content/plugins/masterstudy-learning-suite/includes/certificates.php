<?php
defined( 'ABSPATH' ) || exit;

add_action( 'admin_post_mls_certificate', 'mls_certificate_print' );
add_shortcode( 'mls_verify_certificate', 'mls_certificate_verification' );
add_action( 'init', function () { if ( ! get_page_by_path( 'verify-learning-record' ) ) { wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_name' => 'verify-learning-record', 'post_title' => 'Verify Learning Record', 'post_content' => '[mls_verify_certificate]' ) ); } }, 30 );

function mls_issue_certificate( int $course, int $learner, string $note ) {
	if ( ! mls_manage( $course ) || get_post_type( $course ) !== 'stm-courses' || ! get_userdata( $learner ) ) { return mls_error( 'Select an assigned course and existing learner.', 403 ); }
	$progress = STM_LMS_Course::get_user_course( $learner, $course ); $release = get_post_meta( $course, '_ca_verified_release', true );
	if ( ! $release || (int) ( $progress['progress_percent'] ?? 0 ) < 100 || strlen( trim( $note ) ) < 30 || strlen( $note ) > 2000 ) { return mls_error( 'A verified course release, 100% native completion and a 30-2000 character reviewer note are required.' ); }
	$banks = get_posts( array( 'post_type' => 'mls_bank', 'post_status' => 'publish', 'posts_per_page' => -1, 'meta_query' => array( array( 'key' => '_mls_course', 'value' => $course ), array( 'key' => '_mls_release', 'value' => $release ) ) ) );
	if ( ! $banks ) { return mls_error( 'This course has no reviewed assessment banks. Review the assessment coverage before issuing a record.' ); }
	foreach ( $banks as $bank ) {
		$passed = get_posts( array( 'post_type' => 'mls_attempt', 'post_status' => 'private', 'author' => $learner, 'posts_per_page' => 1, 'fields' => 'ids', 'meta_query' => array( array( 'key' => '_mls_bank', 'value' => $bank->ID ), array( 'key' => '_mls_status', 'value' => 'passed' ), array( 'key' => '_mls_release', 'value' => $release ) ) ) );
		if ( ! $passed ) { return mls_error( 'The learner must pass all enabled assessments for this release before issuance.' ); }
	}
	global $wpdb; $lock = 'mls-cert-' . $course . '-' . $learner;
	if ( (int) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s,2)', $lock ) ) !== 1 ) { return mls_error( 'Please retry shortly.', 409 ); }
	try {
		$existing = get_posts( array( 'post_type' => 'mls_certificate', 'post_status' => 'private', 'author' => $learner, 'posts_per_page' => -1, 'meta_query' => array( array( 'key' => '_mls_course', 'value' => $course ), array( 'key' => '_mls_release', 'value' => $release ) ) ) );
		foreach ( $existing as $record ) { if ( ! get_post_meta( $record->ID, '_mls_revoked', true ) ) { return $record->ID; } }
		$data = array( 'learner' => get_userdata( $learner )->display_name, 'course' => get_the_title( $course ), 'issuer' => get_bloginfo( 'name' ), 'issued_at' => gmdate( 'c' ), 'reviewed_by' => get_current_user_id(), 'review_note' => sanitize_textarea_field( $note ), 'type' => 'Course completion record', 'notice' => 'Non-accredited learning record. Not a qualification, Statement of Attainment or formal competency award.' );
		$id = wp_insert_post( array( 'post_type' => 'mls_certificate', 'post_status' => 'private', 'post_author' => $learner, 'post_title' => $data['course'], 'post_content' => wp_slash( wp_json_encode( $data ) ) ), true );
		if ( is_wp_error( $id ) ) { return $id; }
		foreach ( array( '_mls_course' => $course, '_mls_release' => $release, '_mls_token' => bin2hex( random_bytes( 24 ) ) ) as $key => $value ) { update_post_meta( $id, $key, $value ); }
		mls_event( $course, 0, 'completion_record_issued', array( 'certificate' => $id, 'learner' => $learner ) ); return $id;
	} finally { $wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock ) ); }
}

function mls_certificate_admin( int $course ): void {
	mls_operation_form( 'certificate', $course );
	echo '<p><label>Learner ID <input type="number" name="learner" min="1" required></label></p><p><label>Reviewer note<textarea name="note" rows="3" class="large-text" minlength="30" maxlength="2000" required></textarea></label></p><p>This issues a non-accredited completion record, not an RTO credential. Check course assessment coverage before issuing.</p><button class="button">Issue reviewed completion record</button></form>';
	foreach ( get_posts( array( 'post_type' => 'mls_certificate', 'post_status' => 'private', 'posts_per_page' => 25, 'meta_key' => '_mls_course', 'meta_value' => $course ) ) as $post ) {
		$data = json_decode( $post->post_content, true ); echo '<p>' . esc_html( $data['learner'] ) . ' - ' . esc_html( $data['issued_at'] ) . ( get_post_meta( $post->ID, '_mls_revoked', true ) ? ' (revoked)' : '' ) . '</p>';
		mls_operation_form( 'revoke_certificate', $course ); echo '<input type="hidden" name="record" value="' . $post->ID . '"><button class="button">Revoke record</button></form>';
	}
}

function mls_certificate_url( int $id ): string { return add_query_arg( '_wpnonce', wp_create_nonce( 'mls_certificate_' . $id ), admin_url( 'admin-post.php?action=mls_certificate&id=' . $id ) ); }

function mls_certificate_print(): void {
	$id = absint( $_GET['id'] ?? 0 ); check_admin_referer( 'mls_certificate_' . $id ); $post = get_post( $id );
	if ( ! is_user_logged_in() || ! $post || $post->post_type !== 'mls_certificate' || ( (int) $post->post_author !== get_current_user_id() && ! mls_manage( (int) get_post_meta( $id, '_mls_course', true ) ) ) || get_post_meta( $id, '_mls_revoked', true ) ) { wp_die( 'Completion record unavailable.', '', array( 'response' => 403 ) ); }
	$data = json_decode( $post->post_content, true ); $verify = home_url( '/verify-learning-record/?token=' . get_post_meta( $id, '_mls_token', true ) );
	nocache_headers(); header( 'Content-Type: text/html; charset=utf-8' ); header( 'X-Robots-Tag: noindex, nofollow' ); header( 'Referrer-Policy: no-referrer' );
	echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Course completion record</title><style>body{margin:0;font-family:Arial,sans-serif;color:#243630;background:#fff}main{max-width:880px;margin:40px auto;padding:36px;border:2px solid #145e55}h1{font-size:32px}h2{font-size:28px}p{line-height:1.6;overflow-wrap:anywhere}button{padding:10px 16px;cursor:pointer}@media print{button{display:none}main{margin:0;border:2px solid #145e55}}@media(max-width:600px){main{margin:16px;padding:20px}h1{font-size:26px}}</style></head><body><main><p>' . esc_html( $data['issuer'] ) . '</p><h1>' . esc_html( $data['type'] ) . '</h1><p>Presented to</p><h2>' . esc_html( $data['learner'] ) . '</h2><p>For completing</p><h2>' . esc_html( $data['course'] ) . '</h2><p>Issued: ' . esc_html( substr( $data['issued_at'], 0, 10 ) ) . ' &middot; Record #' . $id . '</p><p>' . esc_html( $data['notice'] ) . '</p><p><a rel="noreferrer" href="' . esc_url( $verify ) . '">Verify this record</a></p><button onclick="window.print()">Print / save PDF</button></main></body></html>'; exit;
}

function mls_certificate_verification(): string {
	$token = sanitize_text_field( $_GET['token'] ?? '' );
	if ( ! preg_match( '/^[a-f0-9]{48}$/', $token ) ) { return '<p>Enter the verification link supplied with a learning record.</p>'; }
	$posts = get_posts( array( 'post_type' => 'mls_certificate', 'post_status' => 'private', 'posts_per_page' => 1, 'meta_key' => '_mls_token', 'meta_value' => $token ) );
	if ( ! $posts ) { return '<p>Record not found.</p>'; }
	$post = $posts[0]; $data = json_decode( $post->post_content, true );
	return '<h2>' . ( get_post_meta( $post->ID, '_mls_revoked', true ) ? 'Revoked record' : 'Issued learning record' ) . '</h2><p>Record #' . $post->ID . '</p><p>' . esc_html( $data['course'] ) . '</p><p>Issued: ' . esc_html( substr( $data['issued_at'], 0, 10 ) ) . '</p><p>' . esc_html( $data['notice'] ) . '</p>';
}

add_action( 'template_redirect', function () { if ( is_page( 'verify-learning-record' ) ) { nocache_headers(); header( 'X-Robots-Tag: noindex, nofollow' ); header( 'Referrer-Policy: no-referrer' ); } } );
