<?php

namespace MasterStudy\Lms\Http\Serializers;

use MasterStudy\Lms\Models\Course;

final class CourseSerializer extends AbstractSerializer {
	/**
	 * @param Course $course
	 */
	public function toArray( $course ): array {
		return apply_filters(
			'masterstudy_lms_pro_course_serialize',
			array(
				'access_status'     => $course->access_status,
				'category'          => $course->category,
				'certificate_id'    => $course->certificate_id,
				'page_style'        => $course->course_page_style,
				'co_instructor'     => $this->when(
					$course->co_instructor,
					function ( \WP_User $co_instructor ) {
						return array(
							'id'   => $co_instructor->ID,
							'name' => $co_instructor->display_name,
						);
					}
				),
				'content'           => $course->content,
				'current_students'  => $course->current_students,
				'duration_info'     => $course->duration_info,
				'requirements'      => $course->requirements,
				'basic_info'        => $course->basic_info,
				'intended_audience' => $course->intended_audience,
				'end_time'          => $course->end_time,
				'excerpt'           => $course->excerpt,
				'expiration'        => $course->expiration,
				'files'             => $course->files,
				'id'                => $course->id,
				'image'             => $course->image,
				'is_featured'       => $course->is_featured,
				'is_lock_lesson'    => $course->is_lock_lesson,
				'owner'             => array(
					'id'     => $course->owner->ID,
					'name'   => $course->owner->display_name,
					'avatar' => \STM_LMS_User::get_avatar_url( $course->owner->ID ),
				),
				'level'             => $course->level,
				'prerequisites'     => $course->prerequisites,
				'shareware'         => $course->shareware,
				'slug'              => $course->slug,
				'status'            => $course->status,
				'status_date_end'   => $course->status_date_end,
				'status_date_start' => $course->status_date_start,
				'title'             => html_entity_decode( $course->title ),
				'video_duration'    => $course->video_duration,
				'views'             => $course->views,
				'access_duration'   => $course->access_duration,
				'access_devices'    => $course->access_devices,
				'certificate_info'  => $course->certificate_info,
			)
		);
	}

}
