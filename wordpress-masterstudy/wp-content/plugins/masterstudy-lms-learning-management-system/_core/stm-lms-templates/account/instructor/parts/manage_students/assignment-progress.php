<?php
use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentStudentRepository;

if ( ! STM_LMS_Helpers::is_pro() || ! is_ms_lms_addon_enabled( 'assignments' ) ) {
	return;
}

$assignments_repo = new AssignmentStudentRepository();
$assignments      = $assignments_repo->get_assignments(
	array(
		'student_id'    => $student_id,
		'assignment_id' => $material['post_id'],
		'return_query'  => true,
	)
);
?>
<div class="masterstudy-student-progress-list__item-content<?php echo esc_attr( $assignments->have_posts() ? ' masterstudy-student-progress-list__item-content_completed' : '' ); ?>">
	<div class="masterstudy-student-progress-assignment">
		<?php
		if ( $assignments->have_posts() ) :
			while ( $assignments->have_posts() ) :
				$assignments->the_post();

				$assignment_id = get_the_ID();
				$review_status = $assignments_repo->get_status( $assignment_id );
				$attempt       = get_post_meta( $assignment_id, 'try_num', true );
				$review        = get_post_meta( $assignment_id, 'editor_comment', true );
				$post_status   = get_post_status( $assignment_id );
				$status_class  = 'passed' === $review_status ? 'correct' : 'wrong';
				$status_class  = 'pending' === $review_status ? 'pending' : $status_class;
				?>
				<div class="masterstudy-student-progress-assignment__attempt masterstudy-student-progress-assignment__attempt_<?php echo esc_attr( $status_class ); ?> masterstudy-student-progress-assignment__attempt_full">
					<div class="masterstudy-student-progress-assignment__attempt-wrapper">
						<div class="masterstudy-student-progress-assignment__attempt-content">
							<div class="masterstudy-student-progress-assignment__attempt-title">
								<?php esc_html_e( 'Student attempt', 'masterstudy-lms-learning-management-system' ); ?>
								<?php echo esc_html( '№' . $attempt ); ?>
							</div>
							<?php the_content(); ?>
							<div class="masterstudy-student-progress-assignment__attempt-media">
								<?php
								$attachments = STM_LMS_Assignments::get_draft_attachments( $assignment_id, 'student_attachments' );
								if ( ! empty( $attachments ) ) {
									STM_LMS_Templates::show_lms_template(
										'components/file-attachment',
										array(
											'attachments' => $attachments,
											'dark_mode'   => false,
										)
									);
								}
								?>
							</div>
						</div>
						<div class="masterstudy-student-progress-assignment__attempt-answer-wrapper">
							<div class="masterstudy-student-progress-assignment__attempt-answer">
								<div class="masterstudy-student-progress-assignment__answer-item">
									<div class="masterstudy-student-progress-assignment__answer-item-wrapper">
										<div class="masterstudy-student-progress-assignment__answer-item-text">
											<div class="masterstudy-student-progress-assignment__answer-item-icon">
												<span class="masterstudy-<?php echo esc_attr( 'pending' === $status_class ? $status_class : $status_class . 'ly' ); ?>"></span>
											</div>
											<div class="masterstudy-student-progress-assignment__answer-item-content">
												<?php echo 'pending' === $status_class ? esc_html__( 'Pending for review...', 'masterstudy-lms-learning-management-system' ) : wp_kses_post( $review ); ?>
											</div>
										</div>
										<div class="masterstudy-student-progress-assignment__answer-item-media">
										<?php
											$attachment_ids     = get_post_meta( $assignment_id, 'instructor_attachments', true );
											$attachment_ids     = ! empty( $attachment_ids ) ? $attachment_ids : array();
											$review_attachments = STM_LMS_Assignments::get_draft_attachments( $assignment_id, 'instructor_attachments' );
											STM_LMS_Templates::show_lms_template(
												'components/file-attachment',
												array(
													'attachments' => $review_attachments,
													'download'    => true,
													'deletable'   => false,
												)
											);
										?>
										</div>
										<div class="masterstudy-student-progress-assignment__instructor">
											<?php
											$instructor = STM_LMS_USER::get_current_user( get_current_user_id() );

											if ( ! empty( $instructor ) ) {
												$username = $instructor['login'] ?? '';
												echo wp_kses_post( $instructor['avatar'] ?? '' );
												?>
											<div class="masterstudy-student-progress-assignment__instructor-info">
												<span class="masterstudy-student-progress-assignment__instructor-position">
													<?php esc_html_e( 'Instructor', 'masterstudy-lms-learning-management-system' ); ?>
												</span>
												<span class="masterstudy-student-progress-assignment__instructor-username">
													<?php echo esc_html( $username ); ?>
												</span>
											</div>
											<?php } ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
			endwhile;
		endif;
		?>
	</div>
</div>
<div class="masterstudy-student-progress-list__item-content_empty">
	<?php esc_html_e( 'No assignments yet...', 'masterstudy-lms-learning-management-system' ); ?>
</div>
