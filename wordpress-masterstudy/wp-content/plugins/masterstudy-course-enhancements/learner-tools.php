<?php
defined( 'ABSPATH' ) || exit;

add_filter( 'the_content', 'mce_append_study_tools', 35 );
add_action( 'rest_api_init', 'mce_register_study_routes' );
add_action( 'admin_menu', 'mce_admin_menu' );

function mce_study_sections( string $content ): array {
	if ( ! class_exists( 'DOMDocument' ) ) {
		return array();
	}
	$doc = new DOMDocument();
	$errors = libxml_use_internal_errors( true );
	$doc->loadHTML( '<?xml encoding="UTF-8"><body>' . $content . '</body>', LIBXML_NONET );
	libxml_clear_errors();
	libxml_use_internal_errors( $errors );
	$sections = array();
	$xpath = new DOMXPath( $doc );
	foreach ( $xpath->query( '//h5' ) as $heading ) {
		$text = array();
		for ( $next = $heading->nextSibling; $next; $next = $next->nextSibling ) {
			if ( $next instanceof DOMElement && preg_match( '/^h[1-6]$/', $next->tagName ) ) {
				break;
			}
			if ( $next instanceof DOMElement && in_array( $next->tagName, array( 'p', 'ul', 'ol' ), true ) ) {
				$line = trim( $next->textContent );
				if ( '' !== $line ) {
					$text[] = $line;
				}
			}
		}
		$label = trim( $heading->textContent );
		if ( $label && $text && count( $sections ) < 24 ) {
			$sections[] = array( 'heading' => $label, 'text' => implode( "\n\n", $text ) );
		}
	}
	return $sections;
}

function mce_pilot_questions(): array {
	// Each answer is grounded in the reviewed BSBTEC202 lesson, with explanatory distractors.
	return array(
		'M01-L01' => array( 'An unexpected multifactor approval arrives. What is your next action?', array( 'Decline it and report through the known support channel.', 'Approve it to avoid delaying a colleague.', 'Share your sign-in code with the sender.' ), 0, 'An unexpected authentication prompt may indicate attempted account access. Do not approve it or share credentials.' ),
		'M01-L02' => array( 'An agenda link allows anyone to open it. What remains to be checked?', array( 'Only the spelling of the subject line.', 'Sharing permissions and the authorised recipients.', 'Nothing, because the document is the correct version.' ), 1, 'The correct attachment is only part of the check. Access must match the intended audience and sensitivity.' ),
		'M01-L03' => array( 'A polished email using your manager\'s name asks for staff data unexpectedly. What should you do?', array( 'Send the data because the grammar is correct.', 'Ask everyone to open the attachment.', 'Verify the request through a known work contact route.' ), 2, 'Names, logos and polished writing can be imitated. Verify an unexpected request independently.' ),
		'M01-L04' => array( 'One of three recipients has a delivery failure. What should you do first?', array( 'Investigate the failed recipient and the stated cause.', 'Immediately resend to all three recipients.', 'Use a personal mailbox to bypass the rejection.' ), 0, 'Other recipients may already have the email. Resolve the actual cause before a controlled retry.' ),
		'M02-L01' => array( 'A personal inbox rule moves invoices into a folder. What does it establish?', array( 'Every invoice in that folder is genuine.', 'The messages meet the rule\'s conditions, but still need appropriate checks.', 'Central security filters can now be disabled.' ), 1, 'Organisation rules do not prove authenticity and do not replace centrally managed protections.' ),
		'M02-L02' => array( 'Your work mailbox is nearly full. Which action is appropriate?', array( 'Export everything to your personal email.', 'Delete all older messages without checking.', 'Use the approved storage and retention process.' ), 2, 'Mailbox capacity does not authorise destruction or personal storage of business records.' ),
		'M02-L03' => array( 'Does moving mail to an archive folder automatically meet retention requirements?', array( 'No; approved retention, context, access and disposal rules still apply.', 'Yes; the folder name proves compliance.', 'Yes; anything in the cloud is retained indefinitely.' ), 0, 'Filing, archiving, backup and disposal have different purposes. Confirm the approved recordkeeping procedure.' ),
		'M02-L04' => array( 'Can a project mailing list be reused for an unrelated promotion by default?', array( 'Yes, if every address is valid.', 'No; confirm purpose, authorisation and organisational requirements.', 'Yes, if recipients are hidden using Bcc.' ), 1, 'Valid addresses and hidden recipients do not authorise a different use of a work mailing list.' ),
		'M03-L01' => array( 'What arrangement helps a group co-author a workshop plan?', array( 'Everyone keeps an unrelated local copy.', 'Make the document publicly editable.', 'Agree one working document, access roles and a review process.' ), 2, 'A shared working version and deliberate permissions reduce conflicting copies and clarify responsibility.' ),
		'M03-L02' => array( 'Which reply best supports a time-sensitive team decision?', array( 'I will check the room availability and update the thread by 3 pm today.', 'I will look at it.', 'Someone should check that eventually.' ), 0, 'The first reply states an action, a responsible person and a realistic response time.' ),
	);
}

function mce_append_study_tools( string $content ): string {
	if ( is_admin() ) {
		return $content;
	}
	$post = get_post();
	$context = $post instanceof WP_Post ? mce_context_for_lesson( $post->ID ) : null;
	if ( ! $context || ! mce_lesson_is_accessible( $context ) ) {
		return $content;
	}
	$sections = mce_study_sections( $post->post_content );
	$uid = 'mce-' . $post->ID;
	$endpoint = rest_url( 'mce/v1/study/' . $post->ID );
	ob_start();
	?>
	<section class="mce-tools mce-study" aria-label="Study and practice" data-endpoint="<?php echo esc_url( $endpoint ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'wp_rest' ) ); ?>" data-member="<?php echo is_user_logged_in() ? '1' : '0'; ?>">
		<h3>Study and practice</h3>
		<?php if ( $sections ) : ?>
		<details class="mce-review"><summary>Review cards</summary>
			<?php foreach ( $sections as $section ) : ?>
				<details class="mce-card"><summary><?php echo esc_html( $section['heading'] ); ?></summary><p><?php echo nl2br( esc_html( $section['text'] ) ); ?></p></details>
			<?php endforeach; ?>
		</details>
		<?php endif; ?>
		<?php $question = MCE_PILOT_COURSE_ID === $context['source_id'] ? ( mce_pilot_questions()[ $context['lesson_key'] ] ?? null ) : null; ?>
		<?php if ( $question ) : ?>
		<form class="mce-quiz" data-correct="<?php echo intval( $question[2] ); ?>" data-feedback="<?php echo esc_attr( $question[3] ); ?>">
			<fieldset><legend>Knowledge check</legend><p><?php echo esc_html( $question[0] ); ?></p>
			<?php foreach ( $question[1] as $index => $option ) : ?>
				<label><input type="radio" name="<?php echo esc_attr( $uid ); ?>-answer" value="<?php echo intval( $index ); ?>" required> <span><?php echo esc_html( $option ); ?></span></label>
			<?php endforeach; ?>
			</fieldset><button type="submit">Check answer</button><p class="mce-feedback" role="status" aria-live="polite"></p>
		</form>
		<?php endif; ?>
		<details class="mce-workbook"><summary>Practice checklist and learning log</summary>
		<form class="mce-study-form">
			<?php if ( $sections ) : ?><fieldset><legend>Practice checklist</legend>
				<?php foreach ( $sections as $index => $section ) : ?>
					<label><input type="checkbox" name="checks" value="<?php echo intval( $index ); ?>"> <span><?php echo esc_html( 'I can explain: ' . $section['heading'] ); ?></span></label>
				<?php endforeach; ?>
			</fieldset><?php endif; ?>
			<label for="<?php echo esc_attr( $uid ); ?>-notes">My study notes</label>
			<textarea id="<?php echo esc_attr( $uid ); ?>-notes" name="notes" rows="4" maxlength="8000"></textarea>
			<label for="<?php echo esc_attr( $uid ); ?>-log">Learning log: practice completed, result and next step</label>
			<textarea id="<?php echo esc_attr( $uid ); ?>-log" name="log" rows="4" maxlength="8000"></textarea>
			<label class="mce-ack"><input type="checkbox" name="acknowledged"> <span>I have reviewed this lesson and reflected on the practice activity.</span></label>
			<div class="mce-actions"><button type="submit" <?php disabled( ! is_user_logged_in() ); ?>>Save my study record</button><button type="button" class="mce-download">Download worksheet</button></div>
			<p class="mce-save-status" role="status" aria-live="polite"><?php echo is_user_logged_in() ? 'Your notes are private to your account.' : 'Sign in and enrol to save a study record.'; ?></p>
		</form></details>
	</section>
	<?php
	return $content . ob_get_clean();
}

function mce_register_study_routes(): void {
	register_rest_route( 'mce/v1', '/study/(?P<lesson_id>\d+)', array(
		'methods' => array( 'GET', 'POST' ),
		'permission_callback' => static function ( WP_REST_Request $request ): bool {
			$context = mce_context_for_lesson( absint( $request['lesson_id'] ) );
			return is_user_logged_in() && $context && mce_lesson_is_accessible( $context );
		},
		'callback' => 'mce_study_record',
	) );
}

function mce_clean_study_record( array $input, int $section_count ): array {
	$checks = array();
	foreach ( is_array( $input['checks'] ?? null ) ? $input['checks'] : array() as $check ) {
		if ( is_numeric( $check ) && (int) $check >= 0 && (int) $check < $section_count ) {
			$checks[] = (int) $check;
		}
	}
	return array(
		'notes' => sanitize_textarea_field( is_string( $input['notes'] ?? null ) ? $input['notes'] : '' ),
		'log' => sanitize_textarea_field( is_string( $input['log'] ?? null ) ? $input['log'] : '' ),
		'checks' => array_values( array_unique( $checks ) ),
		'acknowledged' => true === ( $input['acknowledged'] ?? false ),
	);
}

function mce_study_record( WP_REST_Request $request ) {
	$context = mce_context_for_lesson( absint( $request['lesson_id'] ) );
	if ( ! is_user_logged_in() || ! $context || ! mce_lesson_is_accessible( $context ) ) {
		return new WP_Error( 'mce_access', 'Study record unavailable.', array( 'status' => 403 ) );
	}
	$key = '_mce_study_' . $context['lesson_id'] . '_' . substr( hash( 'sha256', $context['release'] ), 0, 12 );
	$user = get_current_user_id();
	if ( 'POST' === $request->get_method() ) {
		$input = $request->get_json_params();
		if ( ! is_array( $input ) || ! is_string( $input['notes'] ?? '' ) || ! is_string( $input['log'] ?? '' ) || strlen( $request->get_body() ) > 40000 || strlen( $input['notes'] ?? '' ) > 16000 || strlen( $input['log'] ?? '' ) > 16000 ) {
			return new WP_Error( 'mce_input', 'Study record is too large or invalid.', array( 'status' => 400 ) );
		}
		$post = get_post( $context['lesson_id'] );
		$value = mce_clean_study_record( $input, count( mce_study_sections( $post->post_content ) ) );
		$previous = get_user_meta( $user, $key, true );
		$value['saved_at'] = gmdate( 'c' );
		$value['acknowledged_at'] = $value['acknowledged'] ? ( is_array( $previous ) && ! empty( $previous['acknowledged_at'] ) ? $previous['acknowledged_at'] : $value['saved_at'] ) : null;
		$value['release'] = $context['release'];
		if ( false === update_user_meta( $user, $key, $value ) && get_user_meta( $user, $key, true ) !== $value ) {
			return new WP_Error( 'mce_save', 'Could not save. Please try again.', array( 'status' => 500 ) );
		}
	}
	$record = get_user_meta( $user, $key, true );
	$response = new WP_REST_Response( is_array( $record ) ? $record : array( 'notes' => '', 'log' => '', 'checks' => array(), 'acknowledged' => false ), 200 );
	$response->header( 'Cache-Control', 'private, no-store, max-age=0' );
	$response->header( 'X-Content-Type-Options', 'nosniff' );
	return $response;
}

function mce_admin_menu(): void {
	add_management_page( 'Course enhancements', 'Course enhancements', 'manage_options', 'mce-course-enhancements', 'mce_admin_page' );
}

function mce_admin_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	global $wpdb;
	$courses = $wpdb->get_results( "SELECT p.ID, p.post_title, m.meta_value AS release_id FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} m ON m.post_id=p.ID AND m.meta_key='_ca_verified_release' WHERE p.post_type='stm-courses' AND p.post_status='publish' ORDER BY p.ID" );
	$records = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->usermeta} WHERE LEFT(meta_key,11)='_mce_study_'" );
	echo '<div class="wrap"><h1>Course enhancements</h1><p>' . intval( count( $courses ) ) . ' published release courses. ' . $records . ' saved learner study records.</p><p>Study records are private. Practice checks and acknowledgements do not award competency, certificates or MasterStudy completion.</p><table class="widefat striped"><thead><tr><th>Course</th><th>Release</th></tr></thead><tbody>';
	foreach ( $courses as $course ) {
		echo '<tr><td><a href="' . esc_url( get_edit_post_link( $course->ID ) ) . '">' . esc_html( $course->post_title ) . '</a></td><td>' . esc_html( $course->release_id ) . '</td></tr>';
	}
	echo '</tbody></table></div>';
}
