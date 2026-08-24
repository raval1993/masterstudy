<?php
/**
 * @var array $course
 * @var boolean $public
 * @var boolean $reviews
 * @var boolean $student_card
 * @var boolean $instructor_card
 * @var boolean $wishlist
 */

wp_enqueue_style( 'masterstudy-course-card' );

$public              = (bool) ( $public ?? false );
$student_card        = (bool) ( $student_card ?? false );
$instructor_card     = (bool) ( $instructor_card ?? false );
$wishlist            = (bool) ( $wishlist ?? false );
$reviews             = (bool) ( $reviews ?? false );
$course              = STM_LMS_Courses::get_course_submetas( $course );
$is_featured_enabled = STM_LMS_Options::get_option( 'enable_featured_courses', true );

if ( $course['lazyload'] ) {
	wp_enqueue_script( 'masterstudy_lazysizes' );
	wp_enqueue_style( 'masterstudy_lazysizes' );
}
?>

<div class="masterstudy-course-card" <?php echo $instructor_card ? 'data-course-id="' . esc_attr( $course['id'] ) . '"' : ''; ?>>
	<div class="masterstudy-course-card__wrapper">
		<?php if ( ! empty( $course['featured'] ) && $is_featured_enabled ) { ?>
			<div class="masterstudy-course-card__featured">
				<span><?php echo esc_html__( 'Featured', 'masterstudy-lms-learning-management-system' ); ?></span>
			</div>
			<?php
		}
		if ( ! empty( $course['current_status'] ) ) {
			?>
			<div class="masterstudy-course-card__status <?php echo esc_attr( $course['current_status']['status'] ?? '' ); ?>">
				<span><?php echo esc_html( $course['current_status']['label'] ); ?></span>
			</div>
		<?php } ?>
		<a href="<?php echo esc_url( $course['url'] ); ?>" class="masterstudy-course-card__image-link">
			<?php echo wp_kses_post( masterstudy_get_image( $course['id'], $course['lazyload'], 'masterstudy-course-card__image', $course['img_width'], $course['img_height'] ) ); ?>
		</a>
		<div class="masterstudy-course-card__info">
			<?php if ( ! empty( $course['terms'] ) ) { ?>
				<span class="masterstudy-course-card__info-category">
					<a href="<?php echo esc_url( STM_LMS_Course::courses_page_url() . '?terms[]=' . $course['terms']->term_id . '&category[]=' . $course['terms']->term_id ); ?>">
						<?php echo esc_html( $course['terms']->name ); ?>
					</a>
				</span>
			<?php } ?>
				<a href="<?php echo esc_url( $course['url'] ); ?>" class="masterstudy-course-card__info-title">
					<h3><?php echo esc_html( $course['post_title'] ); ?></h3>
				</a>
			<?php
			if ( isset( $course['progress'] ) && $course['progress'] > 0 && ! $public && ! $instructor_card ) {
				STM_LMS_Templates::show_lms_template( 'components/course/card/global/progress-bar', array( 'course' => $course ) );
			}
			?>
			<div class="masterstudy-course-card__meta">
				<?php
				STM_LMS_Templates::show_lms_template( 'components/course/card/global/lectures', array( 'course' => $course ) );
				if ( ! empty( $course['duration_info'] ) ) {
					STM_LMS_Templates::show_lms_template( 'components/course/card/global/duration', array( 'course' => $course ) );
				}
				?>
			</div>
			<?php
			if ( $course['availability'] && is_ms_lms_addon_enabled( 'coming_soon' ) ) {
				STM_LMS_Templates::show_lms_template(
					'global/coming_soon',
					array(
						'course_id' => $course['id'],
						'mode'      => 'card',
					),
				);
			} else {
				?>
				<div class="masterstudy-course-card__bottom">
					<?php
					if ( $reviews ) {
						STM_LMS_Templates::show_lms_template( 'components/course/card/global/rating', array( 'course' => $course ) );
					}
					if ( $student_card ) {
						if ( ! $course['membership_expired'] && ! $course['membership_inactive'] ) {
							STM_LMS_Templates::show_lms_template(
								'components/course/card/global/start-course',
								array( 'course' => $course )
							);
						} else {
							STM_LMS_Templates::show_lms_template(
								'components/course/card/global/membership-status',
								array( 'course' => $course )
							);
						}
						STM_LMS_Templates::show_lms_template(
							'components/course/card/global/start-time',
							array( 'course' => $course )
						);
					} else {
						STM_LMS_Templates::show_lms_template( 'components/course/card/global/price', array( 'course' => $course ) );
					}

					if ( $instructor_card ) {
						STM_LMS_Templates::show_lms_template(
							'components/course/card/global/instructor-actions',
							array( 'course' => $course )
						);
					}
					?>
				</div>
			<?php } ?>
		</div>
	</div>
	<?php
	if ( ! $student_card && ! $instructor_card ) {
		STM_LMS_Templates::show_lms_template(
			'components/course/card/global/popup',
			array(
				'course'   => $course,
				'wishlist' => $wishlist,
			)
		);
	}

	if ( $instructor_card ) {
		STM_LMS_Templates::show_lms_template(
			'components/course/card/global/instructor-modal',
			array( 'course' => $course )
		);
	}
	?>
</div>
<?php
