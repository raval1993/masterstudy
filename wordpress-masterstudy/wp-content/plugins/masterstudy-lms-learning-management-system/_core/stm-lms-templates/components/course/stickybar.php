<?php
/**
 * @var object $course
 * @var array $settings
 * @var array $instructor
 */

wp_enqueue_script( 'masterstudy-single-course-stickybar' );

$settings['enable_sticky_title']    = $settings['enable_sticky_title'] ?? false;
$settings['enable_sticky_teacher']  = $settings['enable_sticky_teacher'] ?? false;
$settings['enable_sticky_category'] = $settings['enable_sticky_category'] ?? false;
$settings['enable_sticky_rating']   = $settings['enable_sticky_rating'] ?? false;
$settings['enable_sticky_button']   = $settings['enable_sticky_button'] ?? false;
$settings['course_tab_reviews']     = $settings['course_tab_reviews'] ?? true;
?>

<div class="masterstudy-single-course-stickybar">
	<div class="masterstudy-single-course-stickybar__wrapper">
		<div class="masterstudy-single-course-stickybar__column">
			<?php if ( $settings['enable_sticky_title'] ) { ?>
				<div class="masterstudy-single-course-stickybar__title">
					<?php echo esc_html( $course->title ); ?>
				</div>
			<?php } ?>
			<div class="masterstudy-single-course-stickybar__row">
				<?php
				if ( $settings['enable_sticky_teacher'] ) {
					STM_LMS_Templates::show_lms_template(
						'components/course/instructor',
						array(
							'course'        => $course,
							'instructor'    => $instructor,
							'without_title' => true,
						)
					);
				}
				if ( $settings['enable_sticky_category'] ) {
					STM_LMS_Templates::show_lms_template(
						'components/course/categories',
						array(
							'term_ids' => $course->category,
							'only_one' => true,
							'inline'   => true,
						)
					);
				}
				?>
			</div>
		</div>
		<div class="masterstudy-single-course-stickybar__row">
			<?php
			if ( $settings['course_tab_reviews'] && $settings['enable_sticky_rating'] && ( ! empty( $course->marks ) || ! empty( $course->udemy_marks ) ) ) {
				STM_LMS_Templates::show_lms_template( 'components/course/rating', array( 'course' => $course ) );
			}
			if ( $settings['enable_sticky_button'] ) {
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'id'    => 'masterstudy-single-course-stickybar-button',
						'title' => __( 'Get this Course', 'masterstudy-lms-learning-management-system' ),
						'link'  => '#',
						'style' => 'primary',
						'size'  => 'sm',
					)
				);
			}
			?>
		</div>
	</div>
</div>
