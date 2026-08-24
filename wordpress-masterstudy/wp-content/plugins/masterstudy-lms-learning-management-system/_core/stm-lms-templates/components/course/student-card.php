<?php
/**
 * @var array $course
 * @var int $user_id
 */

use MasterStudy\Lms\Pro\addons\certificate_builder\CertificateRepository;

wp_enqueue_style( 'masterstudy-student-course-card' );

if ( is_ms_lms_addon_enabled( 'certificate_builder' ) ) {
	wp_register_script( 'jspdf', STM_LMS_PRO_URL . '/assets/js/certificate-builder/jspdf.umd.js', array(), MS_LMS_VERSION, false );
	wp_register_script( 'qrcode', STM_LMS_PRO_URL . '/assets/js/certificate-builder/qrcode.min.js', array(), MS_LMS_VERSION, false );
	wp_register_script( 'html2canvas', STM_LMS_PRO_URL . '/assets/js/certificate-builder/html2canvas.min.js', array(), STM_LMS_PRO_VERSION, false );
	wp_enqueue_script( 'masterstudy_generate_certificate', STM_LMS_URL . 'assets/js/course-player/generate-certificate.js', array( 'jspdf', 'qrcode', 'html2canvas' ), MS_LMS_VERSION, true );

	if ( is_ms_lms_addon_enabled( 'grades' ) ) {
		wp_enqueue_style( 'masterstudy-grades-certificate' );
	}

	$shapes = method_exists( CertificateRepository::class, 'get_shapes' ) ? ( new CertificateRepository() )->get_shapes() : array();

	wp_localize_script(
		'masterstudy_generate_certificate',
		'course_certificate',
		array(
			'nonce'       => wp_create_nonce( 'stm_get_certificate' ),
			'ajax_url'    => admin_url( 'admin-ajax.php' ),
			'shapes'      => $shapes,
			'googleFonts' => STM_LMS_PRO_URL . '/assets/js/certificate-builder/google-fonts.json',
			'user_id'     => $user_id,
		)
	);
}
?>

<div class="masterstudy-student-course-card">
	<div class="masterstudy-student-course-card__wrapper">
		<a href="<?php echo esc_url( $course['url'] ); ?>" class="masterstudy-student-course-card__image-link" target="_blank">
			<img src="<?php echo esc_url( $course['image'] ); ?>" class="masterstudy-student-course-card__image">
		</a>
		<div class="masterstudy-student-course-card__content">
			<a href="<?php echo esc_url( $course['url'] ); ?>" class="masterstudy-student-course-card__title" target="_blank">
				<span><?php echo esc_html( $course['title'] ); ?></span>
			</a>
			<div class="masterstudy-student-course-card__info">
				<div class="masterstudy-student-course-card__info-block">
					<i class="stmlms-cats"></i>
					<span>
						<?php
						echo esc_html( $course['lectures']['lessons'] );
						echo ' ';
						echo esc_html( ( $course['lectures']['lessons'] > 1 || 0 === $course['lectures']['lessons'] ) ? __( 'Lectures', 'masterstudy-lms-learning-management-system' ) : __( 'Lecture', 'masterstudy-lms-learning-management-system' ) );
						?>
					</span>
				</div>
				<?php if ( ! empty( $course['duration_info'] ) ) { ?>
					<div class="masterstudy-student-course-card__info-block">
						<i class="stmlms-lms-clocks"></i>
						<span>
							<?php echo esc_html( $course['duration_info'] ); ?>
						</span>
					</div>
				<?php } ?>
			</div>
		</div>
		<?php
		if ( is_ms_lms_addon_enabled( 'certificate_builder' ) && masterstudy_lms_course_has_certificate( $course['course_id'] ) ) {
			STM_LMS_Templates::show_lms_template(
				'components/button',
				array(
					'title'         => __( 'View Certificate', 'masterstudy-lms-learning-management-system' ),
					'type'          => '',
					'link'          => '#',
					'style'         => 'tertiary',
					'size'          => 'sm',
					'id'            => $course['course_id'],
					'icon_position' => '',
					'icon_name'     => '',
				)
			);
		}
		?>
	</div>
</div>
