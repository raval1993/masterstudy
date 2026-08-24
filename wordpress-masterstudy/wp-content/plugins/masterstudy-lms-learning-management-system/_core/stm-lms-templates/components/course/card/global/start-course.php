<?php
/**
 * @var array $course
 */

use \MasterStudy\Lms\Repositories\CurriculumMaterialRepository;

$material_ids = ( new CurriculumMaterialRepository() )->get_course_materials( $course['id'] );
$prev_lesson  = null;

if ( ! empty( $material_ids ) ) {
	if ( in_array( $course['current_lesson_id'], $material_ids, true ) ) {
		$current_lesson_id = array_search( $course['current_lesson_id'], $material_ids, true );
		$prev_lesson       = $material_ids[ $current_lesson_id - 1 ] ?? null;
	}
}

if ( empty( $course['current_lesson_url'] ) ) {
	return;
}

$is_completed = isset( $course['progress'] ) && 100 === (int) $course['progress'];

STM_LMS_Templates::show_lms_template(
	'components/button',
	array(
		'title' => $is_completed
			? esc_html__( 'Completed', 'masterstudy-lms-learning-management-system' )
			: ( $prev_lesson ? esc_html__( 'Continue', 'masterstudy-lms-learning-management-system' ) : esc_html__( 'Start course', 'masterstudy-lms-learning-management-system' ) ),
		'link'  => esc_url( $course['current_lesson_url'] ),
		'style' => 'primary',
		'size'  => 'sm',
	)
);

