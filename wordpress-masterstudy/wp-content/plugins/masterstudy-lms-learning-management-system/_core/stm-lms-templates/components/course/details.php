<?php
/**
 * @var object $course
 * @var string $style
 */

use MasterStudy\Lms\Repositories\CurriculumSectionRepository;
use MasterStudy\Lms\Repositories\CurriculumMaterialRepository;

$curriculum_repo = new CurriculumMaterialRepository();
$section_ids     = ( new CurriculumSectionRepository() )->get_course_section_ids( $course->id );
$lessons         = $curriculum_repo->count_by_type( $section_ids, 'stm-lessons' );
$quizzes         = $curriculum_repo->count_by_type( $section_ids, 'stm-quizzes' );
$assignments     = $curriculum_repo->count_by_type( $section_ids, 'stm-assignments' );
$meta_fields     = array();
$extra_fields    = array(
	'access_devices',
	'access_duration',
	'udemy_certificate',
	'certificate',
);
$style           = isset( $style ) ? $style : 'default';

if ( ! empty( $course->duration_info ) && ! $course->is_udemy_course ) {
	$meta_fields['duration'] = array(
		'label'      => esc_html__( 'Duration', 'masterstudy-lms-learning-management-system' ),
		'text'       => $course->duration_info,
		'icon_class' => 'masterstudy-single-course-details__icon_duration',
	);
}

if ( ! empty( $lessons ) ) {
	$meta_fields['lectures'] = array(
		'label'      => esc_html__( 'Lectures', 'masterstudy-lms-learning-management-system' ),
		'text'       => $lessons,
		'icon_class' => 'masterstudy-single-course-details__icon_lectures',
	);
}

if ( ! empty( $course->video_duration ) ) {
	$meta_fields['video'] = array(
		'label'      => esc_html__( 'Video', 'masterstudy-lms-learning-management-system' ),
		'text'       => $course->video_duration,
		'icon_class' => 'masterstudy-single-course-details__icon_video',
	);
}

if ( ! empty( $assignments ) ) {
	$meta_fields['assignments'] = array(
		'label'      => esc_html__( 'Assignments', 'masterstudy-lms-learning-management-system' ),
		'text'       => $assignments,
		'icon_class' => 'masterstudy-single-course-details__icon_assignments',
	);
}

if ( ! empty( $quizzes ) ) {
	$meta_fields['quizzes'] = array(
		'label'      => esc_html__( 'Quizzes', 'masterstudy-lms-learning-management-system' ),
		'text'       => $quizzes,
		'icon_class' => 'masterstudy-single-course-details__icon_quiz',
	);
}

if ( ! empty( $course->level ) ) {
	$levels               = STM_LMS_Helpers::get_course_levels();
	$meta_fields['level'] = array(
		'label'      => esc_html__( 'Level', 'masterstudy-lms-learning-management-system' ),
		'text'       => $levels[ $course->level ],
		'icon_class' => 'masterstudy-single-course-details__icon_level',
	);
}

if ( ! empty( $course->udemy_video ) ) {
	$meta_fields['video'] = array(
		'label'      => esc_html__( 'Video', 'masterstudy-lms-learning-management-system' ),
		'text'       => round( $course->udemy_video / 3600, 0 ) . ' ' . esc_html__( 'hours', 'masterstudy-lms-learning-management-system' ),
		'icon_class' => 'masterstudy-single-course-details__icon_video',
	);
}

if ( ! empty( $course->udemy_articles ) ) {
	$meta_fields['lectures'] = array(
		'label'      => esc_html__( 'Lectures', 'masterstudy-lms-learning-management-system' ),
		'text'       => $course->udemy_articles,
		'icon_class' => 'masterstudy-single-course-details__icon_lectures',
	);
}

if ( ! empty( $course->udemy_certificate ) ) {
	$meta_fields['udemy_certificate'] = array(
		'label'      => esc_html__( 'Certificate of Completion', 'masterstudy-lms-learning-management-system' ),
		'text'       => '',
		'icon_class' => 'masterstudy-single-course-details__icon_certificate',
	);
}

if ( ! empty( $course->certificate_info ) ) {
	$meta_fields['certificate'] = array(
		'label'      => esc_html( $course->certificate_info ),
		'text'       => '',
		'icon_class' => 'masterstudy-single-course-details__icon_certificate',
	);
}

if ( ! empty( $course->access_duration ) ) {
	$meta_fields['access_duration'] = array(
		'label'      => esc_html( $course->access_duration ),
		'text'       => '',
		'icon_class' => 'masterstudy-single-course-details__icon_access-duration',
	);
}

if ( ! empty( $course->access_devices ) ) {
	$meta_fields['access_devices'] = array(
		'label'      => esc_html( $course->access_devices ),
		'text'       => '',
		'icon_class' => 'masterstudy-single-course-details__icon_access-devices',
	);
}

if ( ! empty( $meta_fields ) ) {
	?>
	<div class="masterstudy-single-course-details masterstudy-single-course-details_<?php echo esc_attr( $style ); ?>">
		<span class="masterstudy-single-course-details__title">
			<?php echo esc_html__( 'Course details', 'masterstudy-lms-learning-management-system' ); ?>
		</span>
		<?php foreach ( $meta_fields as $meta_field_key => $meta_field ) { ?>
			<div class="masterstudy-single-course-details__item">
				<div class="masterstudy-single-course-details__icon-wrapper">
					<span class="masterstudy-single-course-details__icon <?php echo esc_attr( $meta_field['icon_class'] ); ?>"></span>
				</div>
				<div class="masterstudy-single-course-details__info">
					<span class="masterstudy-single-course-details__name">
						<?php echo esc_html( $meta_field['label'] ); ?>
					</span>
					<?php if ( 'row' === $style && ! in_array( $meta_field_key, $extra_fields, true ) ) { ?>
						<span class="masterstudy-single-course-details__separator">:</span>
					<?php } ?>
					<span class="masterstudy-single-course-details__quantity">
						<?php echo esc_html( $meta_field['text'] ); ?>
					</span>
				</div>
			</div>
		<?php } ?>
	</div>
	<?php
}
