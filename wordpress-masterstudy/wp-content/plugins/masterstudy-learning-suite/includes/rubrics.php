<?php
defined( 'ABSPATH' ) || exit;

function mls_validate_criteria( $criteria ): array {
	if ( ! is_array( $criteria ) || ! array_is_list( $criteria ) || count( $criteria ) > 10 ) { throw new InvalidArgumentException( 'Use up to ten criteria per written question.' ); }
	$clean = array();
	foreach ( $criteria as $index => $criterion ) {
		if ( ! is_array( $criterion ) || ! is_string( $criterion['label'] ?? null ) || strlen( trim( $criterion['label'] ) ) < 5 || strlen( $criterion['label'] ) > 500 ) { throw new InvalidArgumentException( 'Each criterion needs a 5-500 character description.' ); }
		$max = $criterion['max_points'] ?? 5; $min = $criterion['min_points'] ?? 0;
		if ( ! is_int( $max ) || ! is_int( $min ) || $max < 1 || $max > 20 || $min < 0 || $min > $max ) { throw new InvalidArgumentException( 'Criteria require integer maximum points (1-20) and a minimum between zero and the maximum.' ); }
		$label = sanitize_text_field( $criterion['label'] ); if ( strlen( $label ) < 5 ) { throw new InvalidArgumentException( 'Invalid criterion description.' ); }
		$clean[] = array( 'id' => 'c' . $index, 'label' => $label, 'max_points' => $max, 'min_points' => $min, 'evidence_required' => ! empty( $criterion['evidence_required'] ) );
	}
	return $clean;
}

function mls_has_rubric( array $bank ): bool {
	foreach ( $bank['questions'] as $question ) { if ( ! empty( $question['criteria'] ) ) { return true; } } return false;
}

function mls_rubric_result( array $bank, array $answers, array $marks ): array {
	$points = 0; $max = 0; $minimum_met = true; $clean = array(); $keys = array();
	foreach ( $bank['questions'] as $question ) {
		if ( $question['type'] === 'mcq' ) { ++$max; if ( ( $answers[ $question['id'] ] ?? null ) === $question['correct'] ) { ++$points; } continue; }
		if ( empty( $question['criteria'] ) ) { throw new InvalidArgumentException( 'Every written question needs criteria in a weighted rubric.' ); }
		foreach ( $question['criteria'] as $criterion ) {
			$key = $question['id'] . '_' . $criterion['id']; $keys[] = $key; $mark = $marks[ $key ] ?? null;
			if ( ! is_array( $mark ) || ! is_int( $mark['points'] ?? null ) || $mark['points'] < 0 || $mark['points'] > $criterion['max_points'] ) { throw new InvalidArgumentException( 'Record a valid mark for each criterion.' ); }
			$evidence = $mark['evidence'] ?? array();
			if ( ! is_array( $evidence ) || ! array_is_list( $evidence ) || count( $evidence ) > 5 ) { throw new InvalidArgumentException( 'Invalid evidence references.' ); }
			foreach ( $evidence as $id ) { if ( ! is_int( $id ) || $id < 1 ) { throw new InvalidArgumentException( 'Invalid evidence reference.' ); } }
			$clean[ $key ] = array( 'points' => $mark['points'], 'evidence' => array_values( array_unique( $evidence ) ) );
			$points += $mark['points']; $max += $criterion['max_points'];
			if ( $mark['points'] < $criterion['min_points'] || ( $criterion['evidence_required'] && ! $evidence ) ) { $minimum_met = false; }
		}
	}
	if ( array_diff( array_keys( $marks ), $keys ) || ! $keys ) { throw new InvalidArgumentException( 'Unexpected or missing rubric criteria.' ); }
	$score = (int) round( 100 * $points / $max );
	return array( 'marks' => $clean, 'points' => $points, 'max_points' => $max, 'score' => $score, 'minimum_met' => $minimum_met, 'status' => $minimum_met && 100 * $points >= $bank['pass_percent'] * $max ? 'passed' : 'revision_required' );
}

function mls_rubric_evidence_check( int $attempt, array $marks ): bool {
	$verified = array();
	foreach ( $marks as $mark ) {
		foreach ( $mark['evidence'] as $id ) {
			if ( isset( $verified[ $id ] ) ) { continue; }
			$post = get_post( $id );
			if ( ! $post || $post->post_type !== 'mls_evidence' || $post->post_status !== 'private' || (int) $post->post_author !== (int) get_post_field( 'post_author', $attempt ) || (int) get_post_meta( $id, '_mls_attempt', true ) !== $attempt || (int) get_post_meta( $id, '_mls_course', true ) !== (int) get_post_meta( $attempt, '_mls_course', true ) || get_post_meta( $id, '_mls_release', true ) !== get_post_meta( $attempt, '_mls_release', true ) ) { return false; }
			// A reference alone is insufficient: verify the stored evidence is still intact.
			$data = json_decode( $post->post_content, true );
			if ( ! preg_match( '/^[a-f0-9]{48}\.(pdf|png|jpe?g|txt)$/D', $data['storage'] ?? '' ) ) { return false; }
			$path = mls_private_dir() . '/' . $data['storage'];
			if ( ! is_file( $path ) || is_link( $path ) || ! is_string( $data['sha256'] ?? null ) || ! hash_equals( $data['sha256'], hash_file( 'sha256', $path ) ) ) { return false; }
			$verified[ $id ] = true;
		}
	}
	return true;
}

function mls_rubric_marking_fields( array $data, int $attempt ): string {
	if ( ! mls_has_rubric( $data['bank'] ?? array( 'questions' => array() ) ) ) { return ''; }
	$evidence = get_posts( array( 'post_type' => 'mls_evidence', 'post_status' => 'private', 'posts_per_page' => 5, 'meta_key' => '_mls_attempt', 'meta_value' => $attempt ) );
	ob_start(); ?><fieldset><legend><strong>Criterion marks</strong></legend><?php
	foreach ( $data['bank']['questions'] as $question ) {
		foreach ( $question['criteria'] ?? array() as $criterion ) {
			$key = $question['id'] . '_' . $criterion['id']; $saved = $data['rubric_review']['marks'][ $key ] ?? array(); ?>
			<p><strong><?php echo esc_html( $question['prompt'] ); ?></strong><br><label><?php echo esc_html( $criterion['label'] ); ?> <input type="number" name="marks[<?php echo esc_attr( $key ); ?>][points]" min="0" max="<?php echo $criterion['max_points']; ?>" step="1" required value="<?php echo esc_attr( $saved['points'] ?? '' ); ?>"></label> / <?php echo $criterion['max_points']; ?>; minimum <?php echo $criterion['min_points']; ?></p>
			<?php if ( $criterion['evidence_required'] ) : ?><p><strong>Supporting evidence required for a pass</strong></p><?php endif; ?>
			<?php foreach ( $evidence as $file ) : ?><label style="display:block"><input type="checkbox" name="marks[<?php echo esc_attr( $key ); ?>][evidence][]" value="<?php echo $file->ID; ?>" <?php checked( in_array( $file->ID, $saved['evidence'] ?? array(), true ) ); ?>><?php echo esc_html( $file->post_title ); ?></label><?php endforeach;
		}
	} ?></fieldset><?php return ob_get_clean();
}

function mls_marks_from_post( $input ): array {
	if ( ! is_array( $input ) || count( $input ) > 300 ) { throw new InvalidArgumentException( 'Invalid criterion marks.' ); }
	$marks = array();
	foreach ( $input as $key => $mark ) {
		if ( ! preg_match( '/^q\d+_c\d+$/D', $key ) || ! is_array( $mark ) ) { throw new InvalidArgumentException( 'Invalid criterion marks.' ); }
		$points = filter_var( $mark['points'] ?? null, FILTER_VALIDATE_INT );
		if ( $points === false || $points === null || $points < 0 || $points > 20 ) { throw new InvalidArgumentException( 'Enter whole-number marks within the criterion limits.' ); }
		$evidence = $mark['evidence'] ?? array(); if ( ! is_array( $evidence ) || count( $evidence ) > 5 ) { throw new InvalidArgumentException( 'Invalid evidence references.' ); }
		$ids = array(); foreach ( $evidence as $id ) { if ( ! is_string( $id ) || ! ctype_digit( $id ) || (int) $id < 1 ) { throw new InvalidArgumentException( 'Invalid evidence ID.' ); } $ids[] = (int) $id; }
		$marks[ $key ] = array( 'points' => $points, 'evidence' => $ids );
	}
	return $marks;
}

require_once __DIR__ . '/rubrics-admin.php';
