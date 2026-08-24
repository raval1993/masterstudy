<div class="wizard-finish" v-if="active_step === 'finish'">
	<div class="wizard-finish__welcome">
		<div class="wizard-finish__welcome_column">
			<h2>
				<span><?php esc_html_e( 'Welcome to MasterStudy LMS', 'masterstudy-lms-learning-management-system' ); ?></span>
				<span><?php esc_html_e( 'WordPress plugin', 'masterstudy-lms-learning-management-system' ); ?></span>
			</h2>
			<p>
				<?php esc_html_e( 'Now you can easily start creating your courses from scratch or import our demo courses to learn more', 'masterstudy-lms-learning-management-system' ); ?>
			</p>
			<div class="wizard-finish__welcome_button_wrapper">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=stm-lms-settings#section_2' ) ); ?>" class="wizard-finish__welcome_button">
					<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/wizard/run_demo.svg' ); ?>"/>
					<?php esc_html_e( 'Import Demo Courses', 'masterstudy-lms-learning-management-system' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=manage_courses' ) ); ?>" class="wizard-finish__welcome_button">
					<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/wizard/add_course.svg' ); ?>"/>
					<?php esc_html_e( 'Create a course', 'masterstudy-lms-learning-management-system' ); ?>
				</a>
			</div>
		</div>
		<div class="wizard-finish__welcome_column">
			<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/wizard/welcome_bg.png' ); ?>"/>
		</div>
	</div>
	<div class="wizard-finish__column_wrapper">
		<div class="wizard-finish__column">
			<div class="wizard-finish__column_block unlimited">
				<h3><?php esc_html_e( 'Unlimited lessons and quizzes', 'masterstudy-lms-learning-management-system' ); ?></h3>
				<p><?php esc_html_e( 'You have the option to create lessons and quizzes of various types. Choose the type of lesson and quiz, or combine them in the course.', 'masterstudy-lms-learning-management-system' ); ?></p>
				<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/wizard/unlim_learn_bg.png' ); ?>"/>
			</div>
			<div class="wizard-finish__column_block small_companies">
				<h3><?php esc_html_e( 'For small companies and enterprises', 'masterstudy-lms-learning-management-system' ); ?></h3>
				<p><?php esc_html_e( 'With our Course Builder, you can design courses equally professionally for both small schools and full-fledged marketplaces. No coding skills are required at all.', 'masterstudy-lms-learning-management-system' ); ?></p>
				<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/wizard/small_companies_bg.png' ); ?>"/>
			</div>
		</div>
		<div class="wizard-finish__column">
			<div class="wizard-finish__column_block users_manage">
				<h3><?php esc_html_e( 'User management', 'masterstudy-lms-learning-management-system' ); ?></h3>
				<p><?php esc_html_e( 'There are two types of profiles in MasterStudy LMS: for students and for instructors.', 'masterstudy-lms-learning-management-system' ); ?></p>
				<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/wizard/users_manage_bg.png' ); ?>"/>
			</div>
			<div class="wizard-finish__column_block video">
				<h3><?php esc_html_e( 'Video guides and documentation', 'masterstudy-lms-learning-management-system' ); ?></h3>
				<p><?php esc_html_e( 'We have an extensive knowledge base and a playlist on our Youtube channel to explain each feature of the plugin.', 'masterstudy-lms-learning-management-system' ); ?></p>
				<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/wizard/video_bg.png' ); ?>"/>
				<div class="wizard-finish__column_block_button_wrapper">
					<a href="https://www.youtube.com/watch?v=wGtDvLkVvaQ&list=PL3Pyh_1kFGGDikfKuVbGb_dqKmXZY86Ve" class="wizard-finish__column_block_button" target="_blank">
						<?php esc_html_e( 'WATCH PLAYLIST', 'masterstudy-lms-learning-management-system' ); ?>
					</a>
					<a href="https://docs.stylemixthemes.com/masterstudy-lms/" class="wizard-finish__column_block_button" target="_blank">
						<?php esc_html_e( 'READ GUIDES', 'masterstudy-lms-learning-management-system' ); ?>
					</a>
				</div>
			</div>
			<div class="wizard-finish__column_block sell_online">
				<h3><?php esc_html_e( 'Sell courses online', 'masterstudy-lms-learning-management-system' ); ?></h3>
				<p><?php esc_html_e( 'Set up PayPal, Stripe or WooCommerce to monetize your courses.', 'masterstudy-lms-learning-management-system' ); ?></p>
				<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/wizard/sell_online_bg.png' ); ?>"/>
			</div>
		</div>
	</div>
	<div class="wizard-finish__integrations">
		<div class="wizard-finish__integrations-logo">
			<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/wizard/rapyd.png' ); ?>" width="190" height="41" alt="">
		</div>
		<div class="wizard-finish__integrations-title">
			<div class="wizard-finish__integrations-title-top">
				<?php echo esc_html__( 'Enterprise-Grade Hosting from', 'masterstudy-lms-learning-management-system' ); ?>
			</div>
			<div class="wizard-finish__integrations-title-bottom">
				<span><?php echo esc_html__( 'just $29', 'masterstudy-lms-learning-management-system' ); ?></span>
				<small><?php echo esc_html__( 'Without the Hyper Costs.', 'masterstudy-lms-learning-management-system' ); ?></small>
			</div>
		</div>
		<a href="https://rapyd.cloud/pricing/?fpr=stylemixthemes" class="wizard-finish__integrations-button" target="_blank">
			<?php esc_html_e( 'Get Deal', 'masterstudy-lms-learning-management-system' ); ?>
		</a>
	</div>
	<div class="wizard-finish__links">
		<a href="https://docs.stylemixthemes.com/masterstudy-lms/" class="wizard-finish__links_block" target="_blank">
			<div class="wizard-finish__links_block_wrapper">
				<div class="icon_wrapper">
					<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/wizard/help_desk_icon.svg' ); ?>"/>
				</div>
				<span><?php esc_html_e( 'Help Desk', 'masterstudy-lms-learning-management-system' ); ?></span>
				<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/wizard/arrow.svg' ); ?>" class="wizard-arrow"/>
			</div>
		</a>
		<a href="https://stylemix.net/ticket-form/?utm_source=wpadmin-ms&utm_medium=lms-wizard&utm_campaign=customization" class="wizard-finish__links_block" target="_blank">
			<div class="wizard-finish__links_block_wrapper">
				<div class="icon_wrapper">
					<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/wizard/custom_icon.svg' ); ?>"/>
				</div>
				<span><?php esc_html_e( 'Customization', 'masterstudy-lms-learning-management-system' ); ?></span>
				<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/wizard/arrow.svg' ); ?>" class="wizard-arrow"/>
			</div>
		</a>
		<a href="https://www.facebook.com/groups/masterstudylms"class="wizard-finish__links_block" target="_blank">
			<div class="wizard-finish__links_block_wrapper">
				<div class="icon_wrapper">
					<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/wizard/fb_icon.svg' ); ?>"/>
				</div>
				<span><?php esc_html_e( 'Facebook Community', 'masterstudy-lms-learning-management-system' ); ?></span>
				<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/wizard/arrow.svg' ); ?>" class="wizard-arrow"/>
			</div>
		</a>
	</div>
	<?php
	if ( ! STM_LMS_Helpers::is_theme_activated() ) {
		STM_LMS_Templates::show_lms_template(
			'premium-templates/banners/banner-templates',
			array(
				'custom_class' => 'masterstudy-templates-banner',
			)
		);
	}
	?>
	<div class="wizard-finish__bottom_banner">
		<h3><?php esc_html_e( 'Upgrade for the best LMS features', 'masterstudy-lms-learning-management-system' ); ?></h3>
		<p><?php esc_html_e( 'Get MasterStudy Pro Plus to access extra features your users will love: Certificate Builder, Email Editor and Branding Manager, Assignments, Drip Content and Prerequisites, Zoom Conference, Co-Instructors, Course Bundles, Live Streaming, and much more!', 'masterstudy-lms-learning-management-system' ); ?></p>
		<a href="https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin-ms&utm_medium=lms-wizard&utm_campaign=get-masterstudy-pro" target="_blank"><?php esc_html_e( 'Get now', 'masterstudy-lms-learning-management-system' ); ?></a>
	</div>
</div>
