<?php
function masterstudy_get_elementor_course_data( $course_id ) {
	global $masterstudy_elementor_course_data;

	if ( ! isset( $masterstudy_elementor_course_data ) || ! is_array( $masterstudy_elementor_course_data ) ) {
		$masterstudy_elementor_course_data = array();
	}

	if ( empty( $course_id ) ) {
		return array();
	}

	$course_id = intval( $course_id );

	if ( isset( $masterstudy_elementor_course_data[ $course_id ] ) ) {
		return $masterstudy_elementor_course_data[ $course_id ];
	}

	$course_data                                     = apply_filters( 'masterstudy_course_page_header', null, $course_id );
	$masterstudy_elementor_course_data[ $course_id ] = ! empty( $course_data ) ? $course_data : array();

	return $masterstudy_elementor_course_data[ $course_id ];
}
