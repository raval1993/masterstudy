<?php
/**
 * @var array $current_user
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use MasterStudy\Lms\Plugin\PostType;

$reviews = STM_LMS_Options::get_option( 'course_tab_reviews', true );
$user_id = $current_user['id'];

$course_statuses = array(
	array(
		'id'    => 'all',
		'title' => esc_html__( 'All', 'masterstudy-lms-learning-management-system' ),
	),
	array(
		'id'    => 'published',
		'title' => esc_html__( 'Published', 'masterstudy-lms-learning-management-system' ),
	),
	array(
		'id'    => 'draft',
		'title' => esc_html__( 'In draft', 'masterstudy-lms-learning-management-system' ),
	),
);

$render_upcoming_tab = false;

if ( $user_id ) {
	$has_instructor_role = STM_LMS_Instructor::has_instructor_role( $user_id );

	$instructor_role = get_option( 'masterstudy_lms_coming_soon_settings', true );
	if ( is_array( $instructor_role ) && $has_instructor_role && isset( $instructor_role['lms_coming_soon_instructor_allow_status'] ) ) {
		$render_upcoming_tab = true;
	}
}

if ( current_user_can( 'manage_options' ) ) {
	$render_upcoming_tab = true;
}

if ( is_ms_lms_addon_enabled( 'coming_soon' ) && $render_upcoming_tab ) {
	$course_statuses[] = array(
		'id'    => 'coming_soon_status',
		'title' => esc_html__( 'Upcoming', 'masterstudy-lms-learning-management-system' ),
	);
}

$initial_status   = 'all';
$initial_page     = 1;
$courses_per_page = 9;

$args = array(
	'author'         => $user_id,
	'post_type'      => PostType::COURSE,
	'posts_per_page' => $courses_per_page,
	'paged'          => $initial_page,
	'post_status'    => array( 'publish', 'draft', 'pending', 'rejected', 'private' ),
);

if ( function_exists( 'pll_current_language' ) ) {
	$args['lang'] = pll_current_language();
}

$courses             = STM_LMS_Instructor::get_instructor_courses( $args, $courses_per_page );
$initial_total_pages = (int) $courses['pages'];
$initial_posts       = (array) $courses['posts'];

wp_enqueue_style( 'masterstudy-account-instructor-courses' );
wp_enqueue_script( 'masterstudy-account-instructor-courses' );

if ( is_ms_lms_addon_enabled( 'coming_soon' ) ) {
	stm_lms_register_style( 'coming_soon/coming_soon' );
	wp_enqueue_style( 'masterstudy-countdown' );
	wp_enqueue_script( 'masterstudy-countdown' );
}

$links = STM_LMS_Instructor::instructor_links();
?>

<div class="masterstudy-instructor-courses__tabs">
	<?php
	STM_LMS_Templates::show_lms_template(
		'components/tabs',
		array(
			'items'            => $course_statuses,
			'style'            => 'buttons',
			'active_tab_index' => 0,
			'dark_mode'        => false,
		)
	);

	STM_LMS_Templates::show_lms_template(
		'components/button',
		array(
			'title'  => esc_html__( 'Add new course', 'masterstudy-lms-learning-management-system' ),
			'link'   => $links['add_new'],
			'style'  => 'secondary',
			'size'   => 'sm',
			'id'     => 'add_new_course',
			'target' => '_blank',
			'class'  => 'masterstudy-instructor-courses__add-new-course-btn',
		)
	);
	?>
</div>

<div class="masterstudy-instructor-courses">
	<div class="masterstudy-instructor-courses__list">
		<?php
		if ( ! empty( $initial_posts ) ) :
			foreach ( $initial_posts as $course ) :
				STM_LMS_Templates::show_lms_template(
					'components/course/card/default',
					array(
						'course'          => $course,
						'public'          => false,
						'reviews'         => (bool) $reviews,
						'student_card'    => false,
						'instructor_card' => true,
					)
				);
			endforeach;
		endif;
		?>
	</div>

	<div class="masterstudy-instructor-courses__pagination">
		<?php
		if ( ! empty( $initial_posts ) && $initial_total_pages > 1 ) {
			STM_LMS_Templates::show_lms_template(
				'components/pagination',
				array(
					'max_visible_pages' => 5,
					'total_pages'       => $initial_total_pages,
					'current_page'      => $initial_page,
					'dark_mode'         => false,
					'is_queryable'      => false,
					'done_indicator'    => false,
					'is_api'            => true,
					'thin'              => false,
				)
			);
		}
		?>
	</div>

	<div class="masterstudy-instructor-courses__loader">
		<div class="masterstudy-instructor-courses__loader-body"></div>
	</div>

	<div class="masterstudy-instructor-courses__empty <?php echo esc_attr( empty( $initial_posts ) ? 'masterstudy-instructor-courses__empty_show' : '' ); ?>">
		<div class="masterstudy-instructor-courses__empty-block">
			<span class="masterstudy-instructor-courses__empty-icon"></span>
			<span class="masterstudy-instructor-courses__empty-text">
				<?php echo esc_html__( 'No courses yet', 'masterstudy-lms-learning-management-system' ); ?>
			</span>
		</div>
	</div>
</div>
<?php do_action( 'stm_lms_instructor_courses_end' ); ?>
