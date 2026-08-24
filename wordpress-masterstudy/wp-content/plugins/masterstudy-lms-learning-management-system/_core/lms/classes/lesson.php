<?php

use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentStudentRepository;
use \MasterStudy\Lms\Repositories\CurriculumMaterialRepository;
use \MasterStudy\Lms\Repositories\CurriculumSectionRepository;

STM_LMS_Lesson::init();

class STM_LMS_Lesson {

	public static function init() {
		add_action( 'wp_ajax_stm_lms_complete_lesson', 'STM_LMS_Lesson::complete_lesson' );
		add_action( 'wp_ajax_nopriv_stm_lms_complete_lesson', 'STM_LMS_Lesson::complete_lesson' );
		add_action( 'wp_ajax_stm_lms_total_progress', 'STM_LMS_Lesson::total_progress' );
		add_action( 'wp_ajax_stm_lms_answer_video_lesson', 'STM_LMS_Lesson::answer_video_lesson' );
	}

	public static function get_lesson_url( $post_id, $lesson_id ) {
		if ( empty( $lesson_id ) ) {
			$lesson_id = self::get_first_lesson( $post_id );
		}

		if ( 'publish' === get_post_status( $post_id ) ) {
			$course_url = get_permalink( $post_id );
		} else {
			$course_slug = get_post_field( 'post_name', $post_id );
			$course_url  = home_url( STM_LMS_Options::courses_page_slug() . "/{$course_slug}/" );
		}

		return esc_url( "{$course_url}" . stm_lms_get_wpml_binded_id( $lesson_id ) );
	}

	public static function is_lesson_completed( $user_id, $course_id, $lesson_id ) {
		if ( empty( $user_id ) ) {
			$user = STM_LMS_User::get_current_user();
			if ( empty( $user ) ) {
				return false;
			}
			$user_id = $user['id'];
		}

		if ( get_post_type( $lesson_id ) === PostType::LESSON || get_post_type( $lesson_id ) === PostType::GOOGLE_MEET ) {
			$already_completed = stm_lms_get_user_lesson( $user_id, $course_id, $lesson_id, array( 'lesson_id' ) );
		} elseif ( get_post_type( $lesson_id ) === PostType::ASSIGNMENT ) {
			/*If addon is disabled we can skip it*/
			if ( ! class_exists( '\MasterStudy\Lms\Pro\addons\assignments\Assignments' ) ) {
				return true;
			}

			// TODO: Remove method_exists check after few releases
			return method_exists( '\MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentStudentRepository', 'has_passed_assignment' )
				&& ( new AssignmentStudentRepository() )->has_passed_assignment( $lesson_id, $user_id, $course_id );
		} else {
			$already_completed = stm_lms_check_quiz( $user_id, $lesson_id );
		}

		return count( $already_completed ) > 0;
	}

	public static function complete_lesson() {
		check_ajax_referer( 'stm_lms_complete_lesson', 'nonce' );

		$user = STM_LMS_User::get_current_user();
		if ( empty( $user['id'] ) || empty( $_GET['course'] ) || empty( $_GET['lesson'] ) ) {
			die;
		}

		$user_id   = $user['id'];
		$course_id = intval( $_GET['course'] );
		$lesson_id = intval( $_GET['lesson'] );
		$progress  = ! empty( $_GET['progress'] ) ? intval( $_GET['progress'] ) : null;

		/*Check if already passed*/
		if ( self::is_lesson_completed( $user_id, $course_id, $lesson_id ) ) {
			wp_send_json( compact( 'user_id', 'course_id', 'lesson_id' ) );
			die;
		};

		/*Check if lesson in course*/
		$course_materials = ( new CurriculumMaterialRepository() )->get_course_materials( $course_id );

		if ( empty( $course_materials ) || ! in_array( $lesson_id, $course_materials, true ) ) {
			die;
		}

		$end_time   = time();
		$start_time = get_user_meta( $user_id, "stm_lms_course_started_{$lesson_id}_{$course_id}", true );
		stm_lms_add_user_lesson( compact( 'user_id', 'course_id', 'lesson_id', 'start_time', 'end_time', 'progress' ) );

		$email_data = array(
			'user_login'   => \STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $user_id ),
			'course_url'   => get_permalink( $course_id ),
			'course_title' => get_the_title( $course_id ),
			'lesson_title' => get_the_title( $lesson_id ),
			'date'         => current_time( 'mysql' ),
			'blog_name'    => STM_LMS_Helpers::masterstudy_lms_get_site_name(),
			'site_url'     => \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_site_url() ),
		);
		$template = wp_kses_post(
			'We are pleased to inform you that {{user_login}} has completed the lesson {{lesson_title}} in the course {{course_title}}.'
		);

		$message = \MS_LMS_Email_Template_Helpers::render( $template, $email_data );
		$subject = esc_html__( '{{user_login}} Has Completed a Lesson in {{course_title}}', 'masterstudy-lms-learning-management-system' );
		$subject = \MS_LMS_Email_Template_Helpers::render( $subject, $email_data );

		STM_LMS_Helpers::send_email( \STM_LMS_Helpers::masterstudy_lms_get_post_author_email_by_post_id( $course_id ), $subject, $message, 'stm_lms_student_lesson_completed_for_instructor', $email_data );

		STM_LMS_Course::update_course_progress( $user_id, $course_id );

		do_action( 'stm_lms_lesson_passed', $user_id, $lesson_id, $course_id );

		delete_user_meta( $user_id, "stm_lms_course_started_{$lesson_id}_{$course_id}" );

		wp_send_json( compact( 'user_id', 'course_id', 'lesson_id' ) );
	}

	public static function lesson_has_preview( $lesson_id ) {
		return ! empty( get_post_meta( $lesson_id, 'preview', true ) );
	}

	public static function get_lesson_info( $course_id, $lesson_id ) {
		$material = ( new CurriculumMaterialRepository() )->find_by_course_lesson( $course_id, $lesson_id );
		$response = array(
			'section' => esc_html__( 'Section 1', 'masterstudy-lms-learning-management-system' ),
			'lesson'  => '',
		);

		if ( empty( $material ) ) {
			return $response;
		}

		$section             = ( new CurriculumSectionRepository() )->find( $material->section_id );
		$response['section'] = $section->title;

		switch ( $material->post_type ) {
			case 'stm-lessons':
			case 'stm-google-meets':
				$response['type']   = 'lesson';
				$response['lesson'] = sprintf( esc_html__( 'Lecture %s', 'masterstudy-lms-learning-management-system' ), $material->order );
				break;
			case 'stm-assignments':
				$response['type']   = 'assignment';
				$response['lesson'] = sprintf( esc_html__( 'Assignment %s', 'masterstudy-lms-learning-management-system' ), $material->order );
				break;
			default:
				$response['type']   = 'quiz';
				$response['lesson'] = sprintf( esc_html__( 'Quiz %s', 'masterstudy-lms-learning-management-system' ), $material->order );
				break;
		}

		return $response;
	}

	public static function aio_front_scripts() {
		$js_path = UAVC_URL . 'assets/min-js/';
		$ext     = '.min';

		$ultimate_smooth_scroll_compatible = get_option( 'ultimate_smooth_scroll_compatible' );

		// register js
		wp_register_script(
			'ultimate-script',
			UAVC_URL . 'assets/min-js/ultimate.min.js',
			array(
				'jquery',
				'jquery-ui-core',
			),
			ULTIMATE_VERSION,
			true
		);
		wp_register_script( 'ultimate-appear', $js_path . 'jquery-appear' . $ext . '.js', array( 'jquery' ), ULTIMATE_VERSION, true );
		wp_register_script( 'ultimate-custom', $js_path . 'custom' . $ext . '.js', array( 'jquery' ), ULTIMATE_VERSION, true );
		wp_register_script( 'ultimate-vc-params', $js_path . 'ultimate-params' . $ext . '.js', array( 'jquery' ), ULTIMATE_VERSION, true );
		if ( 'enable' === $ultimate_smooth_scroll_compatible ) {
			$smoothScroll = 'SmoothScroll-compatible.min.js';
		} else {
			$smoothScroll = 'SmoothScroll.min.js';
		}
		wp_register_script( 'ultimate-smooth-scroll', UAVC_URL . 'assets/min-js/' . $smoothScroll, array( 'jquery' ), ULTIMATE_VERSION, true );
		wp_register_script( 'ultimate-modernizr', $js_path . 'modernizr-custom' . $ext . '.js', array( 'jquery' ), ULTIMATE_VERSION, true );
		wp_register_script( 'ultimate-tooltip', $js_path . 'tooltip' . $ext . '.js', array( 'jquery' ), ULTIMATE_VERSION, true );

		// register css

		if ( is_rtl() ) {
			$cssext = '-rtl';
		} else {
			$cssext = '';
		}

		Ultimate_VC_Addons::ultimate_register_style( 'ultimate-animate', 'animate' );
		Ultimate_VC_Addons::ultimate_register_style( 'ult_hotspot_rtl_css', UAVC_URL . 'assets/min-css/rtl-common' . $ext . '.css', true );
		Ultimate_VC_Addons::ultimate_register_style( 'ultimate-style', 'style' );
		Ultimate_VC_Addons::ultimate_register_style( 'ultimate-style-min', UAVC_URL . 'assets/min-css/ultimate.min' . $cssext . '.css', true );
		Ultimate_VC_Addons::ultimate_register_style( 'ultimate-tooltip', 'tooltip' );

		$ultimate_smooth_scroll = get_option( 'ultimate_smooth_scroll' );
		if ( 'enable' === $ultimate_smooth_scroll || 'enable' === $ultimate_smooth_scroll_compatible ) {
			$ultimate_smooth_scroll_options = get_option( 'ultimate_smooth_scroll_options' );
			$options                        = array(
				'step'  => ( isset( $ultimate_smooth_scroll_options['step'] ) && '' !== $ultimate_smooth_scroll_options['step'] ) ? (int) $ultimate_smooth_scroll_options['step'] : 80,
				'speed' => ( isset( $ultimate_smooth_scroll_options['speed'] ) && '' !== $ultimate_smooth_scroll_options['speed'] ) ? (int) $ultimate_smooth_scroll_options['speed'] : 480,
			);
			wp_enqueue_script( 'ultimate-smooth-scroll' );
			if ( 'enable' === $ultimate_smooth_scroll ) {
				wp_localize_script( 'ultimate-smooth-scroll', 'php_vars', $options );
			}
		}

		if ( function_exists( 'vc_is_editor' ) ) {
			if ( vc_is_editor() ) {
				wp_enqueue_style( 'vc-fronteditor', UAVC_URL . 'assets/min-css/vc-fronteditor.min.css', array(), ULTIMATE_VERSION );
			}
		}
		$fonts = get_option( 'smile_fonts' );
		if ( is_array( $fonts ) ) {
			foreach ( $fonts as $font => $info ) {
				$style_url = $info['style'] ?? '';
				if ( strpos( $style_url, 'http://' ) !== false ) {
					wp_enqueue_style( 'bsf-' . $font, $info['style'], array(), ULTIMATE_VERSION );
				}
			}
		}

		wp_enqueue_script( 'ultimate-modernizr' );
		wp_enqueue_script( 'jquery_ui' );
		wp_enqueue_script( 'masonry' );
		if ( defined( 'DISABLE_ULTIMATE_GOOGLE_MAP_API' ) && ( true === DISABLE_ULTIMATE_GOOGLE_MAP_API || 'true' === DISABLE_ULTIMATE_GOOGLE_MAP_API ) ) {
			$load_map_api = false;
		} else {
			$load_map_api = true;
		}
		if ( $load_map_api ) {
			wp_enqueue_script( 'googleapis' );
		}
		/* Range Slider Dependecy */
		wp_enqueue_script( 'jquery-ui-mouse' );
		wp_enqueue_script( 'jquery-ui-widget' );
		wp_enqueue_script( 'jquery-ui-slider' );
		wp_enqueue_script( 'ult_range_tick' );
		/* Range Slider Dependecy */
		wp_enqueue_script( 'ultimate-script' );
		wp_enqueue_script( 'ultimate-modal-all' );
		wp_enqueue_script( 'jquery.shake', $js_path . 'jparallax' . $ext . '.js', array(), ULTIMATE_VERSION, true );
		wp_enqueue_script( 'jquery.vhparallax', $js_path . 'vhparallax' . $ext . '.js', array(), ULTIMATE_VERSION, true );

		wp_enqueue_style( 'ultimate-style-min' );
		wp_enqueue_style( 'ult-icons' );
		wp_enqueue_style( 'ultimate-vidcons', UAVC_URL . 'assets/fonts/vidcons.css', array(), ULTIMATE_VERSION );
		wp_enqueue_script( 'jquery.ytplayer', $js_path . 'mb-YTPlayer' . $ext . '.js', array(), ULTIMATE_VERSION, true );

		$ultimate_google_font_manager = new Ultimate_VC_Addons_Google_Font_Manager();
		$ultimate_google_font_manager->enqueue_selected_ultimate_google_fonts();
	}

	public static function get_first_lesson( $course_id ) {
		$course_materials = ( new CurriculumMaterialRepository() )->get_course_materials( $course_id );

		return ! empty( $course_materials ) ? reset( $course_materials ) : 0;
	}

	public static function total_progress() {
		check_ajax_referer( 'stm_lms_total_progress', 'nonce' );

		wp_send_json(
			self::get_total_progress( get_current_user_id(), intval( $_GET['course_id'] ?? 0 ) )
		);
	}

	public static function get_total_progress( $user_id, $course_id ) {
		if ( empty( $user_id ) ) {
			return null;
		}

		$data = array(
			'course'           => STM_LMS_Helpers::simplify_db_array( stm_lms_get_user_course( $user_id, $course_id ) ),
			'curriculum'       => array(),
			'course_completed' => false,
		);

		if ( ( ! empty( $data['course']['progress_percent'] ) ) && $data['course']['progress_percent'] > 100 ) {
			$data['course']['progress_percent'] = 100;
		}

		$curriculum_repository   = new CurriculumMaterialRepository();
		$course_materials        = $curriculum_repository->get_course_materials( $course_id );
		$materials_with_sections = $curriculum_repository->get_course_materials_with_sections( $course_id, $course_materials );
		$curriculum_data         = array();

		foreach ( $course_materials as $item_id ) {
			$type = get_post_meta( $item_id, 'type', true );
			if ( empty( $type ) ) {
				$type = 'text';
			}

			$material = $materials_with_sections[ $item_id ] ?? null;

			$lesson = array(
				'section' => $material ? $material->section_title : esc_html__( 'Section 1', 'masterstudy-lms-learning-management-system' ),
				'lesson'  => '',
				'type'    => 'lesson',
			);

			if ( $material ) {
				switch ( $material->post_type ) {
					case PostType::LESSON:
					case PostType::GOOGLE_MEET:
						$lesson['type']   = 'lesson';
						$lesson['lesson'] = sprintf( esc_html__( 'Lecture %s', 'masterstudy-lms-learning-management-system' ), $material->order );
						break;
					case PostType::ASSIGNMENT:
						$lesson['type']   = 'assignment';
						$lesson['lesson'] = sprintf( esc_html__( 'Assignment %s', 'masterstudy-lms-learning-management-system' ), $material->order );
						break;
					default:
						$lesson['type']   = 'quiz';
						$lesson['lesson'] = sprintf( esc_html__( 'Quiz %s', 'masterstudy-lms-learning-management-system' ), $material->order );
						break;
				}
			}

			$lesson['completed'] = self::is_lesson_completed( $user_id, $course_id, $item_id );

			if ( 'lesson' === $lesson['type'] ) {
				$lesson_type = get_post_meta( $item_id, 'type', true );
				if ( empty( $lesson_type ) ) {
					$lesson_type = 'text';
				}
				$lesson['lesson_type'] = $lesson_type;
			}

			$curriculum_data[] = $lesson;
		}

		foreach ( $curriculum_data as $item_data ) {
			$type = ( 'lesson' === $item_data['type'] && 'text' !== $item_data['lesson_type'] )
				? 'multimedia'
				: $item_data['type'];
			if ( empty( $data['curriculum'][ $type ] ) ) {
				$data['curriculum'][ $type ] = array(
					'total'     => 0,
					'completed' => 0,
				);
			}

			$data['curriculum'][ $type ]['total']++;

			if ( $item_data['completed'] ) {
				$data['curriculum'][ $type ]['completed']++;
			}
		}

		$data['title'] = get_the_title( $course_id );
		$data['url']   = get_permalink( $course_id );

		if ( empty( $data['course'] ) ) {
			$data['course'] = array(
				'progress_percent' => 0,
			);

			return $data;
		}

		/*Completed label*/
		$threshold                = STM_LMS_Options::get_option( 'certificate_threshold', 70 );
		$data['course_completed'] = intval( $threshold ) <= intval( $data['course']['progress_percent'] );
		$data['certificate_url']  = STM_LMS_Course::certificates_page_url( $course_id );

		if ( $data['course_completed'] && \STM_LMS_Helpers::masterstudy_lms_send_course_email_once( $course_id, $user_id ) ) {

			// email course completation to student
			$template = wp_kses_post(
				'We want to congratulate you on successfully completing the <b>{{course_title}}</b>!
			<br> The link to course: <a href="{{course_url}}" target="_blank">{{course_url}}</a>
			<br> We wish you all the best in your future endeavors.'
			);

			$email_data_student = array(
				'user_login'   => \STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $user_id ),
				'course_url'   => get_permalink( $course_id ),
				'course_title' => get_the_title( $course_id ),
			);
			$search             = array(
				'{{user_login}}',
				'{{course_url}}',
				'{{course_title}}',
			);
			$replace            = array(
				$email_data_student['user_login'],
				$email_data_student['course_url'],
				$email_data_student['course_title'],
			);
			$subject            = esc_html__( 'Congratulations on completing {{user_login}}!', 'masterstudy-lms-learning-management-system' );

			$message = str_replace( $search, $replace, $template );
			$subject = str_replace( $search, $replace, $subject );

			STM_LMS_Helpers::send_email(
				\STM_LMS_Helpers::masterstudy_lms_get_user_email( $user_id ),
				$subject,
				$message,
				'stm_lms_course_completed_for_user',
				$email_data_student
			);

			// email course completation to instructor
			$template = wp_kses_post(
				'{{user_login}} has successfully completed your {{course_title}} with great dedication and achievement.
			<br> The link to course: <a href="{{course_url}}" target="_blank">{{course_url}}</a>
			<br> Your support has made all the difference. Thank you for your dedication to student’s success.'
			);

			$subject = esc_html__( 'Congratulations! {{user_login}} Completed {{course_title}}!', 'masterstudy-lms-learning-management-system' );

			$message = str_replace( $search, $replace, $template );
			$subject = str_replace( $search, $replace, $subject );

			STM_LMS_Helpers::send_email(
				\STM_LMS_Helpers::masterstudy_lms_get_post_author_email_by_post_id( $course_id ),
				$subject,
				$message,
				'stm_lms_course_completed_for_instructor',
				$email_data_student
			);
		}

		return $data;
	}

	public static function get_lesson_video_questions( $user_id, $lesson_id ) {
		$markers = stm_lms_get_lesson_markers( $lesson_id );

		if ( ! empty( $markers ) ) {
			foreach ( $markers as &$marker ) {
				if ( empty( $marker['answers'] ) ) {
					continue;
				}

				$marker['answers'] = unserialize( $marker['answers'] );
				$correct_answers   = array_column(
					array_filter(
						$marker['answers'],
						fn ( $a ) => $a['is_correct']
					),
					'answer_id'
				);

				$marker['user_answers'] = stm_lms_get_user_marker_answers( $user_id, $lesson_id, intval( $marker['id'] ) );
				$user_answers           = $marker['user_answers'];

				sort( $correct_answers );
				sort( $user_answers );

				$marker['is_completed'] = $correct_answers === $user_answers;
				$marker['is_answered']  = ! empty( $user_answers );

				if ( ! empty( $marker['answers'] ) ) {
					foreach ( $marker['answers'] as &$answer ) {
						$answer['is_selected'] = in_array( $answer['answer_id'], $marker['user_answers'], true );
					}
				}
			}

			usort(
				$markers,
				function( $a, $b ) {
					return intval( $a['marker'] ) <=> intval( $b['marker'] );
				}
			);
		}

		return $markers;
	}

	public static function answer_video_lesson() {
		check_ajax_referer( 'stm_lms_answer_video_lesson', 'nonce' );

		$user = STM_LMS_User::get_current_user();

		$answer = array(
			'user_id'      => $user['id'] ?? null,
			'course_id'    => isset( $_POST['course_id'] ) ? intval( $_POST['course_id'] ) : null,
			'lesson_id'    => isset( $_POST['lesson_id'] ) ? intval( $_POST['lesson_id'] ) : null,
			'question_id'  => isset( $_POST['question_id'] ) ? intval( $_POST['question_id'] ) : null,
			'user_answers' => isset( $_POST['user_answer'] ) ? sanitize_text_field( wp_unslash( $_POST['user_answer'] ) ) : null,
		);

		stm_lms_add_user_marker_answer( $answer );

		$correct_answer = stm_lms_get_lesson_markers_correct_answer(
			$answer['lesson_id'],
			$answer['question_id']
		);
		$user_answer    = array_map( 'intval', explode( ',', $answer['user_answers'] ) );

		sort( $correct_answer );
		sort( $user_answer );

		$result             = $correct_answer === $user_answer ? 'correct' : 'wrong';
		$per_answer_results = array();

		foreach ( $user_answer as $answer_id ) {
			$per_answer_results[] = array(
				'id'     => $answer_id,
				'status' => in_array( $answer_id, $correct_answer ) ? 'correct' : 'wrong',
			);
		}

		return wp_send_json(
			array(
				'result'      => $result,
				'user_answer' => $per_answer_results,
			)
		);
	}

	public static function get_completed_lessons( $user_id, $course_id ) {
		global $wpdb;

		$table      = esc_sql( stm_lms_user_lessons_name( $wpdb ) );
		$lessons    = stm_lms_get_user_course_lessons( $user_id, $course_id, array( 'lesson_id' ) );
		$lesson_ids = array_map(
			static function( $lesson ) {
				return absint( $lesson[0] );
			},
			$lessons
		);

		if ( empty( $lesson_ids ) ) {
			return array();
		}

		$placeholders = implode( ',', array_fill( 0, count( $lesson_ids ), '%d' ) );

		// Using dynamic table names and pre-sanitized IDs lists
		$lesson_ids_selected = $wpdb->get_col(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT lesson_id FROM {$table} WHERE user_id = %d AND course_id = %d AND lesson_id IN ($placeholders)",
				array_merge( array( $user_id, $course_id ), $lesson_ids )
			)
		);

		return array_fill_keys( $lesson_ids_selected, true );
	}
}
