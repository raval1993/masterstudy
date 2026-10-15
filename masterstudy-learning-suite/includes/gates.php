<?php
defined( 'ABSPATH' ) || exit;

function mls_validate_rules( array $data, int $course ): array {
	$prerequisites = array_values( array_unique( array_map( 'absint', (array) ( $data['prerequisites'] ?? array() ) ) ) );
	foreach ( $prerequisites as $required ) {
		if ( $required === $course || get_post_type( $required ) !== 'stm-courses' ) { throw new InvalidArgumentException( 'Invalid prerequisite course.' ); }
		$stack = array( $required ); $seen = array();
		while ( $stack ) {
			$current = array_pop( $stack );
			if ( $current === $course ) { throw new InvalidArgumentException( 'Prerequisite cycle detected.' ); }
			if ( isset( $seen[ $current ] ) ) { continue; } $seen[ $current ] = true;
			$next = get_post_meta( $current, '_mls_rules', true );
			foreach ( is_array( $next ) ? ( $next['prerequisites'] ?? array() ) : array() as $value ) { $stack[] = (int) $value; }
		}
	}
	$members = array_map( 'intval', (array) get_post_meta( $course, '_ca_lesson_post_ids', true ) );
	$drip = array();
	foreach ( (array) ( $data['drip'] ?? array() ) as $lesson => $days ) {
		if ( ! in_array( (int) $lesson, $members, true ) || ! is_numeric( $days ) || $days < 0 || $days > 365 ) { throw new InvalidArgumentException( 'Drip rules need a course lesson and 0-365 days.' ); }
		$drip[ (int) $lesson ] = (int) $days;
	}
	return array( 'prerequisites' => $prerequisites, 'linear' => ! empty( $data['linear'] ), 'drip' => $drip, 'trial_days' => max( 0, min( 90, (int) ( $data['trial_days'] ?? 0 ) ) ) );
}

function mls_gate_reason( int $course, int $lesson = 0 ): string {
	if ( mls_manage( $course ) ) { return ''; }
	$rules = get_post_meta( $course, '_mls_rules', true );
	if ( ! is_array( $rules ) ) { return ''; }
	$user = get_current_user_id();
	foreach ( $rules['prerequisites'] ?? array() as $required ) {
		$row = STM_LMS_Course::get_user_course( $user, $required );
		if ( (int) ( $row['progress_percent'] ?? 0 ) < 100 ) { return 'Complete the prerequisite course: ' . get_the_title( $required ); }
	}
	$enrolment = $user ? STM_LMS_Course::get_user_course( $user, $course ) : array();
	if ( ! empty( $rules['trial_days'] ) && $enrolment && get_post_meta( $course, 'pricing_mode', true ) === 'free' ) {
		$since = (int) ( $enrolment['start_time'] ?? 0 );
		if ( $since && time() > $since + $rules['trial_days'] * DAY_IN_SECONDS ) { return 'Your trial has ended. Contact the course team to continue.'; }
	}
	if ( ! $lesson ) { return ''; }
	$delay = (int) ( $rules['drip'][ $lesson ] ?? 0 );
	if ( $delay ) {
		$since = (int) ( $enrolment['start_time'] ?? 0 );
		if ( ! $since || time() < $since + $delay * DAY_IN_SECONDS ) { return 'This lesson is scheduled for a later stage of your course.'; }
	}
	if ( ! empty( $rules['linear'] ) ) {
		$members = array_map( 'intval', (array) get_post_meta( $course, '_ca_lesson_post_ids', true ) );
		$index = array_search( $lesson, $members, true );
		if ( $index !== false && $index > 0 && ! STM_LMS_Lesson::is_lesson_completed( $user, $course, $members[ $index - 1 ] ) ) { return 'Complete the previous lesson to continue.'; }
	}
	return '';
}

add_filter( 'stm_lms_has_course_access', function ( $access, $course, $lesson ) {
	return $access && mls_gate_reason( (int) $course, (int) $lesson ) === '';
}, 50, 3 );
add_filter( 'masterstudy_lms_course_player_data', function ( $data ) {
	$reason = mls_gate_reason( (int) $data['post_id'], (int) $data['item_id'] );
	if ( $reason ) { $data['has_access'] = false; $data['has_preview'] = false; $data['has_trial_access'] = false; $data['lesson_lock_before_start'] = true; }
	return $data;
}, 50 );
add_filter( 'rest_pre_dispatch', function ( $result, $server, $request ) {
	if ( preg_match( '#^/mce/v1/(?:lesson|study)/(\d+)$#', $request->get_route(), $match ) ) {
		$context = mls_context( (int) $match[1] );
		if ( $context && mls_gate_reason( $context['course_id'], $context['lesson_id'] ) ) { return mls_error( 'Lesson is not available yet.', 403 ); }
	}
	return $result;
}, 10, 3 );
add_action( 'wp_ajax_stm_lms_complete_lesson', function () {
	$course = absint( $_GET['course'] ?? 0 ); $lesson = absint( $_GET['lesson'] ?? 0 );
	if ( $course && $lesson && mls_gate_reason( $course, $lesson ) ) { wp_send_json_error( array( 'message' => 'Lesson is locked.' ), 403 ); }
}, 0 );
