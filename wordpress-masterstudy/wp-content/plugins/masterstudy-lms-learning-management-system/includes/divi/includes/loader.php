<?php

if ( ! class_exists( 'ET_Builder_Element' ) ) {
	return;
}
require_once __DIR__ . '/modules/BaseModule.php';
$module_files = glob( __DIR__ . '/modules/*/*.php' );

// Load custom  Masterstudy LMS Divi Builder modules
foreach ( (array) $module_files as $module_file ) {
	if ( $module_file && preg_match( "/\/modules\/\b([^\/]+)\/\\1\.php$/", $module_file ) ) {
		if ( defined( 'DMSLMS_D5_NATIVE_BOOTSTRAPPED' ) ) {
			$d5_native_modules = array(
				'CoursesSearchbox.php',
				'CoursesGrid.php',
				'CoursesCarousel.php',
				'RecentCourses.php',
				'CoursesCategories.php',
				'SingleCourseCarousel.php',
				'InstructorsCarousel.php',
				'FeaturedTeacher.php',
				'GoogleClassrooms.php',
				'CourseBundles.php',
				'CertificateChecker.php',
				'IconBox.php',
				'BlogList.php',
			);

			if ( in_array( basename( $module_file ), $d5_native_modules, true ) ) {
				continue;
			}
		}

		require_once $module_file;
	}
}

// Adding filter to disable wrapping with default themes
$theme = wp_get_theme()->name;
if (
	! ( 'Divi' === $theme )
	&&
	! ( 'GlobalStudy Divi Education Theme' === $theme )
	&&
	et_builder_is_frontend()
) {
	add_filter(
		'et_builder_outer_content_id',
		function () {
		},
		1
	);
}
