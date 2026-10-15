<?php
if ( PHP_SAPI !== 'cli' || ! function_exists( 'verify' ) ) { exit( 1 ); }
function invalid_input( callable $callback, string $message ): void { $rejected = false; try { $callback(); } catch ( InvalidArgumentException $error ) { $rejected = true; } verify( $rejected, $message ); }
$schema = mls_validate_form_schema( array(
	array( 'name' => 'goal', 'label' => 'Your learning goal', 'required' => true ),
	array( 'name' => 'mode', 'label' => 'Study mode', 'type' => 'select', 'options' => array( 'Online', 'Blended' ) ),
	array( 'name' => 'confirm', 'label' => 'I confirm my goal', 'type' => 'checkbox', 'required' => true ),
	array( 'name' => 'target', 'label' => 'Target date', 'type' => 'date' ),
	array( 'name' => 'hours', 'label' => 'Weekly hours', 'type' => 'number', 'min' => 1, 'max' => 40 ),
) );
$answers = array( 'goal' => 'Practise secure communication.', 'mode' => 'Online', 'confirm' => true, 'target' => '2028-02-29', 'hours' => '4' );
verify( mls_validate_form_answers( $schema, $answers ) === $answers, 'Valid typed answers rejected.' );
verify( mls_form_fingerprint( $schema ) === mls_form_fingerprint( mls_validate_form_schema( $schema ) ), 'Form fingerprints are unstable.' );
foreach ( array( array( 'goal' => '' ), array( 'goal' => '<script></script>' ), array( 'mode' => 'Unlisted' ), array( 'confirm' => false ), array( 'confirm' => 'true' ), array( 'target' => '2026-02-29' ), array( 'hours' => '1.5' ), array( 'hours' => '41' ), array( 'hours' => array( 3 ) ), array( 'role' => 'administrator' ) ) as $bad ) { invalid_input( fn() => mls_validate_form_answers( $schema, array_merge( $answers, $bad ) ), 'Invalid typed answer accepted.' ); }
foreach ( array( array( $schema[0], $schema[0] ), array( array( 'name' => '_admin', 'label' => 'Invalid key' ) ), array( array( 'name' => 'mode', 'label' => 'Mode', 'type' => 'select', 'options' => array( 'Same', '<b>Same</b>' ) ) ), array( array( 'name' => 'num', 'label' => 'Number', 'type' => 'number', 'min' => 3, 'max' => 1 ) ) ) as $bad ) { invalid_input( fn() => mls_validate_form_schema( $bad ), 'Invalid schema accepted.' ); }
$criteria = array( array( 'label' => 'Select an appropriate secure channel.', 'max_points' => 5, 'min_points' => 3, 'evidence_required' => true ), array( 'label' => 'Explain the escalation decision.', 'max_points' => 5, 'min_points' => 2 ) );
$bank = mls_validate_bank( array( 'pass_percent' => 80, 'questions' => array( array( 'type' => 'practical', 'prompt' => 'Demonstrate a safe message and explain your escalation process.', 'criteria' => $criteria, 'rubric' => 'Private marker-only note.' ) ) ) );
$marks = array( 'q0_c0' => array( 'points' => 4, 'evidence' => array( 123 ) ), 'q0_c1' => array( 'points' => 4 ) );
verify( mls_rubric_result( $bank, array(), $marks )['score'] === 80 && mls_rubric_result( $bank, array(), $marks )['status'] === 'passed', 'Weighted boundary pass failed.' );
$missing = $marks; $missing['q0_c0']['evidence'] = array(); verify( mls_rubric_result( $bank, array(), $missing )['status'] === 'revision_required', 'Required file evidence bypassed.' );
$minimum = $marks; $minimum['q0_c0']['points'] = 2; $minimum['q0_c1']['points'] = 5; $low_bank = $bank; $low_bank['pass_percent'] = 60; verify( mls_rubric_result( $low_bank, array(), $minimum )['status'] === 'revision_required', 'Criterion minimum bypassed by average.' );
foreach ( array( array(), array_merge( $marks, array( 'q9_c0' => array( 'points' => 5 ) ) ), array_replace( $marks, array( 'q0_c0' => array( 'points' => 99 ) ) ), array_replace( $marks, array( 'q0_c0' => array( 'points' => '5' ) ) ) ) as $bad ) { invalid_input( fn() => mls_rubric_result( $bank, array(), $bad ), 'Invalid rubric marks accepted.' ); }
$public = mls_public_bank( $bank ); verify( isset( $public['questions'][0]['criteria'] ) && ! isset( $public['questions'][0]['rubric'] ), 'Criteria visibility or marker note privacy failed.' );
verify( mls_grade( $bank, array( 'q0' => 'Practical response' ) )['status'] === 'pending_review' && mls_grade( $bank, array() )['score'] === null, 'Practical task auto-graded.' );
$mixed = $bank; $mixed['questions'][] = array( 'type' => 'short', 'prompt' => 'Describe another useful security step.' ); invalid_input( fn() => mls_validate_bank( $mixed ), 'Incomplete weighted rubric accepted.' );
invalid_input( fn() => mls_marks_from_post( array( 'q0_c0' => array( 'points' => '1.5' ) ) ), 'Fractional form marks accepted.' );
$rounding = mls_validate_bank( array( 'pass_percent' => 80, 'questions' => array( array( 'type' => 'practical', 'prompt' => 'Provide a complete practical submission for review.', 'criteria' => array_fill( 0, 10, array( 'label' => 'Demonstrate this assessed practice skill.', 'max_points' => 20 ) ) ) ) ) );
$boundary_marks = array(); for ( $i = 0; $i < 10; ++$i ) { $boundary_marks[ 'q0_c' . $i ] = array( 'points' => $i < 7 ? 20 : ( $i === 7 ? 19 : 0 ) ); }
$result = mls_rubric_result( $rounding, array(), $boundary_marks ); verify( $result['score'] === 80 && $result['status'] === 'revision_required', 'Below-threshold points were rounded into a pass.' );
