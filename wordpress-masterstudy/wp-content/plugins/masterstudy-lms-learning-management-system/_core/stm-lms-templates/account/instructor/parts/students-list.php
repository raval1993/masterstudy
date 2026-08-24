<?php
wp_enqueue_style( 'masterstudy-loader' );

if ( is_admin() ) {
	$page_title = get_admin_page_title();
} else {
	$page_title = esc_html__( 'Students', 'masterstudy-lms-learning-management-system' );
}
$points_enabled = is_ms_lms_addon_enabled( 'point_system' );

$columns = array(
	'check'    => '',
	'name'     => array(
		'title'  => esc_html__( 'Student', 'masterstudy-lms-learning-management-system' ),
		'hidden' => false,
	),
	'email'    => array(
		'title'  => __( 'Email', 'masterstudy-lms-learning-management-system' ),
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

<div class="masterstudy-table-list masterstudy-students-list">
	<div class="masterstudy-table-list__top-bar">
		<div class="masterstudy-table-list__title">
			<?php echo esc_html( $page_title ); ?>
		</div>
		<div class="masterstudy-table-list__transfer">
			<button class="masterstudy-table-list-btn__secondary masterstudy-table-list-btn__export" data-id="export-students-to-csv">
				<i class="stmlms-upload-alt"></i>
				<span><?php echo esc_html__( 'Export CSV', 'masterstudy-lms-learning-management-system' ); ?></span>
			</button>
		</div>
	</div>

	<div class="masterstudy-table-list__actions">
		<div class="masterstudy-table-list__actions-left">
			<label class="masterstudy-table-list__select-all">
				<input type="checkbox" class="masterstudy-table-list-checkbox" id="masterstudy-table-list-checkbox">
				<span class="screen-reader-text"><?php echo esc_html__( 'Select all', 'masterstudy-lms-learning-management-system' ); ?></span>
			</label>

			<button class="masterstudy-table-list__delete-btn" data-id="masterstudy-students-delete" disabled>
				<i class="stmlms-table-trash"></i>
				<span><?php echo esc_html__( 'Unenroll Selected', 'masterstudy-lms-learning-management-system' ); ?></span>
			</button>
		</div>

		<div class="masterstudy-table-list__actions-right">
			<div class="filter-students-by-courses__wrapper filter-students-by-courses-default">
				<select name="filter_students" class="filter-students-by-courses" data-placeholder="<?php echo esc_attr__( 'All courses', 'masterstudy-lms-learning-management-system' ); ?>">
					<option value="">
						<?php echo esc_html__( 'All courses', 'masterstudy-lms-learning-management-system' ); ?>
					</option>
				</select>
			</div>

			<div class="masterstudy-table-list__search">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/search-input',
					array(
						'placeholder'     => __( 'Search by name or email', 'masterstudy-lms-learning-management-system' ),
						'classes_wrapper' => 'masterstudy-form-search',
						'classes_input'   => 'masterstudy-form-search__input',
					)
				);
				?>
			</div>

			<div class="masterstudy-table-list__date-picker">
				<?php STM_LMS_Templates::show_lms_template( 'components/analytics/date-field' ); ?>
			</div>
		</div>
	</div>

	<div class="masterstudy-table-list__header">
		<?php
		foreach ( $columns as $key => $thead ) :
			if ( ! empty( $thead['hidden'] ) ) {
				continue;
			}
			$th_class = 'masterstudy-table-list__th';
			if ( 'check' === $key ) {
				$th_class .= ' masterstudy-table-list__th--checkbox-placeholder';
			} else {
				$th_class .= " masterstudy-table-list__th--$key";
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

	<div class="masterstudy-table-list-items">
		<?php
		STM_LMS_Templates::show_lms_template(
			'components/loader',
			array(
				'dark_mode' => false,
				'is_local'  => true,
			)
		);
		?>
		<template id="masterstudy-table-list-row-template">
			<div class="masterstudy-table-list__row">
				<div class="masterstudy-table-list__td masterstudy-table-list__td--checkbox">
					<input type="checkbox" name="student[]">
				</div>
				<div class="masterstudy-table-list__td masterstudy-table-list__td--name"></div>
				<div class="masterstudy-table-list__td masterstudy-table-list__td--email"></div>
				<div class="masterstudy-table-list__td masterstudy-table-list__td--joined" data-title="<?php echo esc_attr__( 'Joined', 'masterstudy-lms-learning-management-system' ); ?>"></div>
				<div class="masterstudy-table-list__td masterstudy-table-list__td--enrolled" data-title="<?php echo esc_attr__( 'Course enrolled', 'masterstudy-lms-learning-management-system' ); ?>"></div>
				<?php if ( $points_enabled ) : ?>
					<div class="masterstudy-table-list__td masterstudy-table-list__td--points" data-title="<?php echo esc_attr__( 'Points', 'masterstudy-lms-learning-management-system' ); ?>"></div>
					<?php
				endif;
				if ( STM_LMS_Helpers::is_pro_plus() || is_admin() ) :
					?>
					<div class="masterstudy-table-list__td masterstudy-table-list__td--view">
						<a href="#" class="masterstudy-table-list__row--link" data-disabled="<?php echo esc_attr( ! STM_LMS_Helpers::is_pro_plus() ); ?>">
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
									'title'         => __( 'Upgrade MasterStudy', 'masterstudy-lms-learning-management-system' ),
									'type'          => '',
									'link'          => $upgrade_link,
									'style'         => 'primary',
									'size'          => 'sm',
									'id'            => 'upgrade-pro-plus',
									'icon_position' => '',
									'icon_name'     => '',
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
		<template id="masterstudy-table-list-no-found-template">
			<div class="masterstudy-table-list-no-found__info">
				<div class="masterstudy-table-list-no-found__info-icon"><span class="stmlms-order"></span></div>
				<div class="masterstudy-table-list-no-found__info-title">
					<?php echo esc_html__( 'No students yet', 'masterstudy-lms-learning-management-system' ); ?>
				</div>
				<div class="masterstudy-table-list-no-found__info-description">
					<?php echo esc_html__( 'All information about your students will be displayed here', 'masterstudy-lms-learning-management-system' ); ?>
				</div>
			</div>
		</template>
	</div>

	<div class="masterstudy-table-list-navigation">
		<div class="masterstudy-table-list-navigation__pagination"></div>
		<div class="masterstudy-table-list-navigation__per-page">
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
