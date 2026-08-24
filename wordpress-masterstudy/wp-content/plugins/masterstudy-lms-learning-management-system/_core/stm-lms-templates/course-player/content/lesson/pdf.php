<?php
/**
 * @var int $item_id
 * @var int $user_id
 * @var int $course_id
 * @var boolean $lesson_completed
 * @var boolean $dark_mode
 * .*/

use MasterStudy\Lms\Repositories\LessonRepository;

wp_enqueue_style( 'masterstudy-course-player-pdf-lesson' );
wp_enqueue_style( 'pdfjs_viewer_styles' );
wp_enqueue_script( 'masterstudy-course-player-pdf-lesson' );

$settings    = get_option( 'stm_lms_settings' );
$theme_fonts = $settings['course_player_theme_fonts'] ?? false;

if ( empty( $theme_fonts ) ) {
	wp_enqueue_style( 'masterstudy-course-player-pdf-lesson-fonts' );
}

$lesson_data     = ( new LessonRepository() )->get( $item_id );
$allow_bookmarks = STM_LMS_Options::get_option( 'course_allow_students_bookmarks', false );
$bookmarks       = array();
$bookmarks_title = STM_LMS_Options::get_option( 'pdf_bookmarks_section_title', 'Bookmarks' );

$script_data = array(
	'pdf_read_all'          => $lesson_data['pdf_read_all'],
	'pdf_file'              => $lesson_data['pdf_file'][0],
	'lesson_id'             => $item_id,
	'course_id'             => $course_id,
	'add_bookmark_nonce'    => wp_create_nonce( 'add_bookmark' ),
	'remove_bookmark_nonce' => wp_create_nonce( 'remove_bookmark' ),
	'update_bookmark_nonce' => wp_create_nonce( 'update_bookmark' ),
	'ajax_url'              => admin_url( 'admin-ajax.php' ),
	'translations'          => array(
		'save'             => esc_html__( 'Save', 'masterstudy-lms-learning-management-system' ),
		'page_number'      => esc_html__( 'Page Number', 'masterstudy-lms-learning-management-system' ),
		'page'             => esc_html__( 'Page', 'masterstudy-lms-learning-management-system' ),
		'note'             => esc_html__( 'Note', 'masterstudy-lms-learning-management-system' ),
		'note_placeholder' => esc_html__( 'Enter your bookmark note', 'masterstudy-lms-learning-management-system' ),
		'cancel'           => esc_html__( 'Cancel', 'masterstudy-lms-learning-management-system' ),
	),
);

wp_localize_script(
	'masterstudy-course-player-pdf-lesson',
	'pdf_lesson',
	$script_data,
);

if ( $allow_bookmarks ) {
	$bookmarks = STM_LMS_Bookmarks::get_user_bookmarks( $item_id, $course_id, $user_id );
}
?>

	<div class="masterstudy-course-player-pdf-lesson hide-on-print">
		<div class="masterstudy-pdf-container">
			<div class="masterstudy-pdf-container__pdf">
				<button class="masterstudy-pdf-btn masterstudy-pdf-container__back-btn">
					<span class="stmlms-arrow-left"></span>
				</button>
				<div class="masterstudy-pdf-container__canvas" style="--scale-factor: 1">
					<canvas class="masterstudy-pdf-container__pdf-view"></canvas>
					<div class="textLayer"></div>
				</div>
				<button class="masterstudy-pdf-btn masterstudy-pdf-container__next-btn">
					<span class="stmlms-arrow-left"></span>
				</button>
			</div>
			<div class="masterstudy-pdf-container__footer">
				<button class="masterstudy-pdf-btn masterstudy-pdf-container__back-btn">
					<span class="stmlms-arrow-left"></span>
				</button>
				<div class="masterstudy-pdf-container__toolbar">
					<div class="masterstudy-toolbar__section masterstudy-toolbar__pages">
						<span><?php echo esc_html__( 'Page', 'masterstudy-lms-learning-management-system' ); ?></span>
						<input id="toolbar__pages-input" value="1">
						<span><?php echo esc_html__( 'of', 'masterstudy-lms-learning-management-system' ); ?></span> <span class="masterstudy-toolbar__total_pages">0</span>
					</div>

					<div class="masterstudy-toolbar__menu">
						<button class="masterstudy-toolbar__section masterstudy-toolbar__icon-btn masterstudy-toolbar__menu-btn">
						</button>
						<div class="masterstudy-toolbar__menu-tooltip">
							<div class="masterstudy-toolbar__menu-tooltip-overlay"></div>
							<div class="masterstudy-toolbar__menu-tooltip-actions">
								<button
									class="masterstudy-toolbar__menu-tooltip-item masterstudy-toolbar__menu-tooltip-item_primary masterstudy-toolbar__download-btn">
									<span class="stmlms-download"></span>
									<span>
										<?php echo esc_html__( 'Download', 'masterstudy-lms-learning-management-system' ); ?>
									</span>
								</button>
								<button
									class="masterstudy-toolbar__menu-tooltip-item masterstudy-toolbar__menu-tooltip-item_primary masterstudy-toolbar__print-btn">
									<span class="stmlms-print"></span>
									<span><?php echo esc_html__( 'Print', 'masterstudy-lms-learning-management-system' ); ?></span>
								</button>
								<button
									class="masterstudy-toolbar__menu-tooltip-item masterstudy-toolbar__menu-tooltip-item_primary masterstudy-toolbar__open-new-tab-btn">
									<span class="stmlms-open-new-tab"></span>
									<span><?php echo esc_html__( 'Open in new tab', 'masterstudy-lms-learning-management-system' ); ?></span>
								</button>
								<button
									class="masterstudy-toolbar__menu-tooltip-item masterstudy-toolbar__menu-tooltip-item_secondary masterstudy-toolbar__close-modal-btn">
									<span><?php echo esc_html__( 'Cancel', 'masterstudy-lms-learning-management-system' ); ?></span>
								</button>
							</div>
						</div>
					</div>
					<div class="masterstudy-toolbar__section masterstudy-toolbar__zoom">
						<button class="masterstudy-pdf-btn masterstudy-toolbar__zoom-out-btn">
						</button>
						<input class="masterstudy-toolbar__zoom-value" value="100%" type="text">
						<button class="masterstudy-pdf-btn masterstudy-toolbar__zoom-in-btn">
						</button>
					</div>
					<button class="masterstudy-toolbar__section masterstudy-toolbar__icon-btn masterstudy-toolbar__download-btn">
						<span class="stmlms-download"></span>
					</button>
					<button class="masterstudy-toolbar__section masterstudy-toolbar__icon-btn masterstudy-toolbar__print-btn">
						<span class="stmlms-print"></span>
					</button>
					<button class="masterstudy-toolbar__section masterstudy-toolbar__icon-btn masterstudy-toolbar__open-new-tab-btn">
						<span class="stmlms-open-new-tab"></span>
					</button>
					<button class="masterstudy-toolbar__section masterstudy-toolbar__icon-btn masterstudy-toolbar__expand-btn">
						<span class="masterstudy-toolbar__expand-icon stmlms-maximize"></span>
						<span class="masterstudy-toolbar__minimize-icon stmlms-minimize"></span>
					</button>
				</div>
				<button class="masterstudy-pdf-btn masterstudy-pdf-container__next-btn">
					<div class="masterstudy-right-arrow-icon">
						<span class="stmlms-arrow-left"></span>
					</div>
				</button>
			</div>
		</div>

		<?php if ( $allow_bookmarks ) : ?>
		<div class="masterstudy-bookmarks masterstudy-bookmarks_opened">
			<div class="masterstudy-bookmarks__header">
				<span><?php echo esc_attr( $bookmarks_title ); ?></span>
				<div class="masterstudy-bookmarks__collapse-icon">
					<span class="stmlms-chevron-left1"></span>
				</div>
			</div>
			<div class="masterstudy-bookmarks-content">
				<ul class="masterstudy-bookmarks__list">
					<?php foreach ( $bookmarks as $bookmark ) : ?>
						<li class="masterstudy-bookmarks__list-item"
							data-bookmark-id="<?php echo esc_attr( $bookmark->id ); ?>">
							<div class="masterstudy-bookmarks__list-item-content">
								<span class="masterstudy-bookmarks__list-item-page"><?php echo esc_attr( $bookmark->page_number ); ?></span>
								<div class="masterstudy-bookmarks__list-item-field">
									<span class="masterstudy-bookmarks__list-item-field-label"><?php echo esc_html__( 'Page Number', 'masterstudy-lms-learning-management-system' ); ?></span>
									<input class="masterstudy-bookmarks__list-item-page__input" name="page" value="<?php echo esc_attr( $bookmark->page_number ); ?>" type="number">
								</div>

								<span class="masterstudy-bookmarks__list-item-title"><?php echo esc_attr( $bookmark->title ); ?></span>
								<div class="masterstudy-bookmarks__list-item-field">
									<span class="masterstudy-bookmarks__list-item-field-label"><?php echo esc_html__( 'Note', 'masterstudy-lms-learning-management-system' ); ?></span>
									<input class="masterstudy-bookmarks__list-item-title__input" name="title" value="<?php echo esc_attr( $bookmark->title ); ?>" type="text">
									<button class="masterstudy-bookmarks__list-item-save">
										<span><?php echo esc_html__( 'Save', 'masterstudy-lms-learning-management-system' ); ?></span>
									</button>
								</div>

								<div class="masterstudy-bookmarks__list-item-actions">
									<button class="masterstudy-bookmarks__list-item-close">
										<?php echo esc_html__( 'Cancel', 'masterstudy-lms-learning-management-system' ); ?>
									</button>
									<button class="masterstudy-bookmarks__list-item-edit-btn">
										<span class="stmlms-pencil1"></span>
									</button>
									<button class="masterstudy-bookmarks__list-item-delete-btn">
										<span class="stmlms-trash1"></span>
									</button>
								</div>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
				<div class="masterstudy-bookmarks__new-bookmark-container">
					<button class="masterstudy-bookmarks__new-bookmark-btn">
						<span class="masterstudy-bookmarks__new-bookmark-btn-icon">
							<span>
								+
							</span>
						</span>
						<span class="masterstudy-bookmarks__new-bookmark-btn-text"><?php echo esc_html__( 'New bookmark', 'masterstudy-lms-learning-management-system' ); ?></span>
					</button>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
<?php
