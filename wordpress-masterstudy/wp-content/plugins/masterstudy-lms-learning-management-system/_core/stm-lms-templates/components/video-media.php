<?php
/**
 * Video Media type component
 *
 * @var array $lesson - $lesson array
 * @var array $id - Video Lesson ID
 * @var int $user_id
 * @var int $course_id
 * @var bool $lesson_completed
 * @var bool $mode - boolean for turn on brand player
 * @var array $video_questions
 * @var array $video_questions_stats
 * @package masterstudy
 */

use MasterStudy\Lms\Enums\LessonVideoType;

if ( empty( $lesson['video_type'] ) ) {
	return;
}

$settings                  = get_option( 'stm_lms_settings' );
$is_pro_plus               = STM_LMS_Helpers::is_pro_plus();
$plyr_vimeo_video_player   = $settings['course_player_vimeo_video_player'] ?? false;
$plyr_youtube_video_player = $settings['course_player_youtube_video_player'] ?? false;
$is_youtube_type           = LessonVideoType::YOUTUBE === $lesson['video_type'];
$is_vimeo_type             = LessonVideoType::VIMEO === $lesson['video_type'];
$user_id                   = isset( $user_id ) ? $user_id : '';
$course_id                 = isset( $course_id ) ? $course_id : '';
$lesson_completed          = isset( $lesson_completed ) ? $lesson_completed : false;
$user_progress             = ! empty( $user_id ) && ! empty( $course_id ) ? masterstudy_lms_get_user_lesson_progress( $user_id, $course_id, $id ) ?? 0 : 0;
$video_strict_mode         = $settings['course_player_video_strict_mode'] ?? false;
$video_captions            = $lesson['video_captions'] ?? array();
$is_pro_plus               = STM_LMS_Helpers::is_pro_plus();

if ( $is_pro_plus && ! empty( $video_questions ) ) {
	$questions_must_done = get_post_meta( $id, 'video_marker_questions_locked', true );
	$plyr_markers        = array_map(
		function ( $marker ) {
			return array(
				'time'  => (int) $marker['marker'],
				'label' => $marker['caption'],
			);
		},
		$video_questions
	);
	$filtered_questions  = array_values(
		array_filter(
			$video_questions,
			function ( $question ) {
				return ! empty( $question['type'] );
			}
		)
	);
	$questions_progress  = ( $video_questions_stats['total'] > 0 ) ? ( $video_questions_stats['answered'] / $video_questions_stats['total'] ) * 100 : 0;
}

if ( ! empty( $mode ) ) {
	$plyr_vimeo_video_player   = $mode;
	$plyr_youtube_video_player = $mode;
}

wp_enqueue_style( 'masterstudy-course-player-video-plyr' );
wp_enqueue_style( 'masterstudy-course-player-lesson-video' );
wp_enqueue_script( 'masterstudy-course-player-lesson-video' );
wp_localize_script(
	'masterstudy-course-player-lesson-video',
	'video_player_data',
	array(
		'course_id'               => $course_id,
		'lesson_id'               => $id,
		'video_type'              => $lesson['video_type'],
		'video_progress'          => ! empty( $lesson['video_required_progress'] ),
		'video_markers'           => $plyr_markers ?? array(),
		'video_questions'         => $filtered_questions ?? array(),
		'video_questions_nonce'   => wp_create_nonce( 'stm_lms_answer_video_lesson' ),
		'questions_must_done'     => $questions_must_done ?? false,
		'strict_mode'             => $video_strict_mode ?? false,
		'plyr_youtube_player'     => $plyr_youtube_video_player,
		'plyr_vimeo_video_player' => $plyr_vimeo_video_player,
		'video_questions_stats'   => $video_questions_stats ?? array(
			'answered'  => 0,
			'completed' => 0,
			'total'     => 0,
		),
	)
);
?>

<div class="masterstudy-course-player-lesson-video">
	<div class="masterstudy-course-player-lesson-video__container">
		<?php
		if ( LessonVideoType::EMBED === $lesson['video_type'] && ! empty( $lesson['embed_ctx'] ) ) {
			?>
			<div class="masterstudy-course-player-lesson-video__embed-wrapper">
				<?php echo wp_kses( htmlspecialchars_decode( $lesson['embed_ctx'] ), stm_lms_allowed_html() ); ?>
			</div>
			<?php
		} elseif ( in_array( $lesson['video_type'], array( LessonVideoType::HTML, LessonVideoType::EXT_LINK, 'external_url' ), true ) ) {
			$uploaded_video = $lesson['external_url'] ?? '';
			$video_format   = 'mp4';

			if ( LessonVideoType::HTML === $lesson['video_type'] ) {
				$uploaded_video = $lesson['video']['url'] ?? '';
				$video_format   = explode( '.', $uploaded_video );
				$video_format   = strtolower( end( $video_format ) );
				$video_width    = ! empty( $lesson['video_width'] ) ? "max-width: {$lesson['video_width']}px" : '';
			}
			?>
			<div class="masterstudy-course-player-lesson-video__wrapper" style="<?php echo esc_attr( ! empty( $video_width ) ? $video_width : '' ); ?>">
				<?php
				if ( strpos( $uploaded_video, 'videopress.com' ) !== false ) {
					?>
					<iframe src="<?php echo esc_url( $uploaded_video ); ?>" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
					<?php
				} else {
					?>
					<video class="masterstudy-plyr-video-player" data-id="<?php echo esc_attr( $id ); ?>"
						data-poster="<?php echo esc_url( $lesson['video_poster']['url'] ?? '' ); ?>"
						controls
						controlsList="nodownload">
						<source
							src="<?php echo esc_url( $uploaded_video ); ?>"
							type='video/<?php echo esc_attr( $video_format ); ?>'>
						<?php if ( ! empty( $video_captions ) && $is_pro_plus ) : ?>
							<?php foreach ( $video_captions as $caption ) : ?>
								<track
									kind="captions"
									label="<?php echo esc_attr( str_replace( '.vtt', '', $caption['label'] ) ); ?>"
									src="<?php echo esc_attr( $caption['url'] ); ?>"
									srclang="<?php echo esc_attr( strtolower( substr( $caption['label'], 0, 2 ) ) ); ?>"
								>
							<?php endforeach; ?>
						<?php endif; ?>
					</video>
					<?php
				}
				?>
			</div>
			<?php
		} elseif ( ( ! empty( $lesson['youtube_url'] ) || ! empty( $lesson['vimeo_url'] ) ) && in_array( $lesson['video_type'], array( LessonVideoType::YOUTUBE, LessonVideoType::VIMEO ), true ) ) {
			$video_id = $is_youtube_type ? ms_plugin_get_youtube_id( $lesson['youtube_url'] ) : ms_plugin_get_vimeo_id( $lesson['vimeo_url'] );

			if ( $plyr_vimeo_video_player && $is_vimeo_type || $plyr_youtube_video_player && $is_youtube_type ) {
				?>
				<div class="masterstudy-plyr-video-player" class="plyr__video-embed">
			<?php } ?>
			<iframe
				id="videoPlayer"
				src="<?php // phpcs:disable
					echo esc_attr(
						'youtube' === $lesson['video_type']
							? "https://www.youtube.com/embed/{$video_id}?&amp;iv_load_policy=3&amp;modestbranding=1&amp;playsinline=1&amp;showinfo=0&amp;rel=0&amp;enablejsapi=1&customControls=true&pip=false"
							: "https://player.vimeo.com/video/{$video_id}?loop=false&amp;byline=false&amp;portrait=false&amp;title=false&amp;pip=false&amp;speed=true&amp;transparent=0&amp;gesture=media&amp;api=1&player_id=videoPlayer"
					);
				?>"
				frameborder="0"
				allowfullscreen
				allowtransparency
				allow="autoplay">
			</iframe>
			<?php if ( $plyr_vimeo_video_player && $is_vimeo_type || $plyr_youtube_video_player && $is_youtube_type ) { ?>
				</div>
			<?php }
		} elseif ( in_array( $lesson['video_type'], array( LessonVideoType::PRESTO_PLAYER, LessonVideoType::SHORTCODE ), true ) ) {
			echo 'presto_player' === $lesson['video_type'] && ! empty( $lesson['presto_player_idx'] ) ? do_shortcode( '[presto_player id="' . esc_attr( $lesson['presto_player_idx'] ) . '"]' ) : do_shortcode( $lesson['shortcode'] );
		} elseif ( LessonVideoType::VDOCIPHER === $lesson['video_type'] && ! empty( $lesson['vdocipher_id'] ) ) {
			$vdocipher_id = preg_replace( '/\[vdo id="([^"]*)"\]/', '$1', $lesson['vdocipher_id'] );

			echo do_shortcode( '[vdo id="' . esc_attr( $vdocipher_id ) . '"]' );
		}
		?>
	</div>
	<?php
	if ( $is_pro_plus ) {
		STM_LMS_Templates::show_lms_template(
			'components/video-questions',
			array(
				'video_questions'     => $filtered_questions ?? array(),
				'total_questions'     => $video_questions_stats['total'] ?? 0,
				'questions_must_done' => $questions_must_done ?? false,
			)
		);
	}
	if ( ! empty( $lesson['video_required_progress'] ) && STM_LMS_Helpers::is_pro_plus() && ! ( 0 === $user_progress && $lesson_completed ) ) {
		?>
		<div class="masterstudy-course-player-lesson-video__progress">
			<div class="masterstudy-course-player-lesson-video__progress-title">
				<?php echo esc_html__( 'Lesson video progress', 'masterstudy-lms-learning-management-system' ) . ':'; ?>
				<div id="current-video-progress-user" class="masterstudy-course-player-lesson-video__progress-user">
					<?php echo esc_html( $user_progress ) . '%'; ?>
				</div>
				<span class="masterstudy-course-player-lesson-video__progress-separator">
					<?php echo esc_html__( 'of', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
				<div class="masterstudy-course-player-lesson-video__progress-required">
					<?php echo esc_html( $lesson['video_required_progress'] ) . '%'; ?>
				</div>
			</div>
			<div class="masterstudy-course-player-lesson-video__progress-bar" data-required-progress="<?php echo esc_attr( $lesson['video_required_progress'] ); ?>" id="required-video-progress">
				<span class="masterstudy-course-player-lesson-video__progress-bar-value" data-progress="<?php echo esc_attr( $user_progress ); ?>" id="current-video-progress" style="width:<?php echo esc_attr( $user_progress ); ?>%"></span>
			</div>
		</div>
	<?php }
	if ( $is_pro_plus && ! empty( $video_questions_stats['total'] ) ) {
		?>
		<div class="masterstudy-course-player-lesson-video__progress">
			<div class="masterstudy-course-player-lesson-video__progress-title">
				<?php echo esc_html__( 'Lesson questions progress', 'masterstudy-lms-learning-management-system' ) . ':'; ?>
				<div id="current-questions-progress-user" class="masterstudy-course-player-lesson-video__progress-user">
					<?php echo esc_html( $video_questions_stats['answered'] ); ?>
				</div>
				/
				<div class="masterstudy-course-player-lesson-video__progress-user">
					<?php echo esc_html( $video_questions_stats['total'] ); ?>
				</div>
			</div>
			<div class="masterstudy-course-player-lesson-video__progress-bar">
				<span class="masterstudy-course-player-lesson-video__progress-bar-value" id="current-questions-progress" style="width:<?php echo esc_attr( $questions_progress ); ?>%">
				</span>
			</div>
		</div>
		<?php
	}
	?>
</div>
