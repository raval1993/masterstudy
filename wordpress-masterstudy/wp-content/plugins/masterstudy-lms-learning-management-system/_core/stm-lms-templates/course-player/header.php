<?php
/**
 * @var array $attachments
 * @var string $lesson_type
 * @var string $course_title
 * @var string $course_url
 * @var string $user_page_url
 * @var boolean $has_access
 * @var boolean $has_preview
 * @var boolean $lesson_lock_before_start
 * @var array $settings
 * @var int $quiz_duration
 * @var int $assignment_duration
 * @var boolean $is_scorm_course
 * @var boolean $dark_mode
 * @var boolean $theme_fonts
 * @var boolean $discussions_sidebar
 * @var int $user_id
 * @var int $course_id
 * @var int $item_id
 * @var array $quiz_data
 * @var boolean $lesson_locked_by_drip
 */

use MasterStudy\Lms\Repositories\QuizRepository;

wp_enqueue_style( 'masterstudy-course-player-header' );
wp_enqueue_script( 'masterstudy-course-player-header' );
wp_localize_script(
	'masterstudy-course-player-header',
	'settings',
	array(
		'theme_fonts' => $theme_fonts,
	)
);

global $post;
global $masterstudy_course_player_template;

$masterstudy_course_player_template = true;
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<title>
		<?php
		if ( ! empty( $is_scorm_course ) ) {
			echo esc_html( $course_title );
		} else {
			echo esc_html( $post->post_title ?? get_bloginfo( 'charset' ) );
		}
		?>
	</title>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php
$classes = implode(
	' ',
	array_filter(
		array(
			$dark_mode ? 'masterstudy-course-player-header_dark-mode' : '',
			$is_scorm_course ? 'masterstudy-course-player-header_scorm' : '',
		)
	)
);

$fallback_back_link_url = ! empty( $user_page_url ) ? $user_page_url : home_url( '/' );
$referrer_url           = wp_get_referer();
$current_lesson_url     = get_permalink();
$course_path            = wp_parse_url( $course_url, PHP_URL_PATH );
$referrer_path          = wp_parse_url( $referrer_url, PHP_URL_PATH );
$is_course_referrer     = false;

if ( ! empty( $course_path ) && ! empty( $referrer_path ) ) {
	$course_path        = trailingslashit( $course_path );
	$referrer_path      = trailingslashit( $referrer_path );
	$is_course_referrer = str_starts_with( $referrer_path, $course_path )
							&& untrailingslashit( $referrer_url ) !== untrailingslashit( $course_url );
}

$back_link_url = ( ! empty( $referrer_url ) && ! $is_course_referrer && untrailingslashit( $referrer_url ) !== untrailingslashit( $current_lesson_url ) )
	? wp_validate_redirect( $referrer_url, $fallback_back_link_url )
	: $fallback_back_link_url;
?>
<div class="masterstudy-course-player-header <?php echo esc_attr( $classes ); ?>">
	<div class="masterstudy-course-player-header__back">
		<?php
		STM_LMS_Templates::show_lms_template(
			'components/back-link',
			array(
				'id'  => 'masterstudy-course-player-back',
				'url' => $back_link_url,
			)
		);
		?>
	</div>
	<?php
	if ( ! empty( $settings['course_player_brand_icon_navigation'] ) ) {
		$logo_url = ! empty( $settings['course_player_brand_icon_navigation_image'] )
			? wp_get_attachment_image_url( $settings['course_player_brand_icon_navigation_image'] )
			: STM_LMS_URL . 'assets/img/image_not_found.png';
		?>
		<div class="masterstudy-course-player-header__logo">
			<img src="<?php echo esc_url( $logo_url ); ?>" alt="">
		</div>
		<?php
	} if ( ! $is_scorm_course && ( $has_preview || $has_access ) ) {
		?>
		<div class="masterstudy-course-player-header__curriculum">
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/switch-button',
				array(
					'title'     => __( 'Curriculum', 'masterstudy-lms-learning-management-system' ),
					'id'        => 'masterstudy-curriculum-switcher',
					'dark_mode' => $dark_mode,
				)
			);
			?>
		</div>
	<?php } ?>
	<div class="masterstudy-course-player-header__course">
		<span class="masterstudy-course-player-header__course-label">
			<?php echo esc_html__( 'Course', 'masterstudy-lms-learning-management-system' ); ?>:
		</span>
		<a href="<?php echo esc_url( $course_url ); ?>" class="masterstudy-course-player-header__course-title">
			<?php echo esc_html( mb_strlen( $course_title ) > 43 ? mb_substr( $course_title, 0, 40 ) . '...' : $course_title ); ?>
		</a>
	</div>
	<div class="masterstudy-course-player-header__navigation">
		<?php
		$course_player_header_tabs = array(
			array(
				'id'    => 'lesson',
				'title' => __( 'Lesson', 'masterstudy-lms-learning-management-system' ),
			),
		);

		if ( ! empty( $attachments ) && $has_access && ! $lesson_lock_before_start ) {
			$course_player_header_tabs[] = array(
				'id'    => 'materials',
				'title' => __( 'Materials', 'masterstudy-lms-learning-management-system' ),
			);
		}
		$course_player_header_tabs = apply_filters(
			'masterstudy_lms_course_player_header_tabs',
			$course_player_header_tabs,
			array(
				'attachments'              => $attachments,
				'lesson_type'              => $lesson_type,
				'course_title'             => $course_title,
				'course_url'               => $course_url,
				'has_access'               => $has_access,
				'has_preview'              => $has_preview,
				'lesson_lock_before_start' => $lesson_lock_before_start,
				'lesson_locked_by_drip'    => $lesson_locked_by_drip ?? false,
				'dark_mode'                => $dark_mode,
				'user_id'                  => $user_id,
				'course_id'                => $course_id,
				'item_id'                  => $item_id ?? get_the_ID(),
			)
		);

		if ( count( $course_player_header_tabs ) > 1 ) {
			STM_LMS_Templates::show_lms_template(
				'components/tabs',
				array(
					'items'            => $course_player_header_tabs,
					'style'            => 'nav-sm',
					'active_tab_index' => 0,
					'dark_mode'        => $dark_mode,
				)
			);
		}

		if ( $has_access && ( new QuizRepository() )->exists( $post->ID ) && ! empty( $quiz_data['show_attempts_history'] ) && ! empty( $quiz_data['has_attempts'] ) ) {
			STM_LMS_Templates::show_lms_template(
				'components/tabs',
				array(
					'items'            => array(
						array(
							'id'    => 'quiz',
							'title' => __( 'Quiz', 'masterstudy-lms-learning-management-system' ),
						),
						array(
							'id'    => 'history',
							'title' => __( 'History', 'masterstudy-lms-learning-management-system' ),
						),
					),
					'class'            => 'masterstudy-tabs-attempts-history',
					'style'            => 'nav-sm',
					'active_tab_index' => 0,
					'dark_mode'        => $dark_mode,
				)
			);
		}
		?>
	</div>
	<?php if ( ! empty( $assignment_duration ) && $assignment_duration > 0 ) : ?>
		<div class="masterstudy-course-player-header__assignment-timer">
			<?php STM_LMS_Templates::show_lms_template( 'course-player/assignments/timer' ); ?>
		</div>
	<?php endif; ?>
	<?php if ( ! empty( $quiz_duration ) && $quiz_duration > 0 ) { ?>
		<div class="masterstudy-course-player-header__quiz-timer">
			<?php STM_LMS_Templates::show_lms_template( 'course-player/content/quiz/timer' ); ?>
		</div>
		<?php
	}
	if ( empty( $user_id ) ) {
		?>
		<div class="masterstudy-course-player-header__login">
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/button',
				array(
					'title' => __( 'Login', 'masterstudy-lms-learning-management-system' ),
					'link'  => '#',
					'style' => 'primary',
					'size'  => 'sm',
					'login' => 'login',
				)
			);
			?>
		</div>
		<?php
	}
	if ( ! $is_scorm_course && ( $has_preview || $has_access ) ) {
		?>
		<div class="masterstudy-course-player-header__dark-mode">
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/dark-mode-button',
				array(
					'dark_mode' => $dark_mode,
				)
			);
			?>
		</div>
		<?php
	} if ( $has_access && ! $is_scorm_course && $discussions_sidebar && ! empty( $user_id ) ) {
		?>
		<div class="masterstudy-course-player-header__discussions">
			<span class="masterstudy-course-player-header__discussions-toggler">
				<span class="masterstudy-course-player-header__discussions-toggler__title">
					<?php echo esc_html__( 'Discussions', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
			</span>
			<span class="masterstudy-course-player-header__discussions-close-btn"></span>
		</div>
	<?php } ?>
</div>
