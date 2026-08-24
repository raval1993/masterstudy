<?php
/**
 * @var int $item_id
 * @var int $user_id
 * @var int $course_id
 * @var boolean $lesson_completed
 * @var boolean $dark_mode
.*/

use MasterStudy\Lms\Repositories\LessonRepository;

wp_enqueue_style( 'masterstudy-course-player-lesson-type-audio' );
wp_enqueue_script( 'masterstudy-course-player-audio-lesson-type' );

$lesson_data    = ( new LessonRepository() )->get( $item_id );
$audio_progress = ! empty( $lesson_data['audio_required_progress'] );
$user_progress  = masterstudy_lms_get_user_lesson_progress( $user_id, $course_id, $item_id ) ?? 0;

if ( empty( $lesson_data['audio_type'] ) ) {
	return;
}

wp_localize_script(
	'masterstudy-course-player-audio-lesson-type',
	'audio_data',
	array(
		'audio_type'     => $lesson_data['audio_type'],
		'audio_progress' => $audio_progress,
	)
);
?>

<div class="masterstudy-course-player-audio-lesson-type">
	<?php
	switch ( $lesson_data['audio_type'] ) {
		case 'file':
			$file_data = $lesson_data['file'] ?? '';
			if ( ! empty( $file_data['url'] ) ) {
				STM_LMS_Templates::show_lms_template(
					'components/audio-player',
					array(
						'preload'       => true,
						'show_progress' => $audio_progress,
						'src'           => $file_data['url'],
						'dark_mode'     => $dark_mode,
					)
				);
			}

			break;
		case 'embed':
			$lesson_embed_ctx = $lesson_data['embed_ctx'] ?? '';
			if ( ! empty( $lesson_embed_ctx ) ) {
				?>
				<div class="masterstudy-course-player-audio-lesson-type__embed-wrapper">
					<?php echo wp_kses( htmlspecialchars_decode( $lesson_embed_ctx ), stm_lms_allowed_html() ); ?>
				</div>
				<?php
			}
			break;
		case 'ext_link':
			$external_link = $lesson_data['external_url'] ?? '';
			if ( ! empty( $external_link ) ) {
				?>
				<audio controls class="audio-external-links-type">
					<?php foreach ( array( 'mpeg', 'webm', 'ogg', 'wav' ) as $format ) : ?>
						<source src="<?php echo esc_url( $external_link ); ?>"
							type="audio/<?php echo esc_html( $format ); ?>">
					<?php endforeach; ?>
					<?php echo esc_html__( 'Your browser does not support the audio external link.', 'masterstudy-lms-learning-management-system' ); ?>
				</audio>
				<?php
			}
			break;
		case 'shortcode':
			$lesson_shortcode = $lesson_data['shortcode'] ?? '';
			if ( ! empty( $lesson_shortcode ) ) {
				echo do_shortcode( $lesson_shortcode );
			}
			break;
	}
	if ( $audio_progress && STM_LMS_Helpers::is_pro_plus() && ! ( 0 === $user_progress && $lesson_completed ) ) {
		?>
		<div class="masterstudy-course-player-audio-lesson-type__progress">
			<div class="masterstudy-course-player-audio-lesson-type__progress-title">
				<?php echo esc_html__( 'Lesson audio progress', 'masterstudy-lms-learning-management-system' ) . ':'; ?>
				<div id="current-audio-progress-user" class="masterstudy-course-player-audio-lesson-type__progress-user">
					<?php echo esc_html( $user_progress ) . '%'; ?>
				</div>
				<span class="masterstudy-course-player-audio-lesson-type__progress-separator">
					<?php echo esc_html__( 'of', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
				<div class="masterstudy-course-player-audio-lesson-type__progress-required">
					<?php echo esc_html( $lesson_data['audio_required_progress'] ) . '%'; ?>
				</div>
			</div>
			<div class="masterstudy-course-player-audio-lesson-type__progress-bar" data-required-progress="<?php echo esc_attr( $lesson_data['audio_required_progress'] ); ?>" id="required-audio-progress">
				<span class="masterstudy-course-player-audio-lesson-type__progress-bar-value" data-progress="<?php echo esc_attr( $user_progress ); ?>" id="current-audio-progress" style="width:<?php echo esc_attr( $user_progress ); ?>%"></span>
			</div>
		</div>
	<?php } ?>
</div>
<?php
