<?php
add_filter(
	'wpcfto_field_grades_table',
	function () {
		return STM_LMS_PATH . '/settings/custom_fields/grades_table/field.php';
	}
);
