<?php

/**
 * @var object $course
 * @var integer $user_id
 * @var boolean $expired_popup
 * @var boolean $is_coming_soon
 */

$course_expiration_days  = STM_LMS_Course::get_course_expiration_days( $course->id );
$is_course_time_expired  = STM_LMS_Course::is_course_time_expired( $user_id, $course->id );
$user_course             = STM_LMS_Course::get_user_course( $user_id, $course->id );
$has_course_time_limit   = get_transient( 'masterstudy_lms_course_upcoming_time_expiration' . $course->id );
$should_reset_start_time = ! empty( $course->coming_soon_date ) && $has_course_time_limit;

if ( $is_coming_soon ) {
	$user_course = array();
	set_transient( 'masterstudy_lms_course_upcoming_time_expiration' . $course->id, true );
} elseif ( $should_reset_start_time ) {
	stm_lms_update_start_time_in_user_course( $user_id, $course->id );
	set_transient( 'masterstudy_lms_course_upcoming_time_expiration' . $course->id, false );
}

$course_duration_time = STM_LMS_Course::get_course_duration_time( $course->id );
$course_end_time      = ! empty( $course_duration_time ) && ! empty( $user_course['start_time'] )
	? intval( $user_course['start_time'] ) + $course_duration_time
	: null;

$expired_popup = ( isset( $expired_popup ) ) ? $expired_popup : true;

wp_localize_script(
	'masterstudy-single-course-components',
	'expired_data',
	array(
		'id'           => $course->id,
		'load_scripts' => $expired_popup && ! $should_reset_start_time && $is_course_time_expired && ! empty( $user_course ) && ! empty( $course_expiration_days ),
	)
);

if ( empty( $user_course ) && ! empty( $course_expiration_days ) ) { ?>
	<div class="masterstudy-single-course-expired">
		<?php
		printf(
			wp_kses_post(
				/* translators: %s Course available days */
				_n(
					'Course available for <strong>%s day</strong>',
					'Course available for <strong>%s days</strong>',
					$course_expiration_days,
					'masterstudy-lms-learning-management-system'
				),
			),
			esc_html( $course_expiration_days )
		);
		?>
	</div>
	<?php
} elseif ( ! $is_course_time_expired && ! empty( $user_course ) && ! empty( $course_expiration_days ) ) {
	$time_left = $course_end_time - time();
	$days_left = floor( $time_left / DAY_IN_SECONDS );
	?>
	<div class="masterstudy-single-course-expired">
		<?php
		wp_enqueue_script( 'stm-lms-countdown' );

		if ( $days_left < 1 ) {
			printf(
				/* translators: %s Time Left */
				esc_html__( 'Course expires in: %s', 'masterstudy-lms-learning-management-system' ),
				wp_kses_post( "<strong><span data-lms-timer='{$time_left}'></span></strong>" )
			);
		} else {
			printf(
				wp_kses_post(
				/* translators: %s Course available days */
					_n(
						'Course expires in: <strong>%s day</strong>',
						'Course expires in: <strong>%s days</strong>',
						$days_left,
						'masterstudy-lms-learning-management-system'
					),
				),
				esc_html( $days_left )
			);
		}
		?>
	</div>
	<?php
} elseif ( $is_course_time_expired && ! empty( $user_course ) && ! empty( $course_expiration_days ) ) {
	?>
	<div class="masterstudy-single-course-expired">
		<?php
		printf(
			wp_kses_post(
				/* translators: %s Course available days */
				_n(
					'Course available for <strong>%s day</strong>',
					'Course available for <strong>%s days</strong>',
					$course_expiration_days,
					'masterstudy-lms-learning-management-system'
				),
			),
			esc_html( $course_expiration_days )
		);
		?>
	</div>
	<?php if ( $expired_popup && ! $should_reset_start_time ) { ?>
		<div class="masterstudy-single-course-expired-popup" style="display:none;">
			<div class="masterstudy-single-course-expired-popup__wrapper">
				<div class="masterstudy-single-course-expired-popup__container">
					<div class="masterstudy-single-course-expired-popup__image">
						<?php echo get_the_post_thumbnail( $course->id, 'img-480-380' ); ?>
					</div>
					<div class="masterstudy-single-course-expired-popup__title">
						<?php echo esc_html( get_the_title( $course->id ) ); ?>
					</div>
					<div class="masterstudy-single-course-expired-popup__notice">
						<?php esc_html_e( 'Course has expired', 'masterstudy-lms-learning-management-system' ); ?>
					</div>
					<div class="masterstudy-single-course-expired-popup__date">
						<?php
						printf(
							/* translators: %s Date of expiry */
							esc_html__( 'Date of expiry: %s', 'masterstudy-lms-learning-management-system' ),
							esc_html( date_i18n( 'Y-m-d g:i', $course_end_time ) )
						);
						?>
					</div>
					<div class="masterstudy-single-course-expired-popup__cta">
						<?php
						STM_LMS_Templates::show_lms_template(
							'components/button',
							array(
								'title' => __( 'Got it', 'masterstudy-lms-learning-management-system' ),
								'link'  => '#',
								'style' => 'primary',
								'size'  => 'sm',
							)
						);
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
