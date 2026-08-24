
<div class="masterstudy-checkout-no-result">
	<div class="masterstudy-checkout-no-result__icon"><span class="stmlms-cart"></span></div>
	<h3><?php echo esc_html__( 'Cart is empty', 'masterstudy-lms-learning-management-system' ); ?></h3>
	<p><?php echo esc_html__( 'All information about your orders will be displayed here', 'masterstudy-lms-learning-management-system' ); ?></p>
	<?php
	STM_LMS_Templates::show_lms_template(
		'components/button',
		array(
			'title'  => __( 'Add Courses', 'masterstudy-lms-learning-management-system' ),
			'link'   => STM_LMS_Course::courses_page_url(),
			'style'  => 'primary',
			'size'   => 'md',
			'target' => '_blank',
		)
	);
	?>
</div>
