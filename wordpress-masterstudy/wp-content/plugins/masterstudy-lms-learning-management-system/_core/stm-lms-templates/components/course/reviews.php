<?php
/**
 * @var object $course
 * @var integer $user_id
 * @var string $style
 *
 * masterstudy-single-course-reviews__form_active - to show add review form
 */

$style = isset( $style ) ? $style : '';

wp_localize_script(
	'masterstudy-single-course-components',
	'reviews_data',
	array(
		'course_id'              => $course->id,
		'author_label'           => esc_html__( 'by', 'masterstudy-lms-learning-management-system' ),
		'editor_id'              => 'editor_add_review_' . $course->id,
		'status'                 => 'pending for review',
		'style'                  => $style,
		'student_public_profile' => STM_LMS_Options::get_option( 'student_public_profile', true ),
	)
);

$stars        = range( 1, 5 );
$marks        = array(
	'5' => 0,
	'4' => 0,
	'3' => 0,
	'2' => 0,
	'1' => 0,
);
$course_marks = $course->is_udemy_course ? $course->udemy_marks : $course->marks;
$rate         = $course->is_udemy_course ? $course->udemy_rate : $course->rate['average'];

if ( $course->is_udemy_course ) {
	foreach ( $course->udemy_rating_distribution as $index => $review ) {
		$marks[ $review['rating'] ] = $review['count'];
	}
} else {
	foreach ( $course->marks as $review ) {
		$marks[ $review ]++;
	}
}
?>
<div class="masterstudy-single-course-reviews masterstudy-single-course-reviews_<?php echo esc_attr( $style ); ?>" data-course-id="<?php echo esc_attr( $course->id ); ?>">
	<div class="masterstudy-single-course-reviews__main <?php echo empty( $course_marks ) ? 'masterstudy-single-course-reviews__main_empty' : ''; ?>">
		<?php if ( ! empty( $user_id ) ) { ?>
			<div class="masterstudy-single-course-reviews__form <?php echo empty( $course_marks ) ? 'masterstudy-single-course-reviews__form_empty' : ''; ?>">
				<div class="masterstudy-single-course-reviews__form-header">
					<span class="masterstudy-single-course-reviews__form-title">
						<?php echo esc_html__( 'Leave your review', 'masterstudy-lms-learning-management-system' ); ?>
					</span>
					<span class="masterstudy-single-course-reviews__form-close"></span>
				</div>
				<div class="masterstudy-single-course-reviews__form-rating">
					<?php foreach ( $stars as $star ) { ?>
						<span class="masterstudy-single-course-reviews__star"></span>
					<?php } ?>
				</div>
				<div class="masterstudy-single-course-reviews__form-editor">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/wp-editor',
						array(
							'id'       => 'editor_add_review_' . $course->id,
							'content'  => '',
							'settings' => array(
								'quicktags'     => false,
								'media_buttons' => false,
								'textarea_rows' => 13,
							),
						)
					);
					?>
				</div>
				<div class="masterstudy-single-course-reviews__form-actions">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/button',
						array(
							'id'    => 'masterstudy-single-course-reviews-submit',
							'title' => esc_html__( 'Submit review', 'masterstudy-lms-learning-management-system' ),
							'link'  => '#',
							'style' => 'primary',
							'size'  => 'sm',
						)
					);
					?>
				</div>
			</div>
		<?php } ?>
		<div class="masterstudy-single-course-reviews__form-message"></div>
		<div class="masterstudy-single-course-reviews__row">
			<div class="masterstudy-single-course-reviews__detailed	">
				<?php if ( ! empty( $course_marks ) ) { ?>
					<div class="masterstudy-single-course-reviews__count">
						<?php echo is_float( $rate ) && floor( $rate ) === $rate ? (int) $rate . '.0' : esc_html( $rate ); ?>
					</div>
					<div class="masterstudy-single-course-reviews__stars">
						<?php foreach ( $stars as $star ) { ?>
							<span class="masterstudy-single-course-reviews__star <?php echo esc_attr( ( $star <= floor( $rate ) ) ? 'masterstudy-single-course-reviews__star_filled ' : '' ); ?>"></span>
						<?php } ?>
					</div>
					<div class="masterstudy-single-course-reviews__quantity">
						<?php
							printf(
								esc_html(
									/* translators: %d integer marks */
									_n(
										'%s review',
										'%s reviews',
										array_sum( $marks ),
										'masterstudy-lms-learning-management-system'
									)
								),
								esc_html( array_sum( $marks ) )
							);
						?>
					</div>
					<?php
				} if ( ! empty( $user_id ) ) {
					if ( STM_LMS_Options::get_option( 'course_allow_review', true ) || STM_LMS_User::has_course_access( $course->id ) ) {
						?>
						<span class="masterstudy-single-course-reviews__add-button">
							<span class="masterstudy-single-course-reviews__add-button-icon"></span>
							<?php echo esc_html__( 'Write review', 'masterstudy-lms-learning-management-system' ); ?>
						</span>
						<?php
					} else {
						?>
						<div class="masterstudy-single-course-reviews__buy">
							<?php echo esc_html__( 'Please buy the course to leave your feedback', 'masterstudy-lms-learning-management-system' ); ?>
						</div>
						<?php
					}
				} else {
					?>
					<div class="masterstudy-single-course-reviews__login">
						<?php
						printf(
							/* translators: %s: leave review */
							wp_kses_post( __( 'Please, <a href="%s" class="masterstudy-single-course-reviews__login-link" target="_blank">login</a> to leave a review', 'masterstudy-lms-learning-management-system' ) ),
							esc_url( STM_LMS_User::login_page_url() )
						);
						?>
					</div>
				<?php } ?>
			</div>
			<?php if ( ! empty( $course_marks ) ) { ?>
				<div class="masterstudy-single-course-reviews__stats">
					<?php foreach ( $marks as $mark => $mark_count ) { ?>
						<div class="masterstudy-single-course-reviews__stats-item">
							<div class="masterstudy-single-course-reviews__stats-item-mark">
								<?php
								printf(
									/* translators: %s Marks */
									esc_html__( 'Stars %s', 'masterstudy-lms-learning-management-system' ),
									esc_html( $mark )
								);
								?>
							</div>
							<?php
							$total = $course->is_udemy_course ? $course_marks : count( $course_marks );
							STM_LMS_Templates::show_lms_template(
								'components/progress',
								array(
									'progress'  => $mark_count * 100 / $total,
									'hide_info' => true,
								)
							);
							?>
							<div class="masterstudy-single-course-reviews__stats-item-count">
								<?php echo esc_html( $mark_count ); ?>
							</div>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</div>
	<?php if ( ! empty( $course_marks ) ) { ?>
		<div class="masterstudy-single-course-reviews__list">
			<div class="masterstudy-single-course-reviews__list-wrapper"></div>
			<div class="masterstudy-single-course-reviews__more">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'id'    => 'masterstudy-single-course-reviews-more',
						'title' => esc_html__( 'Show more', 'masterstudy-lms-learning-management-system' ),
						'link'  => '#',
						'style' => 'primary',
						'size'  => 'sm',
					)
				);
				?>
			</div>
		</div>
	<?php } ?>
</div>
