<?php
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit( 1 ); }
$course = 9838; $lesson = 9840; $context = mls_context( $lesson );
if ( ! $context || $context['course_id'] !== $course || $context['source_id'] !== 'BSBTEC202' || ! in_array( MLS_VERSION, array( '0.3.1', '0.3.2' ), true ) ) { WP_CLI::error( 'Unexpected pilot or release; no changes made.' ); }
$admin = (int) get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) )[0]; wp_set_current_user( $admin );
$original = get_post_meta( $course, '_mls_form', true ); $form_status = 'existing form preserved';
if ( ! $original ) {
	$schema = array( array( 'name' => 'learning_goal', 'label' => 'What would you like to practise in this course?', 'type' => 'textarea', 'required' => true ), array( 'name' => 'study_mode', 'label' => 'Preferred study mode', 'type' => 'select', 'options' => array( 'Self-paced', 'Trainer-supported' ) ), array( 'name' => 'weekly_hours', 'label' => 'Planned study hours each week', 'type' => 'number', 'min' => 1, 'max' => 40 ), array( 'name' => 'target_date', 'label' => 'Your target completion date', 'type' => 'date' ) );
	$result = mls_save_form_schema( $course, $schema, mls_form_fingerprint( array() ) ); if ( is_wp_error( $result ) ) { WP_CLI::error( $result->get_error_message() ); } $form_status = 'typed learning-goals form enabled';
}
$existing = get_post_meta( $lesson, '_mls_rubric_pilot_bank', true );
if ( $existing && get_post_status( $existing ) === 'publish' && get_post_meta( $existing, '_mls_release', true ) === $context['release'] ) { echo wp_json_encode( array( 'bank' => (int) $existing, 'status' => 'existing rubric pilot preserved', 'form' => $form_status ) ) . "\n"; return; }
$bank = mls_bank( $context );
if ( ! $bank || get_post_meta( $bank->ID, '_mls_origin', true ) !== 'authored_pilot_v1' || mls_has_rubric( json_decode( $bank->post_content, true ) ) ) { WP_CLI::error( 'The pilot bank changed. Review it before applying this one-time enhancement.' ); }
$changes = array(
	'pass_percent' => 80, 'minutes' => 0,
	'task' => 'Practice task: using a training account or mock environment, prepare a safe workplace-email sign-in and sign-out checklist. Explain how you verify the approved portal and account, respond to an unexpected multifactor approval request, contact known support if sign-in fails, and finish safely on a shared device. Upload a redacted training checklist or annotated mock screenshot covering these steps. Do not include real passwords, recovery codes, personal email addresses or private messages. Explain your decisions below. This is formative practice, not a formal competency assessment.',
	'task_criteria' => array(
		array( 'label' => 'Identify the approved application, verified portal and correct organisation/account before entering credentials.', 'max_points' => 5, 'min_points' => 3 ),
		array( 'label' => 'Reject an unexpected multifactor prompt and use a known recovery/support route without sharing passwords or codes.', 'max_points' => 5, 'min_points' => 3 ),
		array( 'label' => 'Explain safe sign-out and device locking; support the steps with a redacted training checklist or mock screenshot.', 'max_points' => 5, 'min_points' => 3, 'evidence_required' => true ),
	),
);
$id = mls_save_rubric_bank( $course, $lesson, $bank->ID, hash( 'sha256', $bank->post_content ), $changes, true );
if ( is_wp_error( $id ) ) { WP_CLI::error( $id->get_error_message() ); }
update_post_meta( $lesson, '_mls_rubric_pilot_bank', $id );
echo wp_json_encode( array( 'bank' => $id, 'parent_bank_preserved' => $bank->ID, 'form' => $form_status, 'purpose' => 'formative practice only', 'existing_attempts' => 'original snapshots preserved' ) ) . "\n";
