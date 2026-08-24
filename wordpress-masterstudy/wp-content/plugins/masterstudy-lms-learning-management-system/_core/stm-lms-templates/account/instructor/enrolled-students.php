<?php
if ( ! STM_LMS_Instructor::instructor_show_list_students() || ! STM_LMS_Instructor::is_instructor() ) {
	STM_LMS_User::js_redirect( STM_LMS_User::login_page_url() );
	die;
}

$lms_current_user = STM_LMS_User::get_current_user( '', true, true );

do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $lms_current_user );
wp_enqueue_style( 'masterstudy-account-main' );

wp_enqueue_script( 'masterstudy-pagination-utils' );
wp_enqueue_script( 'masterstudy-account-enrolled-students' );
wp_enqueue_style( 'masterstudy-account-enrolled-students' );
wp_enqueue_style( 'masterstudy-loader' );

$points_enabled = is_ms_lms_addon_enabled( 'point_system' );

$columns = array(
	'check'    => '',
	'name'     => array(
		'title'  => esc_html__( 'Student', 'masterstudy-lms-learning-management-system' ),
		'hidden' => false,
	),
	'joined'   => array(
		'title'  => __( 'Joined', 'masterstudy-lms-learning-management-system' ),
		'sort'   => 'joined',
		'hidden' => false,
	),
	'enrolled' => array(
		'title'  => __( 'Course enrolled', 'masterstudy-lms-learning-management-system' ),
		'sort'   => 'enrolled',
		'hidden' => false,
	),
	'points'   => array(
		'title'  => __( 'Points', 'masterstudy-lms-learning-management-system' ),
		'sort'   => 'points',
		'hidden' => ! $points_enabled,
	),
	'view'     => array(
		'hidden' => ! STM_LMS_Helpers::is_pro_plus() && ! is_admin(),
	),
);
?>

<div class="masterstudy-account">
	<?php do_action( 'stm_lms_admin_after_wrapper_start', $lms_current_user ); ?>
	<div class="masterstudy-account-sidebar">
		<div class="masterstudy-account-sidebar__wrapper">
			<?php do_action( 'masterstudy_account_sidebar', $lms_current_user ); ?>
		</div>
	</div>
	<div class="masterstudy-account-container">
		<div class="masterstudy-account-enrolled-students">
			<div class="masterstudy-account-enrolled-students__top-bar">
				<div class="masterstudy-account-enrolled-students__title">
					<?php echo esc_html__( 'Students', 'masterstudy-lms-learning-management-system' ); ?>
				</div>
				<div class="masterstudy-account-enrolled-students__header-actions">
					<div class="masterstudy-account-enrolled-students__search">
						<?php
						STM_LMS_Templates::show_lms_template(
							'components/search-input',
							array(
								'placeholder'     => __( 'Search students', 'masterstudy-lms-learning-management-system' ),
								'classes_wrapper' => 'masterstudy-form-search',
								'classes_input'   => 'masterstudy-form-search__input',
							)
						);
						?>
					</div>
					<div class="masterstudy-account-enrolled-students__transfer">
						<?php
						STM_LMS_Templates::show_lms_template(
							'components/button',
							array(
								'title'         => esc_html__( 'Export CSV', 'masterstudy-lms-learning-management-system' ),
								'link'          => '#',
								'style'         => 'secondary',
								'size'          => 'sm',
								'id'            => 'export-students-to-csv',
								'class'         => 'masterstudy-account-enrolled-students__export',
								'icon_name'     => 'csv',
								'icon_position' => 'left',
							)
						);
						?>
					</div>
				</div>
			</div>

			<div class="masterstudy-account-enrolled-students__actions">
				<div class="masterstudy-account-enrolled-students__actions-left">
					<label class="masterstudy-account-enrolled-students__select-all">
						<input type="checkbox" class="masterstudy-account-enrolled-students-checkbox" id="masterstudy-account-enrolled-students-checkbox">
						<span class="screen-reader-text"><?php echo esc_html__( 'Select all', 'masterstudy-lms-learning-management-system' ); ?></span>
					</label>

					<?php
					STM_LMS_Templates::show_lms_template(
						'components/button',
						array(
							'title'         => esc_html__( 'Unenroll Selected', 'masterstudy-lms-learning-management-system' ),
							'link'          => '#',
							'style'         => 'secondary',
							'size'          => 'sm',
							'id'            => 'masterstudy-students-delete',
							'class'         => 'masterstudy-account-enrolled-students__delete-btn masterstudy-button_disabled',
							'icon_name'     => 'table-trash',
							'icon_position' => 'left',
						)
					);
					?>
				</div>

				<div class="masterstudy-account-enrolled-students__actions-right">
					<div class="filter-students-by-courses__wrapper filter-students-by-courses-default">
						<select name="filter_students" class="filter-students-by-courses" data-placeholder="<?php echo esc_attr__( 'All courses', 'masterstudy-lms-learning-management-system' ); ?>">
							<option value="">
								<?php echo esc_html__( 'All courses', 'masterstudy-lms-learning-management-system' ); ?>
							</option>
						</select>
					</div>

					<div class="masterstudy-account-enrolled-students__search">
						<?php
						STM_LMS_Templates::show_lms_template(
							'components/search-input',
							array(
								'placeholder'     => __( 'Search students', 'masterstudy-lms-learning-management-system' ),
								'classes_wrapper' => 'masterstudy-form-search',
								'classes_input'   => 'masterstudy-form-search__input',
							)
						);
						?>
					</div>

					<div class="masterstudy-account-enrolled-students__date-picker">
						<?php STM_LMS_Templates::show_lms_template( 'components/analytics/date-field' ); ?>
					</div>
				</div>
			</div>

			<div class="masterstudy-account-enrolled-students__header">
				<?php
				foreach ( $columns as $key => $thead ) :
					if ( ! empty( $thead['hidden'] ) ) {
						continue;
					}
					$th_class = 'masterstudy-account-enrolled-students__th';
					if ( 'check' === $key ) {
						$th_class .= ' masterstudy-account-enrolled-students__th--checkbox-placeholder';
					} else {
						$th_class .= " masterstudy-account-enrolled-students__th--$key";
					}
					?>
					<div class="<?php echo esc_attr( $th_class ); ?>">
						<div class="masterstudy-tcell__header" data-sort="<?php echo esc_attr( $thead['sort'] ?? 'none' ); ?>">
							<span class="masterstudy-tcell__title"><?php echo esc_html( $thead['title'] ?? '' ); ?></span>
							<?php
							if ( ! empty( $thead['sort'] ) ) {
								STM_LMS_Templates::show_lms_template( 'components/sort-indicator' );
							}
							?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="masterstudy-account-enrolled-students__items">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/loader',
					array(
						'dark_mode' => false,
						'is_local'  => true,
					)
				);
				?>
				<template id="masterstudy-account-enrolled-students-row-template">
					<div class="masterstudy-account-enrolled-students__row">
						<div class="masterstudy-account-enrolled-students__td masterstudy-account-enrolled-students__td--checkbox">
							<input type="checkbox" name="student[]">
						</div>
						<div class="masterstudy-account-enrolled-students__td masterstudy-account-enrolled-students__td--name">
							<div class="masterstudy-account-enrolled-students__student-row">
								<span class="masterstudy-account-enrolled-students__student-row-name"></span>
								<span class="masterstudy-account-enrolled-students__student-row-email"></span>
							</div>						</div>
						<div class="masterstudy-account-enrolled-students__td masterstudy-account-enrolled-students__td--joined" data-title="<?php echo esc_attr__( 'Joined', 'masterstudy-lms-learning-management-system' ); ?>"></div>
						<div class="masterstudy-account-enrolled-students__td masterstudy-account-enrolled-students__td--enrolled" data-title="<?php echo esc_attr__( 'Course enrolled', 'masterstudy-lms-learning-management-system' ); ?>"></div>
						<?php if ( $points_enabled ) : ?>
							<div class="masterstudy-account-enrolled-students__td masterstudy-account-enrolled-students__td--points" data-title="<?php echo esc_attr__( 'Points', 'masterstudy-lms-learning-management-system' ); ?>"></div>
							<?php
						endif;
						if ( STM_LMS_Helpers::is_pro_plus() || is_admin() ) :
							?>
							<div class="masterstudy-account-enrolled-students__td masterstudy-account-enrolled-students__td--view">
								<a href="#" class="masterstudy-account-enrolled-students__row-link" data-disabled="<?php echo esc_attr( ! STM_LMS_Helpers::is_pro_plus() ); ?>">
									<?php echo esc_html__( 'View', 'masterstudy-lms-learning-management-system' ); ?>
								</a>
								<?php
								if ( ! STM_LMS_Helpers::is_pro_plus() ) {
									ob_start();

									if ( ! STM_LMS_Helpers::is_pro() ) {
										$upgrade_link = admin_url( 'admin.php?page=stm-lms-go-pro&utm_source=mswpadmin&utm_medium=students-tab&utm_campaign=upgrade-to-pro-button' );
										echo esc_html__( 'The ability to view detailed reports on students is only available in the PRO version of the plugin.', 'masterstudy-lms-learning-management-system' );
									} else {
										$upgrade_link = 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=mswpadmin&utm_medium=students-tab&utm_campaign=upgrade-to-pro-button';
										echo esc_html__( 'The ability to view detailed reports on students is only available in the PRO PLUS version of the plugin.', 'masterstudy-lms-learning-management-system' );
									}

									STM_LMS_Templates::show_lms_template(
										'components/button',
										array(
											'title'     => __( 'Upgrade MasterStudy', 'masterstudy-lms-learning-management-system' ),
											'type'      => '',
											'link'      => $upgrade_link,
											'style'     => 'primary',
											'size'      => 'sm',
											'id'        => 'upgrade-pro-plus',
											'icon_position' => '',
											'icon_name' => '',
										)
									);

									$hint_content = ob_get_clean();

									STM_LMS_Templates::show_lms_template(
										'components/hint',
										array(
											'content'   => $hint_content,
											'side'      => is_rtl() ? 'left' : 'right',
											'dark_mode' => false,
										)
									);
								}
								?>
							</div>
						<?php endif; ?>
					</div>
				</template>
				<template id="masterstudy-account-enrolled-students-no-found-template">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/no-records',
						array(
							'title_items'     => esc_html__( 'No students yet', 'masterstudy-lms-learning-management-system' ),
							'container_class' => 'masterstudy-account-enrolled-students-no-found__info',
							'icon'            => 'stmlms-students',
						)
					);
					?>
				</template>
			</div>

			<div class="masterstudy-account-enrolled-students__navigation">
				<div class="masterstudy-account-enrolled-students__navigation-pagination"></div>
				<div class="masterstudy-account-enrolled-students__navigation-per-page">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/select',
						array(
							'select_id'    => 'items-per-page',
							'select_width' => '170px',
							'select_name'  => 'per_page',
							'placeholder'  => esc_html__( '10 per page', 'masterstudy-lms-learning-management-system' ),
							'default'      => 10,
							'is_queryable' => false,
							'options'      => array(
								'25'  => esc_html__( '25 per page', 'masterstudy-lms-learning-management-system' ),
								'50'  => esc_html__( '50 per page', 'masterstudy-lms-learning-management-system' ),
								'75'  => esc_html__( '75 per page', 'masterstudy-lms-learning-management-system' ),
								'100' => esc_html__( '100 per page', 'masterstudy-lms-learning-management-system' ),
							),
						)
					);
					?>
				</div>
			</div>
		</div>
	</div>

	<?php
	STM_LMS_Templates::show_lms_template(
		'components/analytics/datepicker-modal',
		array(
			'id'    => 'students',
			'items' => array(
				'all_time' => esc_html__( 'All time', 'masterstudy-lms-learning-management-system' ),
			),
		)
	);
	STM_LMS_Templates::show_lms_template(
		'components/alert',
		array(
			'id'                  => 'masterstudy-delete-students',
			'title'               => esc_html__( 'Unenrolling Students', 'masterstudy-lms-learning-management-system' ),
			'text'                => esc_html__( 'Are you sure you want to unenroll the selected students from your courses? This action cannot be undone. All course progress for the selected students will be permanently lost.', 'masterstudy-lms-learning-management-system' ),
			'submit_button_text'  => esc_html__( 'Unenroll', 'masterstudy-lms-learning-management-system' ),
			'cancel_button_text'  => esc_html__( 'Cancel', 'masterstudy-lms-learning-management-system' ),
			'submit_button_style' => 'danger',
			'cancel_button_style' => 'tertiary',
			'dark_mode'           => false,
		)
	);
	?>
</div>
<?php do_action( 'masterstudy_after_account', $lms_current_user ); ?>
