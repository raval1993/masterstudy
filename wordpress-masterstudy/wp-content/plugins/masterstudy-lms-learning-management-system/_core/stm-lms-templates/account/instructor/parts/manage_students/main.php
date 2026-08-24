<?php
/**
 * @var int $course_id
 */
use MasterStudy\Lms\Repositories\StudentsRepository;

wp_enqueue_style( 'masterstudy-account-manage-students-main' );
wp_enqueue_script( 'masterstudy-account-manage-students-export-students' );
wp_enqueue_script( 'masterstudy-account-manage-students-main' );
wp_enqueue_style( 'masterstudy-loader' );

$coming_soon = is_ms_lms_addon_enabled( 'coming_soon' );
$theads      = array(
	'username'         => array(
		'title'    => __( 'Student', 'masterstudy-lms-learning-management-system' ),
		'position' => 'start',
		'sort'     => 'username',
		'hidden'   => false,
	),
	'subscribed'       => array(
		'title'    => __( 'Subscribed', 'masterstudy-lms-learning-management-system' ),
		'position' => 'start',
		'hidden'   => ! $coming_soon,
	),
	'ago'              => array(
		'title'    => __( 'Started', 'masterstudy-lms-learning-management-system' ),
		'position' => 'start',
		'sort'     => 'ago',
		'hidden'   => false,
	),
	'progress_percent' => array(
		'title'    => __( 'Progress', 'masterstudy-lms-learning-management-system' ),
		'position' => 'start',
		'sort'     => 'progress_percent',
		'hidden'   => false,
	),
	'actions'          => array(
		'position' => 'start',
		'hidden'   => true,
	),
);

$total_students = ( new StudentsRepository() )->get_course_students_count( $course_id );
$student_public = STM_LMS_Options::get_option( 'student_public_profile', true );
?>
<div id="masterstudy-manage-students" class="masterstudy-manage-students">
	<div class="masterstudy-manage-students__top">
		<div class="masterstudy-manage-students__top-container">
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/button',
				array(
					'title'         => '',
					'link'          => STM_LMS_User::user_page_url(),
					'style'         => 'secondary',
					'size'          => 'sm',
					'icon_position' => 'left',
					'icon_name'     => 'arrow-left',
					'class'         => 'masterstudy-manage-students__back-btn',
				)
			);
			?>
			<div class="masterstudy-manage-students__top-info">
				<div class="masterstudy-manage-students__top-info-container">
					<div class="masterstudy-manage-students__course-title">
						<?php echo esc_html( get_the_title( $course_id ) ); ?>
					</div>
					<div class="masterstudy-manage-students__count">
						<span class="masterstudy-manage-students__count-number"></span>
						<span class="masterstudy-manage-students__count-label">
							<?php echo esc_html__( 'students', 'masterstudy-lms-learning-management-system' ); ?>
						</span>
					</div>
				</div>
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'title'         => esc_html__( 'Add', 'masterstudy-lms-learning-management-system' ),
						'link'          => '#',
						'style'         => 'primary',
						'size'          => 'sm',
						'id'            => 'add-student',
						'icon_position' => 'left',
						'icon_name'     => '',
					)
				);
				?>
			</div>
		</div>
		<div class="masterstudy-table__toolbar">
			<div class="masterstudy-table__toolbar-search-wrapper">
				<?php
					STM_LMS_Templates::show_lms_template(
						'components/search',
						array(
							'select_name'  => 's',
							'is_queryable' => false,
							'placeholder'  => esc_html__( 'Search student', 'masterstudy-lms-learning-management-system' ),
						)
					);
					?>
			</div>
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/button',
				array(
					'title' => esc_html__( 'Import CSV', 'masterstudy-lms-learning-management-system' ),
					'link'  => '#',
					'style' => 'secondary',
					'size'  => 'sm',
					'id'    => 'import-students-via-csv',
				)
			);
			STM_LMS_Templates::show_lms_template(
				'components/button',
				array(
					'title' => esc_html__( 'Export CSV', 'masterstudy-lms-learning-management-system' ),
					'link'  => '#',
					'style' => 'secondary',
					'size'  => 'sm',
					'id'    => 'export-students-to-csv',
				)
			);
			?>
		</div>
	</div>
	<div class="masterstudy-table">
		<div class="masterstudy-table__wrapper">
			<div class="masterstudy-thead">
				<?php foreach ( $theads as $thead ) : ?>
					<?php
					if ( isset( $thead['hidden'] ) && $thead['hidden'] ) {
						continue;
					}
					?>
					<div class="masterstudy-tcell masterstudy-tcell_is-<?php echo esc_attr( ( $thead['position'] ?? 'center' ) . ' ' . ( $thead['grow'] ?? '' ) ); ?>">
						<div class="masterstudy-tcell__header" data-sort="<?php echo esc_attr( $thead['sort'] ?? 'none' ); ?>">
							<span class="masterstudy-tcell__title"><?php echo esc_html( $thead['title'] ?? '' ); ?></span>
							<?php
							if ( isset( $thead['sort'] ) ) {
								STM_LMS_Templates::show_lms_template( 'components/sort-indicator' );
							}
							?>
						</div>
					</div>
				<?php endforeach; ?>
				<div class="masterstudy-tcell masterstudy-tcell_is-center masterstudy-tcell_is-hidden-md"></div>
			</div>
			<div class="masterstudy-tbody">
				<div class="masterstudy-table__item masterstudy-table__item--hidden">
					<div class="masterstudy-tcell masterstudy-tcell_is-start masterstudy-tcell_is-sm-space-between masterstudy-tcell_is-sm-border-bottom" data-th="<?php echo esc_html( $theads['username']['title'] ?? '' ); ?>:" data-th-inlined="true">
						<a
							<?php if ( $student_public ) { ?>
								href="#"
							<?php } ?>
							class="masterstudy-tcell__login-link <?php echo ! $student_public ? 'masterstudy-tcell__login-link_disabled' : ''; ?>"
						>
							<span class="masterstudy-tcell__data masterstudy-tcell__data-login" data-key="login" data-value=""></span>
							<span class="masterstudy-tcell__data masterstudy-tcell__data-email" data-key="email" data-value=""></span>
						</a>
					</div>
					<?php if ( $coming_soon ) { ?>
						<div class="masterstudy-tcell masterstudy-tcell_is-start masterstudy-tcell_is-sm-space-between masterstudy-tcell_is-sm-border-bottom" data-th="<?php echo esc_html( $theads['subscribed']['title'] ?? '' ); ?>:" data-th-inlined="true">
							<span class="masterstudy-tcell__label"><?php echo esc_html( $theads['subscribed']['title'] ?? '' ); ?></span>
							<span class="masterstudy-tcell__data" data-key="subscribed_time" data-value=""></span>
						</div>
					<?php } ?>
					<div class="masterstudy-tcell masterstudy-tcell_is-start masterstudy-tcell_is-sm-space-between masterstudy-tcell_is-sm-border-bottom" data-th="<?php echo esc_html( $theads['ago']['title'] ?? '' ); ?>:" data-th-inlined="true">
						<span class="masterstudy-tcell__label"><?php echo esc_html( $theads['ago']['title'] ?? '' ); ?></span>
						<span class="masterstudy-tcell__data masterstudy-tcell__data-started" data-key="ago" data-value=""></span>
					</div>
					<div class="masterstudy-tcell masterstudy-tcell_is-start masterstudy-tcell_is-sm-space-between" data-th="<?php echo esc_html( $theads['progress_percent']['title'] ?? '' ); ?>:" data-th-inlined="true">
						<span class="masterstudy-tcell__label">
							<?php echo esc_html__( 'Course progress', 'masterstudy-lms-learning-management-system' ); ?>
						</span>
						<span class="masterstudy-tcell__data masterstudy-tcell__data-progress" data-key="progress_percent" data-value="">
							<?php STM_LMS_Templates::show_lms_template( 'components/progress', array( 'hide_info' => true ) ); ?>
							<div class="masterstudy-tcell__data-course-progress">
								<span class="masterstudy-tcell__data-course-progress-title"><?php esc_html_e( 'Course progress:', 'masterstudy-lms-learning-management-system' ); ?></span>
								<span class="masterstudy-tcell__data-course-progress-value"></span>
							</div>
						</span>
					</div>
					<div class="masterstudy-tcell masterstudy-tcell__actions">
						<span class="masterstudy-tcell__data" data-key="progress_link" data-value="">
							<?php
							STM_LMS_Templates::show_lms_template(
								'components/button',
								array(
									'title'         => esc_html__( 'Progress', 'masterstudy-lms-learning-management-system' ),
									'style'         => 'secondary',
									'size'          => 'sm',
									'link'          => '',
									'id'            => 'manage-students-view-progress',
									'icon_position' => '',
									'icon_name'     => '',
								)
							);
							?>
						</span>
						<span class="masterstudy-tcell__data masterstudy-tcell__data-delete-btn" data-key="course_id" data-value="">
							<?php
							STM_LMS_Templates::show_lms_template(
								'components/button',
								array(
									'title' => '',
									'style' => 'danger',
									'size'  => 'sm',
									'link'  => '',
									'id'    => 'manage-students-delete',
								)
							);
							?>
						</span>
					</div>
				</div>
				<div class="masterstudy-table__item masterstudy-table__item--hidden">
					<div class="masterstudy-tcell masterstudy-tcell_is-empty">
						<?php echo esc_html__( 'No Students Found.', 'masterstudy-lms-learning-management-system' ); ?>
					</div>
				</div>
			</div>

			<div class="masterstudy-tfooter masterstudy-tfooter--hidden">
				<div class="masterstudy-tcell masterstudy-tcell_is-space-between">
					<span>
						<?php
							STM_LMS_Templates::show_lms_template(
								'components/pagination',
								array(
									'max_visible_pages' => 3,
									'total_pages'       => 1,
									'current_page'      => 1,
									'dark_mode'         => false,
									'is_queryable'      => false,
									'done_indicator'    => false,
									'is_hidden'         => false,
									'is_ajax'           => true,
								)
							);
							?>
					</span>
				</div>
				<div class="masterstudy-tcell masterstudy-tcell_is-space-between">
					<span>
					<?php
						STM_LMS_Templates::show_lms_template(
							'components/select',
							array(
								'select_id'    => 'assignments-per-page',
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
					</span>
				</div>
			</div>
		</div>
		<?php
		STM_LMS_Templates::show_lms_template(
			'components/loader',
			array(
				'dark_mode' => false,
				'is_local'  => true,
			)
		);
		STM_LMS_Templates::show_lms_template(
			'components/alert',
			array(
				'id'                  => 'masterstudy-manage-students-delete-student',
				'title'               => esc_html__( 'Delete student', 'masterstudy-lms-learning-management-system' ),
				'text'                => esc_html__( 'Are you sure you want to delete this student from course ?', 'masterstudy-lms-learning-management-system' ),
				'submit_button_text'  => esc_html__( 'Delete', 'masterstudy-lms-learning-management-system' ),
				'cancel_button_text'  => esc_html__( 'Cancel', 'masterstudy-lms-learning-management-system' ),
				'submit_button_style' => 'danger',
				'cancel_button_style' => 'tertiary',
				'dark_mode'           => false,
			)
		);
		?>
	</div>
</div>
<?php
	STM_LMS_Templates::show_lms_template( 'account/instructor/parts/manage_students/import-modal', compact( 'course_id' ) );
?>
