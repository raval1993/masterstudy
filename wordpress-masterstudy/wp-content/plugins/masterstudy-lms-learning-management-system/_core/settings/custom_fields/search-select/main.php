<?php
add_filter(
	'wpcfto_field_search-select',
	function () {
		return STM_LMS_PATH . '/settings/custom_fields/search-select/field.php';
	}
);
