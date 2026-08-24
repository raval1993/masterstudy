<?php
/**
 * @var int $course_id
 * @var array $curriculum
 * @var boolean $show_section_title
 * @var integer $section_to_show
 * @var boolean $dark_mode
 */

use MasterStudy\Lms\Repositories\CoursePlayerRepository;

$trial_lessons       = 0;
$material_index      = 0;
$section_index       = 0;
$guest_trial_enabled = false;
$is_enrolled         = is_user_logged_in()
	? STM_LMS_Course::get_user_course( get_current_user_id(), $course_id )
	: false;

if ( class_exists( 'STM_LMS_Shareware' ) ) {
	$is_trial_course = get_post_meta( $course_id, 'shareware', true );

	if ( 'on' === $is_trial_course ) {
		$shareware_settings  = get_option( 'stm_lms_shareware_settings' );
		$guest_trial_enabled = $shareware_settings['shareware_guest_trial'] ?? true;
		$trial_lessons       = intval( $shareware_settings['shareware_count'] ?? 0 );
	}
}

$user_id      = get_current_user_id();
$user_courses = STM_LMS_User::get_user_course_access_list( $user_id );

foreach ( $curriculum as $section ) {
	$current_index = $section_index++;

	if ( 'all' !== $section_to_show && $section_index !== (int) $section_to_show ) {
		continue;
	}

	$section['materials'] = ( new CoursePlayerRepository() )->hydrate_materials( $section['materials'], $course_id, get_current_user_id() );
	?>
	<div class="masterstudy-curriculum-list__wrapper masterstudy-curriculum-list__wrapper_opened">
		<?php if ( $show_section_title ) { ?>
			<div class="masterstudy-curriculum-list__section">
				<span class="masterstudy-curriculum-list__section-title"><?php echo esc_html( $section['title'] ); ?></span>
				<span class="masterstudy-curriculum-list__toggler"></span>
			</div>
		<?php } ?>
		<ul class="masterstudy-curriculum-list__materials">
			<?php
			foreach ( $section['materials'] as $material ) {
				$material_index++;
				$is_trial       = ! $is_enrolled && $trial_lessons > 0 && $material_index <= $trial_lessons;
				$is_preview     = ! $is_enrolled && STM_LMS_Lesson::lesson_has_preview( $material['post_id'] );
				$has_access     = isset( $user_courses[ $course_id ] );
				$material       = apply_filters( 'masterstudy_lms_lesson_curriculum_data', $material, $curriculum, $course_id );
				$lesson_excerpt = get_post_meta( $material['post_id'], 'lesson_excerpt', true );
				$question_count = ! empty( $material['questions_array'] ) ? count( $material['questions_array'] ) : 0;
				$question_count = ! empty( $material['question_bank_total_items'] ) ? $material['question_bank_total_items'] : $question_count;
				?>
				<li class="masterstudy-curriculum-list__item">
				<?php
				if ( ! get_post_meta( get_the_ID(), 'coming_soon_status', true ) || ! is_ms_lms_addon_enabled( 'coming_soon' ) ) {
					?>
					<a href="<?php echo esc_url( STM_LMS_Lesson::get_lesson_url( $course_id, $material['post_id'] ) ); ?>"
					<?php
				} else {
					?>
					<a
					<?php
				}
				?>
					class="masterstudy-curriculum-list__link <?php echo esc_attr( $material['lesson_locked_by_drip'] || ( ! $has_access && ! $is_preview && ( ! $is_trial || ! $guest_trial_enabled ) ) ? 'masterstudy-curriculum-list__link_disabled' : '' ); ?>">
						<?php if ( 'yes' === $show_lesson_order ) { ?>
							<div class="masterstudy-curriculum-list__order">
								<?php echo esc_html( $material_index ); ?>
							</div>
						<?php } ?>
						<img src="<?php echo esc_url( STM_LMS_URL . "/assets/icons/lessons/{$material['icon']}.svg" ); ?>" class="masterstudy-curriculum-list__image">
						<div class="masterstudy-curriculum-list__container">
							<div class="masterstudy-curriculum-list__container-wrapper">
								<div class="masterstudy-curriculum-list__title">
									<?php echo esc_html( $material['title'] ); ?>
								</div>
								<div class="masterstudy-curriculum-list__meta-wrapper">
									<?php
									if ( $material['lesson_lock_before_start'] || $material['lesson_locked_by_drip'] ) {
										?>
										<span class="masterstudy-curriculum-list__locked">
											<?php
											STM_LMS_Templates::show_lms_template(
												'components/hint',
												array(
													'content' => $material['lesson_lock_message'],
													'side' => 'right',
													'dark_mode' => $dark_mode,
												)
											);
											?>
										</span>
										<?php
									}
									if ( $is_trial ) {
										?>
										<span class="masterstudy-curriculum-list__trial">
											<?php echo esc_html__( 'Trial', 'masterstudy-lms-learning-management-system' ); ?>
										</span>
									<?php } elseif ( $is_preview ) { ?>
										<span class="masterstudy-curriculum-list__preview">
											<?php echo esc_html__( 'Preview', 'masterstudy-lms-learning-management-system' ); ?>
										</span>
									<?php } ?>
									<span class="masterstudy-curriculum-list__meta">
										<?php
										if ( 'stm-quizzes' === $material['post_type'] ) {
											/* translators: %s: number */
											echo esc_html( $question_count ? sprintf( __( '%d questions', 'masterstudy-lms-learning-management-system' ), $question_count ) : '' );
											echo esc_html( ! $question_count ? $material['label'] : '' );
										} else {
											if ( ! empty( $material['progress'] ) ) {
												?>
												<div class="masterstudy-curriculum-list__meta-value">
													<?php echo esc_html( $material['progress'] ); ?>
												</div>
												<?php
											}
											echo esc_html( $material['duration'] ?? '' );
											echo esc_html( empty( $material['progress'] ) && empty( $material['duration'] ) ? $material['label'] : '' );
										}
										?>
									</span>
									<?php if ( ! empty( $lesson_excerpt ) ) { ?>
										<span class="masterstudy-curriculum-list__excerpt-toggler"></span>
									<?php } ?>
								</div>
							</div>
							<?php if ( ! empty( $lesson_excerpt ) ) { ?>
								<div class="masterstudy-curriculum-list__excerpt">
									<div class="masterstudy-curriculum-list__excerpt-wrapper">
										<?php echo wp_kses_post( $lesson_excerpt ); ?>
									</div>
								</div>
							<?php } ?>
						</div>
					</a>
				</li>
			<?php } ?>
		</ul>
	</div>
	<?php
	if ( 'all' !== $section_to_show ) {
		break;
	}
}
