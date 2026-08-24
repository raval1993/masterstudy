<?php
/**
 * @var boolean $alert
 * @var boolean $new
 */

wp_enqueue_style( 'masterstudy-course-templates-modal' );

$new   = $new ?? false;
$alert = $alert ?? false;
?>

<div id="<?php echo esc_attr( $new ? 'masterstudy-course-templates-modal-new' : ( $alert ? 'masterstudy-course-templates-modal-delete' : 'masterstudy-course-templates-modal-create' ) ); ?>" class="masterstudy-course-templates__modal" style="display:none">
	<div class="masterstudy-course-templates__modal-wrapper">
		<div class="masterstudy-course-templates__modal-container">
			<div class="masterstudy-course-templates__modal-header">
				<span class="masterstudy-course-templates__modal-header-title">
					<?php
					if ( $new ) {
						echo esc_html__( 'Give your template a title', 'masterstudy-lms-learning-management-system' );
					} else {
						echo $alert ? esc_html__( 'Are you sure you want to delete this template?', 'masterstudy-lms-learning-management-system' ) : esc_html__( 'Give your copy a title', 'masterstudy-lms-learning-management-system' );
					}
					?>
				</span>
				<div class="masterstudy-course-templates__modal-close"></div>
			</div>
			<?php if ( ! $alert ) { ?>
				<div class="masterstudy-course-templates__modal-content">
					<input type="text" name="masterstudy-template-name" class="masterstudy-course-templates__modal-input" placeholder="<?php echo esc_attr__( 'Enter template name', 'masterstudy-lms-learning-management-system' ); ?>">
					<span class="masterstudy-course-templates__modal-error">
						<?php echo esc_html__( 'Field is required', 'masterstudy-lms-learning-management-system' ); ?>
					</span>
				</div>
			<?php } ?>
			<div class="masterstudy-course-templates__modal-actions">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'title' => __( 'Cancel', 'masterstudy-lms-learning-management-system' ),
						'id'    => 'masterstudy-modal-course-cancel',
						'style' => 'tertiary',
						'size'  => 'sm',
						'url'   => '#',
					)
				);
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'title' => $alert ? __( 'Delete', 'masterstudy-lms-learning-management-system' ) : __( 'Continue', 'masterstudy-lms-learning-management-system' ),
						'id'    => $new
							? 'masterstudy-modal-course-template-new'
							: ( $alert
								? 'masterstudy-modal-course-template-delete'
								: 'masterstudy-modal-course-template-copy'
							),
						'style' => $alert ? 'danger' : 'primary',
						'size'  => 'sm',
						'url'   => '#',
					)
				);
				?>
			</div>
		</div>
	</div>
</div>
