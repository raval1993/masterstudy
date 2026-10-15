<?php
defined( 'ABSPATH' ) || exit;
add_action( 'admin_menu', function () { add_submenu_page( 'mls-suite', 'Assessment rubrics', 'Assessment rubrics', 'read', 'mls-rubrics', 'mls_rubrics_admin' ); }, 20 );
add_action( 'admin_post_mls_rubric_bank', 'mls_rubric_bank_post' );

function mls_criteria_editor( string $name, array $criteria ): string {
	ob_start(); ?><table class="widefat striped"><thead><tr><th>Criterion</th><th>Maximum</th><th>Minimum</th><th>File evidence</th></tr></thead><tbody><?php
	for ( $i = 0; $i < min( 10, max( 3, count( $criteria ) + 1 ) ); ++$i ) { $row = $criteria[ $i ] ?? array(); $prefix = $name . '[' . $i . ']'; ?>
	<tr><td><input class="large-text" aria-label="Criterion description" name="<?php echo esc_attr( $prefix ); ?>[label]" maxlength="500" value="<?php echo esc_attr( $row['label'] ?? '' ); ?>"></td><td><input type="number" aria-label="Maximum points" name="<?php echo esc_attr( $prefix ); ?>[max_points]" min="1" max="20" value="<?php echo (int) ( $row['max_points'] ?? 5 ); ?>"></td><td><input type="number" aria-label="Minimum points" name="<?php echo esc_attr( $prefix ); ?>[min_points]" min="0" max="20" value="<?php echo (int) ( $row['min_points'] ?? 0 ); ?>"></td><td><label><input type="checkbox" name="<?php echo esc_attr( $prefix ); ?>[evidence_required]" value="1" <?php checked( ! empty( $row['evidence_required'] ) ); ?>>Required</label></td></tr>
	<?php } ?></tbody></table><?php return ob_get_clean();
}

function mls_rubrics_admin(): void {
	$course = absint( $_GET['course'] ?? 9838 ); if ( ! mls_manage( $course ) || get_post_type( $course ) !== 'stm-courses' ) { wp_die( 'Select an assigned course.', '', array( 'response' => 403 ) ); }
	$lessons = array_filter( array_map( 'intval', (array) get_post_meta( $course, '_ca_lesson_post_ids', true ) ) ); $lesson = absint( $_GET['lesson'] ?? ( reset( $lessons ) ?: 0 ) ); $context = mls_context( $lesson );
	if ( ! $context || $context['course_id'] !== $course ) { wp_die( 'Verified lesson mapping required.', '', array( 'response' => 400 ) ); }
	$post = mls_bank( $context ); $bank = $post ? json_decode( $post->post_content, true ) : array( 'questions' => array(), 'pass_percent' => 80, 'minutes' => 0 );
	echo '<div class="wrap"><h1>Assessment rubric editor</h1><h2>' . esc_html( get_the_title( $course ) ) . '</h2><form method="get"><input type="hidden" name="page" value="mls-rubrics"><input type="hidden" name="course" value="' . $course . '"><label>Lesson<select name="lesson">';
	foreach ( $lessons as $id ) { echo '<option value="' . $id . '" ' . selected( $lesson, $id, false ) . '>' . esc_html( get_the_title( $id ) ) . '</option>'; }
	echo '</select></label> <button class="button">Open lesson</button></form><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">'; wp_nonce_field( 'mls_rubric_bank' );
	echo '<input type="hidden" name="action" value="mls_rubric_bank"><input type="hidden" name="course" value="' . $course . '"><input type="hidden" name="lesson" value="' . $lesson . '"><input type="hidden" name="bank" value="' . ( $post ? $post->ID : 0 ) . '"><input type="hidden" name="bank_hash" value="' . esc_attr( hash( 'sha256', $post ? $post->post_content : '' ) ) . '"><p><label>Pass percentage <input name="pass_percent" type="number" min="1" max="100" value="' . intval( $bank['pass_percent'] ) . '" required></label></p><p><label>Time limit in minutes (0 = untimed) <input name="minutes" type="number" min="0" max="180" value="' . intval( $bank['minutes'] ) . '" required></label></p>';
	foreach ( $bank['questions'] as $index => $question ) { echo '<h3>' . esc_html( $question['prompt'] ) . '</h3>'; if ( $question['type'] !== 'mcq' ) { echo mls_criteria_editor( 'criteria[' . $index . ']', $question['criteria'] ?? array() ); } }
	echo '<h2>Additional practical task</h2><p><label>Task<textarea name="task" class="large-text" rows="4" maxlength="2000"></textarea></label></p>' . mls_criteria_editor( 'task_criteria', array() ) . '<p><label><input type="checkbox" name="publish" value="1"> I reviewed this practice task and its scoring criteria.</label></p><button class="button button-primary">Save new assessment version</button></form></div>';
}

function mls_criteria_from_post( $rows ): array {
	if ( ! is_array( $rows ) || count( $rows ) > 10 ) { throw new InvalidArgumentException( 'Invalid criteria.' ); }
	$result = array();
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) || ! is_string( $row['label'] ?? null ) ) { throw new InvalidArgumentException( 'Invalid criterion.' ); }
		if ( ! trim( $row['label'] ) ) { continue; }
		foreach ( array( 'max_points', 'min_points' ) as $key ) { $value = filter_var( $row[ $key ] ?? null, FILTER_VALIDATE_INT ); if ( $value === false || $value === null ) { throw new InvalidArgumentException( 'Invalid criterion points.' ); } $row[ $key ] = $value; }
		$result[] = $row;
	}
	return mls_validate_criteria( $result );
}

function mls_save_rubric_bank( int $course, int $lesson, int $source, string $expected, array $changes, bool $publish ) {
	$context = mls_context( $lesson ); if ( ! mls_manage( $course ) || ! $context || $context['course_id'] !== $course ) { return mls_error( 'Assigned instructor required.', 403 ); }
	global $wpdb; $lock = 'mls-bank-' . $lesson;
	if ( (int) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s,2)', $lock ) ) !== 1 ) { return mls_error( 'Please retry shortly.', 409 ); }
	try {
		$post = mls_bank( $context );
		if ( ( $post ? $post->ID : 0 ) !== $source || ! hash_equals( hash( 'sha256', $post ? $post->post_content : '' ), $expected ) ) { return mls_error( 'The assessment changed. Reload before creating a new version.', 409 ); }
		$bank = $post ? json_decode( $post->post_content, true ) : array( 'questions' => array(), 'minutes' => 0, 'randomize' => false );
		$bank['pass_percent'] = $changes['pass_percent'] ?? null; if ( ! is_int( $bank['pass_percent'] ) || $bank['pass_percent'] < 1 || $bank['pass_percent'] > 100 ) { return mls_error( 'Pass percentage must be 1-100.' ); }
		$minutes = $changes['minutes'] ?? (int) $bank['minutes']; if ( ! is_int( $minutes ) || $minutes < 0 || $minutes > 180 ) { return mls_error( 'Time limit must be 0-180 minutes.' ); } $bank['minutes'] = $minutes;
		foreach ( $bank['questions'] as $index => &$question ) { if ( $question['type'] !== 'mcq' ) { $question['criteria'] = mls_validate_criteria( $changes['criteria'][ $index ] ?? array() ); } } unset( $question );
		$task = $changes['task'] ?? ''; if ( ! is_string( $task ) ) { return mls_error( 'Invalid practical task.' ); }
		if ( trim( $task ) ) { $criteria = mls_validate_criteria( $changes['task_criteria'] ?? array() ); if ( ! $criteria ) { return mls_error( 'A practical task needs at least one criterion.' ); } $bank['questions'][] = array( 'type' => 'practical', 'prompt' => $task, 'criteria' => $criteria ); }
		$bank = mls_validate_bank( $bank ); if ( ! mls_has_rubric( $bank ) ) { return mls_error( 'Add a practical task or written-question criteria.' ); }
		$id = wp_insert_post( array( 'post_type' => 'mls_bank', 'post_status' => $publish ? 'publish' : 'draft', 'post_author' => get_current_user_id(), 'post_title' => get_the_title( $lesson ), 'post_content' => wp_slash( wp_json_encode( $bank ) ) ), true ); if ( is_wp_error( $id ) ) { return $id; }
		foreach ( array( '_mls_course' => $course, '_mls_lesson' => $lesson, '_mls_release' => $context['release'], '_mls_origin' => 'instructor-rubric-v1', '_mls_parent_bank' => $source ) as $key => $value ) { update_post_meta( $id, $key, $value ); }
		if ( $publish ) { update_post_meta( $id, '_mls_reviewed_by', get_current_user_id() ); }
		mls_event( $course, $lesson, 'rubric_version_created', array( 'bank' => $id, 'parent' => $source, 'published' => $publish ) ); return $id;
	} catch ( InvalidArgumentException $error ) { return mls_error( $error->getMessage() ); }
	finally { $wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock ) ); }
}

function mls_rubric_bank_post(): void {
	check_admin_referer( 'mls_rubric_bank' ); $course = absint( $_POST['course'] ?? 0 );
	try {
		$input = wp_unslash( $_POST ); $criteria = $input['criteria'] ?? array(); if ( ! is_array( $criteria ) || count( $criteria ) > 30 ) { throw new InvalidArgumentException( 'Invalid question criteria.' ); }
		$clean = array(); foreach ( $criteria as $key => $rows ) { $clean[ $key ] = mls_criteria_from_post( $rows ); }
		$changes = array( 'criteria' => $clean, 'task' => $input['task'] ?? '', 'task_criteria' => mls_criteria_from_post( $input['task_criteria'] ?? array() ), 'pass_percent' => filter_var( $input['pass_percent'] ?? null, FILTER_VALIDATE_INT ), 'minutes' => filter_var( $input['minutes'] ?? null, FILTER_VALIDATE_INT ) );
		$result = mls_save_rubric_bank( $course, absint( $input['lesson'] ?? 0 ), absint( $input['bank'] ?? 0 ), sanitize_text_field( $input['bank_hash'] ?? '' ), $changes, ! empty( $input['publish'] ) );
		if ( is_wp_error( $result ) ) { wp_die( esc_html( $result->get_error_message() ), '', array( 'response' => $result->get_error_data()['status'] ) ); }
		wp_safe_redirect( admin_url( 'admin.php?page=mls-suite&course=' . $course ) ); exit;
	} catch ( Throwable $error ) { wp_die( esc_html( $error->getMessage() ), '', array( 'response' => 400 ) ); }
}
