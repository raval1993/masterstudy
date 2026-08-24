<?php // phpcs:ignoreFile

class DMSLMS_IconBox extends DMSLMS_BaseModule {

	public $slug = 'dmslms_icon_box';

	protected $module_credits = array(
		'module_uri' => 'masterstudy-lms-divi-modules',
		'author'     => 'StylemixThemes',
		'author_uri' => 'https://stylemixthemes.com/',
	);

	public function init() {
		$this->name = esc_html__( 'MS Icon Box', 'masterstudy-lms-divi' );
		$this->icon = 'a';
	}

	public function get_fields() {
		return array(

			'image_url'  => array(
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
			'button_url' => array(
				'label'           => esc_html__( 'Button Link URL', 'masterstudy-lms-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the destination URL for your button.', 'masterstudy-lms-divi' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'url',
			),
			'body_title' => array(
				'label'            => esc_html__( 'Module Title', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
				'default_on_front' => 'MS Icon Box Module Title',
			),
			'body_text'  => array(
				'label'            => esc_html__( 'Icon Box description', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
				'default_on_front' => 'Icon Box Description input field',
			),
			'body_btn'   => array(
				'label'            => esc_html__( 'Icon Box button', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
				'default_on_front' => 'Read more',
			),

			'image_width' => array(
				'label'            => esc_html__( 'Image ( Content ) width', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
				'default_on_front' => '220',
			),
			'body_width'  => array(
				'label'            => esc_html__( 'Icon Box ( Body Content ) Width', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
				'default_on_front' => '380',
			),

			'title_color' => array(
				'label'           => esc_html__( 'Button Title Color', 'masterstudy-lms-divi' ),
				'type'            => 'color-alpha',
				'default'         => '#fff',
				'option_category' => 'basic_option',
				'toggle_slug'     => 'main_content',
			),
			'back_color'  => array(
				'label'           => esc_html__( 'Button Background Color', 'masterstudy-lms-divi' ),
				'type'            => 'color-alpha',
				'default'         => '#385bce',
				'option_category' => 'basic_option',
				'toggle_slug'     => 'main_content',
			),

		);
	}

	public function render( $attrs, $content = null, $render_slug = null ) {

		$inline    = "background-color:" . $this->props['back_color'] . ";  color: " . $this->props['title_color'] . "!important;";
		$btn_url   = ! empty( $this->props['button_url'] ) ? $this->props['button_url'] : '';
		$image_url = ! empty( $this->props['image_url'] ) ? $this->props['image_url'] : '';
		$icon_data =
			'<div class="icon-box">
                <div class="image" style="width:' . $this->props['image_width'] . 'px' . ';">
                    <img src="' . $image_url . '" alt="">
                </div>
                <div class="icon-body">
                    <h2> ' . $this->props['body_title'] . '</h2>
                    <p> ' . $this->props['body_text'] . '</p>
                    <a href="' . $btn_url . '" class="icon-box-btn et_pb_button_0 et_pb_bg_layout_dark " style="' . $inline . '"> ' . $this->props['body_btn'] . '</a>
                </div>
            </div>';
		// Render module content
		$output = sprintf( $icon_data );
		// Render wrapper
		// 3rd party module with no full VB support has to wrap its render output with $this->_render_module_wrapper().
		// This method will automatically add module attributes and proper structure for parallax image/video background
		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new DMSLMS_IconBox();
