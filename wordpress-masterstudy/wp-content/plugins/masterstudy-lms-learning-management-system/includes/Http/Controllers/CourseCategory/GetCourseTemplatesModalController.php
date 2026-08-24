<?php

namespace MasterStudy\Lms\Http\Controllers\CourseCategory;

use MasterStudy\Lms\Utility\TemplateAssetCapture;
use STM_LMS_Templates;
use WP_REST_Response;

final class GetCourseTemplatesModalController {
	public function __invoke(): WP_REST_Response {
		$result = TemplateAssetCapture::capture(
			function () {
				STM_LMS_Templates::show_lms_template(
					'components/course-templates',
					array( 'single_page' => false )
				);
			},
			array(
				function () {
					stm_lms_enqueue_component_scripts( '' );
				},
			)
		);

		$user           = new \stmLms\Classes\Models\StmUser( get_current_user_id() );
		$course         = $user->get_first_course();
		$my_preview_url = ! empty( $course )
			? esc_url_raw( \STM_LMS_Course::courses_page_url() . $course->post_name )
			: home_url();

		$native_templates = masterstudy_lms_get_native_templates();
		$my_templates     = function_exists( 'masterstudy_lms_get_my_templates' ) ? masterstudy_lms_get_my_templates() : array();

		$data = array(
			'ajax_url'         => admin_url( 'admin-ajax.php' ),
			'empty_img'        => esc_url( STM_LMS_URL . 'assets/img/course/empty-layout.png' ),
			'preview_url'      => 'https://masterstudy.stylemixthemes.com/lms-plugin/courses-page/basics-of-masterstudy/?course_style=',
			'my_preview_url'   => $my_preview_url,
			'edit_url'         => esc_url( admin_url() . 'post.php?post=' ),
			'img_url'          => STM_LMS_URL . 'assets/img/course/',
			'preview'          => esc_html__( 'Preview', 'masterstudy-lms-learning-management-system' ),
			'change'           => esc_html__( 'Change Template', 'masterstudy-lms-learning-management-system' ),
			'edit'             => esc_html__( 'Edit Template', 'masterstudy-lms-learning-management-system' ),
			'none'             => esc_html__( 'None', 'masterstudy-lms-learning-management-system' ),
			'find_course'      => esc_html__( 'Find Course...', 'masterstudy-lms-learning-management-system' ),
			'native_templates' => $native_templates,
			'my_templates'     => $my_templates,
			'edit_text'        => esc_html__( 'Edit in Elementor', 'masterstudy-lms-learning-management-system' ),
		);

		return new WP_REST_Response(
			array(
				'html'    => $result['html'],
				'data'    => $data,
				'css_url' => $result['css_urls'],
				'js_url'  => $result['js_urls'],
			)
		);
	}
}
