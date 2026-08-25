<?php
/**
 * Plugin Name: Course Automation Publisher
 * Description: Imports generated course packages into WordPress/MasterStudy draft courses, lessons, and curriculum.
 * Version: 0.4.1
 * Author: Course Automation
 * Text Domain: course-automation-publisher
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const CA_PUBLISHER_VERSION         = '0.4.1';
const CA_PUBLISHER_META_COURSE_ID  = '_ca_course_id';
const CA_PUBLISHER_META_LESSON_KEY = '_ca_lesson_key';

add_action( 'init', 'ca_publisher_register_preview_post_type' );
add_action( 'init', 'ca_publisher_maybe_ensure_catalog_page', 20 );
add_action( 'admin_menu', 'ca_publisher_admin_menu' );
add_action( 'admin_post_ca_publisher_import', 'ca_publisher_admin_post_import' );
add_action( 'rest_api_init', 'ca_publisher_register_rest_routes' );
add_shortcode( 'course_automation_catalog', 'ca_publisher_catalog_shortcode' );
register_activation_hook( __FILE__, 'ca_publisher_activate' );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command( 'course-automation import-blueprints', 'ca_publisher_cli_import_blueprints' );
}

function ca_publisher_register_preview_post_type(): void {
	register_post_type(
		'ca_course_preview',
		array(
			'labels'       => array(
				'name'          => __( 'Course Previews', 'course-automation-publisher' ),
				'singular_name' => __( 'Course Preview', 'course-automation-publisher' ),
			),
			'public'       => true,
			'show_ui'      => true,
			'show_in_menu' => true,
			'supports'     => array( 'title', 'editor', 'custom-fields' ),
			'menu_icon'    => 'dashicons-welcome-learn-more',
		)
	);
}

function ca_publisher_admin_menu(): void {
	add_menu_page(
		__( 'Course Automation', 'course-automation-publisher' ),
		__( 'Course Automation', 'course-automation-publisher' ),
		'manage_options',
		'course-automation',
		'ca_publisher_render_admin_page',
		'dashicons-update',
		26
	);
}

function ca_publisher_render_admin_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$notice = isset( $_GET['ca_imported'] ) ? intval( $_GET['ca_imported'] ) : null;
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Course Automation', 'course-automation-publisher' ); ?></h1>
		<?php if ( null !== $notice ) : ?>
			<div class="notice notice-success is-dismissible">
				<p><?php echo esc_html( sprintf( 'Imported or updated %d courses.', $notice ) ); ?></p>
			</div>
		<?php endif; ?>

		<table class="widefat striped" style="max-width: 900px;">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Blueprint directory', 'course-automation-publisher' ); ?></th>
					<td><code><?php echo esc_html( ca_publisher_blueprint_dir() ); ?></code></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Generated course directory', 'course-automation-publisher' ); ?></th>
					<td><code><?php echo esc_html( ca_publisher_course_package_dir() ); ?></code></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Target post type', 'course-automation-publisher' ); ?></th>
					<td><code><?php echo esc_html( ca_publisher_target_post_type() ); ?></code></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Import files found', 'course-automation-publisher' ); ?></th>
					<td><?php echo esc_html( count( ca_publisher_import_files() ) ); ?></td>
				</tr>
			</tbody>
		</table>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top: 20px;">
			<?php wp_nonce_field( 'ca_publisher_import' ); ?>
			<input type="hidden" name="action" value="ca_publisher_import">
			<?php submit_button( __( 'Import Course Blueprints', 'course-automation-publisher' ) ); ?>
		</form>
	</div>
	<?php
}

function ca_publisher_admin_post_import(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'course-automation-publisher' ) );
	}

	check_admin_referer( 'ca_publisher_import' );
	$result = ca_publisher_import_all_blueprints();
	wp_safe_redirect(
		add_query_arg(
			array( 'page' => 'course-automation', 'ca_imported' => $result['imported'] ),
			admin_url( 'admin.php' )
		)
	);
	exit;
}

function ca_publisher_register_rest_routes(): void {
	register_rest_route(
		'course-automation/v1',
		'/import',
		array(
			'methods'             => 'POST',
			'callback'            => 'ca_publisher_rest_import',
			'permission_callback' => function (): bool {
				return current_user_can( 'manage_options' );
			},
		)
	);

	register_rest_route(
		'course-automation/v1',
		'/status',
		array(
			'methods'             => 'GET',
			'callback'            => 'ca_publisher_rest_status',
			'permission_callback' => function (): bool {
				return current_user_can( 'manage_options' );
			},
		)
	);
}

function ca_publisher_rest_import(): WP_REST_Response {
	return new WP_REST_Response( ca_publisher_import_all_blueprints() );
}

function ca_publisher_rest_status(): WP_REST_Response {
	return new WP_REST_Response(
		array(
			'blueprint_dir'      => ca_publisher_blueprint_dir(),
			'course_package_dir' => ca_publisher_course_package_dir(),
			'files_found'        => count( ca_publisher_import_files() ),
			'target_type'        => ca_publisher_target_post_type(),
		)
	);
}

function ca_publisher_cli_import_blueprints(): void {
	$result = ca_publisher_import_all_blueprints();
	WP_CLI::success( sprintf( 'Imported or updated %d courses.', $result['imported'] ) );
}

function ca_publisher_blueprint_dir(): string {
	$from_env = getenv( 'COURSE_AUTOMATION_BLUEPRINT_DIR' );
	if ( is_string( $from_env ) && '' !== trim( $from_env ) ) {
		return rtrim( $from_env, "/\\" );
	}

	return WP_CONTENT_DIR . '/course-automation/blueprints';
}

function ca_publisher_blueprint_files(): array {
	$dir = ca_publisher_blueprint_dir();
	if ( ! is_dir( $dir ) ) {
		return array();
	}

	$files = glob( $dir . '/*.blueprint.json' );
	return is_array( $files ) ? $files : array();
}

function ca_publisher_course_package_dir(): string {
	$from_env = getenv( 'COURSE_AUTOMATION_COURSE_DIR' );
	if ( is_string( $from_env ) && '' !== trim( $from_env ) ) {
		return rtrim( $from_env, "/\\" );
	}

	return WP_CONTENT_DIR . '/course-automation/courses';
}

function ca_publisher_course_package_files(): array {
	$dir = ca_publisher_course_package_dir();
	if ( ! is_dir( $dir ) ) {
		return array();
	}

	$files = glob( $dir . '/*.course.json' );
	return is_array( $files ) ? $files : array();
}

function ca_publisher_import_files(): array {
	$course_packages = ca_publisher_course_package_files();
	if ( ! empty( $course_packages ) ) {
		return $course_packages;
	}

	return ca_publisher_blueprint_files();
}

function ca_publisher_target_post_type(): string {
	if ( post_type_exists( 'stm-courses' ) ) {
		return 'stm-courses';
	}

	if ( post_type_exists( 'stm_lms_courses' ) ) {
		return 'stm_lms_courses';
	}

	return 'ca_course_preview';
}

function ca_publisher_import_all_blueprints(): array {
	$files    = ca_publisher_import_files();
	$imported = 0;
	$errors   = array();

	foreach ( $files as $file ) {
		$result = ca_publisher_import_blueprint_file( $file );
		if ( is_wp_error( $result ) ) {
			$errors[] = array(
				'file'  => $file,
				'error' => $result->get_error_message(),
			);
			continue;
		}

		$imported++;
	}

	ca_publisher_ensure_catalog_page();

	return array(
		'imported' => $imported,
		'errors'   => $errors,
	);
}

function ca_publisher_import_blueprint_file( string $file ) {
	$json = file_get_contents( $file );
	if ( false === $json ) {
		return new WP_Error( 'ca_read_failed', 'Could not read blueprint file.' );
	}

	$data = json_decode( $json, true );
	if ( ! is_array( $data ) ) {
		return new WP_Error( 'ca_invalid_json', 'Blueprint JSON is invalid.' );
	}

	$course_id = ca_publisher_string_value( $data, 'course_id' );
	$title     = ca_publisher_string_value( $data, 'title' );

	if ( '' === $course_id || '' === $title ) {
		return new WP_Error( 'ca_missing_required_fields', 'Blueprint requires course_id and title.' );
	}

	$post_type = ca_publisher_target_post_type();
	$post_id   = ca_publisher_find_existing_course( $course_id, $post_type );
	$content   = ca_publisher_render_course_content( $data );
	$status    = $post_id > 0 && 'publish' === get_post_status( $post_id ) ? 'publish' : 'draft';

	$postarr = array(
		'post_type'    => $post_type,
		'post_status'  => $status,
		'post_title'   => sprintf( '%s - %s', $course_id, $title ),
		'post_content' => $content,
		'post_author'  => ca_publisher_default_author_id(),
	);

	if ( $post_id > 0 ) {
		$postarr['ID'] = $post_id;
		$result        = wp_update_post( $postarr, true );
	} else {
		$result = wp_insert_post( $postarr, true );
	}

	if ( is_wp_error( $result ) ) {
		return $result;
	}

	$post_id = intval( $result );
	update_post_meta( $post_id, CA_PUBLISHER_META_COURSE_ID, $course_id );
	update_post_meta( $post_id, '_ca_category', ca_publisher_string_value( $data, 'category' ) );
	update_post_meta( $post_id, '_ca_source_file', ca_publisher_string_value( $data, 'source_file' ) );
	update_post_meta( $post_id, '_ca_source_word_count', intval( $data['source_word_count'] ?? 0 ) );
	update_post_meta( $post_id, '_ca_lesson_count', ca_publisher_lesson_count( $data ) );
	update_post_meta( $post_id, '_ca_topic_count', ca_publisher_topic_count( $data ) );
	update_post_meta( $post_id, '_ca_video_status', ca_publisher_course_video_status( $data ) );
	update_post_meta( $post_id, '_ca_schema_version', ca_publisher_string_value( $data, 'schema_version' ) );
	update_post_meta( $post_id, '_ca_source_image_count', intval( $data['source_image_count'] ?? 0 ) );
	update_post_meta( $post_id, '_ca_blueprint_imported_at', gmdate( 'c' ) );
	ca_publisher_update_course_category_terms( $post_id, $data );
	ca_publisher_update_course_video_meta( $post_id, $data );
	ca_publisher_update_course_featured_image( $post_id, $data );

	$curriculum = ca_publisher_sync_masterstudy_curriculum( $post_id, $data );
	update_post_meta( $post_id, '_ca_curriculum_sections', intval( $curriculum['sections'] ) );
	update_post_meta( $post_id, '_ca_curriculum_lessons', intval( $curriculum['lessons'] ) );
	update_post_meta( $post_id, '_ca_curriculum_status', $curriculum['status'] );

	return $post_id;
}

function ca_publisher_find_existing_course( string $course_id, string $post_type ): int {
	$posts = get_posts(
		array(
			'post_type'      => $post_type,
			'post_status'    => array( 'draft', 'publish', 'private', 'pending' ),
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => CA_PUBLISHER_META_COURSE_ID,
			'meta_value'     => $course_id,
		)
	);

	return empty( $posts ) ? 0 : intval( $posts[0] );
}

function ca_publisher_update_course_category_terms( int $post_id, array $data ): void {
	if ( ! taxonomy_exists( 'stm_lms_course_taxonomy' ) ) {
		return;
	}

	$category = ca_publisher_string_value( $data, 'category' );
	if ( '' === $category ) {
		return;
	}

	$term = term_exists( $category, 'stm_lms_course_taxonomy' );
	if ( ! $term ) {
		$term = wp_insert_term( $category, 'stm_lms_course_taxonomy' );
	}

	if ( is_wp_error( $term ) ) {
		return;
	}

	$term_id = is_array( $term ) ? intval( $term['term_id'] ?? 0 ) : intval( $term );
	if ( $term_id > 0 ) {
		wp_set_object_terms( $post_id, array( $term_id ), 'stm_lms_course_taxonomy', false );
		update_post_meta( $post_id, '_ca_category_term_id', $term_id );
	}
}

function ca_publisher_update_course_video_meta( int $post_id, array $data ): void {
	$video = is_array( $data['course_video'] ?? null ) ? $data['course_video'] : array();
	$url   = ca_publisher_video_url( ca_publisher_string_value( $video, 'relative_path' ) );

	if ( '' === $url ) {
		return;
	}

	update_post_meta( $post_id, 'video_type', 'external_url' );
	update_post_meta( $post_id, 'external_url', $url );
	update_post_meta( $post_id, 'video_duration', ca_publisher_video_duration_label( $video ) );
	update_post_meta( $post_id, '_ca_course_video_url', $url );
	update_post_meta( $post_id, '_ca_course_video_status', ca_publisher_string_value( $video, 'status', 'rendered' ) );
}

function ca_publisher_update_course_featured_image( int $post_id, array $data ): void {
	$asset = ca_publisher_pick_course_featured_asset( $data );
	if ( empty( $asset ) ) {
		update_post_meta( $post_id, '_ca_featured_image_status', 'no_source_image' );
		return;
	}

	$relative_path = ca_publisher_string_value( $asset, 'relative_path' );
	if ( '' === $relative_path ) {
		update_post_meta( $post_id, '_ca_featured_image_status', 'missing_relative_path' );
		return;
	}

	$current_thumbnail_id = get_post_thumbnail_id( $post_id );
	if ( $current_thumbnail_id > 0 ) {
		$current_relative_path = get_post_meta( $current_thumbnail_id, '_ca_media_relative_path', true );
		if ( '' !== $current_relative_path && $current_relative_path !== $relative_path ) {
			delete_post_thumbnail( $post_id );
		} elseif ( '' === $current_relative_path ) {
			update_post_meta( $post_id, '_ca_featured_image_status', 'kept_existing_manual_thumbnail' );
			return;
		}
	}

	$attachment_id = ca_publisher_ensure_media_attachment( $asset, $post_id );
	if ( $attachment_id <= 0 ) {
		update_post_meta( $post_id, '_ca_featured_image_status', 'attachment_failed' );
		return;
	}

	set_post_thumbnail( $post_id, $attachment_id );
	update_post_meta( $post_id, '_ca_featured_image_status', 'set' );
	update_post_meta( $post_id, '_ca_featured_image_relative_path', $relative_path );
}

function ca_publisher_pick_course_featured_asset( array $data ): array {
	$candidates = array();

	foreach ( array( 'featured_image', 'course_image' ) as $key ) {
		if ( is_array( $data[ $key ] ?? null ) ) {
			$candidates[] = $data[ $key ];
		}
	}

	$modules = is_array( $data['modules'] ?? null ) ? $data['modules'] : array();
	foreach ( $modules as $module ) {
		if ( ! is_array( $module ) ) {
			continue;
		}

		$lessons = is_array( $module['lessons'] ?? null ) ? $module['lessons'] : array();
		foreach ( $lessons as $lesson ) {
			if ( ! is_array( $lesson ) ) {
				continue;
			}

			$assets = is_array( $lesson['assets'] ?? null ) ? $lesson['assets'] : array();
			foreach ( $assets as $asset ) {
				if ( is_array( $asset ) && 'image' === ca_publisher_string_value( $asset, 'type' ) ) {
					$candidates[] = $asset;
				}
			}
		}
	}

	$best_asset = array();
	$best_score = 0;
	foreach ( $candidates as $asset ) {
		if ( ! is_array( $asset ) ) {
			continue;
		}

		$relative_path = ca_publisher_string_value( $asset, 'relative_path' );
		if ( '' === $relative_path ) {
			continue;
		}

		$width = max( 0, intval( $asset['width'] ?? 0 ) );
		$height = max( 0, intval( $asset['height'] ?? 0 ) );
		$score = $width * $height;
		if ( $width >= 120 && $height >= 90 ) {
			$score += 1000000;
		}

		if ( $score > $best_score ) {
			$best_score = $score;
			$best_asset = $asset;
		}
	}

	return $best_asset;
}

function ca_publisher_ensure_media_attachment( array $asset, int $parent_post_id ): int {
	$relative_path = ca_publisher_clean_relative_path( ca_publisher_string_value( $asset, 'relative_path' ) );
	if ( '' === $relative_path ) {
		return 0;
	}

	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_ca_media_relative_path',
			'meta_value'     => $relative_path,
		)
	);
	if ( ! empty( $existing ) ) {
		$attachment_id = intval( $existing[0] );
		ca_publisher_update_media_attachment_labels( $attachment_id );
		return $attachment_id;
	}

	$source_path = ca_publisher_media_file_path( $relative_path );
	if ( '' === $source_path || ! is_file( $source_path ) ) {
		return 0;
	}

	$filetype = wp_check_filetype( basename( $source_path ), null );
	if ( empty( $filetype['type'] ) || 0 !== strpos( $filetype['type'], 'image/' ) ) {
		return 0;
	}

	$upload_dir = wp_upload_dir();
	if ( ! empty( $upload_dir['error'] ) ) {
		return 0;
	}

	$featured_dir = trailingslashit( $upload_dir['basedir'] ) . 'course-automation-featured';
	if ( ! wp_mkdir_p( $featured_dir ) ) {
		return 0;
	}

	$filename = wp_unique_filename( $featured_dir, basename( $source_path ) );
	$target_path = trailingslashit( $featured_dir ) . $filename;
	if ( ! copy( $source_path, $target_path ) ) {
		return 0;
	}

	$attachment_id = wp_insert_attachment(
		array(
			'guid'           => trailingslashit( $upload_dir['baseurl'] ) . 'course-automation-featured/' . $filename,
			'post_mime_type' => $filetype['type'],
			'post_title'     => 'Course image',
			'post_content'   => '',
			'post_excerpt'   => '',
			'post_status'    => 'inherit',
		),
		$target_path,
		$parent_post_id,
		true
	);

	if ( is_wp_error( $attachment_id ) || intval( $attachment_id ) <= 0 ) {
		return 0;
	}

	if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
	}

	$attachment_id = intval( $attachment_id );
	$metadata = wp_generate_attachment_metadata( $attachment_id, $target_path );
	if ( is_array( $metadata ) ) {
		wp_update_attachment_metadata( $attachment_id, $metadata );
	}

	ca_publisher_update_media_attachment_labels( $attachment_id );
	update_post_meta( $attachment_id, '_ca_media_relative_path', $relative_path );
	update_post_meta( $attachment_id, '_ca_media_source', 'course-automation' );

	return $attachment_id;
}

function ca_publisher_update_media_attachment_labels( int $attachment_id ): void {
	if ( $attachment_id <= 0 ) {
		return;
	}

	wp_update_post(
		array(
			'ID'           => $attachment_id,
			'post_title'   => 'Course image',
			'post_excerpt' => '',
			'post_content' => '',
		)
	);
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', 'Course image' );
}

function ca_publisher_ensure_catalog_page(): int {
	$content = '<!-- wp:shortcode -->[course_automation_catalog]<!-- /wp:shortcode -->';
	$page    = get_page_by_path( 'courses', OBJECT, 'page' );

	if ( $page instanceof WP_Post ) {
		$updates = array( 'ID' => $page->ID );
		if ( 'Courses' !== $page->post_title ) {
			$updates['post_title'] = 'Courses';
		}
		if ( trim( $page->post_content ) !== $content ) {
			$updates['post_content'] = $content;
		}
		if ( count( $updates ) > 1 ) {
			wp_update_post( $updates );
		}
		return intval( $page->ID );
	}

	$page_id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => 'Courses',
			'post_name'    => 'courses',
			'post_content' => $content,
			'post_author'  => ca_publisher_default_author_id(),
		)
	);

	return is_wp_error( $page_id ) ? 0 : intval( $page_id );
}

function ca_publisher_activate(): void {
	$page_id = ca_publisher_ensure_catalog_page();
	if ( $page_id > 0 ) {
		update_option( 'ca_publisher_catalog_page_version', CA_PUBLISHER_VERSION, false );
	}
}

function ca_publisher_maybe_ensure_catalog_page(): void {
	if ( CA_PUBLISHER_VERSION === get_option( 'ca_publisher_catalog_page_version' ) ) {
		return;
	}

	$page_id = ca_publisher_ensure_catalog_page();
	if ( $page_id > 0 ) {
		update_option( 'ca_publisher_catalog_page_version', CA_PUBLISHER_VERSION, false );
	}
}

function ca_publisher_catalog_shortcode( $atts = array() ): string {
	$courses = ca_publisher_catalog_courses();
	if ( empty( $courses ) ) {
		return '<div class="ca-course-automation-catalog"><p>No courses are published yet.</p></div>';
	}

	$groups = ca_publisher_catalog_group_courses( $courses );

	ob_start();
	?>
	<style>
		.stm_lms_courses_wrapper{display:none!important}
		.ca-course-automation-catalog{margin:18px 0 54px}
		.ca-course-automation-catalog *{box-sizing:border-box}
		.ca-catalog-tabs{display:flex;flex-wrap:wrap;gap:10px;margin:0 0 28px;padding:0;list-style:none}
		.ca-catalog-tabs a{display:inline-flex;align-items:center;min-height:38px;padding:9px 15px;border:1px solid #dce3ec;background:#fff;color:#273044;font-weight:600;text-decoration:none}
		.ca-catalog-tabs a:hover{border-color:#385bce;color:#385bce}
		.ca-category-section{margin:0 0 46px}
		.ca-category-heading{display:flex;align-items:flex-end;justify-content:space-between;gap:18px;margin:0 0 20px;padding-bottom:12px;border-bottom:1px solid #e5eaf0}
		.ca-category-heading h2{margin:0;text-transform:uppercase;font-size:28px;line-height:1.2}
		.ca-category-count{color:#66828f;font-size:14px;white-space:nowrap}
		.ca-course-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:22px}
		.ca-course-card{display:flex;flex-direction:column;min-height:100%;border:1px solid #dfe5ec;background:#fff;color:#273044;text-decoration:none;transition:box-shadow .18s ease,transform .18s ease,border-color .18s ease}
		.ca-course-card:hover{transform:translateY(-3px);border-color:#385bce;box-shadow:0 12px 30px rgba(39,48,68,.16);color:#273044;text-decoration:none}
		.ca-course-card__image{position:relative;aspect-ratio:16/9;background:#eef2f6;overflow:hidden}
		.ca-course-card__image img{display:block;width:100%;height:100%;object-fit:cover}
		.ca-course-card__fallback{display:flex;align-items:center;justify-content:center;width:100%;height:100%;padding:18px;color:#66828f;font-weight:700;text-align:center;background:linear-gradient(135deg,#eef4fb,#f8fafc)}
		.ca-course-card__body{display:flex;flex-direction:column;gap:10px;min-height:178px;padding:18px}
		.ca-course-card__eyebrow{color:#385bce;font-size:12px;font-weight:700;letter-spacing:0;text-transform:uppercase}
		.ca-course-card__title{margin:0;font-size:17px;line-height:1.35;font-weight:700;color:#273044}
		.ca-course-card__meta{display:flex;flex-wrap:wrap;gap:8px;color:#66828f;font-size:13px}
		.ca-course-card__button{margin-top:auto;display:inline-flex;align-items:center;justify-content:center;min-height:38px;padding:9px 13px;background:#17d292;color:#fff;font-size:13px;font-weight:700;text-transform:uppercase}
		.ca-course-card:hover .ca-course-card__button{background:#385bce}
		@media (max-width:1024px){.ca-course-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
		@media (max-width:768px){.ca-course-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.ca-category-heading{align-items:flex-start;flex-direction:column}.ca-category-heading h2{font-size:24px}}
		@media (max-width:520px){.ca-course-grid{grid-template-columns:1fr}}
	</style>
	<div class="ca-course-automation-catalog">
		<ul class="ca-catalog-tabs">
			<?php foreach ( $groups as $group ) : ?>
				<li>
					<a href="#<?php echo esc_attr( $group['anchor'] ); ?>">
						<?php echo esc_html( $group['label'] ); ?> (<?php echo esc_html( count( $group['courses'] ) ); ?>)
					</a>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php foreach ( $groups as $group ) : ?>
			<section class="ca-category-section" id="<?php echo esc_attr( $group['anchor'] ); ?>">
				<div class="ca-category-heading">
					<h2><?php echo esc_html( $group['label'] ); ?></h2>
					<span class="ca-category-count"><?php echo esc_html( count( $group['courses'] ) ); ?> courses</span>
				</div>
				<div class="ca-course-grid">
					<?php foreach ( $group['courses'] as $course ) : ?>
						<?php echo ca_publisher_render_catalog_card( $course ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endforeach; ?>
	</div>
	<?php
	return strval( ob_get_clean() );
}

function ca_publisher_catalog_courses(): array {
	$post_type = ca_publisher_target_post_type();
	if ( ! post_type_exists( $post_type ) ) {
		return array();
	}

	return get_posts(
		array(
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_key'       => CA_PUBLISHER_META_COURSE_ID,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);
}

function ca_publisher_catalog_group_courses( array $courses ): array {
	$groups = array();
	foreach ( $courses as $course ) {
		if ( ! $course instanceof WP_Post ) {
			continue;
		}

		$label = ca_publisher_catalog_course_category_label( $course );
		$key   = sanitize_title( $label );
		if ( ! isset( $groups[ $key ] ) ) {
			$groups[ $key ] = array(
				'label'   => $label,
				'anchor'  => 'course-category-' . $key,
				'courses' => array(),
			);
		}
		$groups[ $key ]['courses'][] = $course;
	}

	uasort(
		$groups,
		function ( array $left, array $right ): int {
			return strcasecmp( $left['label'], $right['label'] );
		}
	);

	return array_values( $groups );
}

function ca_publisher_catalog_course_category_label( WP_Post $course ): string {
	if ( taxonomy_exists( 'stm_lms_course_taxonomy' ) ) {
		$terms = get_the_terms( $course, 'stm_lms_course_taxonomy' );
		if ( is_array( $terms ) && ! empty( $terms ) && $terms[0] instanceof WP_Term ) {
			return $terms[0]->name;
		}
	}

	$category = trim( strval( get_post_meta( $course->ID, '_ca_category', true ) ) );
	return '' === $category ? 'Courses' : $category;
}

function ca_publisher_render_catalog_card( WP_Post $course ): string {
	$course_id     = trim( strval( get_post_meta( $course->ID, CA_PUBLISHER_META_COURSE_ID, true ) ) );
	$lesson_count  = intval( get_post_meta( $course->ID, '_ca_lesson_count', true ) );
	$category      = ca_publisher_catalog_course_category_label( $course );
	$permalink     = get_permalink( $course );
	$title         = get_the_title( $course );
	$image_url     = get_the_post_thumbnail_url( $course, 'medium_large' );
	$relative_path = trim( strval( get_post_meta( $course->ID, '_ca_featured_image_relative_path', true ) ) );
	if ( ! $image_url && '' !== $relative_path ) {
		$image_url = ca_publisher_media_url( $relative_path );
	}

	ob_start();
	?>
	<a class="ca-course-card" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php echo esc_attr( 'Open ' . $title ); ?>">
		<span class="ca-course-card__image">
			<?php if ( $image_url ) : ?>
				<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
			<?php else : ?>
				<span class="ca-course-card__fallback"><?php echo esc_html( $category ); ?></span>
			<?php endif; ?>
		</span>
		<span class="ca-course-card__body">
			<span class="ca-course-card__eyebrow"><?php echo esc_html( $course_id ); ?></span>
			<span class="ca-course-card__title"><?php echo esc_html( $title ); ?></span>
			<span class="ca-course-card__meta">
				<span><?php echo esc_html( $category ); ?></span>
				<?php if ( $lesson_count > 0 ) : ?>
					<span><?php echo esc_html( $lesson_count ); ?> lessons</span>
				<?php endif; ?>
			</span>
			<span class="ca-course-card__button">View course</span>
		</span>
	</a>
	<?php
	return strval( ob_get_clean() );
}

function ca_publisher_default_author_id(): int {
	$admin = get_user_by( 'login', 'admin' );
	if ( $admin instanceof WP_User ) {
		return intval( $admin->ID );
	}

	$admins = get_users(
		array(
			'role'   => 'administrator',
			'number' => 1,
			'fields' => 'ID',
		)
	);

	return ! empty( $admins ) ? intval( $admins[0] ) : 1;
}

function ca_publisher_render_course_content( array $data ): string {
	$html = '';

	$overview = ca_publisher_string_value( $data, 'overview' );
	if ( '' !== $overview ) {
		$html .= '<!-- wp:heading --><h2>Course Overview</h2><!-- /wp:heading -->';
		$html .= '<!-- wp:paragraph --><p>' . esc_html( $overview ) . '</p><!-- /wp:paragraph -->';
	}

	$objectives = is_array( $data['objectives'] ?? null ) ? $data['objectives'] : array();
	if ( ! empty( $objectives ) ) {
		$html .= '<!-- wp:heading {"level":3} --><h3>What You Will Learn</h3><!-- /wp:heading -->';
		$html .= '<!-- wp:list --><ul>';
		foreach ( $objectives as $objective ) {
			$html .= '<li>' . esc_html( strval( $objective ) ) . '</li>';
		}
		$html .= '</ul><!-- /wp:list -->';
	}

	$modules = is_array( $data['modules'] ?? null ) ? $data['modules'] : array();
	if ( ! empty( $modules ) ) {
		$html .= '<!-- wp:heading {"level":3} --><h3>Course Structure</h3><!-- /wp:heading -->';
	}

	foreach ( $modules as $module ) {
		if ( ! is_array( $module ) ) {
			continue;
		}

		$html .= '<!-- wp:heading {"level":3} --><h3>' . esc_html( ca_publisher_string_value( $module, 'title' ) ) . '</h3><!-- /wp:heading -->';
		$lessons = is_array( $module['lessons'] ?? null ) ? $module['lessons'] : array();
		if ( empty( $lessons ) ) {
			continue;
		}

		$html .= '<!-- wp:list --><ul>';
		foreach ( $lessons as $lesson ) {
			if ( ! is_array( $lesson ) ) {
				continue;
			}

			$html .= '<li>' . esc_html( ca_publisher_string_value( $lesson, 'title' ) ) . '</li>';
		}
		$html .= '</ul><!-- /wp:list -->';
	}

	return $html;
}

function ca_publisher_render_text_items( $items ): string {
	if ( ! is_array( $items ) ) {
		return '';
	}

	$html = '';
	foreach ( $items as $item ) {
		$text = trim( strval( $item ) );
		if ( '' === $text ) {
			continue;
		}
		$html .= '<!-- wp:paragraph --><p>' . nl2br( esc_html( $text ) ) . '</p><!-- /wp:paragraph -->';
	}
	return $html;
}

function ca_publisher_render_study_blocks( $blocks ): string {
	if ( ! is_array( $blocks ) ) {
		return '';
	}

	$html = '';
	foreach ( $blocks as $block ) {
		if ( is_array( $block ) ) {
			$type = ca_publisher_string_value( $block, 'type' );
			$text = ca_publisher_string_value( $block, 'text' );
		} else {
			$type = 'paragraph';
			$text = trim( strval( $block ) );
		}

		if ( '' === $text ) {
			continue;
		}

		if ( 'image' === $type && is_array( $block ) ) {
			$html .= ca_publisher_render_image_asset( $block );
		} elseif ( 'heading' === $type ) {
			$html .= '<!-- wp:heading {"level":5} --><h5>' . esc_html( $text ) . '</h5><!-- /wp:heading -->';
		} else {
			$html .= '<!-- wp:paragraph --><p>' . nl2br( esc_html( $text ) ) . '</p><!-- /wp:paragraph -->';
		}
	}

	return $html;
}

function ca_publisher_render_lesson_assets( array $lesson ): string {
	$assets = is_array( $lesson['assets'] ?? null ) ? $lesson['assets'] : array();
	if ( empty( $assets ) ) {
		return '';
	}

	$html = '<!-- wp:heading {"level":3} --><h3>Lesson Visuals</h3><!-- /wp:heading -->';
	foreach ( $assets as $asset ) {
		if ( is_array( $asset ) && 'image' === ca_publisher_string_value( $asset, 'type' ) ) {
			$html .= ca_publisher_render_image_asset( $asset );
		}
	}

	return $html;
}

function ca_publisher_render_image_asset( array $asset ): string {
	$url = ca_publisher_media_url( ca_publisher_string_value( $asset, 'relative_path' ) );
	if ( '' === $url ) {
		return '';
	}

	return '<!-- wp:image --><figure class="wp-block-image"><img src="' . esc_url( $url ) . '" alt="Lesson image"/></figure><!-- /wp:image -->';
}

function ca_publisher_sync_masterstudy_curriculum( int $course_post_id, array $data ): array {
	if ( ! post_type_exists( 'stm-lessons' ) || ! ca_publisher_curriculum_tables_ready() ) {
		return array(
			'status'   => 'skipped',
			'sections' => 0,
			'lessons'  => 0,
		);
	}

	$modules = is_array( $data['modules'] ?? null ) ? $data['modules'] : array();
	if ( empty( $modules ) ) {
		return array(
			'status'   => 'no_modules',
			'sections' => 0,
			'lessons'  => 0,
		);
	}

	global $wpdb;
	$sections_table  = $wpdb->prefix . 'stm_lms_curriculum_sections';
	$materials_table = $wpdb->prefix . 'stm_lms_curriculum_materials';
	$course_id       = ca_publisher_string_value( $data, 'course_id' );
	$section_count   = 0;
	$lesson_count    = 0;
	$lesson_post_ids = array();

	ca_publisher_delete_curriculum_for_course( $course_post_id );

	foreach ( $modules as $module_index => $module ) {
		if ( ! is_array( $module ) ) {
			continue;
		}

		$module_title = ca_publisher_string_value( $module, 'title', 'Module ' . ( $module_index + 1 ) );
		$wpdb->insert(
			$sections_table,
			array(
				'title'     => $module_title,
				'course_id' => $course_post_id,
				'order'     => $module_index + 1,
			),
			array( '%s', '%d', '%d' )
		);

		$section_id = intval( $wpdb->insert_id );
		if ( $section_id <= 0 ) {
			continue;
		}

		$section_count++;
		$lessons = is_array( $module['lessons'] ?? null ) ? $module['lessons'] : array();
		foreach ( $lessons as $lesson_index => $lesson ) {
			if ( ! is_array( $lesson ) ) {
				continue;
			}

			$lesson_post_id = ca_publisher_upsert_masterstudy_lesson( $lesson, $course_id, $course_post_id, $module_title );
			if ( $lesson_post_id <= 0 ) {
				continue;
			}

			$wpdb->insert(
				$materials_table,
				array(
					'post_id'    => $lesson_post_id,
					'post_type'  => 'stm-lessons',
					'section_id' => $section_id,
					'order'      => $lesson_index + 1,
				),
				array( '%d', '%s', '%d', '%d' )
			);

			$lesson_count++;
			$lesson_post_ids[] = $lesson_post_id;
		}
	}

	update_post_meta( $course_post_id, '_ca_lesson_post_ids', array_values( array_unique( $lesson_post_ids ) ) );
	update_post_meta( $course_post_id, '_ca_curriculum_synced_at', gmdate( 'c' ) );

	return array(
		'status'   => 'synced',
		'sections' => $section_count,
		'lessons'  => $lesson_count,
	);
}

function ca_publisher_curriculum_tables_ready(): bool {
	global $wpdb;

	return ca_publisher_table_exists( $wpdb->prefix . 'stm_lms_curriculum_sections' )
		&& ca_publisher_table_exists( $wpdb->prefix . 'stm_lms_curriculum_materials' );
}

function ca_publisher_table_exists( string $table ): bool {
	global $wpdb;

	return $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) === $table;
}

function ca_publisher_delete_curriculum_for_course( int $course_post_id ): void {
	global $wpdb;

	$sections_table  = $wpdb->prefix . 'stm_lms_curriculum_sections';
	$materials_table = $wpdb->prefix . 'stm_lms_curriculum_materials';
	$section_ids     = $wpdb->get_col(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT id FROM {$sections_table} WHERE course_id = %d",
			$course_post_id
		)
	);

	if ( ! empty( $section_ids ) ) {
		$section_ids  = array_map( 'intval', $section_ids );
		$placeholders = implode( ',', array_fill( 0, count( $section_ids ), '%d' ) );
		$wpdb->query(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"DELETE FROM {$materials_table} WHERE section_id IN ({$placeholders})",
				...$section_ids
			)
		);
	}

	$wpdb->delete( $sections_table, array( 'course_id' => $course_post_id ), array( '%d' ) );
}

function ca_publisher_upsert_masterstudy_lesson( array $lesson, string $course_id, int $course_post_id, string $module_title ): int {
	$lesson_id = ca_publisher_string_value( $lesson, 'lesson_id' );
	$title     = ca_publisher_string_value( $lesson, 'title', 'Lesson' );

	if ( '' === $lesson_id ) {
		$lesson_id = sanitize_title( $title );
	}

	$lesson_key = $course_id . ':' . $lesson_id;
	$post_id    = ca_publisher_find_existing_lesson( $lesson_key );
	$postarr    = array(
		'post_type'    => 'stm-lessons',
		'post_status'  => 'publish',
		'post_title'   => $title,
		'post_content' => ca_publisher_render_lesson_content( $lesson, $module_title ),
		'post_author'  => ca_publisher_default_author_id(),
	);

	if ( $post_id > 0 ) {
		$postarr['ID'] = $post_id;
		$result        = wp_update_post( $postarr, true );
	} else {
		$result = wp_insert_post( $postarr, true );
	}

	if ( is_wp_error( $result ) ) {
		return 0;
	}

	$post_id = intval( $result );
	update_post_meta( $post_id, CA_PUBLISHER_META_LESSON_KEY, $lesson_key );
	update_post_meta( $post_id, CA_PUBLISHER_META_COURSE_ID, $course_id );
	update_post_meta( $post_id, '_ca_course_post_id', $course_post_id );
	update_post_meta( $post_id, '_ca_lesson_id', $lesson_id );
	update_post_meta( $post_id, '_ca_video_status', ca_publisher_lesson_video_status( $lesson ) );
	update_post_meta( $post_id, '_ca_video_script', ca_publisher_lesson_video_script( $lesson ) );
	update_post_meta( $post_id, '_ca_video_scenes', ca_publisher_json_meta( ca_publisher_lesson_video_scenes( $lesson ) ) );
	update_post_meta( $post_id, 'type', 'video' );
	update_post_meta( $post_id, 'duration', ca_publisher_lesson_duration_label( $lesson ) );
	update_post_meta( $post_id, 'lesson_excerpt', ca_publisher_string_value( $lesson, 'learning_outcome' ) );
	$lesson_video_url = ca_publisher_lesson_video_url( $lesson );
	if ( '' !== $lesson_video_url ) {
		update_post_meta( $post_id, 'video_type', 'ext_link' );
		update_post_meta( $post_id, 'lesson_ext_link_url', $lesson_video_url );
		update_post_meta( $post_id, 'lesson_embed_ctx', '' );
	} else {
		update_post_meta( $post_id, 'video_type', 'embed' );
		update_post_meta( $post_id, 'lesson_embed_ctx', ca_publisher_video_placeholder_html( $lesson ) );
		update_post_meta( $post_id, 'lesson_ext_link_url', '' );
	}
	update_post_meta( $post_id, 'video_required_progress', 0 );

	return $post_id;
}

function ca_publisher_find_existing_lesson( string $lesson_key ): int {
	$posts = get_posts(
		array(
			'post_type'      => 'stm-lessons',
			'post_status'    => array( 'draft', 'publish', 'private', 'pending' ),
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => CA_PUBLISHER_META_LESSON_KEY,
			'meta_value'     => $lesson_key,
		)
	);

	return empty( $posts ) ? 0 : intval( $posts[0] );
}

function ca_publisher_render_lesson_content( array $lesson, string $module_title ): string {
	$html    = '<!-- wp:paragraph --><p><strong>Module:</strong> ' . esc_html( $module_title ) . '</p><!-- /wp:paragraph -->';
	$outcome = ca_publisher_string_value( $lesson, 'learning_outcome' );

	if ( '' !== $outcome ) {
		$html .= '<!-- wp:heading {"level":3} --><h3>Learning Outcome</h3><!-- /wp:heading -->';
		$html .= '<!-- wp:paragraph --><p>' . esc_html( $outcome ) . '</p><!-- /wp:paragraph -->';
	}

	$html .= ca_publisher_render_lesson_assets( $lesson );

	$study_blocks = $lesson['study_material'] ?? array();
	$html .= ca_publisher_render_study_blocks( $study_blocks );

	if ( empty( $study_blocks ) ) {
		$html .= ca_publisher_render_text_items( $lesson['source_body'] ?? array() );

		$topics = is_array( $lesson['topics'] ?? null ) ? $lesson['topics'] : array();
		foreach ( $topics as $topic ) {
			if ( ! is_array( $topic ) ) {
				continue;
			}
			$html .= '<!-- wp:heading {"level":4} --><h4>' . esc_html( ca_publisher_string_value( $topic, 'heading' ) ) . '</h4><!-- /wp:heading -->';
			$html .= ca_publisher_render_text_items( $topic['body'] ?? array() );
		}
	}

	return $html;
}

function ca_publisher_video_placeholder_html( array $lesson ): string {
	$title = ca_publisher_string_value( $lesson, 'title', 'Lesson video' );

	$html  = '<div class="ca-video-placeholder">';
	$html .= '<p><strong>' . esc_html( $title ) . '</strong></p>';
	$html .= '<p>Video content is being prepared for this lesson.</p>';
	$html .= '</div>';

	return $html;
}

function ca_publisher_lesson_video_status( array $lesson ): string {
	$video = is_array( $lesson['video'] ?? null ) ? $lesson['video'] : array();
	$status = ca_publisher_string_value( $video, 'status' );

	return '' === $status ? 'planned' : $status;
}

function ca_publisher_lesson_video_script( array $lesson ): string {
	$video = is_array( $lesson['video'] ?? null ) ? $lesson['video'] : array();
	return ca_publisher_string_value( $video, 'narration_script' );
}

function ca_publisher_lesson_video_scenes( array $lesson ): array {
	$video = is_array( $lesson['video'] ?? null ) ? $lesson['video'] : array();
	return is_array( $video['scenes'] ?? null ) ? $video['scenes'] : array();
}

function ca_publisher_lesson_video_url( array $lesson ): string {
	$video = is_array( $lesson['video'] ?? null ) ? $lesson['video'] : array();
	return ca_publisher_video_url( ca_publisher_string_value( $video, 'relative_path' ) );
}

function ca_publisher_lesson_duration_label( array $lesson ): string {
	$minutes = intval( $lesson['duration_minutes'] ?? 0 );
	if ( $minutes <= 0 ) {
		$minutes = 2;
	}

	return sprintf( '%d minutes', $minutes );
}

function ca_publisher_lesson_count( array $data ): int {
	if ( isset( $data['lesson_count'] ) ) {
		return intval( $data['lesson_count'] );
	}

	$count = 0;
	$modules = is_array( $data['modules'] ?? null ) ? $data['modules'] : array();
	foreach ( $modules as $module ) {
		if ( is_array( $module ) && is_array( $module['lessons'] ?? null ) ) {
			$count += count( $module['lessons'] );
		}
	}

	return $count;
}

function ca_publisher_topic_count( array $data ): int {
	if ( isset( $data['topic_count'] ) ) {
		return intval( $data['topic_count'] );
	}

	$count = 0;
	$modules = is_array( $data['modules'] ?? null ) ? $data['modules'] : array();
	foreach ( $modules as $module ) {
		if ( ! is_array( $module ) || ! is_array( $module['lessons'] ?? null ) ) {
			continue;
		}
		foreach ( $module['lessons'] as $lesson ) {
			if ( is_array( $lesson ) && is_array( $lesson['topics'] ?? null ) ) {
				$count += count( $lesson['topics'] );
			}
		}
	}

	return $count;
}

function ca_publisher_course_video_status( array $data ): string {
	$status = ca_publisher_string_value( $data, 'video_status' );
	return '' === $status ? 'pending' : $status;
}

function ca_publisher_video_duration_label( array $video ): string {
	$seconds = intval( $video['duration_seconds'] ?? 0 );
	if ( $seconds <= 0 ) {
		return '';
	}

	return sprintf( '%d seconds', $seconds );
}

function ca_publisher_media_url( string $relative_path ): string {
	$relative_path = ca_publisher_clean_relative_path( $relative_path );
	if ( '' === $relative_path ) {
		return '';
	}

	return content_url( 'course-automation/media/' . $relative_path );
}

function ca_publisher_media_file_path( string $relative_path ): string {
	$relative_path = ca_publisher_clean_relative_path( $relative_path );
	if ( '' === $relative_path ) {
		return '';
	}

	return WP_CONTENT_DIR . '/course-automation/media/' . $relative_path;
}

function ca_publisher_video_url( string $relative_path ): string {
	$relative_path = ca_publisher_clean_relative_path( $relative_path );
	if ( '' === $relative_path ) {
		return '';
	}

	return content_url( 'course-automation/videos/' . $relative_path );
}

function ca_publisher_clean_relative_path( string $relative_path ): string {
	$relative_path = trim( str_replace( '\\', '/', $relative_path ) );
	$relative_path = ltrim( $relative_path, '/' );
	if ( false !== strpos( $relative_path, '..' ) ) {
		return '';
	}

	return $relative_path;
}

function ca_publisher_json_meta( $value ): string {
	$json = wp_json_encode( $value );
	return is_string( $json ) ? $json : '';
}

function ca_publisher_string_value( array $data, string $key, string $default = '' ): string {
	$value = $data[ $key ] ?? $default;
	if ( is_scalar( $value ) ) {
		return trim( strval( $value ) );
	}

	return $default;
}
