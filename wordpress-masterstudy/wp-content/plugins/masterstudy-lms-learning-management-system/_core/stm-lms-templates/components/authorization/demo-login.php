<div class="masterstudy-authorization__demo">
	<span class="masterstudy-authorization__demo-title">
		<?php echo esc_html__( 'Demo login for', 'masterstudy-lms-learning-management-system' ); ?>:
	</span>
	<?php
	global $wp;
	$current_url = home_url( add_query_arg( array(), $wp->request ) );
	?>
	<div class="masterstudy-authorization__demo-role">
		<a href="<?php echo esc_url( get_site_url() . '?demo_login=' . $current_url ); ?>" class="masterstudy-authorization__demo-role-title">
			<?php echo esc_html__( 'Instructor', 'masterstudy-lms-learning-management-system' ); ?>
		</a>
		<a href="<?php echo esc_url( get_site_url() . '?generate_demo_user=' . $current_url ); ?>" class="masterstudy-authorization__demo-role-title">
			<?php echo esc_html__( 'Student', 'masterstudy-lms-learning-management-system' ); ?>
		</a>
	</div>
</div>
