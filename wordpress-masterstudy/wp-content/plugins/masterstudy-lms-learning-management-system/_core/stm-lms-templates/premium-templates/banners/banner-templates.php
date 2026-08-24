<?php
/**
 * @var $custom_class
 */
wp_enqueue_style( 'premium-templates' );
?>
<div class="masterstudy-templates-banner <?php echo esc_attr( $custom_class ? $custom_class : '' ); ?>">
	<div class="masterstudy-templates-banner-info">
		<div class="masterstudy-templates-banner-info-title">
		<?php
			echo sprintf(
				wp_kses(
					/* translators: %1$s - first word, %2$s - second word (to be bold) */
					__( '%1$s <strong>%2$s</strong>', 'masterstudy-lms-learning-management-system' ),
					array( 'strong' => array() )
				),
				'MasterStudy',
				'Templates'
			);
			?>
		</div>
		<div class="masterstudy-templates-banner-info-description"><?php echo esc_html__( 'See for yourself why MasterStudy is one of the most popular WordPress e-learning Themes.', 'masterstudy-lms-learning-management-system' ); ?></div>
	</div>
	<div class="masterstudy-templates-banner-buttons">
	<?php
		STM_LMS_Templates::show_lms_template(
			'components/button',
			array(
				'title'  => esc_html__( 'View Demos', 'masterstudy-lms-learning-management-system' ),
				'link'   => 'https://stylemixthemes.com/wordpress-lms-plugin/starter-templates/',
				'style'  => 'outline',
				'size'   => 'sm',
				'id'     => 'view-demos-lms-wizard',
				'target' => '_blank',
			)
		);
		STM_LMS_Templates::show_lms_template(
			'components/button',
			array(
				'title'  => esc_html__( 'Start for Free', 'masterstudy-lms-learning-management-system' ),
				'link'   => 'https://stylemixthemes.com/wordpress-lms-plugin/starter-templates/',
				'style'  => 'secondary',
				'size'   => 'sm',
				'id'     => 'start-for-free-lms-wizard',
				'target' => '_blank',
			)
		);
		?>
	</div>
</div>
