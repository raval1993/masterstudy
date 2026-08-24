<?php
/**
 * @var $course
 */
?>

<div class="masterstudy-course-card__popup">
	<a href="<?php echo esc_url( $course['url'] ); ?>" class="masterstudy-course-card__popup-title">
		<h3><?php echo esc_html( $course['post_title'] ); ?></h3>
	</a>
	<div class="masterstudy-course-card__popup-excerpt">
		<?php echo wp_kses_post( stm_lms_minimize_word( strip_shortcodes( $course['post_excerpt'] ), 130, '...' ) ); ?>
	</div>
	<div class="masterstudy-course-card__popup-meta">
		<?php
		STM_LMS_Templates::show_lms_template( 'components/course/card/global/level', array( 'course' => $course ) );
		STM_LMS_Templates::show_lms_template( 'components/course/card/global/lectures', array( 'course' => $course ) );
		if ( ! empty( $course['duration_info'] ) ) {
			STM_LMS_Templates::show_lms_template( 'components/course/card/global/duration', array( 'course' => $course ) );
		}
		?>
	</div>
	<div class="masterstudy-course-card__popup-button-wrapper">
		<a href="<?php echo esc_url( $course['url'] ); ?>" class="masterstudy-course-card__popup-button">
			<span><?php esc_html_e( 'Preview this course', 'masterstudy-lms-learning-management-system' ); ?></span>
			<?php if ( 'on' === $course['is_trial'] ) { ?>
				<small><?php esc_html_e( 'Free Lesson(s) Offer', 'masterstudy-lms-learning-management-system' ); ?></small>
			<?php } ?>
		</a>
		<div class="masterstudy-course-card__popup-bottom">
			<?php
			if ( ( ! empty( $wishlist ) ) ) {
				?>
				<div class="masterstudy-course-card__popup-wishlist">
					<?php STM_LMS_Templates::show_lms_template( 'global/wish-list', array( 'course_id' => $course['id'] ) ); ?>
				</div>
				<?php
			}
			if ( ! STM_LMS_Helpers::masterstudy_lms_is_course_coming_soon( $course['id'] ) ) {
				STM_LMS_Templates::show_lms_template( 'components/course/card/global/popup-price', array( 'course' => $course ) );
			}
			?>
		</div>
	</div>
</div>
