<?php
/**
 * @var int $id
 * @var int $user_id
 * @var int $course_id
 * @var array $video_questions
 * @var array $video_questions_stats
 * @var boolean $lesson_completed
 */

use MasterStudy\Lms\Repositories\LessonRepository;

STM_LMS_Templates::show_lms_template(
	'components/video-media',
	array(
		'lesson'                => ( new LessonRepository() )->get( $id ),
		'id'                    => $id,
		'user_id'               => $user_id,
		'course_id'             => $course_id,
		'video_questions'       => $video_questions,
		'video_questions_stats' => $video_questions_stats,
		'lesson_completed'      => $lesson_completed,
	)
);
