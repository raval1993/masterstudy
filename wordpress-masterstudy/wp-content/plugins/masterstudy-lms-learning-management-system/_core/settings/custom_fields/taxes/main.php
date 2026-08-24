<?php
add_filter(
	'wpcfto_field_taxes',
	function () {
		return STM_LMS_PATH . '/settings/custom_fields/taxes/field.php';
	}
);
