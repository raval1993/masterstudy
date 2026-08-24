<?php
add_filter(
	'wpcfto_field_course_templates',
	function () {
		return STM_LMS_PATH . '/settings/custom_fields/course_templates/field.php';
	}
);
