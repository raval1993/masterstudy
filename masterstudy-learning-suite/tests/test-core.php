<?php
if ( PHP_SAPI !== 'cli' ) { exit( 1 ); }
define( 'ABSPATH', __DIR__ );
function wp_strip_all_tags( $v ) { return strip_tags( $v ); }
function sanitize_textarea_field( $v ) { return trim( strip_tags( $v ) ); }
function sanitize_text_field( $v ) { return trim( strip_tags( $v ) ); }
function add_action( ...$args ) {}
function wp_json_encode( $v ) { return json_encode( $v ); }
require dirname( __DIR__ ) . '/includes/core.php';
require dirname( __DIR__ ) . '/includes/rubrics.php';
require dirname( __DIR__ ) . '/includes/forms.php';
function verify( bool $value, string $message ): void { if ( ! $value ) { fwrite( STDERR, $message . "\n" ); exit( 1 ); } }
$bank = mls_validate_bank( array( 'minutes' => 20, 'questions' => array( array( 'type' => 'mcq', 'prompt' => 'Which choice matches the source?', 'options' => array( 'Incorrect option', 'Correct option' ), 'correct' => 1, 'explanation' => 'Source answer' ) ) ) );
verify( mls_grade( $bank, array( 'q0' => 1 ) )['status'] === 'passed', 'correct choice passes' );
verify( mls_grade( $bank, array( 'q0' => 0 ) )['status'] === 'revision_required', 'incorrect choice fails' );
verify( mls_grade( $bank, array( 'q0' => '1' ) )['status'] === 'revision_required', 'string coercion cannot award marks' );
$public = mls_public_bank( $bank );
verify( ! isset( $public['questions'][0]['correct'] ) && ! isset( $public['questions'][0]['explanation'] ), 'answer keys are hidden' );
$bank['questions'][] = array( 'id' => 'q1', 'type' => 'practical', 'prompt' => 'Demonstrate this skill.' );
verify( mls_grade( $bank, array( 'q0' => 1, 'q1' => 'written response' ) )['status'] === 'pending_review', 'written evidence cannot automatically pass' );
foreach ( array( array( 'questions' => array() ), array( 'questions' => array( array( 'type' => 'mcq', 'prompt' => 'Invalid answer position', 'options' => array( 'a', 'b' ), 'correct' => 5 ) ) ) ) as $invalid ) {
	$failed = false; try { mls_validate_bank( $invalid ); } catch ( InvalidArgumentException $error ) { $failed = true; } verify( $failed, 'invalid bank was accepted' );
}
$quote = 'Use the known support channel for assistance.';
$draft = array( 'questions' => array_fill( 0, 2, array( 'type' => 'mcq', 'prompt' => 'Which action matches the lesson guidance?', 'options' => array( $quote, 'Give someone your password.' ), 'correct' => 0 ) ) );
verify( count( mls_validate_ai_bank( $draft, $quote )['questions'] ) === 2, 'Source-backed draft rejected.' );
$bad = $draft; $bad['questions'][0]['correct'] = 1;
$negated = $draft; $negated['questions'][0]['prompt'] = 'Which action is NOT correct for this lesson?';
foreach ( array( $bad, $negated, array( 'questions' => array( $draft['questions'][0] ) ) ) as $invalid ) {
	$failed = false; try { mls_validate_ai_bank( $invalid, $quote ); } catch ( InvalidArgumentException $error ) { $failed = true; } verify( $failed, 'Unsupported AI draft was accepted.' );
}
require __DIR__ . '/test-form-rubric-core.php';
echo "PASS: question validation, answer-key privacy, scoring, manual review, AI source guards, typed form schemas/answers and weighted rubric grading.\n";
