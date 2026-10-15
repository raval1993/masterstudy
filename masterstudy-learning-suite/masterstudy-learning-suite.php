<?php
/**
 * Plugin Name: MasterStudy Learning Suite
 * Description: Question banks, assessment submissions, course rules, learner records and local AI tutoring.
 * Version: 0.2.3
 */
defined( 'ABSPATH' ) || exit;
const MLS_VERSION = '0.2.3';
foreach ( array( 'core', 'assessments', 'admin', 'gates', 'tutor', 'workflow', 'authoring', 'portal', 'evidence', 'cohorts', 'operations', 'reviews', 'certificates', 'seo', 'mail', 'media' ) as $module ) {
	require_once __DIR__ . '/includes/' . $module . '.php';
}
register_activation_hook( __FILE__, 'mls_activate' );
add_action( 'init', 'mls_post_types' );
add_filter( 'map_meta_cap', function ( $caps, $cap, $user, $args ) {
	if ( ! in_array( $cap, array( 'edit_post', 'delete_post', 'read_post' ), true ) || empty( $args[0] ) ) { return $caps; }
	$post = get_post( (int) $args[0] );
	if ( ! $post || ! str_starts_with( $post->post_type, 'mls_' ) ) { return $caps; }
	// Raw WordPress editors must not bypass our course-scoped private workflows.
	return user_can( $user, 'manage_options' ) ? array( 'manage_options' ) : array( 'do_not_allow' );
}, 20, 4 );
add_action( 'rest_api_init', 'mls_routes' );
add_action( 'admin_menu', 'mls_admin_menu' );
add_action( 'admin_post_mls_save', 'mls_admin_save' );
add_action( 'admin_post_mls_generate', 'mls_schedule_generation' );
add_action( 'mls_generate_job', 'mls_generate_job' );
add_action( 'admin_post_mls_records', 'mls_download_records' );
add_shortcode( 'mls_learning_hub', 'mls_portal' );
add_filter( 'the_content', 'mls_lesson_tools', 40 );
add_action( 'stm_lms_before_item_template_start', 'mls_assets', 25, 2 );
add_action( 'wp_enqueue_scripts', function () {
	if ( is_page( 'learning-hub' ) ) {
		mls_enqueue();
		if ( function_exists( 'mse_icon' ) ) {
			wp_enqueue_style( 'mls-brand', plugins_url( 'student-experience/assets/site.css' ), array( 'dashicons' ), MLS_VERSION );
			wp_enqueue_script( 'mls-brand', plugins_url( 'student-experience/assets/site.js' ), array(), MLS_VERSION, true );
		}
	}
} );
add_filter( 'template_include', function ( $template ) { return is_page( 'learning-hub' ) && function_exists( 'mse_icon' ) ? __DIR__ . '/includes/portal-template.php' : $template; }, 100 );

function mls_activate(): void {
	mls_post_types();
	$page = get_page_by_path( 'learning-hub' );
	if ( ! $page ) {
		wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_name' => 'learning-hub', 'post_title' => 'My Learning', 'post_content' => '[mls_learning_hub]' ) );
	}
}

function mls_post_types(): void {
	foreach ( array( 'mls_bank' => 'Question banks', 'mls_attempt' => 'Assessment attempts', 'mls_event' => 'Learning events', 'mls_group' => 'Learning groups', 'mls_job' => 'AI authoring jobs', 'mls_draft' => 'Teaching drafts', 'mls_evidence' => 'Private evidence', 'mls_revision' => 'Teaching revisions', 'mls_certificate' => 'Completion certificates', 'mls_mail' => 'Learning mail outbox' ) as $type => $label ) {
		register_post_type( $type, array( 'label' => $label, 'public' => false, 'show_ui' => false, 'show_in_rest' => false, 'supports' => array( 'title', 'editor', 'author' ), 'map_meta_cap' => true ) );
	}
}

function mls_context( int $lesson ): ?array {
	return function_exists( 'mce_context_for_lesson' ) ? mce_context_for_lesson( $lesson ) : null;
}

function mls_manage( int $course ): bool {
	if ( current_user_can( 'manage_options' ) ) { return true; }
	$user = wp_get_current_user();
	$team = array_map( 'intval', (array) get_post_meta( $course, '_mls_instructors', true ) );
	$instructor = in_array( 'stm_lms_instructor', $user->roles, true );
	return $instructor && ( (int) get_post_field( 'post_author', $course ) === $user->ID || in_array( $user->ID, $team, true ) );
}

function mls_access( array $context, bool $preview = false ): bool {
	if ( mls_manage( $context['course_id'] ) ) { return true; }
	if ( ! is_user_logged_in() && ! $preview ) { return false; }
	if ( mls_gate_reason( $context['course_id'], $context['lesson_id'] ) ) { return false; }
	if ( $preview && 'on' === get_post_meta( $context['lesson_id'], 'preview', true ) ) { return true; }
	return is_user_logged_in() && STM_LMS_User::has_course_access( $context['course_id'], $context['lesson_id'], false );
}

function mls_private_response( $data, int $status = 200 ): WP_REST_Response {
	$response = new WP_REST_Response( $data, $status );
	$response->header( 'Cache-Control', 'private, no-store, max-age=0' );
	$response->header( 'X-Content-Type-Options', 'nosniff' );
	return $response;
}

function mls_error( string $message, int $status = 400 ): WP_Error {
	return new WP_Error( 'mls_request', $message, array( 'status' => $status ) );
}

function mls_event( int $course, int $lesson, string $type, array $details = array() ): void {
	$id = wp_insert_post( array( 'post_type' => 'mls_event', 'post_status' => 'private', 'post_author' => get_current_user_id(), 'post_title' => $type, 'post_content' => wp_slash( wp_json_encode( $details ) ) ), true );
	if ( ! is_wp_error( $id ) ) {
		update_post_meta( $id, '_mls_course', $course );
		update_post_meta( $id, '_mls_lesson', $lesson );
		do_action( 'mls_event_recorded', $id, $course, $lesson, $type, $details );
	}
}

function mls_routes(): void {
	foreach ( array( 'assessment' => array( 'GET', 'POST' ), 'attempt' => array( 'GET', 'POST' ), 'tutor' => array( 'POST' ), 'cards' => array( 'POST' ), 'form' => array( 'POST' ) ) as $route => $methods ) {
		register_rest_route( 'mls/v1', '/' . $route . '/(?P<lesson>\d+)', array(
			'methods' => $methods,
			'permission_callback' => function ( $request ) {
				$context = mls_context( absint( $request['lesson'] ) );
				return $context && mls_access( $context );
			},
			'callback' => 'mls_' . $route . '_request',
		) );
	}
}

function mls_enqueue(): void {
	wp_enqueue_style( 'mls-learning', plugins_url( 'assets/suite.css', __FILE__ ), is_page( 'learning-hub' ) && function_exists( 'mse_icon' ) ? array( 'mls-brand' ) : array(), MLS_VERSION );
	wp_enqueue_script( 'mls-learning', plugins_url( 'assets/suite.js', __FILE__ ), array(), MLS_VERSION, true );
}

function mls_assets( $course, $lesson ): void {
	$context = mls_context( (int) $lesson );
	if ( $context && $context['course_id'] === (int) $course && mls_access( $context, true ) ) { mls_enqueue(); }
}
