<?php
/**
 * @var int    $lesson_id
 * @var string $lms_page_path
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_action(
	'wp_head',
	function () {
		wp_enqueue_style( 'masterstudy-loader' );
	}
);

use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentRepository;
use MasterStudy\Lms\Repositories\CoursePlayerRepository;

global $post;

$post = get_post( $lesson_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

if ( $post instanceof \WP_Post ) {
	setup_postdata( $post );
}

$course_player = new CoursePlayerRepository();
$data          = $course_player->get_main_data( $lms_page_path, (int) $lesson_id );

$quiz_data     = 'quiz' === $data['lesson_type']
	? $course_player->get_quiz_data( $data['item_id'], $data['user_id'], $data['post_id'] )
	: array();

$assignment_data = is_ms_lms_addon_enabled( 'assignments' ) && 'assignments' === $data['lesson_type'] ?
	( new AssignmentRepository() )->get( $data['item_id'] ) :
	array();

do_action( 'masterstudy_lms_course_player_register_assets' );

wp_enqueue_style( 'masterstudy-course-player-main' );
wp_enqueue_script( 'masterstudy-course-player-quiz-attempt' );

if ( empty( $data['theme_fonts'] ) ) {
	wp_enqueue_style( 'masterstudy-fonts' );
	wp_enqueue_style( 'masterstudy-course-player-fonts' );
	wp_enqueue_style( 'masterstudy-components-fonts' );
}

do_action( 'stm_lms_before_item_template_start', $data['post_id'], $data['item_id'], $data['material_ids'] );

do_action( 'stm_lms_template_main' );

STM_LMS_Templates::show_lms_template(
	'course-player/header',
	array(
		'attachments'              => $data['lesson_attachments'],
		'course_title'             => $data['course_title'],
		'lesson_type'              => $data['lesson_type'],
		'has_access'               => $data['has_access'],
		'has_preview'              => $data['has_preview'],
		'lesson_lock_before_start' => $data['lesson_lock_before_start'],
		'course_url'               => $data['course_url'],
		'user_page_url'            => $data['user_page_url'],
		'quiz_duration'            => 'quiz' === $data['content_type'] ? $quiz_data['duration'] : '',
		'assignment_duration'      => $assignment_data['time_limit'] ?? '',
		'is_scorm_course'          => $data['is_scorm_course'],
		'settings'                 => $data['settings'],
		'dark_mode'                => $data['dark_mode'],
		'theme_fonts'              => $data['theme_fonts'],
		'discussions_sidebar'      => $data['discussions_sidebar'],
		'user_id'                  => $data['user_id'],
		'course_id'                => $data['post_id'],
		'item_id'                  => $data['item_id'],
		'quiz_data'                => $quiz_data,
		'lesson_locked_by_drip'    => $data['lesson_locked_by_drip'],
	)
);

STM_LMS_Templates::show_lms_template(
	'components/loader',
	array(
		'global'    => true,
		'dark_mode' => $data['dark_mode'],
	)
);

if ( apply_filters( 'stm_lms_stop_item_output', false, $data['post_id'] ) ) {
	if ( $data['has_access'] || $data['has_preview'] ) {
		do_action( 'stm_lms_before_item_lesson_start', $data['post_id'], $data['item_id'] );
	} else {
		?>
		<div class="masterstudy-course-player-content masterstudy-course-player-content_locked <?php echo esc_attr( $data['dark_mode'] ? 'masterstudy-course-player-content_dark-mode' : '' ); ?>">
			<div class="masterstudy-course-player-content__wrapper">
				<?php
				STM_LMS_Templates::show_lms_template(
					'course-player/locked',
					array(
						'post_id'   => $data['post_id'],
						'item_id'   => $data['item_id'],
						'user_id'   => $data['user_id'],
						'dark_mode' => $data['dark_mode'],
					)
				);
				?>
			</div>
		</div>
		<?php
	}
} else {
	if ( $data['has_access'] && ! $data['has_trial_access'] ) {
		do_action( 'stm_lms_lesson_started', $data['item_id'], $data['post_id'], $data['user_id'] );
	}

	if ( $data['has_access'] && intval( $data['user_course']['start_time'] ?? 0 ) === 0 ) {
		stm_lms_update_start_time_in_user_course( $data['user_id'], $data['post_id'] );
	}

	do_action( 'masterstudy_lms_course_player_update_user_current_lesson', $data['post_id'], $data['item_id'] );
	?>
	<div class="masterstudy-course-player-content <?php echo esc_attr( $data['dark_mode'] ? 'masterstudy-course-player-content_dark-mode' : '' ); ?>">
		<?php
		if ( ! $data['is_scorm_course'] && ( $data['has_access'] || $data['has_preview'] ) ) {
			STM_LMS_Templates::show_lms_template(
				'course-player/curriculum',
				array(
					'post_id'       => $data['post_id'],
					'item_id'       => $data['item_id'],
					'user_id'       => $data['user_id'],
					'course_title'  => $data['course_title'],
					'curriculum'    => $data['curriculum'],
					'user_course'   => $data['user_course'] ?? array(),
					'trial_lessons' => $data['trial_lesson_count'],
					'trial_access'  => $data['has_trial_access'],
					'is_enrolled'   => $data['is_enrolled'],
					'dark_mode'     => $data['dark_mode'],
				)
			);
		}
		?>
		<div class="masterstudy-course-player-content__wrapper <?php echo esc_attr( 'video' === $data['lesson_type'] && ! empty( $data['video_questions'] ) ? 'masterstudy-has-video-questions' : '' ); ?>">
			<?php
			$has_access = apply_filters( 'masterstudy_lms_course_guest_trial_enabled', false, $data['post_id'] )
				? $data['has_access']
				: $data['has_access'] && is_user_logged_in();

			if ( $has_access || $data['has_preview'] ) {
				if ( ! $data['lesson_lock_before_start'] && ! $data['lesson_locked_by_drip'] ) {
					?>
					<div class="masterstudy-course-player-content__header <?php echo esc_attr( 'quiz' === $data['lesson_type'] ? 'masterstudy-course-player-content__header_quiz' : '' ); ?>">
						<span class="masterstudy-course-player-content__header-lesson-type">
							<?php echo esc_html( $data['lesson_type_label'] ); ?>
						</span>
						<h1><?php echo esc_html( get_the_title( $data['item_id'] ) ); ?></h1>
					</div>
					<?php
				}

				$item_content = apply_filters( 'stm_lms_show_item_content', true, $data['post_id'], $data['item_id'] );

				if ( $item_content && ! empty( $data['item_id'] ) ) {
					STM_LMS_Templates::show_lms_template(
						'course-player/content/' . $data['content_type'] . '/main',
						array(
							'post_id'               => $data['post_id'],
							'item_id'               => $data['item_id'],
							'user_id'               => $data['user_id'],
							'lesson_type'           => $data['lesson_type'],
							'lesson_completed'      => $data['lesson_completed'],
							'data'                  => 'quiz' === $data['content_type'] ? $quiz_data : array(),
							'last_lesson'           => $data['last_lesson'],
							'video_questions'       => $data['video_questions'] ?? array(),
							'video_questions_stats' => $data['video_questions_stats'] ?? array(),
							'dark_mode'             => $data['dark_mode'],
							'has_attempts'          => $quiz_data['has_attempts'] ?? false,
						)
					);
					if ( 'quiz' === $data['content_type'] && $quiz_data['show_attempts_history'] && ! empty( $quiz_data['has_attempts'] ) ) {
						STM_LMS_Templates::show_lms_template(
							'course-player/content/quiz/history',
							array(
								'post_id'          => $data['post_id'],
								'item_id'          => $data['item_id'],
								'user_id'          => $data['user_id'],
								'lesson_type'      => $data['lesson_type'],
								'lesson_completed' => $data['lesson_completed'],
								'data'             => $quiz_data,
								'last_lesson'      => $data['last_lesson'],
								'dark_mode'        => $data['dark_mode'],
							)
						);
					}
				}

				$content = apply_filters( 'stm_lms_course_item_content', '', $data['post_id'], $data['item_id'], $data );

				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $content;

				if ( ! empty( $data['lesson_attachments'] ) && ! $data['lesson_lock_before_start'] ) {
					STM_LMS_Templates::show_lms_template(
						'course-player/content/lesson/materials',
						array(
							'attachments' => $data['lesson_attachments'],
							'dark_mode'   => $data['dark_mode'],
						)
					);
				}

				if ( ! empty( $data['last_lesson'] ) && intval( $data['item_id'] ) === intval( $data['last_lesson'] ) ) {
					STM_LMS_Templates::show_lms_template(
						'components/course/complete',
						array(
							'user_id'          => $data['user_id'],
							'settings'         => $data['settings'],
							'course_id'        => $data['post_id'],
							'dark_mode'        => $data['dark_mode'],
							'user_course'      => $data['user_course'] ?? array(),
							'lesson_completed' => $data['lesson_completed'],
						)
					);
				}
			} else {
				STM_LMS_Templates::show_lms_template(
					'course-player/locked',
					array(
						'post_id'   => $data['post_id'],
						'item_id'   => $data['item_id'],
						'user_id'   => $data['user_id'],
						'dark_mode' => $data['dark_mode'],
					)
				);
			}

			if ( ! empty( $data['lesson_post_type'] ) ) {
				STM_LMS_Templates::show_lms_template(
					'course-player/navigation',
					array(
						'post_id'                  => $data['post_id'],
						'item_id'                  => $data['item_id'],
						'user_id'                  => $data['user_id'],
						'lesson_type'              => $data['lesson_type'],
						'video_type'               => $data['video_type'] ?? null,
						'audio_type'               => $data['audio_type'] ?? null,
						'video_required_progress'  => $data['video_required_progress'] ?? null,
						'audio_required_progress'  => $data['audio_required_progress'] ?? null,
						'material_ids'             => $data['material_ids'],
						'lesson_completed'         => $data['lesson_completed'],
						'has_access'               => $data['has_access'],
						'lesson_lock_before_start' => $data['lesson_lock_before_start'],
						'lesson_locked_by_drip'    => $data['lesson_locked_by_drip'],
						'video_questions_stats'    => $data['video_questions_stats'] ?? array(),
						'dark_mode'                => $data['dark_mode'],
						'pdf_read_all'             => $data['pdf_read_all'] ?? null,
						'retry_after_passing'      => $quiz_data['retry_after_passing'] ?? false,
					)
				);
			}
			?>
		</div>
		<?php
		if ( $data['has_access'] ) {
			STM_LMS_Templates::show_lms_template(
				'course-player/discussions',
				array(
					'post_id'             => $data['post_id'],
					'item_id'             => $data['item_id'],
					'user_id'             => $data['user_id'],
					'lesson_type'         => $data['lesson_type'],
					'quiz_data'           => 'quiz' === $data['content_type'] ? $quiz_data : array(),
					'dark_mode'           => $data['dark_mode'],
					'discussions_sidebar' => $data['discussions_sidebar'],
					'settings'            => $data['settings'],
				)
			);
		}
		?>
	</div>
	<?php
}

do_action( 'template_redirect' );

STM_LMS_Templates::show_lms_template( 'course-player/footer' );

do_action( 'stm_lms_template_main_after' );
