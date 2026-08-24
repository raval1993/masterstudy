<?php
/**
 * Course templates field template.
 *
 * @var $field
 *
 */

wp_enqueue_style( 'masterstudy-course-templates', STM_LMS_URL . 'assets/css/components/course-templates.css', array(), MS_LMS_VERSION );
wp_enqueue_script( 'masterstudy-course-templates-settings', STM_LMS_URL . 'assets/js/components/course-templates-settings.js', array( 'vue.js', 'wpcfto_metaboxes.js' ), MS_LMS_VERSION, true );
wp_localize_script(
	'masterstudy-course-templates-settings',
	'courseTemplates',
	array(
		'preview_url' => 'https://masterstudy.stylemixthemes.com/lms-plugin/courses-page/basics-of-masterstudy/?course_style=',
		'edit_url'    => esc_url( admin_url() . 'post.php?post=' ),
		'img_url'     => STM_LMS_URL . 'assets/img/course/',
		'preview'     => esc_html__( 'Preview', 'masterstudy-lms-learning-management-system' ),
		'change'      => esc_html__( 'Change Template', 'masterstudy-lms-learning-management-system' ),
		'edit'        => esc_html__( 'Edit Template', 'masterstudy-lms-learning-management-system' ),
	)
);
?>

<course_templates
	:fields="<?php echo esc_attr( $field ); ?>"
	:field_label="<?php echo esc_attr( $field_label ); ?>"
	:field_name="'<?php echo esc_attr( $field_name ); ?>'"
	:field_id="'<?php echo esc_attr( $field_id ); ?>'"
	:field_value="<?php echo esc_attr( $field_value ); ?>"
	@wpcfto-get-value="<?php echo esc_attr( $field_value ); ?> = $event"
>
</course_templates>
