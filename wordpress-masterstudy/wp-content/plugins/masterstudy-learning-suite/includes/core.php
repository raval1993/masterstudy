<?php
defined( 'ABSPATH' ) || exit;

function mls_validate_bank( array $bank ): array {
	$questions = $bank['questions'] ?? array();
	if ( ! is_array( $questions ) || ! $questions || count( $questions ) > 30 ) { throw new InvalidArgumentException( 'Provide 1-30 questions.' ); }
	$clean = array();
	foreach ( $questions as $index => $question ) {
		$type = $question['type'] ?? 'short';
		$prompt = trim( wp_strip_all_tags( is_string( $question['prompt'] ?? null ) ? $question['prompt'] : '' ) );
		if ( ! in_array( $type, array( 'mcq', 'short', 'practical' ), true ) || strlen( $prompt ) < 10 || strlen( $prompt ) > 2000 ) { throw new InvalidArgumentException( 'Invalid question prompt/type.' ); }
		$row = array( 'id' => 'q' . $index, 'type' => $type, 'prompt' => $prompt, 'explanation' => sanitize_textarea_field( $question['explanation'] ?? '' ), 'rubric' => sanitize_textarea_field( $question['rubric'] ?? '' ) );
		if ( $type === 'mcq' ) {
			$options = $question['options'] ?? array();
			if ( ! is_array( $options ) || count( $options ) < 2 || count( $options ) > 6 || ! isset( $question['correct'] ) || ! is_int( $question['correct'] ) || $question['correct'] < 0 || $question['correct'] >= count( $options ) ) { throw new InvalidArgumentException( 'Invalid choices or answer index.' ); }
			foreach ( $options as $option ) { if ( ! is_string( $option ) || strlen( $option ) < 1 || strlen( $option ) > 2000 ) { throw new InvalidArgumentException( 'Invalid choice.' ); } }
			$row['options'] = array_map( 'sanitize_text_field', array_values( $options ) );
			$row['correct'] = $question['correct'];
		}
		$clean[] = $row;
	}
	return array( 'questions' => $clean, 'minutes' => max( 0, min( 180, (int) ( $bank['minutes'] ?? 0 ) ) ), 'pass_percent' => max( 1, min( 100, (int) ( $bank['pass_percent'] ?? 80 ) ) ), 'randomize' => ! empty( $bank['randomize'] ) );
}

function mls_grade( array $bank, array $answers ): array {
	$correct = 0; $count = 0; $manual = false;
	foreach ( $bank['questions'] as $question ) {
		$answer = $answers[ $question['id'] ] ?? null;
		if ( $question['type'] !== 'mcq' ) { $manual = true; continue; }
		++$count;
		if ( is_int( $answer ) && $answer === $question['correct'] ) { ++$correct; }
	}
	$score = $count ? (int) round( 100 * $correct / $count ) : null;
	return array( 'score' => $score, 'status' => $manual ? 'pending_review' : ( $score !== null && $score >= $bank['pass_percent'] ? 'passed' : 'revision_required' ) );
}

function mls_public_bank( array $bank ): array {
	$copy = $bank;
	foreach ( $copy['questions'] as &$question ) { unset( $question['correct'], $question['explanation'], $question['rubric'] ); }
	return $copy;
}

function mls_validate_ai_bank( array $raw, string $source ): array {
	$bank = mls_validate_bank( $raw );
	if ( count( $bank['questions'] ) !== 2 ) { throw new InvalidArgumentException( 'AI draft must contain exactly two questions.' ); }
	$normalize = fn( $value ) => strtolower( preg_replace( '/\s+/', ' ', trim( $value ) ) );
	foreach ( $bank['questions'] as $question ) {
		if ( $question['type'] !== 'mcq' ) { throw new InvalidArgumentException( 'Expected multiple-choice draft questions.' ); }
		$correct = $question['options'][ $question['correct'] ];
		if ( strlen( $correct ) < 15 || strpos( $normalize( $source ), $normalize( $correct ) ) === false ) { throw new InvalidArgumentException( 'The proposed correct answer is not an exact quote from the teaching source.' ); }
		if ( preg_match( '/\b(not|except|incorrect|false)\b/i', $question['prompt'] ) ) { throw new InvalidArgumentException( 'Negated AI questions require rewriting before review.' ); }
	}
	return $bank;
}

function mls_rate( string $action, int $seconds ): bool {
	$key = '_mls_rate_' . get_current_user_id() . '_' . $action;
	$now = time();
	$last = (int) get_user_meta( get_current_user_id(), $key, true );
	if ( $last && $now - $last < $seconds ) { return false; }
	update_user_meta( get_current_user_id(), $key, $now );
	return true;
}
