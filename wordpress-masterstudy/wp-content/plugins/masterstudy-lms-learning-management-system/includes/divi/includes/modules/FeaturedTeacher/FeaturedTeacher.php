<?php // phpcs:ignoreFile
class DMSLMS_FeaturedTeacher extends DMSLMS_BaseModule {

	public $slug = 'dmslms_featured_teacher';

	protected $module_credits = array(
		'module_uri' => 'masterstudy-lms-divi-modules',
		'author'     => 'StylemixThemes',
		'author_uri' => 'https://stylemixthemes.com/',
	);

	public function init() {
		$this->name = esc_html__( 'MS Featured Teacher', 'masterstudy-lms-divi' );
		$this->icon = 'a';
	}

	public function get_fields() {
		$users                = array();
		$lms_instructor_users = get_users( array( 'role__in' => array( 'stm_lms_instructor' ) ) );
		foreach ( $lms_instructor_users as $user ) {
			$user_id           = $user->ID;
			$name              = ( ! empty( $user->data->display_name ) ) ? $user->data->display_name : $user->data->user_login;
			$users[ $user_id ] = $name;
		}

		return array(

			'title'               => array(
				'label'            => esc_html__( 'Module Title', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Input your desired heading here.', 'masterstudy-lms-divi' ),
				'toggle_slug'      => 'main_content',
				'default_on_front' => 'MS Featured Teacher Module Title',
			),
			'instructor_position' => array(
				'label'            => esc_html__( 'Instructor Position', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Input your desired heading here.', 'masterstudy-lms-divi' ),
				'toggle_slug'      => 'main_content',
				'default_on_front' => 'Teacher',
			),
			'instructor_bio'      => array(
				'label'            => esc_html__( 'Instructor Bio', 'masterstudy-lms-divi' ),
				'type'             => 'tiny_mce',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Content entered here will appear below the heading text.', 'masterstudy-lms-divi' ),
				'toggle_slug'      => 'main_content',
				'default_on_front' => 'Here will be your text content for the instructor biography field',

			),
			'image_url'           => array(
				'label'              => esc_html__( 'Image', 'masterstudy-lms-divi' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_html__( 'Upload an image', 'masterstudy-lms-divi' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'masterstudy-lms-divi' ),
				'update_text'        => esc_attr__( 'Set As Image', 'masterstudy-lms-divi' ),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'masterstudy-lms-divi' ),
				'toggle_slug'        => 'main_content',
				'dynamic_content'    => 'image',
				'mobile_options'     => true,
				'hover'              => 'tabs',
			),
			'lms_teacher_list'    => array(
				'label'            => esc_html__( 'Select instructor', 'masterstudy-lms-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => $users,
				'default_on_front' => empty( array_keys( $users )[0] ) ? 0 : array_keys( $users )[0],
				'depends_show_if'  => 'on',
				'toggle_slug'      => 'main_content',
				'description'      => esc_html__( 'Here you can choose the Featured instructor', 'masterstudy-lms-divi' ),
			),
		);
	}

	public function render( $attrs, $render_slug, $content = null ) {
		$args = array( '</p>', '<p>' );
		$bio  = str_replace( $args, '', $this->props['instructor_bio'] );
		$img  = ! empty( $this->props['image_url'] ) ? $this->props['image_url'] : '';
		$atts = array(
			'css'        => '',
			'instructor' => ! empty( $this->props['lms_teacher_list'] ) ? $this->props['lms_teacher_list'] : '',
			'position'   => ! empty( $this->props['instructor_position'] ) ? $this->props['instructor_position'] : '',
			'bio'        => ! empty( $bio ) ? $bio : '',
			'image'      => attachment_url_to_postid( $img ),
		);

		return \STM_LMS_Templates::load_lms_template( 'shortcodes/stm_lms_featured_teacher', $atts );
	}
}

new DMSLMS_FeaturedTeacher();
