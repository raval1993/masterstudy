<?php
/**
 * @var array $current_user
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use MasterStudy\Lms\Repositories\StudentsRepository;

wp_enqueue_style( 'masterstudy-account-enrolled-courses' );
wp_enqueue_script( 'masterstudy-account-enrolled-courses' );
wp_enqueue_script( 'stm-lms-countdown' );

if ( is_ms_lms_addon_enabled( 'coming_soon' ) ) {
	stm_lms_register_style( 'coming_soon/coming_soon' );
	wp_enqueue_style( 'masterstudy-countdown' );
	wp_enqueue_script( 'masterstudy-countdown' );
}

$is_pro_plus = STM_LMS_Helpers::is_pro_plus();
$settings    = get_option( 'stm_lms_settings', array() );
$student_id  = ! empty( $current_user['id'] ) ? (int) $current_user['id'] : 0;

$settings['student_reports']    = $settings['student_reports'] ?? true;
$settings['course_tab_reviews'] = $settings['course_tab_reviews'] ?? true;

$course_bundle   = is_ms_lms_addon_enabled( 'course_bundle' );
$enterprise      = is_ms_lms_addon_enabled( 'enterprise_courses' );
$certificate     = is_ms_lms_addon_enabled( 'certificate_builder' );
$point           = is_ms_lms_addon_enabled( 'point_system' );
$point_label     = esc_html__( 'Points', 'masterstudy-lms-learning-management-system' );
$not_empty_stats = ( $settings['course_tab_reviews'] || $point || $certificate || $enterprise || $course_bundle );

if ( $point && class_exists( 'STM_LMS_Point_System' ) ) {
	$point_settings = get_option( 'stm_lms_point_system_settings', array() );
	$point_label    = ! empty( $point_settings['point_label'] ) ? $point_settings['point_label'] : $point_label;
}

$stats = array(
	'reviews'          => 0,
	'courses_statuses' => array(
		'summary'     => 0,
		'completed'   => 0,
		'in_progress' => 0,
		'failed'      => 0,
	),
	'courses_types'    => array(
		'bundle_count'     => 0,
		'enterprise_count' => 0,
	),
	'total_points'     => 0,
	'certificates'     => 0,
);

$tab_counts = array(
	'summary'     => 0,
	'completed'   => 0,
	'in_progress' => 0,
	'failed'      => 0,
);

$repo = new StudentsRepository();

$tab_counts = (array) $repo->student_courses_statuses( $student_id );
$tab_counts = array_merge(
	array(
		'summary'     => 0,
		'completed'   => 0,
		'in_progress' => 0,
		'failed'      => 0,
	),
	array_map( 'intval', $tab_counts )
);

if ( $student_id && $is_pro_plus && $settings['student_reports'] ) {
	$completed_courses      = $repo->student_completed_courses( $student_id, array( 'course_id' ), -1 );
	$stats['courses']       = (int) ( $tab_counts['summary'] ?? 0 );
	$stats['reviews']       = (int) $repo->student_reviews_count( $student_id );
	$stats['courses_types'] = (array) $repo->student_courses_types( $student_id );
	$stats['total_points']  = (int) $repo->student_total_points( $student_id );
	$stats['certificates']  = (int) $repo->student_certificates_count( $completed_courses );
}

$status_tabs = array(
	'all'         => array(
		'label' => esc_html__( 'All', 'masterstudy-lms-learning-management-system' ),
		'value' => (int) ( $tab_counts['summary'] ?? 0 ),
	),
	'completed'   => array(
		'label' => esc_html__( 'Completed', 'masterstudy-lms-learning-management-system' ),
		'value' => (int) ( $tab_counts['completed'] ?? 0 ),
	),
	'in_progress' => array(
		'label' => esc_html__( 'In progress', 'masterstudy-lms-learning-management-system' ),
		'value' => (int) ( $tab_counts['in_progress'] ?? 0 ),
	),
	'failed'      => array(
		'label' => esc_html__( 'Failed', 'masterstudy-lms-learning-management-system' ),
		'value' => (int) ( $tab_counts['failed'] ?? 0 ),
	),
);

$courses_data = STM_LMS_User::get_user_courses_data( 1, 'all' );
$courses_data = apply_filters( 'stm_lms_get_user_courses_filter', $courses_data );

$initial_total_pages = ! empty( $courses_data['total_pages'] ) ? (int) $courses_data['total_pages'] : 0;
$initial_posts       = ! empty( $courses_data['posts'] ) ? (array) $courses_data['posts'] : array();

if ( $is_pro_plus && $settings['student_reports'] ) : ?>
	<div class="masterstudy-enrolled-courses-sorting">
		<div class="masterstudy-enrolled-courses-sorting__block-wrapper">
			<div class="masterstudy-enrolled-courses-sorting__block">
				<div class="masterstudy-enrolled-courses-sorting__block-icon masterstudy-enrolled-courses-sorting__block-icon_courses"></div>
				<div class="masterstudy-enrolled-courses-sorting__block-content">
					<span class="masterstudy-enrolled-courses-sorting__block-title">
						<?php echo esc_html__( 'Courses', 'masterstudy-lms-learning-management-system' ); ?>
					</span>
					<span class="masterstudy-enrolled-courses-sorting__block-value">
						<?php echo esc_html( (string) ( $stats['courses'] ?? 0 ) ); ?>
					</span>
				</div>
			</div>
		</div>
		<?php if ( $course_bundle ) : ?>
			<div class="masterstudy-enrolled-courses-sorting__block-wrapper">
				<div class="masterstudy-enrolled-courses-sorting__block">
					<div class="masterstudy-enrolled-courses-sorting__block-icon masterstudy-enrolled-courses-sorting__block-icon_bundles"></div>
					<div class="masterstudy-enrolled-courses-sorting__block-content">
						<span class="masterstudy-enrolled-courses-sorting__block-title">
							<?php echo esc_html__( 'Bundles', 'masterstudy-lms-learning-management-system' ); ?>
						</span>
						<span class="masterstudy-enrolled-courses-sorting__block-value">
							<?php echo esc_html( (string) ( $stats['courses_types']['bundle_count'] ?? 0 ) ); ?>
						</span>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $enterprise ) : ?>
			<div class="masterstudy-enrolled-courses-sorting__block-wrapper">
				<div class="masterstudy-enrolled-courses-sorting__block">
					<div class="masterstudy-enrolled-courses-sorting__block-icon masterstudy-enrolled-courses-sorting__block-icon_groups"></div>
					<div class="masterstudy-enrolled-courses-sorting__block-content">
						<span class="masterstudy-enrolled-courses-sorting__block-title">
							<?php echo esc_html__( 'Groups', 'masterstudy-lms-learning-management-system' ); ?>
						</span>
						<span class="masterstudy-enrolled-courses-sorting__block-value">
							<?php echo esc_html( (string) ( $stats['courses_types']['enterprise_count'] ?? 0 ) ); ?>
						</span>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $settings['course_tab_reviews'] ) : ?>
			<div class="masterstudy-enrolled-courses-sorting__block-wrapper">
				<div class="masterstudy-enrolled-courses-sorting__block">
					<div class="masterstudy-enrolled-courses-sorting__block-icon masterstudy-enrolled-courses-sorting__block-icon_reviews"></div>
					<div class="masterstudy-enrolled-courses-sorting__block-content">
						<span class="masterstudy-enrolled-courses-sorting__block-title">
							<?php echo esc_html__( 'Reviews', 'masterstudy-lms-learning-management-system' ); ?>
						</span>
						<span class="masterstudy-enrolled-courses-sorting__block-value">
							<?php echo esc_html( (string) ( $stats['reviews'] ?? 0 ) ); ?>
						</span>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $certificate ) : ?>
			<div class="masterstudy-enrolled-courses-sorting__block-wrapper">
				<div class="masterstudy-enrolled-courses-sorting__block">
					<div class="masterstudy-enrolled-courses-sorting__block-icon masterstudy-enrolled-courses-sorting__block-icon_certificates"></div>
					<div class="masterstudy-enrolled-courses-sorting__block-content">
						<span class="masterstudy-enrolled-courses-sorting__block-title">
							<?php echo esc_html__( 'Certificates', 'masterstudy-lms-learning-management-system' ); ?>
						</span>
						<span class="masterstudy-enrolled-courses-sorting__block-value">
							<?php echo esc_html( (string) ( $stats['certificates'] ?? 0 ) ); ?>
						</span>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $point ) : ?>
			<div class="masterstudy-enrolled-courses-sorting__block-wrapper">
				<div class="masterstudy-enrolled-courses-sorting__block">
					<div class="masterstudy-enrolled-courses-sorting__block-icon masterstudy-enrolled-courses-sorting__block-icon_points"></div>
					<div class="masterstudy-enrolled-courses-sorting__block-content">
						<span class="masterstudy-enrolled-courses-sorting__block-title">
							<?php echo esc_html( $point_label ); ?>
						</span>
						<span class="masterstudy-enrolled-courses-sorting__block-value">
							<?php echo esc_html( (string) ( $stats['total_points'] ?? 0 ) ); ?>
						</span>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>

<div class="masterstudy-enrolled-courses-tabs">
	<h3 class="masterstudy-enrolled-courses__title">
		<?php echo esc_html__( 'Enrolled courses', 'masterstudy-lms-learning-management-system' ); ?>
	</h3>
	<div class="masterstudy-enrolled-courses-tabs__blocks">
		<?php
		foreach ( $status_tabs as $key => $status_tab ) :
			$is_active = ( 'all' === $key );
			?>
			<div
				class="masterstudy-enrolled-courses-tabs__block <?php echo $is_active ? 'masterstudy-enrolled-courses-tabs__block_active' : ''; ?>"
				data-status="<?php echo esc_attr( $key ); ?>"
			>
				<div class="masterstudy-enrolled-courses-tabs__block-content">
					<span class="masterstudy-enrolled-courses-tabs__block-title">
						<?php echo esc_html( $status_tab['label'] ); ?>
					</span>
					<span class="masterstudy-enrolled-courses-tabs__block-value" data-status="<?php echo esc_attr( $key ); ?>">
						<?php echo esc_html( (string) $status_tab['value'] ); ?>
					</span>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>

<div class="masterstudy-enrolled-courses">
	<div class="masterstudy-enrolled-courses__list">
		<?php
		if ( ! empty( $initial_posts ) ) :
			foreach ( $initial_posts as $course ) :
				STM_LMS_Templates::show_lms_template(
					'components/course/card/default',
					array(
						'course'       => $course,
						'public'       => false,
						'reviews'      => (bool) $settings['course_tab_reviews'],
						'student_card' => true,
					)
				);
			endforeach;
		endif;
		?>
	</div>

	<?php if ( ! empty( $initial_posts ) && $initial_total_pages > 1 ) { ?>
		<div class="masterstudy-enrolled-courses__pagination">
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/pagination',
				array(
					'max_visible_pages' => 5,
					'total_pages'       => $initial_total_pages,
					'current_page'      => 1,
					'dark_mode'         => false,
					'is_queryable'      => false,
					'done_indicator'    => false,
					'is_api'            => true,
					'thin'              => false,
				)
			);
			?>
		</div>
	<?php } ?>

	<div class="masterstudy-enrolled-courses__loader">
		<div class="masterstudy-enrolled-courses__loader-body"></div>
	</div>

	<div class="masterstudy-enrolled-courses__empty <?php echo esc_attr( empty( $initial_posts ) ? 'masterstudy-enrolled-courses__empty_show' : '' ); ?>">
		<div class="masterstudy-enrolled-courses__empty-block">
			<span class="masterstudy-enrolled-courses__empty-icon"></span>
			<span class="masterstudy-enrolled-courses__empty-text">
				<?php echo esc_html__( 'No courses yet', 'masterstudy-lms-learning-management-system' ); ?>
			</span>
			<a href="<?php echo esc_url( STM_LMS_Course::courses_page_url() ); ?>" target="_blank" class="masterstudy-enrolled-courses__empty-button">
				<?php echo esc_html__( 'Explore courses', 'masterstudy-lms-learning-management-system' ); ?>
			</a>
		</div>
	</div>
</div>
