<?php
/**
 * @var $student_id
 */

$student = STM_LMS_User::get_current_user( $student_id, false, true );

if ( empty( $student['id'] ) ) {
	return;
};

STM_LMS_Templates::show_lms_template( 'header' );

$settings       = get_option( 'stm_lms_settings' );
$profile_active = $settings['student_public_profile'] ?? true;
$profile_style  = $_GET['public'] ?? $settings['student_public_profile_style'] ?? 'compact';
$show_stats     = $settings['student_stats_public_profile'] ?? true;

if ( ! $profile_active ) {
	echo esc_html__( 'This page does not exist.', 'masterstudy-lms-learning-management-system' );

	return;
}

wp_enqueue_style( 'masterstudy-student-public-account' );
wp_enqueue_script( 'masterstudy-student-public-account' );
wp_localize_script(
	'masterstudy-student-public-account',
	'student_data',
	array(
		'user'             => $student_id,
		'user_login'       => $student['login'],
		'show_stats'       => $show_stats,
		'courses_per_page' => 9,
	)
);

$args        = array(
	'user'   => $student_id,
	'pp'     => 9,
	'page'   => 1,
	'status' => 'completed',
);
$courses     = STM_LMS_Courses::get_student_courses( $args );
$logged_in   = is_user_logged_in();
$user_info   = get_userdata( $student['id'] );
$stats_types = array(
	array(
		'key'        => 'completed_courses',
		'is_visible' => true,
	),
	array(
		'key'        => 'groups',
		'is_visible' => is_ms_lms_addon_enabled( 'enterprise_courses' ),
	),
	array(
		'key'        => 'certificates',
		'is_visible' => is_ms_lms_addon_enabled( 'certificate_builder' ),
	),
	array(
		'key'        => 'assignments',
		'is_visible' => is_ms_lms_addon_enabled( 'assignments' ),
	),
	array(
		'key'        => 'quizzes',
		'is_visible' => true,
	),
	array(
		'key'        => 'points',
		'is_visible' => is_ms_lms_addon_enabled( 'point_system' ),
	),
);

$stats_types = array_filter(
	$stats_types,
	function( $type ) {
		return $type['is_visible'];
	}
);

STM_LMS_Templates::show_lms_template(
	'components/modals/message',
	array(
		'username'  => $student['login'],
		'user_id'   => $student_id,
		'logged_in' => $logged_in,
	)
);
?>

<div class="masterstudy-student-public <?php echo esc_attr( 'masterstudy-student-public_' . $profile_style ); ?>">
	<div class="masterstudy-student-public__profile">
		<div class="masterstudy-student-public__profile-container">
			<div class="masterstudy-student-public__user">
				<div class="masterstudy-student-public__avatar">
					<?php
					if ( ! empty( $student['avatar'] ) ) {
						echo wp_kses_post( $student['avatar'] );
					}
					?>
				</div>
				<div class="masterstudy-student-public__personal">
					<div class="masterstudy-student-public__name">
						<?php echo esc_html( $student['login'] ); ?>
					</div>
					<div class="masterstudy-student-public__member">
						<?php
						echo esc_html__( 'Member since', 'masterstudy-lms-learning-management-system' );
						echo esc_html( ' ' . date_i18n( 'F Y', strtotime( $user_info->user_registered ) ) );
						?>
					</div>
				</div>
				<div class="masterstudy-student-public__actions">
					<?php
					$button_args = array(
						'title' => esc_html__( 'Send message', 'masterstudy-lms-learning-management-system' ),
						'link'  => '#',
						'style' => 'primary',
						'size'  => 'sm',
						'id'    => 'masterstudy-student-message-send',
					);

					if ( ! $logged_in ) {
						$button_args['login'] = 'login';
					}

					STM_LMS_Templates::show_lms_template( 'components/button', $button_args );
					if ( 'extended' === $profile_style ) {
						?>
						<div class="masterstudy-student-public__details">
							<span class="masterstudy-student-public__details-show">
								<?php echo esc_html__( 'Show Details', 'masterstudy-lms-learning-management-system' ); ?>
							</span>
							<span class="masterstudy-student-public__details-hide">
								<?php echo esc_html__( 'Hide Details', 'masterstudy-lms-learning-management-system' ); ?>
							</span>
						</div>
					<?php } ?>
				</div>
			</div>
			<?php if ( $show_stats ) { ?>
				<div class="masterstudy-student-public__stats">
					<?php
					foreach ( $stats_types as $stats ) {
						STM_LMS_Templates::show_lms_template(
							'components/statistics-block',
							array(
								'type' => $stats['key'],
							)
						);
					}
					?>
				</div>
				<?php
			}
			STM_LMS_Templates::show_lms_template( 'components/form-builder-fields/public-fields', array( 'user_id' => $student_id ) );
			?>
		</div>
	</div>
	<div class="masterstudy-student-public__content">
		<div class="masterstudy-student-public__list-header <?php echo esc_attr( ! empty( $courses['posts'] ) ? 'masterstudy-student-public__list-header_show' : '' ); ?>">
			<div class="masterstudy-student-public__list-header-title">
				<?php echo esc_html__( 'Completed courses', 'masterstudy-lms-learning-management-system' ); ?>
			</div>
		</div>
		<div class="masterstudy-student-public__list">
			<?php
			if ( ! empty( $courses['posts'] ) ) {
				foreach ( $courses['posts'] as $course ) {
					STM_LMS_Templates::show_lms_template(
						'components/course/student-card',
						array(
							'course'  => $course,
							'user_id' => $student_id,
						)
					);
				}
			}
			?>
		</div>
		<div class="masterstudy-student-public__loader">
			<div class="masterstudy-student-public__loader-body"></div>
		</div>
		<div class="masterstudy-student-public__empty <?php echo esc_attr( empty( $courses['posts'] ) ? 'masterstudy-student-public__empty_show' : '' ); ?>">
			<div class="masterstudy-student-public__empty-block">
				<span class="masterstudy-student-public__empty-icon"></span>
				<span class="masterstudy-student-public__empty-text">
					<?php echo esc_html__( 'Nothing to show yet', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
			</div>
		</div>
		<div class="masterstudy-student-public__list-pagination">
			<?php
			if ( ! empty( $courses ) && $courses['total_pages'] > 0 ) {
				STM_LMS_Templates::show_lms_template(
					'components/pagination',
					array(
						'max_visible_pages' => 5,
						'total_pages'       => $courses['total_pages'],
						'current_page'      => 1,
						'dark_mode'         => false,
						'is_queryable'      => false,
						'done_indicator'    => false,
						'is_api'            => true,
					)
				);
			}
			?>
		</div>
	</div>
</div>
<?php
STM_LMS_Templates::show_lms_template( 'footer' );
