<?php
add_filter(
	'wpcfto_field_button-links',
	function () {
		return STM_LMS_PATH . '/settings/custom_fields/button-links/field.php';
	}
);
