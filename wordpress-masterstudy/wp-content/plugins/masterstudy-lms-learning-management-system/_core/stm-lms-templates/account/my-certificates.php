<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use MasterStudy\Lms\Pro\addons\certificate_builder\CertificateRepository;
use MasterStudy\Lms\Repositories\CurriculumMaterialRepository;
use MasterStudy\Lms\Repositories\CurriculumSectionRepository;

$lms_current_user = (array) STM_LMS_User::get_current_user( '', true, true );

do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $lms_current_user );
wp_enqueue_style( 'masterstudy-account-main' );

wp_enqueue_style( 'masterstudy-account-user-certificates' );
wp_enqueue_script( 'masterstudy-account-user-certificates' );

if ( is_ms_lms_addon_enabled( 'grades' ) ) {
	wp_enqueue_style( 'masterstudy-grades-certificate' );
}

$completed = stm_lms_get_user_completed_courses( $lms_current_user['id'], array( 'user_course_id', 'course_id' ), -1 );
?>

<div class="masterstudy-account">
	<?php do_action( 'stm_lms_admin_after_wrapper_start', $lms_current_user ); ?>
	<div class="masterstudy-account-sidebar">
		<div class="masterstudy-account-sidebar__wrapper">
			<?php do_action( 'masterstudy_account_sidebar', $lms_current_user ); ?>
		</div>
	</div>
	<div class="masterstudy-account-container">
		<?php
		if ( ! empty( $completed ) ) {
			if ( is_ms_lms_addon_enabled( 'certificate_builder' ) ) {
				wp_register_script( 'jspdf', STM_LMS_PRO_URL . '/assets/js/certificate-builder/jspdf.umd.js', array(), MS_LMS_VERSION, false );
				wp_register_script( 'qrcode', STM_LMS_PRO_URL . '/assets/js/certificate-builder/qrcode.min.js', array(), MS_LMS_VERSION, false );
				wp_register_script( 'html2canvas', STM_LMS_PRO_URL . '/assets/js/certificate-builder/html2canvas.min.js', array(), STM_LMS_PRO_VERSION, false );
				wp_enqueue_script( 'masterstudy_generate_certificate', STM_LMS_URL . 'assets/js/course-player/generate-certificate.js', array( 'jspdf', 'qrcode', 'html2canvas' ), MS_LMS_VERSION, true );

				$shapes = method_exists( CertificateRepository::class, 'get_shapes' ) ? ( new CertificateRepository() )->get_shapes() : array();

				wp_localize_script(
					'masterstudy_generate_certificate',
					'course_certificate',
					array(
						'nonce'        => wp_create_nonce( 'stm_get_certificate' ),
						'ajax_url'     => admin_url( 'admin-ajax.php' ),
						'shapes'       => $shapes,
						'googleFonts'  => STM_LMS_PRO_URL . '/assets/js/certificate-builder/google-fonts.json',
						'emit_pdf_url' => true,
					)
				);
			}
			?>
			<div class="masterstudy-account-my-certificates">
				<h2 class="masterstudy-account-my-certificates__title">
					<?php esc_html_e( 'My Certificates', 'masterstudy-lms-learning-management-system' ); ?>
				</h2>

				<div class="masterstudy-account-my-certificates__container">
				<?php
				foreach ( $completed as $course ) :
					if ( masterstudy_lms_course_has_certificate( $course['course_id'] ) ) {
						$code     = STM_LMS_Certificates::stm_lms_certificate_code( $course['user_course_id'], $course['course_id'] );
						$image_id = get_post_thumbnail_id( $course['course_id'] );
						if ( $image_id ) {
							$image_url = wp_get_attachment_image_src( $image_id, 'full' )[0] ?? '';
						} else {
							$image_url = STM_LMS_URL . 'assets/img/placeholder.gif';
						}

						$curriculum_repo = new CurriculumMaterialRepository();
						$section_ids     = ( new CurriculumSectionRepository() )->get_course_section_ids( $course['course_id'] );
						$lessons         = $curriculum_repo->count_by_type( $section_ids, 'stm-lessons' );
						$duration        = get_post_meta( $course['course_id'], 'duration_info', true );
						?>
					<div class="masterstudy-account-my-certificates__certificate">
						<div class="masterstudy-account-my-certificates__certificate-course">
							<div class="masterstudy-account-my-certificates__certificate-course-img">
								<img src="<?php echo esc_attr( $image_url ); ?>" alt="<<?php echo esc_html__( 'Course', 'masterstudy-lms-learning-management-system' ); ?>" />
							</div>
							<div class="masterstudy-account-my-certificates__certificate-course-info">
								<a class="masterstudy-account-my-certificates__certificate-course-title" href="<?php echo esc_url( get_the_permalink( $course['course_id'] ) ); ?>">
									<?php echo wp_kses_post( get_the_title( $course['course_id'] ) ); ?>
								</a>
								<div class="masterstudy-account-my-certificates__certificate-course-stats">
									<div class="masterstudy-account-my-certificates__certificate-course-stat">
										<span class="stmlms-list1 masterstudy-account-my-certificates__certificate-course-stat-lecture-icon"></span>
										<span class="masterstudy-account-my-certificates__certificate-course-stat-text">
											<?php
											printf(
												/* translators: %d: lectures */
												esc_html( _n( '%d lecture', '%d lectures', $lessons, 'masterstudy-lms-learning-management-system' ) ),
												esc_html( $lessons )
											);
											?>
										</span>
									</div>
									<?php if ( ! empty( $duration ) ) : ?>
										<div class="masterstudy-account-my-certificates__certificate-course-stat">
											<span class="stmlms-clock masterstudy-account-my-certificates__certificate-course-stat-duration-icon"></span>
											<span class="masterstudy-account-my-certificates__certificate-course-stat-text">
												<?php echo esc_html( $duration ); ?>
											</span>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<div class="masterstudy-account-my-certificates__certificate-actions">
							<?php
							STM_LMS_Templates::show_lms_template(
								'components/button',
								array(
									'title' => esc_html__( 'View', 'masterstudy-lms-learning-management-system' ),
									'link'  => '#',
									'style' => 'secondary',
									'size'  => 'sm',
									'class' => 'masterstudy-account-my-certificates__certificate-actions-view masterstudy_preview_certificate',
									'id'    => esc_attr( $course['course_id'] ),
								)
							);

							STM_LMS_Templates::show_lms_template(
								'components/button',
								array(
									'title' => esc_html__( 'Copy code', 'masterstudy-lms-learning-management-system' ),
									'link'  => '#',
									'style' => 'secondary',
									'size'  => 'sm',
									'id'    => $code,
									'class' => 'masterstudy-account-my-certificates__certificate-actions-copy',
								)
							);

							if ( is_ms_lms_addon_enabled( 'certificate_builder' ) ) {
								STM_LMS_Templates::show_lms_template(
									'components/button',
									array(
										'title' => esc_html__( 'Download', 'masterstudy-lms-learning-management-system' ),
										'link'  => '#',
										'style' => 'secondary',
										'size'  => 'sm',
										'class' => 'masterstudy-account-my-certificates__certificate-actions-download masterstudy_preview_certificate',
										'id'    => esc_attr( $course['course_id'] ),
									)
								);
							} else {
								STM_LMS_Templates::show_lms_template(
									'components/button',
									array(
										'title'  => esc_html__( 'Download', 'masterstudy-lms-learning-management-system' ),
										'link'   => esc_url( STM_LMS_Course::certificates_page_url( $course['course_id'] ) ),
										'target' => '_blank',
										'style'  => 'secondary',
										'class'  => 'masterstudy-account-my-certificates__certificate-actions-download',
										'size'   => 'sm',
									)
								);
							}
							?>
						</div>
					</div>
						<?php
					}
					endforeach;
				?>
				</div>
			</div>

		<?php } else { ?>
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/no-records',
				array(
					'title_items'     => esc_html__( 'No issued certificates yet', 'masterstudy-lms-learning-management-system' ),
					'icon'            => 'stmlms-my-certificates',
					'container_class' => 'masterstudy-account-my-certificates__no-records',
				)
			);
			?>
		<?php } ?>
	</div>
</div>

<?php do_action( 'masterstudy_after_account', $lms_current_user ); ?>
