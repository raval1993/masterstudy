<?php

namespace MasterStudy\Lms\Http\Controllers\Lesson;

use MasterStudy\Lms\Enums\LessonType;
use MasterStudy\Lms\Enums\LessonVideoType;
use MasterStudy\Lms\Enums\LessonAudioType;
use MasterStudy\Lms\Http\Controllers\CourseBuilder\UpdateCustomFieldsController;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\LessonRepository;
use MasterStudy\Lms\Validation\ConditionalRules;
use MasterStudy\Lms\Validation\Validator;

class CreateController {
	public function __invoke( \WP_REST_Request $request ): \WP_REST_Response {
		$lesson_types = apply_filters( 'masterstudy_lms_lesson_types', array_map( 'strval', LessonType::cases() ) );
		$video_types  = apply_filters( 'masterstudy_lms_lesson_video_types', array_map( 'strval', LessonVideoType::cases() ) );
		$audio_types  = apply_filters( 'masterstudy_lms_lesson_audio_types', array_map( 'strval', LessonAudioType::cases() ) );
		// phpcs:ignore Squiz.PHP.CommentedOutCode.Found
		// todo: uncomment when validation will support array of objects
		// 'files.*.id'        => 'required|integer'
		//* 'files.*.label'     => 'required|string'
		$rules = array(
			'type'                    => 'required|contains_list,' . implode( ';', $lesson_types ),
			'title'                   => 'required|string',
			'excerpt'                 => 'nullable|string',
			'duration'                => 'nullable|string',
			'preview'                 => 'boolean',
			'content'                 => 'required_if,type;' . LessonType::TEXT . '|string',
			'video_type'              => 'required_if,type;' . LessonType::VIDEO . '|contains_list,' . implode( ';', $video_types ),
			'audio_type'              => 'required_if,type;audio|contains_list,' . implode( ';', $audio_types ),
			'audio_required_progress' => 'nullable|integer|min,0|max,100',
			'embed_ctx'               => 'nullable|string',
			'external_url'            => 'nullable|string',
			'presto_player_idx'       => 'nullable|integer',
			'vdocipher_id'            => 'nullable|string',
			'shortcode'               => 'nullable|string',
			'youtube_url'             => 'nullable|string',
			'video'                   => 'nullable|integer',
			'video_poster'            => 'nullable|integer',
			'video_width'             => 'nullable|integer|min,1',
			'video_required_progress' => 'nullable|integer|min,0|max,100',
			'vimeo_url'               => 'nullable|string',
			'file'                    => 'nullable|integer',
			'video_captions'          => 'array',
			'files'                   => 'array',
			'custom_fields'           => 'array',
			'pdf_file'                => 'array',
			'pdf_read_all'            => 'boolean',
		);

		$rules = apply_filters( 'masterstudy_lms_lesson_validation_rules', $rules );

		$validator = new Validator(
			$request->get_json_params(),
			$rules
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$lesson_repository = new LessonRepository();
		$data              = $validator->get_validated();
		$lesson_id         = $lesson_repository->create( $data );

		if ( LessonType::PDF === $data['type'] && ! \STM_LMS_Helpers::is_pro_plus() ) {
			return WpResponseFactory::forbidden();
		}

		if ( ! empty( $data['custom_fields'] ) ) {
			$response = ( new UpdateCustomFieldsController() )->update( $lesson_id, $data['custom_fields'] );

			if ( is_array( $response ) ) {
				return WpResponseFactory::validation_failed( $response );
			}
		}

		return new \WP_REST_Response(
			array(
				'id' => $lesson_id,
			)
		);
	}
}
