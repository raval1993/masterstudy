<?php
defined( 'ABSPATH' ) || exit;

function mls_review_page( int $course ): void {
	foreach ( get_posts( array( 'post_type' => 'mls_draft', 'post_status' => 'private', 'posts_per_page' => 20, 'meta_key' => '_mls_course', 'meta_value' => $course ) ) as $draft ) {
		if ( get_post_meta( $draft->ID, '_mls_kind', true ) !== 'enrichment' && ! str_starts_with( $draft->post_title, 'enrichment:' ) ) { continue; }
		$lesson = (int) get_post_meta( $draft->ID, '_mls_lesson', true ); $content = get_post_field( 'post_content', $lesson );
		echo '<details><summary>' . esc_html( $draft->post_title ) . '</summary><div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:20px"><div><h3>Current lesson</h3><div style="max-height:400px;overflow:auto">' . wp_kses_post( $content ) . '</div></div><div>'; mls_operation_form( 'apply_draft', $course );
		echo '<input type="hidden" name="record" value="' . $draft->ID . '"><input type="hidden" name="hash" value="' . esc_attr( hash( 'sha256', $content ) ) . '"><label>Reviewed student material<textarea name="text" rows="12" class="large-text" required maxlength="15000">' . esc_textarea( $draft->post_content ) . '</textarea></label><p><label><input type="checkbox" required> I reviewed accuracy and relevance against the lesson source.</label></p><button class="button">Append reviewed material</button></form></div></div></details>';
	}
	foreach ( get_posts( array( 'post_type' => 'mls_revision', 'post_status' => 'private', 'posts_per_page' => 10, 'meta_key' => '_mls_course', 'meta_value' => $course ) ) as $revision ) {
		$data = json_decode( $revision->post_content, true ); $lesson = (int) get_post_meta( $revision->ID, '_mls_lesson', true );
		echo '<details><summary>' . esc_html( $revision->post_title ) . '</summary>' . wp_kses_post( wp_text_diff( $data['before'], $data['after'], array( 'title_left' => 'Before', 'title_right' => 'After' ) ) ); mls_operation_form( 'restore_revision', $course );
		echo '<input type="hidden" name="record" value="' . $revision->ID . '"><input type="hidden" name="hash" value="' . esc_attr( hash( 'sha256', get_post_field( 'post_content', $lesson ) ) ) . '"><p><label><input type="checkbox" required> Restore the saved lesson content, after reviewing the comparison.</label></p><button class="button">Restore saved version</button></form></details>';
	}
}

function mls_apply_review( int $course, string $mode, int $record, string $expected, string $text ) {
	$post = get_post( $record ); $lesson = (int) get_post_meta( $record, '_mls_lesson', true ); $context = mls_context( $lesson );
	if ( ! $post || ! mls_manage( $course ) || ! $context || $context['course_id'] !== $course || (int) get_post_meta( $record, '_mls_course', true ) !== $course || get_post_meta( $record, '_mls_release', true ) !== $context['release'] || $post->post_type !== ( $mode === 'apply_draft' ? 'mls_draft' : 'mls_revision' ) ) { return mls_error( 'Wrong course, permissions or release.', 403 ); }
	global $wpdb; $lock = 'mls-lesson-edit-' . $lesson;
	if ( (int) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s,2)', $lock ) ) !== 1 ) { return mls_error( 'Please retry shortly.', 409 ); }
	try {
		$before = get_post_field( 'post_content', $lesson );
		if ( ! hash_equals( hash( 'sha256', $before ), $expected ) ) { return mls_error( 'The lesson changed since you opened the editor. Reload and compare first.', 409 ); }
		if ( $mode === 'apply_draft' ) {
			if ( get_post_meta( $record, '_mls_applied', true ) || ( get_post_meta( $record, '_mls_kind', true ) !== 'enrichment' && ! str_starts_with( $post->post_title, 'enrichment:' ) ) ) { return mls_error( 'Only an unapplied teaching-enrichment draft can be added.' ); }
			$text = sanitize_textarea_field( $text );
			if ( strlen( $text ) < 100 || strlen( $text ) > 15000 || preg_match( '/\b(accredited|officially certified|guaranteed compliant)\b/i', $text ) ) { return mls_error( 'Provide 100-15000 characters of reviewed teaching material without unsupported accreditation claims.' ); }
			$after = $before . '<h3>Workplace practice</h3>' . wpautop( esc_html( $text ) );
		} else {
			$data = json_decode( $post->post_content, true );
			if ( ! isset( $data['before'] ) ) { return mls_error( 'Invalid saved revision.' ); } $after = $data['before'];
		}
		$snapshot = wp_insert_post( array( 'post_type' => 'mls_revision', 'post_status' => 'private', 'post_author' => get_current_user_id(), 'post_title' => $mode . ': ' . get_the_title( $lesson ) . ' ' . gmdate( 'c' ), 'post_content' => wp_slash( wp_json_encode( array( 'before' => $before, 'after' => $after, 'source_record' => $record, 'reviewer' => get_current_user_id(), 'recorded_at' => gmdate( 'c' ) ) ) ) ), true );
		if ( is_wp_error( $snapshot ) ) { return $snapshot; }
		foreach ( array( '_mls_course' => $course, '_mls_lesson' => $lesson, '_mls_release' => $context['release'] ) as $key => $value ) { update_post_meta( $snapshot, $key, $value ); }
		$result = wp_update_post( array( 'ID' => $lesson, 'post_content' => wp_slash( $after ) ), true );
		if ( is_wp_error( $result ) ) { return $result; }
		if ( $mode === 'apply_draft' ) { update_post_meta( $record, '_mls_applied', $snapshot ); }
		update_post_meta( $lesson, '_mls_manual_content_lock', gmdate( 'c' ) );
		mls_event( $course, $lesson, 'teaching_revision', array( 'revision' => $snapshot, 'before_sha256' => hash( 'sha256', $before ), 'after_sha256' => hash( 'sha256', $after ) ) );
		return $snapshot;
	} finally { $wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock ) ); }
}
