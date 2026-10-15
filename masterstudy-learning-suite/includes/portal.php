<?php
defined( 'ABSPATH' ) || exit;

function mls_lesson_tools( string $content ): string {
	if ( is_admin() ) { return $content; }
	$post = get_post(); $context = $post instanceof WP_Post ? mls_context( $post->ID ) : null;
	if ( ! $context || ! mls_access( $context, true ) ) { return $content; }
	if ( ! is_user_logged_in() ) { return $content . '<p><a href="' . esc_url( home_url( '/student-login/' ) ) . '">Sign in for assessments and tutoring</a></p>'; }
	if ( ! mls_access( $context ) ) { return $content . '<p><a href="' . esc_url( get_permalink( $context['course_id'] ) ) . '">Enrol to access learning activities</a></p>'; }
	$bank = mls_bank( $context );
	$sections = mce_study_sections( $post->post_content );
	$live = get_post_meta( $context['course_id'], '_mls_live', true );
	ob_start(); ?>
	<section class="mls-tools" data-base="<?php echo esc_url( rest_url( 'mls/v1/' ) ); ?>" data-lesson="<?php echo $post->ID; ?>" data-attempt="<?php echo absint( $_GET['mls_attempt'] ?? 0 ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'wp_rest' ) ); ?>">
		<h3>Learning activities</h3>
		<?php echo mls_form_render( $context ); ?>
		<?php if ( $bank ) : ?><details><summary><?php echo in_array( get_post_meta( $bank->ID, '_mls_origin', true ), array( 'authored_pilot_v1', 'instructor-rubric-v1' ), true ) ? 'Practice assessment' : 'Assessment'; ?></summary><div class="mls-assessment"><button type="button" class="mls-start">Start assessment</button><p class="mls-assessment-status" role="status"></p><div class="mls-assessment-body"></div></div></details><?php endif; ?>
		<details><summary>Course tutor</summary><form class="mls-tutor"><label for="mls-question-<?php echo $post->ID; ?>">Your lesson question</label><textarea id="mls-question-<?php echo $post->ID; ?>" name="question" rows="3" minlength="5" maxlength="1500" required></textarea><button type="submit">Ask tutor</button></form><div class="mls-tutor-answer" aria-live="polite"></div></details>
		<?php if ( $sections ) : ?><details><summary>Spaced review</summary>
		<?php foreach ( $sections as $index => $section ) : ?><div class="mls-review-card" data-card="<?php echo $index; ?>"><details><summary><?php echo esc_html( $section['heading'] ); ?></summary><p><?php echo nl2br( esc_html( $section['text'] ) ); ?></p></details><div class="mls-rating"><button type="button" data-rating="again">Again</button><button type="button" data-rating="good">Good</button><button type="button" data-rating="easy">Easy</button></div><p class="mls-card-status" role="status"></p></div><?php endforeach; ?>
		</details><?php endif; ?>
		<?php if ( is_array( $live ) && ! empty( $live['url'] ) ) : ?><details><summary>Live classroom</summary><p><?php echo esc_html( $live['starts_at'] ?? '' ); ?></p><a class="mls-meeting" href="<?php echo esc_url( $live['url'] ); ?>" target="_blank" rel="noopener noreferrer">Join classroom</a></details><?php endif; ?>
		<p><a href="<?php echo esc_url( home_url( '/learning-hub/' ) ); ?>">My assessment results and review schedule</a></p>
	</section>
	<?php return $content . ob_get_clean();
}

function mls_portal(): string {
	if ( ! is_user_logged_in() ) { return '<p><a href="' . esc_url( home_url( '/student-login/' ) ) . '">Sign in to view your learning</a></p>'; }
	$attempts = get_posts( array( 'post_type' => 'mls_attempt', 'post_status' => 'private', 'author' => get_current_user_id(), 'posts_per_page' => 50 ) );
	$rewards = mls_rewards( get_current_user_id() );
	ob_start(); ?><section class="mls-portal"><h2>Learning progress</h2><p><?php echo intval( $rewards['xp'] ); ?> XP &middot; <?php echo intval( $rewards['passed_lessons'] ); ?> <?php echo $rewards['passed_lessons'] === 1 ? 'assessment' : 'assessments'; ?> passed</p>
	<?php foreach ( $rewards['badges'] as $badge ) : ?><span class="mls-badge"><?php echo esc_html( $badge ); ?></span><?php endforeach; ?>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><?php wp_nonce_field( 'mls_records' ); ?><input type="hidden" name="action" value="mls_records"><button type="submit">Download my learning records</button></form><h2>My assessments</h2>
	<?php if ( ! $attempts ) : ?><p>No assessment attempts yet.</p><?php else : ?><div class="mls-table-wrap"><table><thead><tr><th>Assessment</th><th>Status</th><th>Score</th><th>Instructor feedback</th></tr></thead><tbody>
	<?php foreach ( $attempts as $attempt ) : $data = json_decode( $attempt->post_content, true ); $lesson = (int) get_post_meta( $attempt->ID, '_mls_lesson', true ); $context = mls_context( $lesson ); ?>
	<tr><td><?php if ( $context ) : ?><a href="<?php echo esc_url( add_query_arg( 'mls_attempt', $attempt->ID, STM_LMS_Course::item_url( $context['course_id'], $lesson ) ) ); ?>"><?php echo esc_html( $attempt->post_title ); ?></a><?php else : echo esc_html( $attempt->post_title ); endif; ?></td><td><?php echo esc_html( str_replace( '_', ' ', get_post_meta( $attempt->ID, '_mls_status', true ) ) ); ?></td><td><?php echo isset( $data['score'] ) ? intval( $data['score'] ) . '%' : 'Awaiting review'; ?></td><td><?php echo esc_html( $data['feedback'] ?? '' ); ?></td></tr>
	<?php endforeach; ?></tbody></table></div><?php endif; ?>
	<h2>Review schedule</h2><?php
	$meta = get_user_meta( get_current_user_id() ); $found = 0;
	foreach ( $meta as $key => $value ) {
		if ( ! preg_match( '/^_mls_cards_(\d+)_([a-f0-9]{12})$/', $key, $match ) ) { continue; }
		$lesson = (int) $match[1]; $context = mls_context( $lesson );
		if ( ! $context || ! mls_access( $context ) || substr( hash( 'sha256', $context['release'] ), 0, 12 ) !== $match[2] ) { continue; }
		$cards = maybe_unserialize( $value[0] );
		$due = array_filter( is_array( $cards ) ? $cards : array(), fn( $row ) => ( $row['due'] ?? PHP_INT_MAX ) <= time() );
		if ( $due ) { ++$found; echo '<p><a href="' . esc_url( STM_LMS_Course::item_url( $context['course_id'], $lesson ) ) . '">' . esc_html( get_the_title( $lesson ) ) . '</a>: ' . count( $due ) . ' cards due.</p>'; }
	}
	if ( ! $found ) { echo '<p>No review cards due now.</p>'; }
	?><h2>My completion records</h2><?php
	foreach ( get_posts( array( 'post_type' => 'mls_certificate', 'post_status' => 'private', 'author' => get_current_user_id(), 'posts_per_page' => 25 ) ) as $record ) { echo '<p>' . esc_html( $record->post_title ) . ( get_post_meta( $record->ID, '_mls_revoked', true ) ? ' (revoked)' : ' <a href="' . esc_url( mls_certificate_url( $record->ID ) ) . '">View completion record</a>' ) . '</p>'; }
	echo mls_cohort_portal();
	echo mls_email_preference();
	?></section><?php return ob_get_clean();
}
