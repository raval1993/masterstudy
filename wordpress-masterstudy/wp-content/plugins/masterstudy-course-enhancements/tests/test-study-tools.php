<?php
define( 'ABSPATH', __DIR__ );
function add_filter( ...$args ) {}
function add_action( ...$args ) {}
function absint( $v ) { return abs( (int) $v ); }
function get_post( $id = null ) { return $GLOBALS['post']; }
function get_post_meta( $id, $key, $single = true ) { return $GLOBALS['meta'][ $id ][ $key ] ?? ''; }
function home_url() { return 'http://example.test'; }
function wp_parse_url( $url ) { return parse_url( $url ); }
function is_user_logged_in() { return $GLOBALS['logged_in']; }
function get_current_user_id() { return $GLOBALS['user_id']; }
function current_user_can( ...$args ) { return false; }
function sanitize_textarea_field( $v ) { return trim( strip_tags( $v ) ); }
function get_user_meta( $user, $key, $single = true ) { return $GLOBALS['records'][ $user ][ $key ] ?? ''; }
function update_user_meta( $user, $key, $value ) { $GLOBALS['records'][ $user ][ $key ] = $value; return true; }
class WP_Post { public $ID = 44; public $post_type = 'stm-lessons'; public $post_status = 'publish'; public $post_content = '<h5>Use an approved account</h5><p>Check the sign-in address.</p><h3>Self-check</h3><p>Excluded.</p>'; }
class STM_LMS_User { public static function has_course_access( ...$args ) { return $GLOBALS['enrolled']; } }
class WP_Error { public function __construct( public $code, public $message, public $data ) {} }
class WP_REST_Response { public $headers = array(); public function __construct( public $data, public $status ) {} public function header( $k, $v ) { $this->headers[ $k ] = $v; } }
class WP_REST_Request implements ArrayAccess {
	public function __construct( public $method = 'GET', public $input = array() ) {}
	public function get_method() { return $this->method; }
	public function get_json_params() { return $this->input; }
	public function get_body() { return json_encode( $this->input ); }
	public function offsetExists( mixed $offset ): bool { return $offset === 'lesson_id'; }
	public function offsetGet( mixed $offset ): mixed { return 44; }
	public function offsetSet( mixed $offset, mixed $value ): void {}
	public function offsetUnset( mixed $offset ): void {}
}
require dirname( __DIR__ ) . '/learner-tools.php';
function mce_context_for_lesson( $id ) { return array( 'lesson_id' => 44, 'course_id' => 88, 'release' => $GLOBALS['release'] ); }
function mce_lesson_is_accessible( $context ) { return $GLOBALS['enrolled']; }
function check( $condition, $message ) { if ( ! $condition ) { fwrite( STDERR, 'FAIL: ' . $message . "\n" ); exit( 1 ); } }
$GLOBALS['post'] = new WP_Post();
$GLOBALS['records'] = array();
$GLOBALS['user_id'] = 10;
$GLOBALS['logged_in'] = true;
$GLOBALS['enrolled'] = true;
$GLOBALS['release'] = 'BSBTEC202-a1b2c3d4e5f6';
$sections = mce_study_sections( $GLOBALS['post']->post_content );
check( count( $sections ) === 1 && $sections[0]['text'] === 'Check the sign-in address.', 'study cards use authored sections without swallowing self-checks' );
$input = array( 'notes' => '<script>malicious</script>My notes', 'log' => 'Training account exercise', 'checks' => array( 0, 0, 99, -1 ), 'acknowledged' => true, 'user_id' => 999 );
$response = mce_study_record( new WP_REST_Request( 'POST', $input ) );
check( $response->status === 200 && $response->data['checks'] === array( 0 ), 'only existing checklist items are saved' );
check( ! str_contains( $response->data['notes'], '<' ), 'saved text cannot contain executable markup' );
check( ! isset( $GLOBALS['records'][999] ) && isset( $GLOBALS['records'][10] ), 'client cannot select another learner account' );
check( $response->data['acknowledged_at'] && $response->headers['Cache-Control'] === 'private, no-store, max-age=0', 'acknowledgements have timestamps and private responses' );
$GLOBALS['user_id'] = 11;
check( mce_study_record( new WP_REST_Request() )->data['notes'] === '', 'one learner cannot read another learner notes' );
$GLOBALS['user_id'] = 10;
$GLOBALS['release'] = 'BSBTEC202-000000000000';
check( mce_study_record( new WP_REST_Request() )->data['notes'] === '', 'new releases do not overwrite or reuse earlier acknowledgements' );
$GLOBALS['enrolled'] = false;
check( mce_study_record( new WP_REST_Request() ) instanceof WP_Error, 'lost course access blocks study records' );
$GLOBALS['enrolled'] = true;
$GLOBALS['logged_in'] = false;
check( mce_study_record( new WP_REST_Request() ) instanceof WP_Error, 'guests cannot save private records' );
$GLOBALS['logged_in'] = true;
check( mce_study_record( new WP_REST_Request( 'POST', array( 'notes' => str_repeat( 'x', 16001 ) ) ) ) instanceof WP_Error, 'oversized records fail cleanly' );
check( mce_study_record( new WP_REST_Request( 'POST', array( 'notes' => array( 'invalid' ) ) ) ) instanceof WP_Error, 'malformed note types fail without PHP warnings' );
check( count( mce_pilot_questions() ) === 10, 'each reviewed pilot lesson has a scenario question' );
echo "Study privacy, versioning, sanitization and activity tests passed.\n";
