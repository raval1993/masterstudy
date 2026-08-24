<?php
/**
 * @var object $course
 * @var array $course_preview
 * @var integer $user_id
 * @var boolean $with_image
 * @var string $style
 * @var string $mode
 * @var boolean $grades_enabled
 * @var string $grades_display
 *
 * masterstudy-single-course-tabs__item_active - for item active state
 * masterstudy-single-course-tabs_style-default|underline - for tabs style change
 */

$grades_enabled = $grades_enabled ?? false;
$grades_display = $grades_display ?? 'tab';
$course_tabs    = array(
	'description'  => esc_html__( 'Description', 'masterstudy-lms-learning-management-system' ),
	'curriculum'   => esc_html__( 'Curriculum', 'masterstudy-lms-learning-management-system' ),
	'faq'          => esc_html__( 'FAQ', 'masterstudy-lms-learning-management-system' ),
	'announcement' => esc_html__( 'Notice', 'masterstudy-lms-learning-management-system' ),
	'reviews'      => esc_html__( 'Reviews', 'masterstudy-lms-learning-management-system' ),
);

if ( $grades_enabled && 'tab' === $grades_display ) {
	$course_tabs['grades'] = esc_html__( 'Grade', 'masterstudy-lms-learning-management-system' );
}

$course_tabs = apply_filters( 'stm_lms_course_tabs', $course_tabs, $course->id );
$active      = STM_LMS_Options::get_option( 'course_page_tab', 'description' );

if ( isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ) {
	$active = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
}

$tabs_length = count( $course_tabs );
$style       = isset( $style ) ? $style : 'default';
$with_image  = isset( $with_image ) ? $with_image : false;

if ( $tabs_length > 0 ) { ?>
	<ul class="masterstudy-single-course-tabs <?php echo esc_attr( 'masterstudy-single-course-tabs_style-' . $style ); ?>">
		<?php foreach ( $course_tabs as $slug => $name ) { ?>
			<li class="masterstudy-single-course-tabs__item <?php echo ( $slug === $active ) ? 'masterstudy-single-course-tabs__item_active' : ''; ?>" data-id="<?php echo esc_attr( $slug ); ?>">
				<?php echo wp_kses_post( $name ); ?>
			</li>
		<?php } ?>
	</ul>
<?php } ?>

<div class="masterstudy-single-course-tabs__content">
	<?php foreach ( $course_tabs as $slug => $name ) { ?>
		<div class="masterstudy-single-course-tabs__container <?php echo ( $slug === $active ) ? 'masterstudy-single-course-tabs__container_active' : ''; ?> " data-id="<?php echo esc_attr( $slug ); ?>">
			<?php
			if ( 'curriculum' === $slug ) {
				$slug = 'curriculum/main';
			}

			STM_LMS_Templates::show_lms_template(
				'components/course/' . $slug,
				array(
					'course'         => $course,
					'course_preview' => $course_preview ?? '',
					'user_id'        => $user_id,
					'with_image'     => $with_image,
					'mode'           => $mode ?? null,
				)
			);
			?>
		</div>
	<?php } ?>
</div>
