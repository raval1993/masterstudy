<?php
/**
 * @var int $post_id
 * @var int $item_id
 * @var int $user_id
 * @var string $lesson_type
 * @var string $video_type
 * @var string $audio_type
 * @var string $video_required_progress
 * @var string $audio_required_progress
 * @var array $video_questions_stats
 * @var array $material_ids
 * @var boolean $lesson_completed
 * @var boolean $has_access
 * @var boolean $lesson_lock_before_start
 * @var boolean $lesson_locked_by_drip
 * @var boolean $dark_mode
 * @var boolean $pdf_read_all
 * @var boolean $retry_after_passing
 */

use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentStudentRepository;

wp_enqueue_style( 'masterstudy-course-player-navigation' );
wp_enqueue_script( 'masterstudy-course-player-navigation' );

$is_pro_plus         = STM_LMS_Helpers::is_pro_plus();
$current_lesson_id   = array_search( $item_id, $material_ids, true );
$prev_lesson         = $material_ids[ $current_lesson_id - 1 ] ?? null;
$prev_lesson_url     = '';
$prev_lesson_preview = false;
$next_lesson         = $material_ids[ $current_lesson_id + 1 ] ?? null;
$next_lesson_url     = '';
$next_lesson_preview = false;
$progress_video_type = ! empty( $video_type ) && ! in_array( $video_type, array( 'embed', 'shortcode' ), true );
$progress_audio_type = ! empty( $audio_type ) && ! in_array( $audio_type, array( 'embed', 'shortcode' ), true );
$progress_pdf_type   = 'pdf' === $lesson_type && ! empty( $pdf_read_all );

$questions_must_done = $progress_video_type && get_post_meta( $item_id, 'video_marker_questions_locked', true );
$progress_hint_text  = 'video' === $lesson_type
	? sprintf(
		/* translators: %s: video required progress */
		esc_html__( 'You must watch at least %s%% of the video to complete the lesson', 'masterstudy-lms-learning-management-system' ),
		$video_required_progress
	)
	: sprintf(
		/* translators: %s: video required progress */
		esc_html__( 'You must listen at least %s%% of the audio to complete the lesson', 'masterstudy-lms-learning-management-system' ),
		$audio_required_progress
	);

if ( 'pdf' === $lesson_type ) {
	$progress_hint_text = esc_html__( 'You must read at least 100% of the PDF document to complete the lesson.', 'masterstudy-lms-learning-management-system' );
}

if ( 'video' === $lesson_type && $questions_must_done && $video_questions_stats['total'] > 0 ) {
	$progress_hint_text .= ' ' . esc_html__( 'and you must answer all questions.', 'masterstudy-lms-learning-management-system' );
}

$video_questions_hint = esc_html__( 'You must answer all questions to complete the lesson', 'masterstudy-lms-learning-management-system' );
$is_draft_assignment  = 'assignments' === $lesson_type
	&& method_exists( 'MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentStudentRepository', 'is_assignment_draft' )
	&& ( new AssignmentStudentRepository() )->is_assignment_draft( $item_id, $user_id );

if ( ! empty( $prev_lesson ) ) {
	$prev_lesson_url     = esc_url( STM_LMS_Lesson::get_lesson_url( $post_id, $prev_lesson ) );
	$prev_lesson_preview = STM_LMS_Lesson::lesson_has_preview( $prev_lesson );
}

if ( ! empty( $next_lesson ) ) {
	$next_lesson_url     = esc_url( STM_LMS_Lesson::get_lesson_url( $post_id, $next_lesson ) );
	$next_lesson_preview = STM_LMS_Lesson::lesson_has_preview( $next_lesson );
}
?>

<div class="masterstudy-course-player-navigation <?php echo esc_attr( $dark_mode ? 'masterstudy-course-player-navigation_dark-mode' : '' ); ?>">
	<div class="masterstudy-course-player-navigation__wrapper">
		<div class="masterstudy-course-player-navigation__prev">
			<?php
			if ( ! empty( $prev_lesson ) && $has_access ) {
				STM_LMS_Templates::show_lms_template(
					'components/nav-button',
					array(
						'title'     => __( 'Previous', 'masterstudy-lms-learning-management-system' ),
						'type'      => 'prev',
						'link'      => $prev_lesson_url,
						'style'     => 'secondary',
						'dark_mode' => $dark_mode,
						'data'      => array(),
					)
				);
			}
			?>
		</div>
		<?php if ( $lesson_completed && ! ( 'quiz' === $lesson_type && $retry_after_passing ) ) { ?>
			<div class="masterstudy-course-player-navigation__status">
				<?php echo esc_html__( 'Completed', 'masterstudy-lms-learning-management-system' ); ?>
			</div>
			<?php if ( ( 'quiz' === $lesson_type || $is_draft_assignment ) && empty( $next_lesson ) ) { ?>
				<div class="masterstudy-course-player-navigation__next"></div>
				<?php
			}
		} elseif ( $has_access && 'quiz' === $lesson_type && ! empty( $user_id ) ) {
			?>
			<div class="masterstudy-course-player-navigation__submit-quiz masterstudy-course-player-navigation__submit-quiz_hide <?php echo esc_attr( empty( $next_lesson ) ? 'masterstudy-course-player-navigation__submit-quiz_last' : '' ); ?>">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'title'         => __( 'Submit', 'masterstudy-lms-learning-management-system' ),
						'type'          => '',
						'link'          => '#',
						'style'         => 'primary',
						'size'          => 'sm',
						'id'            => 'submit-quiz',
						'icon_position' => '',
						'icon_name'     => '',
					)
				);
				?>
			</div>
			<?php
		} elseif ( $has_access && $is_draft_assignment && ! empty( $user_id ) ) {
			?>
			<div class="masterstudy-course-player-navigation__send-assignment <?php echo esc_attr( empty( $next_lesson ) ? 'masterstudy-course-player-navigation__send-assignment_last' : '' ); ?>">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'id'            => 'masterstudy-course-player-assignments-save-draft-button',
						'title'         => __( 'Save as draft', 'masterstudy-lms-learning-management-system' ),
						'link'          => '#',
						'style'         => 'tertiary',
						'size'          => 'sm',
						'icon_position' => '',
						'icon_name'     => '',
					)
				);
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'id'            => 'masterstudy-course-player-assignments-send-button',
						'title'         => __( 'Submit', 'masterstudy-lms-learning-management-system' ),
						'link'          => '#',
						'style'         => 'primary',
						'size'          => 'sm',
						'icon_position' => '',
						'icon_name'     => '',
					)
				);
				?>
			</div>
			<?php
		}

		if ( $has_access ) {
			if ( ! $lesson_completed && ! empty( $next_lesson ) ) {
				if ( ! empty( $user_id ) ) {
					$buttont_title    = __( 'Complete & Next', 'masterstudy-lms-learning-management-system' );
					$button_style     = 'primary';
					$button_id        = 'masterstudy-course-player-lesson-submit';
					$next_lesson_data = array(
						'course' => $post_id,
						'lesson' => $item_id,
					);
				}

				if ( 'assignments' === $lesson_type || 'quiz' === $lesson_type || $lesson_lock_before_start || empty( $user_id ) ) {
					$buttont_title    = __( 'Next', 'masterstudy-lms-learning-management-system' );
					$button_style     = 'secondary';
					$button_id        = 'masterstudy-course-player-lesson-next';
					$next_lesson_data = array();
				}

				if ( ! $lesson_locked_by_drip ) {
					?>
					<div class="masterstudy-course-player-navigation__next">
						<?php
						if ( $is_pro_plus ) {
							if ( $questions_must_done && ( $video_questions_stats['completed'] < $video_questions_stats['total'] ) ) {
								STM_LMS_Templates::show_lms_template(
									'components/hint',
									array(
										'content'   => $video_questions_hint,
										'side'      => is_rtl() ? 'left' : 'right',
										'dark_mode' => $dark_mode,
									)
								);
							}

							if ( 'masterstudy-course-player-lesson-submit' === $button_id &&
							( $progress_video_type || $progress_audio_type || $progress_pdf_type ) &&
							( $video_required_progress || $audio_required_progress || $progress_pdf_type ) ) {
								STM_LMS_Templates::show_lms_template(
									'components/hint',
									array(
										'content'   => $progress_hint_text,
										'side'      => is_rtl() ? 'left' : 'right',
										'dark_mode' => $dark_mode,
									)
								);
							}
						}

						STM_LMS_Templates::show_lms_template(
							'components/nav-button',
							array(
								'title'     => $buttont_title,
								'id'        => $button_id,
								'type'      => 'next',
								'link'      => $next_lesson_url,
								'style'     => "$button_style " . apply_filters( 'masterstudy_lms_course_player_complete_button_class', '' ),
								'dark_mode' => $dark_mode,
								'data'      => $next_lesson_data,
							)
						);
						?>
					</div>
					<?php
				}
			} elseif ( ! $lesson_completed && empty( $next_lesson ) && ! $lesson_lock_before_start
					&& ! $lesson_locked_by_drip && 'assignments' !== $lesson_type && 'quiz' !== $lesson_type && ! empty( $user_id ) ) {
				?>
				<div class="masterstudy-course-player-navigation__next">
					<?php
					if ( $is_pro_plus ) {
						if ( $questions_must_done && ( $video_questions_stats['completed'] < $video_questions_stats['total'] ) ) {
							STM_LMS_Templates::show_lms_template(
								'components/hint',
								array(
									'content'   => $video_questions_hint,
									'side'      => is_rtl() ? 'left' : 'right',
									'dark_mode' => $dark_mode,
								)
							);
						}

						if ( ( $progress_video_type || $progress_audio_type || $progress_pdf_type ) &&
							( $video_required_progress || $audio_required_progress || $progress_pdf_type ) ) {
							STM_LMS_Templates::show_lms_template(
								'components/hint',
								array(
									'content'   => $progress_hint_text,
									'side'      => is_rtl() ? 'left' : 'right',
									'dark_mode' => $dark_mode,
								)
							);
						}
					}

					STM_LMS_Templates::show_lms_template(
						'components/nav-button',
						array(
							'title'     => __( 'Complete', 'masterstudy-lms-learning-management-system' ),
							'id'        => 'masterstudy-course-player-lesson-submit',
							'type'      => 'next',
							'link'      => '',
							'style'     => 'primary',
							'dark_mode' => $dark_mode,
							'data'      => array(
								'course' => $post_id,
								'lesson' => $item_id,
							),
						)
					);
					?>
				</div>
				<?php
			}
		}

		if ( ! empty( $next_lesson ) && $lesson_completed && $has_access ) {
			?>
			<div class="masterstudy-course-player-navigation__next">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/nav-button',
					array(
						'title'     => __( 'Next', 'masterstudy-lms-learning-management-system' ),
						'type'      => 'next',
						'link'      => $next_lesson_url,
						'style'     => 'secondary',
						'dark_mode' => $dark_mode,
						'data'      => array(),
					)
				);
				?>
			</div>
			<?php
		}
		?>
	</div>
</div>
