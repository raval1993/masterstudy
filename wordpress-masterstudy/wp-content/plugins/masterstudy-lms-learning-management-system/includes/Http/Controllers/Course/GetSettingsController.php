<?php

namespace MasterStudy\Lms\Http\Controllers\Course;

use MasterStudy\Lms\Http\Serializers\CertificateSerializer;
use MasterStudy\Lms\Http\Serializers\CourseCategorySerializer;
use MasterStudy\Lms\Http\Serializers\CourseLevelSerializer;
use MasterStudy\Lms\Http\Serializers\CourseSerializer;
use MasterStudy\Lms\Http\Serializers\CustomFieldsSerializer;
use MasterStudy\Lms\Plugin\Taxonomy;
use MasterStudy\Lms\Repositories\CertificateRepository;
use MasterStudy\Lms\Repositories\ComingSoonRepository;
use MasterStudy\Lms\Repositories\CourseRepository;
use STM_LMS_Options;
use WP_REST_Request;

class GetSettingsController {
	private CourseRepository $course_repository;
	private CertificateRepository $certificate_repository;

	public function __construct() {
		$this->course_repository      = new CourseRepository();
		$this->certificate_repository = new CertificateRepository();
	}

	public function __invoke( $course_id, WP_REST_Request $request ): \WP_REST_Response {
		$course           = $this->course_repository->find( $course_id );
		$elementor_active = class_exists( '\Elementor\Plugin' );
		$drip_settings    = class_exists( '\STM_LMS_Sequential_Drip_Content' ) && method_exists( '\STM_LMS_Sequential_Drip_Content', 'stm_lms_get_settings' )
		? \STM_LMS_Sequential_Drip_Content::stm_lms_get_settings()
		: array();

		return new \WP_REST_Response(
			array(
				'categories'       => ( new CourseCategorySerializer() )->collectionToArray( Taxonomy::all_categories() ),
				'certificates'     => ( new CertificateSerializer() )->collectionToArray( $this->certificate_repository->get_all() ),
				'is_enabled_crtfs' => STM_LMS_Options::get_option( 'instructors_certificates', false ),
				'course'           => ( new CourseSerializer() )->toArray( $course ),
				'levels'           => ( new CourseLevelSerializer() )->collectionToArray( \STM_LMS_Helpers::get_course_levels() ),
				'featured_quota'   => \STM_LMS_Subscriptions::get_featured_quota(),
				'coming_soon'      => ( new ComingSoonRepository() )->find_by_course( $course_id ),
				'course_style'     => ms_plugin_get_course_page_style( $course_id ),
				'course_templates' => array(
					'native_templates' => masterstudy_lms_get_native_templates(),
					'my_templates'     => masterstudy_lms_get_my_templates(),
					'template_library' => masterstudy_lms_get_template_library(),
				),
				'custom_fields'    => ( new CustomFieldsSerializer() )->collectionToArray(
					$course_id,
					apply_filters( 'masterstudy_lms_course_custom_fields', array() )
				),
				'lock_all_lesson'  => $drip_settings['locked'] ?? false,
			)
		);
	}
}
