<?php
defined( 'ABSPATH' ) || exit;

function mls_mail_ready(): bool {
	return defined( 'MLS_SMTP_HOST' ) && defined( 'MLS_SMTP_USER' ) && defined( 'MLS_SMTP_PASSWORD' ) && defined( 'MLS_SMTP_FROM' ) && MLS_SMTP_HOST && MLS_SMTP_USER && MLS_SMTP_PASSWORD && is_email( MLS_SMTP_FROM );
}

add_action( 'phpmailer_init', function ( $mail ) {
	if ( ! mls_mail_ready() ) { return; }
	$mail->isSMTP(); $mail->Host = MLS_SMTP_HOST; $mail->SMTPAuth = true; $mail->Username = MLS_SMTP_USER; $mail->Password = MLS_SMTP_PASSWORD;
	$mail->Port = defined( 'MLS_SMTP_PORT' ) && (int) MLS_SMTP_PORT === 465 ? 465 : 587;
	$mail->SMTPSecure = $mail->Port === 465 ? 'ssl' : 'tls'; $mail->SMTPAutoTLS = true; $mail->Timeout = 15;
	$mail->setFrom( MLS_SMTP_FROM, get_bloginfo( 'name' ), false );
} );

add_action( 'mls_event_recorded', function ( $id, $course, $lesson, $type, $details ) {
	if ( ( defined( 'MLS_TEST_RUN' ) && MLS_TEST_RUN ) || get_user_meta( get_current_user_id(), '_mls_browser_fixture', true ) ) { return; }
	$user = $type === 'instructor_decision' ? (int) get_post_field( 'post_author', $details['attempt'] ?? 0 ) : ( $type === 'completion_record_issued' ? (int) ( $details['learner'] ?? 0 ) : 0 );
	if ( ! $user || ! get_user_meta( $user, '_mls_learning_email_opt_in', true ) ) { return; }
	$subject = $type === 'completion_record_issued' ? 'Your course completion record is available' : 'Your assessment has been reviewed';
	$message = $subject . ".\n\n" . get_the_title( $course ) . "\n\nView your private learning records: " . home_url( '/learning-hub/' );
	$record = wp_insert_post( array( 'post_type' => 'mls_mail', 'post_status' => 'private', 'post_author' => $user, 'post_title' => $subject, 'post_content' => wp_slash( $message ) ), true );
	if ( ! is_wp_error( $record ) ) { update_post_meta( $record, '_mls_status', mls_mail_ready() ? 'queued' : 'blocked_transport' ); update_post_meta( $record, '_mls_event', $id ); }
}, 20, 5 );

function mls_run_mail_outbox(): void {
	if ( ! defined( 'WP_CLI' ) || ! WP_CLI || ! mls_mail_ready() ) { return; }
	global $wpdb;
	if ( (int) $wpdb->get_var( "SELECT GET_LOCK('mls-mail-outbox',0)" ) !== 1 ) { return; }
	try {
		$posts = get_posts( array( 'post_type' => 'mls_mail', 'post_status' => 'private', 'posts_per_page' => 5, 'meta_key' => '_mls_status', 'meta_value' => 'queued', 'order' => 'ASC' ) );
		foreach ( $posts as $post ) {
			$user = get_userdata( $post->post_author );
			if ( ! $user || ! is_email( $user->user_email ) || ! get_user_meta( $user->ID, '_mls_learning_email_opt_in', true ) ) { update_post_meta( $post->ID, '_mls_status', 'cancelled' ); continue; }
			update_post_meta( $post->ID, '_mls_status', 'sending' );
			$sent = wp_mail( $user->user_email, $post->post_title, $post->post_content );
			update_post_meta( $post->ID, '_mls_status', $sent ? 'accepted_by_transport' : 'failed' ); update_post_meta( $post->ID, '_mls_sent_at', gmdate( 'c' ) );
		}
	} finally { $wpdb->get_var( "SELECT RELEASE_LOCK('mls-mail-outbox')" ); }
}

add_action( 'admin_post_mls_email_preference', function () {
	if ( ! is_user_logged_in() ) { wp_die( 'Sign in first.', '', array( 'response' => 403 ) ); } check_admin_referer( 'mls_email_preference' );
	update_user_meta( get_current_user_id(), '_mls_learning_email_opt_in', ! empty( $_POST['opt_in'] ) ? 1 : 0 ); wp_safe_redirect( home_url( '/learning-hub/' ) ); exit;
} );

function mls_email_preference(): string {
	return '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">' . wp_nonce_field( 'mls_email_preference', '_wpnonce', true, false ) . '<input type="hidden" name="action" value="mls_email_preference"><label><input type="checkbox" name="opt_in" value="1" ' . checked( get_user_meta( get_current_user_id(), '_mls_learning_email_opt_in', true ), 1, false ) . '> Email me when my assessment is reviewed or a completion record is issued</label><button>Save email preference</button></form>';
}
