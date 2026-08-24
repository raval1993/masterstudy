<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lms_current_user = STM_LMS_User::get_current_user( '', true, true );
$is_instructor    = STM_LMS_Instructor::is_instructor();
$tpl              = $is_instructor ? 'instructor' : 'student';

STM_LMS_Templates::show_lms_template(
	'account/' . $tpl . '/dashboard',
	array(
		'current_user' => $lms_current_user,
	)
);
