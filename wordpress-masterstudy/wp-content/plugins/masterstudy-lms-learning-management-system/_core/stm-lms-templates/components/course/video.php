<?php
/**
 * Video Media type component
 *
 * @var array $course
 * @var int $course_id
 * @var bool $mode
 */

use MasterStudy\Lms\Enums\LessonVideoType;

if ( empty( $course['video_type'] ) ) {
	return;
}

$video_type = $course['video_type'];
$poster     = $course['video_poster']['url'] ?? '';
$video_id   = '';
$extra      = '';

switch ( $video_type ) {
	case LessonVideoType::YOUTUBE:
		$video_id = ms_plugin_get_youtube_id( $course['youtube_url'] );

		if ( empty( $poster ) ) {
			$maxres   = "https://img.youtube.com/vi/{$video_id}/maxresdefault.jpg";
			$fallback = "https://img.youtube.com/vi/{$video_id}/hqdefault.jpg";
			$response = wp_remote_head( $maxres );
			$poster   = is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ? $fallback : $maxres;
		}
		break;

	case LessonVideoType::VIMEO:
		$video_id = ms_plugin_get_vimeo_id( $course['vimeo_url'] );
		break;

	case LessonVideoType::EMBED:
		if ( ! empty( $course['embed_ctx'] ) ) {
			$video_id = base64_encode( $course['embed_ctx'] );
			$poster   = '';
		}
		break;

	case LessonVideoType::HTML:
		$video_id = $course['video']['url'] ?? '';
		$extra    = wp_json_encode(
			array(
				'type'  => strtolower( pathinfo( $video_id, PATHINFO_EXTENSION ) ),
				'width' => $course['video_width'] ?? '',
			)
		);
		break;

	case LessonVideoType::EXT_LINK:
	case 'external_url':
		$video_id = $course['external_url'] ?? '';
		$extra    = wp_json_encode(
			array(
				'type' => strtolower( pathinfo( $video_id, PATHINFO_EXTENSION ) ),
			)
		);
		break;

	case LessonVideoType::PRESTO_PLAYER:
		$video_id = (string) ( $course['presto_player_idx'] ?? '' );
		break;

	case LessonVideoType::SHORTCODE:
		if ( ! empty( $course['shortcode'] ) ) {
			$video_id = base64_encode( $course['shortcode'] );
		}
		break;

	case LessonVideoType::VDOCIPHER:
		if ( ! empty( $course['vdocipher_id'] ) ) {
			$video_id = preg_replace( '/\[vdo id="([^"]*)"\]/', '$1', $course['vdocipher_id'] );
		}
		break;
}

wp_enqueue_style( 'masterstudy-single-course-video-plyr' );
wp_enqueue_style( 'masterstudy-single-course-video' );
wp_enqueue_script( 'masterstudy-single-course-video' );
wp_localize_script(
	'masterstudy-single-course-video',
	'video_player_data',
	array(
		'video_type' => $video_type,
	)
);
?>

<div class="masterstudy-single-course-video">
	<div class="masterstudy-single-course-video__container">
		<?php if ( ! empty( $video_id ) ) : ?>
			<div class="masterstudy-single-course-video__wrapper masterstudy-single-course-video__wrapper_lazy"
				data-video-type="<?php echo esc_attr( $video_type ); ?>"
				data-video-id="<?php echo esc_attr( $video_id ); ?>"
				data-poster="<?php echo esc_url( $poster ); ?>"
				data-extra='<?php echo esc_attr( $extra ); ?>'>

				<?php if ( $poster ) : ?>
					<img class="masterstudy-single-course-video__poster" src="<?php echo esc_url( $poster ); ?>" alt="Video preview">
				<?php endif; ?>

				<div class="masterstudy-single-course-video__play-button"></div>
				<div class="masterstudy-single-course-video__loader">
					<span class="masterstudy-single-course-video__loader-body"></span>
				</div>
			</div>
		<?php else : ?>
			<p><?php esc_html_e( 'Video source is missing or invalid.', 'masterstudy-lms-learning-management-system' ); ?></p>
		<?php endif; ?>
	</div>
</div>
