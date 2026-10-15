<?php
define( 'ABSPATH', __DIR__ . '/' );
define( 'MCE_BUSINESS_RELEASES_DIRECTORY', sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mce-test-releases' );
function add_filter( ...$args ): void {}
function add_action( ...$args ): void {}
function absint( $value ): int { return abs( (int) $value ); }
function wp_strip_all_tags( $text, $remove_breaks = false ): string { return strip_tags( $text ); }
function sanitize_text_field( $text ): string { return trim( $text ); }
function get_post( $id = null ) { return null === $id ? ( $GLOBALS['mce_current_post'] ?? null ) : ( $GLOBALS['mce_posts'][ $id ] ?? null ); }
function get_post_meta( $id, $key, $single = true ) { return $GLOBALS['mce_meta'][ $id ][ $key ] ?? ''; }
function wp_parse_url( $url ) { return parse_url( $url ); }
function home_url() { return 'http://example.test/'; }
function current_user_can( $capability, $post_id = 0 ) { return false; }
function is_user_logged_in() { return $GLOBALS['mce_logged_in'] ?? false; }
function is_admin() { return false; }
function get_the_title( $post ) { return $post->post_title ?? ''; }
function esc_attr( $value ) { return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ); }
function parse_blocks( $content ) { return $GLOBALS['mce_blocks'] ?? array(); }
function plugins_url( $path, $file ) { return $path; }
function wp_enqueue_style( ...$args ) { $GLOBALS['mce_assets'][] = $args[0]; }
function wp_enqueue_script( ...$args ) { $GLOBALS['mce_assets'][] = $args[0]; }
class WP_Post { public $ID; public $post_type; public $post_status; public $post_content = ''; public $post_title = ''; public function __construct( $id, $type, $status ) { $this->ID = $id; $this->post_type = $type; $this->post_status = $status; } }
class STM_LMS_User { public static function has_course_access( $course_id, $item_id = '', $add = true ) { return $GLOBALS['mce_access'] ?? false; } }
require dirname( __DIR__ ) . '/masterstudy-course-enhancements.php';

function assert_same( $expected, $actual, string $message ): void {
	if ( $expected !== $actual ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
}

$vtt = "WEBVTT\n\n00:00:00.000 --> 00:00:12.450\nWelcome to the lesson.\n\n00:00:12.450 --> 00:01:05.000\nCheck the <b>approved</b> process &amp; record your result.\n";
$cues = mce_parse_vtt( $vtt, array( array( 'heading' => 'Welcome' ), array( 'heading' => 'Practice' ) ) );
assert_same( 2, count( $cues ), 'both VTT cues parse' );
assert_same( 12.45, $cues[1]['start'], 'cue timestamps become seconds' );
assert_same( 'Practice', $cues[1]['label'], 'scene metadata labels a cue' );
assert_same( 'Check the approved process & record your result.', $cues[1]['text'], 'cue markup is removed and entities decoded' );
assert_same( array(), mce_parse_vtt( "not a transcript" ), 'invalid headers are rejected' );
assert_same( array(), mce_parse_vtt( "WEBVTT\n\n00:00:03.000 --> 00:00:02.000\nBad timing\n" ), 'non-positive cue ranges are rejected' );
assert_same( false, mce_vtt_seconds( '00:60:00.000' ), 'invalid minute values are rejected' );
assert_same( '00:01:05.000', mce_vtt_stamp( 65.0 ), 'timestamps serialize with stable VTT format' );
$GLOBALS['mce_blocks'] = array(
	array( 'blockName' => 'core/heading', 'attrs' => array( 'level' => 3 ), 'innerHTML' => '<h3>Learning Outcome</h3>' ),
	array( 'blockName' => 'core/heading', 'attrs' => array( 'level' => 5 ), 'innerHTML' => '<h5>Scene title &amp; details</h5>' ),
	array( 'blockName' => 'core/heading', 'attrs' => array( 'level' => 5 ), 'innerHTML' => '<h5>Workplace practice</h5>' ),
);

$release_root = MCE_BUSINESS_RELEASES_DIRECTORY;
$release = 'BSBTEC202-a1b2c3d4e5f6';
$lesson_key = 'M01-L01';
$lesson_dir = $release_root . DIRECTORY_SEPARATOR . $release . DIRECTORY_SEPARATOR . $lesson_key;
mkdir( $lesson_dir, 0777, true );
file_put_contents( $lesson_dir . DIRECTORY_SEPARATOR . 'transcript.vtt', $vtt );
$GLOBALS['mce_posts'] = array(
	88 => new WP_Post( 88, 'stm-courses', 'publish' ),
	44 => new WP_Post( 44, 'stm-lessons', 'publish' ),
);
$GLOBALS['mce_posts'][44]->post_title = 'BSBTEC202 - Use digital technologies to communicate in a work environment';
$GLOBALS['mce_current_post'] = $GLOBALS['mce_posts'][44];
$GLOBALS['mce_posts'][44]->post_content = 'fixture';
$GLOBALS['mce_blocks'] = array(
	array( 'blockName' => 'core/heading', 'attrs' => array( 'level' => 3 ), 'innerHTML' => '<h3>Learning Outcome</h3>' ),
	array( 'blockName' => 'core/heading', 'attrs' => array( 'level' => 5 ), 'innerHTML' => '<h5>Scene title &amp; details</h5>' ),
	array( 'blockName' => 'core/heading', 'attrs' => array( 'level' => 5 ), 'innerHTML' => '<h5>Workplace practice</h5>' ),
);
assert_same( 'Scene title & details', mce_lesson_scene_headings( 44 )[0]['heading'] ?? '', 'chapter names come from authored Gutenberg headings' );
$GLOBALS['mce_meta'] = array(
	88 => array( '_ca_course_id' => 'BSBTEC202', '_ca_verified_release' => $release, '_ca_lesson_post_ids' => array( 44 ) ),
	44 => array(
		'_ca_course_post_id' => 88, '_ca_course_id' => 'BSBTEC202',
		'_ca_lesson_id' => $lesson_key, '_ca_verified_release' => $release,
		'video_type' => 'ext_link', 'lesson_ext_link_url' => 'http://example.test/wp-content/course-automation/videos/M01-L01.mp4',
	),
);
$context = mce_context_for_lesson( 44 );
assert_same( $release, $context['release'] ?? '', 'verified pilot course maps to its lesson release' );
assert_same( '<img src="slide.png" alt="Instructional slide covering Use digital technologies to communicate in a work environment">', mce_pilot_course_alt_text( '<img src="slide.png" alt="Lesson image">' ), 'pilot alt text becomes topic-specific' );
assert_same( '<img alt="Original authored description">', mce_pilot_course_alt_text( '<img alt="Original authored description">' ), 'existing authored alt text remains unchanged' );
assert_same( $lesson_dir . DIRECTORY_SEPARATOR . 'transcript.vtt', mce_transcript_path( $release, $lesson_key ), 'private transcript path resolves inside configured release root' );
assert_same( null, mce_transcript_path( '../../outside', $lesson_key ), 'release traversal cannot escape the private root' );
assert_same( false, mce_lesson_is_accessible( $context ), 'non-enrolled anonymous visitors cannot read non-preview transcript' );
$GLOBALS['mce_logged_in'] = true;
$GLOBALS['mce_access'] = true;
assert_same( true, mce_lesson_is_accessible( $context ), 'enrolled learner can read the transcript' );
$GLOBALS['mce_assets'] = array();
mce_enqueue_player_assets( 88, 44 );
assert_same( array( 'mce-course-tools', 'mce-course-tools', 'mce-study-tools' ), $GLOBALS['mce_assets'], 'native player hook loads all study and video assets before its header' );
$GLOBALS['mce_assets'] = array();
mce_enqueue_player_assets( 99, 44 );
assert_same( array(), $GLOBALS['mce_assets'], 'assets cannot be loaded through an unrelated player course' );
$GLOBALS['mce_meta'][88]['_ca_course_id'] = 'OTHER';
assert_same( null, mce_context_for_lesson( 44 ), 'course and lesson identity must agree' );
$GLOBALS['mce_meta'][88]['_ca_course_id'] = 'BSBTEC202';
$GLOBALS['mce_meta'][88]['_ca_lesson_post_ids'] = array( 99 );
assert_same( null, mce_context_for_lesson( 44 ), 'lesson must belong to the actual course curriculum' );
assert_same( array(), mce_parse_vtt( "invalid WEBVTT\n\n00:00:00.000 --> 00:00:01.000\nText" ), 'embedded header cannot disguise invalid VTT' );
unlink( $lesson_dir . DIRECTORY_SEPARATOR . 'transcript.vtt' );
rmdir( $lesson_dir );
rmdir( dirname( $lesson_dir ) );
rmdir( $release_root );
echo "VTT parser tests passed.\n";
