<?php
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit( 1 ); }
$admin = (int) get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) )[0];
wp_set_current_user( $admin );
$key = '_mls_rate_' . $admin . '_tutor'; $exists = metadata_exists( 'user', $admin, $key ); $old = get_user_meta( $admin, $key, true );
try {
	delete_user_meta( $admin, $key );
	$request = new WP_REST_Request( 'POST', '/mls/v1/tutor/9840' ); $request->set_param( 'lesson', 9840 );
	$request->set_header( 'Content-Type', 'application/json' ); $request->set_body( wp_json_encode( array( 'question' => 'What should I do when an unexpected multifactor authentication prompt arrives?' ) ) );
	$answer = mls_tutor_request( $request );
	if ( is_wp_error( $answer ) ) { throw new RuntimeException( $answer->get_error_message() ); }
	$output = $answer->get_data();
	if ( ! $output['sources'] || ! trim( $output['answer'] ) ) { throw new RuntimeException( 'Tutor did not return grounded lesson sources.' ); }
	echo wp_json_encode( array( 'tutor_answer' => $output['answer'], 'source_headings' => array_column( $output['sources'], 'heading' ) ), JSON_PRETTY_PRINT ) . "\n";
	$context = mls_context( 9840 );
	$id = wp_insert_post( array( 'post_type' => 'mls_job', 'post_status' => 'private', 'post_author' => $admin, 'post_title' => 'Pilot quiz draft: ' . get_the_title( 9840 ), 'post_content' => wp_slash( wp_json_encode( array( 'context' => $context, 'kind' => 'quiz', 'attempt' => 0 ) ) ) ), true );
	if ( is_wp_error( $id ) ) { throw new RuntimeException( $id->get_error_message() ); }
	update_post_meta( $id, '_mls_course', 9838 ); update_post_meta( $id, '_mls_status', 'queued' );
	mls_generate_job( $id );
	$status = get_post_meta( $id, '_mls_status', true );
	echo wp_json_encode( array( 'pilot_job' => $id, 'status' => $status, 'error' => get_post_meta( $id, '_mls_error', true ), 'output' => get_post_meta( $id, '_mls_output', true ) ) ) . "\n";
	if ( $status !== 'needs_review' ) { throw new RuntimeException( 'Local quiz generation failed validation.' ); }
	$bank = get_post( (int) get_post_meta( $id, '_mls_output', true ) );
	if ( $bank->post_status !== 'draft' ) { throw new RuntimeException( 'AI bank was auto-published.' ); }
	echo $bank->post_content . "\nPASS: live source retrieval, free local inference and source-quote validation. Quiz is an unreviewed draft, not an approved assessment.\n";
} catch ( Throwable $error ) { WP_CLI::warning( $error->getMessage() ); }
finally { if ( $exists ) { update_user_meta( $admin, $key, $old ); } else { delete_user_meta( $admin, $key ); } wp_set_current_user( 0 ); }
