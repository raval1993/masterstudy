<?php

namespace MasterStudy\Lms\Repositories;

use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Repositories\StudentProgress\AssignmentDetailsBuilder;
use MasterStudy\Lms\Repositories\StudentProgress\ProgressHelper;
use MasterStudy\Lms\Repositories\StudentProgress\ProgressOverviewBuilder;
use MasterStudy\Lms\Repositories\StudentProgress\QuizDetailsBuilder;

final class StudentProgressRepository {
	private ProgressOverviewBuilder $overview_builder;
	private AssignmentDetailsBuilder $assignment_details_builder;
	private QuizDetailsBuilder $quiz_details_builder;
	private StudentsRepository $students_repository;
	private ProgressHelper $helper;

	public function __construct() {
		$this->helper                     = new ProgressHelper();
		$this->overview_builder           = new ProgressOverviewBuilder( $this->helper );
		$this->assignment_details_builder = new AssignmentDetailsBuilder( $this->helper );
		$this->quiz_details_builder       = new QuizDetailsBuilder();
		$this->students_repository        = new StudentsRepository();
	}

	public function get_student_progress( int $course_id, int $student_id ): array {
		$progress = \STM_LMS_User_Manager_Course_User::_student_progress( $course_id, $student_id );

		return $this->overview_builder->build( $course_id, $student_id, $progress );
	}

	public function get_student_progress_material_details( int $course_id, int $student_id, int $material_id ): array {
		if ( ! $this->students_repository->course_has_material( $course_id, $material_id ) ) {
			return array();
		}

		$material_type = get_post_type( $material_id );
		$title         = $this->helper->decode_progress_text( get_the_title( $material_id ) );

		switch ( $material_type ) {
			case PostType::ASSIGNMENT:
				return array(
					'material_id' => $material_id,
					'type'        => 'assignment',
					'title'       => $title,
					'details'     => $this->assignment_details_builder->build( $course_id, $student_id, $material_id ),
				);
			case PostType::QUIZ:
				return array(
					'material_id' => $material_id,
					'type'        => 'quiz',
					'title'       => $title,
					'details'     => $this->quiz_details_builder->build( $course_id, $student_id, $material_id ),
				);
			default:
				return array();
		}
	}
}
