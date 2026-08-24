<?php
/**
 * @var boolean $single_page
 */

wp_enqueue_style( 'masterstudy-course-templates' );
wp_enqueue_style( 'masterstudy-select2' );
wp_enqueue_script( 'masterstudy-course-templates' );

$elementor_active = class_exists( '\Elementor\Plugin' );
$native_templates = masterstudy_lms_get_native_templates();
$my_templates     = masterstudy_lms_get_my_templates();
$user             = new \stmLms\Classes\Models\StmUser( get_current_user_id() );
$course           = $user->get_first_course();
$my_preview_url   = ! empty( $course ) ? STM_LMS_Course::courses_page_url() . $course->post_name : home_url();
$upgrade_link     = 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=master_study&utm_campaign=singlecoursetemplates&utm_content=freetoprobutton';
$upgrade_link_pro = admin_url( 'admin.php?page=stm-lms-go-pro&source=singlecoursetemplates' );

wp_localize_script(
	'masterstudy-course-templates',
	'masterstudy_course_templates_data',
	array(
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
	)
);

$settings                 = get_option( 'stm_lms_settings' );
$settings['course_style'] = $settings['course_style'] ?? 'default';
$is_pro                   = STM_LMS_Helpers::is_pro();
$is_pro_plus              = STM_LMS_Helpers::is_pro_plus() || STM_LMS_Helpers::is_ms_starter_purchased();
$pro_hint                 = __( 'Upgrade to PRO PLUS', 'masterstudy-lms-learning-management-system' );
$pro_plus_hint            = __( 'Upgrade to PRO', 'masterstudy-lms-learning-management-system' );
$preview_url              = 'https://masterstudy.stylemixthemes.com/lms-plugin/courses-page/basics-of-masterstudy/';
$pro_img_url              = STM_LMS_URL . 'assets/img/pro-features/pro_plus.svg';
$pro_plus_img_url         = STM_LMS_URL . 'assets/img/pro-features/unlock-pro-logo.svg';
$templates_library        = masterstudy_lms_get_template_library();
$courses                  = $single_page ? STM_LMS_Courses::get_all_courses_for_options() : array();
$layout_preview_urls      = array(
	'minimalism'  => 'https://masterstudy.stylemixthemes.com/lms-plugin/courses-page/minimalism/?course_style=minimalism-3',
	'streamline'  => 'https://masterstudy.stylemixthemes.com/lms-plugin/courses-page/fashion-photography-from-professional/?course_style=streamline-3',
	'masterclass' => 'https://masterstudy.stylemixthemes.com/lms-plugin/courses-page/basics-of-masterstudy/?course_style=masterclass-2',
);
$template_tabs            = array(
	array(
		'id'        => 'templates_library',
		'title'     => __( 'Template Library', 'masterstudy-lms-learning-management-system' ),
		'hint'      => is_array( $templates_library ) ? count( $templates_library ) : 0,
		'elementor' => true,
	),
	array(
		'id'    => 'native_templates',
		'title' => __( 'Native Templates', 'masterstudy-lms-learning-management-system' ),
		'hint'  => is_array( $native_templates ) ? count( $native_templates ) : 0,
	),
	array(
		'id'        => 'my_templates',
		'title'     => __( 'My Templates', 'masterstudy-lms-learning-management-system' ),
		'hint'      => is_array( $my_templates ) ? count( $my_templates ) : 0,
		'elementor' => true,
	),
);

if ( $single_page ) {
	$template_tabs = array_filter(
		$template_tabs,
		function ( $tab ) {
			return 'native_templates' !== $tab['id'];
		}
	);
	$template_tabs = array_values( $template_tabs );
}
?>

<div class="masterstudy-course-templates" style="display:none">
	<div class="masterstudy-course-templates__header">
		<?php
		STM_LMS_Templates::show_lms_template(
			'components/back-link',
			array(
				'id'  => 'masterstudy-course-player-back',
				'url' => admin_url(),
			)
		);
		?>
		<div class="masterstudy-course-templates__title">
			<?php echo esc_html__( 'Select Course Template', 'masterstudy-lms-learning-management-system' ); ?>
		</div>
		<div class="masterstudy-course-templates__tabs">
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/tabs',
				array(
					'items'            => $template_tabs,
					'style'            => 'nav-sm',
					'active_tab_index' => 1,
					'dark_mode'        => false,
				)
			);
			?>
		</div>
		<div class="masterstudy-course-templates__save">
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/button',
				array(
					'title' => __( 'Save', 'masterstudy-lms-learning-management-system' ),
					'id'    => 'masterstudy-save-course-template',
					'style' => 'primary',
					'size'  => 'sm',
					'url'   => '#',
				)
			);
			?>
		</div>
	</div>
	<div class="masterstudy-course-templates__content">
		<?php if ( $single_page ) { ?>
			<div class="masterstudy-course-templates__course">
				<div class="masterstudy-course-templates__course-title">
					<?php echo esc_html__( 'Select Course to show on page', 'masterstudy-lms-learning-management-system' ); ?>
				</div>
				<select id="masterstudy-course-select" class="masterstudy-course-templates__course-select">
					<?php
					foreach ( $courses as $value => $name ) {
						?>
						<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $name ); ?></option>
						<?php
					}
					?>
				</select>
			</div>
		<?php } ?>
		<div id="templates_library" class="masterstudy-course-templates__list">
			<?php foreach ( $templates_library as $layout ) { ?>
				<div class="masterstudy-course-templates__item">
					<div class="masterstudy-course-templates__item-wrapper masterstudy-course-templates__item-wrapper_library">
						<?php if ( ! $is_pro_plus ) { ?>
							<span class="masterstudy-course-templates__item-lock"></span>
						<?php } ?>
						<div class="masterstudy-course-templates__item-header">
							<img src="<?php echo esc_html( STM_LMS_URL . "/assets/img/course/layout/{$layout['name']}.png" ); ?>" class="masterstudy-course-templates__item-img">
							<div class="masterstudy-course-templates__item-hint">
								<?php
								if ( ! $is_pro_plus ) {
									STM_LMS_Templates::show_lms_template(
										'components/button',
										array(
											'title'  => $is_pro && ! $is_pro_plus ? $pro_hint : $pro_plus_hint,
											'id'     => 'masterstudy-upgrade-pro',
											'style'  => 'primary',
											'size'   => 'sm',
											'link'   => $is_pro && ! $is_pro_plus ? esc_url( $upgrade_link ) : esc_url( $upgrade_link_pro ),
											'target' => '_blank',
										)
									);
								} elseif ( ! $elementor_active ) {
									?>
									<a data-id="masterstudy-elementor-install" href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>" class="masterstudy-course-templates__item-elementor" target="_blank">
										<?php echo esc_html__( 'Install Elementor', 'masterstudy-lms-learning-management-system' ); ?>
									</a>
									<?php
								} else {
									STM_LMS_Templates::show_lms_template(
										'components/button',
										array(
											'title'  => __( 'Copy & Edit', 'masterstudy-lms-learning-management-system' ),
											'id'     => $layout['id'],
											'class'  => 'masterstudy-copy-template',
											'style'  => 'primary',
											'size'   => 'sm',
											'url'    => admin_url(),
											'target' => '_blank',
										)
									);
								}
								?>
								<a data-id="masterstudy-template-preview" href="<?php echo esc_url( $layout_preview_urls[ $layout['name'] ] ); ?>" class="masterstudy-course-templates__item-preview" target="_blank">
									<?php echo esc_html__( 'Preview', 'masterstudy-lms-learning-management-system' ); ?>
								</a>
							</div>
						</div>
						<div class="masterstudy-course-templates__item-bottom">
							<div class="masterstudy-course-templates__item-title masterstudy-course-templates__item-title_library">
								<div class="masterstudy-course-templates__item-title-text">
									<?php echo esc_html( $layout['title'] ); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php
		if ( ! $single_page ) {
			?>
			<div id="native_templates" class="masterstudy-course-templates__list masterstudy-course-templates__list_show">
				<?php foreach ( $native_templates as $layout ) { ?>
					<div class="masterstudy-course-templates__item">
						<div class="masterstudy-course-templates__item-wrapper <?php echo esc_attr( $layout['disabled'] ? 'masterstudy-course-templates__item-wrapper_disabled' : '' ); ?>">
							<?php if ( $layout['disabled'] ) { ?>
								<span class="masterstudy-course-templates__item-lock"></span>
							<?php } ?>
							<div class="masterstudy-course-templates__item-header <?php echo $settings['course_style'] === $layout['name'] ? 'masterstudy-course-templates__item-header_active' : ''; ?>">
							<img src="<?php echo esc_html( STM_LMS_URL . "/assets/img/course/{$layout['name']}.png" ); ?>" class="masterstudy-course-templates__item-img">
								<div class="masterstudy-course-templates__item-hint">
									<?php
									if ( $layout['disabled'] ) {
										STM_LMS_Templates::show_lms_template(
											'components/button',
											array(
												'title'  => $is_pro && ! $is_pro_plus ? $pro_hint : $pro_plus_hint,
												'id'     => 'masterstudy-upgrade-pro',
												'style'  => 'primary',
												'size'   => 'sm',
												'link'   => $is_pro && ! $is_pro_plus ? esc_url( $upgrade_link ) : esc_url( $upgrade_link_pro ),
												'target' => '_blank',
											)
										);
									}
									?>
									<a data-id="masterstudy-template-preview" href="<?php echo esc_url( $preview_url . '?course_style=' . $layout['name'] ); ?>" class="masterstudy-course-templates__item-preview" target="_blank">
										<?php echo esc_html__( 'Preview', 'masterstudy-lms-learning-management-system' ); ?>
									</a>
								</div>
							</div>
							<div class="masterstudy-course-templates__item-bottom">
							<input
								type="radio"
								name="masterstudy_course_style"
								value="<?php echo esc_attr( $layout['name'] ); ?>"
								class="masterstudy-course-templates__item-input"
								<?php checked( $settings['course_style'], $layout['name'] ); ?>
							>
							<div class="masterstudy-course-templates__item-title">
								<div class="masterstudy-course-templates__item-title-text">
									<?php echo esc_html( $layout['title'] ); ?>
								</div>
							</div>
						</div>
						</div>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
		<div id="my_templates" class="masterstudy-course-templates__list">
			<?php if ( $is_pro_plus ) { ?>
				<div class="masterstudy-course-templates__add">
					<div class="masterstudy-course-templates__add-wrapper">
						<div class="masterstudy-course-templates__add-icon"></div>
						<div class="masterstudy-course-templates__add-title">
							<?php echo esc_html__( 'Blank Template', 'masterstudy-lms-learning-management-system' ); ?>
						</div>
					</div>
				</div>
				<?php foreach ( $my_templates as $layout ) { ?>
					<div id="<?php echo esc_attr( $layout['id'] ); ?>" class="masterstudy-course-templates__item">
						<div class="masterstudy-course-templates__item-wrapper">
							<div class="masterstudy-course-templates__item-header <?php echo $settings['course_style'] === $layout['name'] ? 'masterstudy-course-templates__item-header_active' : ''; ?>">
								<div class="masterstudy-course-templates__item-bottom">
									<input
										type="radio"
										name="masterstudy_course_style"
										value="<?php echo esc_attr( $layout['name'] ); ?>"
										class="masterstudy-course-templates__item-input"
										<?php checked( $settings['course_style'], $layout['name'] ); ?>
									>
									<div class="masterstudy-course-templates__item-title">
										<div contenteditable="true" class="masterstudy-course-templates__item-title-text">
											<?php echo esc_html( $layout['title'] ); ?>
										</div>
										<span class="masterstudy-course-templates__item-title-edit"></span>
									</div>
								</div>
								<div class="masterstudy-course-templates__item-hint">
									<?php
									if ( ! $elementor_active ) {
										?>
										<a data-id="masterstudy-elementor-install" href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>" class="masterstudy-course-templates__item-elementor" target="_blank">
											<?php echo esc_html__( 'Install Elementor', 'masterstudy-lms-learning-management-system' ); ?>
										</a>
										<?php
									} else {
										?>
										<a data-id="masterstudy-elementor-edit" href="<?php echo esc_url( admin_url() . 'post.php?post=' . $layout['id'] . '&action=elementor' ); ?>" class="masterstudy-course-templates__item-elementor" target="_blank">
											<?php echo esc_html__( 'Edit in Elementor', 'masterstudy-lms-learning-management-system' ); ?>
										</a>
										<a data-id="masterstudy-template-preview" href="<?php echo esc_url( $my_preview_url . '?course_style=' . $layout['name'] ); ?>" class="masterstudy-course-templates__item-preview" target="_blank">
											<?php echo esc_html__( 'Preview', 'masterstudy-lms-learning-management-system' ); ?>
										</a>
									<?php } ?>
									<span data-id="<?php echo esc_attr( $layout['id'] ); ?>" class="masterstudy-course-templates__item-copy"></span>
									<span data-id="<?php echo esc_attr( $layout['id'] ); ?>" class="masterstudy-course-templates__item-delete"></span>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
			} else {
				?>
				<div class="stm-lms-unlock-pro-banner">
					<div class="stm-lms-unlock-banner-wrapper">
						<div class="unlock-banner-image">
							<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/pro-features/elementor-templates.png' ); ?>">
						</div>
						<div class="unlock-wrapper-content">
							<h2>
								<?php echo esc_html__( 'Unlock', 'masterstudy-lms-learning-management-system' ); ?>
								<span class="unlock-addon-name">
									<?php echo esc_html__( 'Course Templates', 'masterstudy-lms-learning-management-system' ); ?>
								</span>
								<?php echo esc_html__( 'with', 'masterstudy-lms-learning-management-system' ); ?>
								<div class="unlock-pro-logo-wrapper">
									<span class="unlock-pro-logo"><?php echo esc_html__( 'MasterStudy', 'masterstudy-lms-learning-management-system' ); ?></span>
									<img src="<?php echo esc_url( $is_pro && ! $is_pro_plus ? $pro_img_url : $pro_plus_img_url ); ?>">
								</div>
							</h2>
							<p>
								<?php echo esc_html__( 'Transform your course pages into high-converting landing experiences. Build clean, purpose-driven layouts with full control using 50+ customizable Elementor widgets. Apply advanced pre-built templates or design your own without any limits.', 'masterstudy-lms-learning-management-system' ); ?>
							</p>
							<div class="unlock-pro-banner-footer">
								<div class="unlock-addons-buttons">
									<a href="<?php echo esc_url( $is_pro && ! $is_pro_plus ? $upgrade_link : $upgrade_link_pro ); ?>" target="_blank" class="primary button btn">
										<?php echo esc_html( $is_pro && ! $is_pro_plus ? $pro_hint : $pro_plus_hint ); ?>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	<?php
	STM_LMS_Templates::show_lms_template( 'components/course-templates-modal', array( 'new' => true ) );
	STM_LMS_Templates::show_lms_template( 'components/course-templates-modal', array( 'alert' => false ) );
	STM_LMS_Templates::show_lms_template( 'components/course-templates-modal', array( 'alert' => true ) );
	?>
</div>
