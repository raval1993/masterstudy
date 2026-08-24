<?php

use MasterStudy\Lms\Repositories\AdminInstructorRepository;
use MasterStudy\Lms\Repositories\CurriculumMaterialRepository;

STM_LMS_Instructor::init();

class STM_LMS_Instructor extends STM_LMS_User {
	public static function init() {
		add_filter( 'map_meta_cap', array( self::class, 'meta_cap' ), 10, 4 );
		add_filter( 'user_has_cap', array( self::class, 'user_has_cap' ), 10, 4 );

		add_action( 'wp_ajax_stm_lms_get_instructor_courses', 'STM_LMS_Instructor::get_courses' );

		add_filter( 'manage_stm-courses_posts_columns', 'STM_LMS_Instructor::columns' );
		add_action( 'manage_stm-courses_posts_custom_column', 'STM_LMS_Instructor::column_fields', 10, 2 );

		add_filter( 'manage_stm-lessons_posts_columns', 'STM_LMS_Instructor::lesson_columns' );
		add_action( 'manage_stm-lessons_posts_custom_column', 'STM_LMS_Instructor::column_fields', 10, 2 );

		add_filter( 'manage_stm-quizzes_posts_columns', 'STM_LMS_Instructor::quiz_columns' );
		add_action( 'manage_stm-quizzes_posts_custom_column', 'STM_LMS_Instructor::column_fields', 10, 2 );

		add_action( 'admin_enqueue_scripts', 'STM_LMS_Instructor::scripts' );

		add_action( 'wp_ajax_stm_lms_change_lms_author', 'STM_LMS_Instructor::change_author' );

		add_filter( 'pre_get_posts', 'STM_LMS_Instructor::posts_for_current_author' );

		add_action( 'wp_ajax_stm_lms_add_student_manually', 'STM_LMS_Instructor::add_student_manually' );

		add_action( 'wp_ajax_stm_lms_change_course_status', 'STM_LMS_Instructor::change_status' );

		add_action( 'wp_ajax_stm_lms_get_users_submissions', 'STM_LMS_Instructor::get_submissions' );

		add_action( 'wp_ajax_stm_lms_update_user_status', 'STM_LMS_Instructor::update_user_status' );

		add_action( 'wp_ajax_stm_lms_ban_user', 'STM_LMS_Instructor::ban_user' );

		add_action( 'wp_ajax_stm_lms_toggle_user_ai_access', 'STM_LMS_Instructor::toggle_user_ai_access' );

		add_action( 'wp_ajax_stm_lms_toggle_users_ai_access', 'STM_LMS_Instructor::toggle_users_ai_access' );

		add_action( 'pending_to_publish', 'STM_LMS_Instructor::post_published', 10, 2 );

		add_action( 'admin_menu', array( self::class, 'manage_instructors' ), 10000 );

		add_filter( 'ajax_query_attachments_args', 'STM_LMS_Instructor::restrict_media_to_own' );

		add_filter( 'wp_insert_post_data', array( self::class, 'maybe_publish_elementor_template' ), 10, 2 );

		/*Plug for add student*/
		if ( ! class_exists( 'STM_LMS_Enterprise_Courses' ) ) {
			add_action(
				'wp_ajax_stm_lms_get_enterprise_groups',
				function () {
					return 'ok';
				}
			);
		}
	}

	public static function post_published( $post ) {
		$post_id = $post->ID;
		if ( get_post_type( $post_id ) === 'stm-courses' ) {
			$course_title = get_the_title( $post_id );
			$author_id    = intval( get_post_field( 'post_author', $post_id ) );

			$user    = STM_LMS_User::get_current_user( $author_id );
			$subject = esc_html__( 'Course published', 'masterstudy-lms-learning-management-system' );

			$email_data = array(
				'course_title'    => $course_title,
				'blog_name'       => \STM_LMS_Helpers::masterstudy_lms_get_site_name(),
				'site_url'        => \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_site_url() ),
				'date'            => current_time( 'mysql' ),
				'course_edit_url' => \MS_LMS_Email_Template_Helpers::link( ms_plugin_manage_course_url( $post_id ) ),
				'course_url'      => \MS_LMS_Email_Template_Helpers::link( get_permalink( $post_id ) ),
			);

			$template = wp_kses_post(
				'Your course - {{course_title}} was approved, and is now live on the website'
			);

			$message = \MS_LMS_Email_Template_Helpers::render( $template, $email_data );

			if ( class_exists( 'STM_LMS_Email_Manager' ) ) {
				$email_manager = STM_LMS_Email_Manager::stm_lms_get_settings();
				$subject       = $email_manager['stm_lms_course_published_subject'] ?? esc_html__( 'Course published', 'masterstudy-lms-learning-management-system' );
			}
			$subject = \MS_LMS_Email_Template_Helpers::render( $subject, $email_data );

			STM_LMS_Helpers::send_email( $user['email'], $subject, $message, 'stm_lms_course_published', $email_data );
		}
	}

	public static function change_status() {
		check_ajax_referer( 'stm_lms_change_course_status', 'nonce' );

		$statuses = array(
			'draft',
			'publish',
		);

		$user = STM_LMS_User::get_current_user();

		if ( empty( $user['id'] ) ) {
			die;
		}

		$user_id = $user['id'];

		$course_id = absint( $_GET['post_id'] ?? 0 );
		$status    = sanitize_text_field( $_GET['status'] ?? '' );

		if ( empty( $course_id ) || empty( $status ) || ! in_array( $status, $statuses, true ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Invalid request.', 'masterstudy-lms-learning-management-system' ),
				),
				400
			);
		}

		if ( ! STM_LMS_Course::check_course_author( $course_id, $user_id ) && ! current_user_can( 'edit_post', $course_id ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Unauthorized request.', 'masterstudy-lms-learning-management-system' ),
				),
				403
			);
		}

		if ( apply_filters( 'stm_lms_before_change_course_status', false ) ) {
			do_action( 'stm_lms_change_course_status', $status );
			wp_send_json( $status );
		}

		if ( 'publish' === $status && ! current_user_can( 'manage_options' ) ) {
			$premoderation = STM_LMS_Helpers::is_pro()
				&& STM_LMS_Options::get_option( 'course_premoderation', false );
			$status        = ( $premoderation ) ? 'pending' : 'publish';
		}

		wp_update_post(
			array(
				'ID'          => $course_id,
				'post_status' => $status,
			)
		);

		wp_send_json( $status );
	}

	public static function change_author() {
		check_ajax_referer( 'stm_lms_change_lms_author', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			die;
		}

		$author_id = intval( $_GET['author_id'] );
		$course_id = intval( $_GET['post_id'] );

		$arg = array(
			'ID'          => $course_id,
			'post_author' => $author_id,
		);

		wp_update_post( $arg );

		$material_ids = ( new CurriculumMaterialRepository() )->get_course_materials( $course_id );

		/*Change all authors of curriculum*/
		if ( ! empty( $material_ids ) ) {
			foreach ( $material_ids as $material_id ) {
				wp_update_post(
					array(
						'ID'          => $material_id,
						'post_author' => $author_id,
					)
				);
			}
		}

		wp_send_json( $author_id );
	}

	public static function columns( $columns ) {
		$columns['lms_course_students'] = esc_html__( 'Course Students', 'masterstudy-lms-learning-management-system' );

		$columns['lms_author'] = esc_html__( 'Course Author', 'masterstudy-lms-learning-management-system' );

		unset( $columns['author'] );

		return $columns;
	}

	public static function lesson_columns( $columns ) {
		$columns['lms_author'] = esc_html__( 'Lesson Author', 'masterstudy-lms-learning-management-system' );

		unset( $columns['author'] );

		return $columns;
	}

	public static function quiz_columns( $columns ) {
		$columns['lms_author'] = esc_html__( 'Quiz Author', 'masterstudy-lms-learning-management-system' );

		unset( $columns['author'] );

		return $columns;
	}

	public static function column_fields( $columns, $post_id ) {
		switch ( $columns ) {
			case 'lms_author':
				$args           = array(
					'role__in' => array( 'keymaster', 'administrator', 'stm_lms_instructor' ),
					'order'    => 'ASC',
					'orderby'  => 'display_name',
				);
				$wp_user_query  = new WP_User_Query( $args );
				$authors        = $wp_user_query->get_results();
				$authors        = wp_list_pluck( $authors, 'data' );
				$post_author_id = get_post_field( 'post_author', $post_id );
				?>
				<select name="lms_author" data-post="<?php echo esc_attr( $post_id ); ?>">
					<?php foreach ( $authors as $author ) : ?>
						<option value="<?php echo esc_attr( $author->ID ); ?>" <?php selected( $post_author_id, $author->ID ); ?>>
							<?php echo esc_html( $author->user_login ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<a href="<?php echo esc_url( STM_LMS_Helpers::get_current_url() ); ?>" class="button action">
					<?php echo esc_html__( 'Change Author', 'masterstudy-lms-learning-management-system' ); ?>
				</a>
				<?php
				break;
			case 'lms_course_students':
				?>
				<a href="<?php echo esc_url( self::manage_course_students_admin_url( $post_id ) ); ?>" class="button action">
					<?php echo esc_html__( 'Manage students', 'masterstudy-lms-learning-management-system' ); ?>
				</a>
				<?php if ( STM_LMS_Helpers::is_pro_plus() ) { ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=engagement&course=' . str_replace( ' ', '+', get_the_title( $post_id ) ) . "&course_id={$post_id}" ) ); ?>" class="button action">
						<?php echo esc_html__( 'Analytics', 'masterstudy-lms-learning-management-system' ); ?>
					</a>
					<?php
				}
				if ( STM_LMS_Helpers::is_pro_plus() && is_ms_lms_addon_enabled( 'grades' ) ) {
					?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=grades&course=' . $post_id ) ); ?>" class="button action">
						<?php echo esc_html__( 'Grades', 'masterstudy-lms-learning-management-system' ); ?>
					</a>
					<?php
				}
				break;
		}
	}

	public static function posts_for_current_author( $query ) {
		if ( is_admin() ) {
			global $pagenow;
			if ( 'edit.php' !== $pagenow || ! $query->is_admin ) {
				return $query;
			}

			if ( ! function_exists( 'cache_users' ) ) {
				require_once ABSPATH . 'wp-includes/pluggable.php';
			}

			if ( ! current_user_can( 'edit_others_posts' ) ) {
				global $user_ID;
				$query->set( 'author', $user_ID );
			}
		}

		return $query;
	}

	public static function scripts( $hook ) {
		if ( ( 'stm-courses' === get_post_type() || 'stm-lessons' === get_post_type() || 'stm-quizzes' === get_post_type() ) && 'edit.php' === $hook ) {
			stm_lms_register_script( 'admin/change_lms_author', array( 'jquery' ), true );
			wp_localize_script(
				'stm-lms-admin/change_lms_author',
				'stm_lms_change_lms_author',
				array(
					'notice' => esc_html__(
						"After changing the course's author, the author of all lessons, quizzes and assignments related to this course will be changed automatically. Do you really want to change the author of the course?",
						'masterstudy-lms-learning-management-system'
					),
				)
			);
			stm_lms_register_style( 'admin/change_lms_author' );
		}
	}

	public static function meta_cap( $caps, $cap, $user_id, $args ) {
		remove_filter( 'map_meta_cap', array( self::class, 'meta_cap' ) );

		if ( ! self::is_instructor() ) {
			return $caps;
		}

		$post      = null;
		$post_type = null;

		if ( isset( $args[0] ) && $args[0] ) {
			$post = get_post( $args[0] );
			if ( $post ) {
				$post_type = get_post_type_object( $post->post_type );
			}
		}

		$is_course_co_instructor = false;

		if ( $post && 'stm-courses' === $post->post_type ) {
			$co_instructors = array();
			$co_meta        = get_post_meta( $post->ID, 'co_instructor', false );

			if ( ! empty( $co_meta ) ) {
				foreach ( (array) $co_meta as $co_instructor ) {
					if ( is_array( $co_instructor ) ) {
						$co_instructors = array_merge( $co_instructors, $co_instructor );
					} else {
						$co_instructors[] = $co_instructor;
					}
				}
			}

			$co_instructors          = array_filter( array_map( 'intval', $co_instructors ) );
			$is_course_co_instructor = in_array( (int) $user_id, $co_instructors, true );
		}

		if ( $post && in_array( $cap, array( 'edit_stm_lms_post', 'delete_stm_lms_post', 'read_stm_lms_post' ), true ) ) {
			$caps = array();

			if ( 'edit_stm_lms_post' === $cap ) {
				$caps[] = ( strval( $user_id ) === strval( $post->post_author ) || $is_course_co_instructor )
					? $post_type->cap->edit_posts
					: $post_type->cap->edit_others_posts;
			}

			if ( 'delete_stm_lms_post' === $cap ) {
				$caps[] = ( strval( $user_id ) === strval( $post->post_author ) )
					? $post_type->cap->delete_posts
					: $post_type->cap->delete_others_posts;
			}

			if ( 'read_stm_lms_post' === $cap ) {
				if ( 'private' !== $post->post_status || $user_id === (int) $post->post_author || $is_course_co_instructor ) {
					$caps[] = 'read';
				} else {
					$caps[] = $post_type->cap->read_private_posts;
				}
			}
		}

		if ( $post && 'elementor_library' === $post->post_type ) {
			$user_data = get_userdata( $user_id );
			$is_admin  = $user_data && in_array( 'administrator', (array) $user_data->roles, true );

			if ( 'edit_post' === $cap ) {
				$caps = array( ( $user_id === (int) $post->post_author || $is_admin ) ? 'edit_elementor_libraries' : 'do_not_allow' );
			}

			if ( 'delete_post' === $cap ) {
				$caps = array( ( $user_id === (int) $post->post_author || $is_admin ) ? 'delete_elementor_libraries' : 'do_not_allow' );
			}

			if ( 'read_post' === $cap ) {
				$caps = array( ( 'private' !== $post->post_status || $user_id === (int) $post->post_author ) ? 'read_elementor_libraries' : 'do_not_allow' );
			}

			if ( 'publish_post' === $cap ) {
				$caps = array( ( $user_id === (int) $post->post_author ) ? 'publish_elementor_libraries' : 'do_not_allow' );
			}
		}

		add_filter( 'map_meta_cap', array( self::class, 'meta_cap' ), 10, 4 );

		return $caps;
	}

	private static $private_course_access = null;

	public static function user_has_cap( $allcaps, $caps, $args, $user ) {
		if ( is_admin() || empty( $user->ID ) ) {
			return $allcaps;
		}

		if ( ! in_array( 'read_private_stm_lms_posts', (array) $caps, true ) ) {
			return $allcaps;
		}

		if ( null === self::$private_course_access ) {
			self::$private_course_access = self::resolve_private_course_access( $user->ID );
		}

		if ( self::$private_course_access ) {
			$allcaps['read_private_stm_lms_posts'] = true;
		}

		return $allcaps;
	}

	private static function resolve_private_course_access( $user_id ) {
		global $wpdb;
		$post = null;

		// Plain query URL: /?post_type=stm-courses&p=14
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_GET['post_type'] ) && 'stm-courses' === sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) && ! empty( $_GET['p'] ) ) {
			$candidate = get_post( intval( $_GET['p'] ) );
			if ( $candidate && 'stm-courses' === $candidate->post_type && 'private' === $candidate->post_status ) {
				$post = $candidate;
			}
		}

		// Pretty permalink: query var 'stm-courses' holds the post slug after rewrite parsing.
		if ( ! $post ) {
			global $wp;
			if ( ! empty( $wp->query_vars['stm-courses'] ) ) {
				$slug = sanitize_title( $wp->query_vars['stm-courses'] );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$course_id = (int) $wpdb->get_var(
					$wpdb->prepare(
						"SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_type = 'stm-courses' AND post_status = 'private' LIMIT 1",
						$slug
					)
				);
				if ( $course_id ) {
					$post = get_post( $course_id );
				}
			}
		}

		if ( ! $post ) {
			return false;
		}

		if ( (int) $post->post_author === $user_id ) {
			return true;
		}

		$co_instructors = array();
		$co_meta        = get_post_meta( $post->ID, 'co_instructor', false );
		foreach ( (array) $co_meta as $co_instructor ) {
			if ( is_array( $co_instructor ) ) {
				$co_instructors = array_merge( $co_instructors, $co_instructor );
			} else {
				$co_instructors[] = $co_instructor;
			}
		}

		if ( in_array( $user_id, array_filter( array_map( 'intval', $co_instructors ) ), true ) ) {
			return true;
		}

		return (bool) \STM_LMS_User::has_course_access( (int) $post->ID, '', false );
	}

	public static function role(): string {
		return 'stm_lms_instructor';
	}

	public static function is_instructor( $user_id = null ): bool {
		$user = parent::get_current_user( $user_id, true, false, true );
		if ( empty( $user['id'] ) ) {
			return false;
		}

		/*If admin*/
		if ( in_array( 'administrator', $user['roles'], true ) ) {
			return true;
		}
		return in_array( self::role(), $user['roles'], true );
	}

	public static function has_instructor_role( $user_id = null ): bool {
		$user = parent::get_current_user( $user_id, true, false, true );
		if ( empty( $user['id'] ) ) {
			return false;
		}

		return in_array( self::role(), $user['roles'], true );
	}

	public static function instructor_links() {
		return apply_filters(
			'stm_lms_instructor_links',
			array(
				'add_new' => ms_plugin_manage_course_url(),
			)
		);
	}

	public static function get_courses( $args = array(), $return = false, $get_all = false ) {
		if ( ! $return ) {
			check_ajax_referer( 'stm_lms_get_instructor_courses', 'nonce' );
		}

		$user = STM_LMS_User::get_current_user();
		if ( empty( $user['id'] ) ) {
			die;
		}

		$user_id = $user['id'];
		$pp      = ( empty( $_GET['pp'] ) ) ? 8 : sanitize_text_field( $_GET['pp'] );
		$offset  = ( ! empty( $_GET['offset'] ) ) ? intval( $_GET['offset'] ) : 0;

		if ( ! empty( $args['posts_per_page'] ) ) {
			$pp = intval( $args['posts_per_page'] );
		}

		$offset       = $offset * $pp;
		$default_args = array(
			'post_type'      => 'stm-courses',
			'posts_per_page' => $pp,
			'post_status'    => array( 'publish', 'draft', 'pending' ),
			'offset'         => $offset,
		);

		if ( ! $get_all ) {
			$default_args['author'] = $user_id;
		}

		$args = wp_parse_args( $args, $default_args );

		if ( empty( $args['s'] ) && ! empty( $_GET['s'] ) ) {
			$args['s'] = sanitize_text_field( $_GET['s'] );
		}

		if ( ! empty( $_GET['status'] ) ) {
			$args['post_status'] = sanitize_text_field( $_GET['status'] );
		}

		if ( ! empty( $_GET['post_types'] ) ) {
			$post_types_param = is_array( $_GET['post_types'] )
				? ''
				: sanitize_text_field( wp_unslash( $_GET['post_types'] ) );

			if ( '' !== $post_types_param ) {
				$post_types         = array_map( 'sanitize_key', explode( ',', (string) $post_types_param ) );
				$allowed_post_types = array( 'stm-courses', 'stm-course-bundles' );
				$post_types         = array_values( array_intersect( $post_types, $allowed_post_types ) );

				if ( ! empty( $post_types ) ) {
					$args['post_type'] = $post_types;
				}
			}
		}

		if ( ! empty( $_GET['coming_soon_bundle'] ) ) {
			$args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key'     => 'coming_soon_status',
					'value'   => '',
					'compare' => '=',
				),
				array(
					'key'     => 'coming_soon_status',
					'compare' => 'NOT EXISTS',
				),
			);
		}

		if ( $return ) {
			return self::masterstudy_lms_get_instructor_courses( $args, $offset, $pp );
		}

		wp_send_json( self::masterstudy_lms_get_instructor_courses( $args, $offset, $pp ) );
	}

	public static function masterstudy_lms_get_instructor_courses( $args = array(), $offset = 0, $pp = 0 ): array {
		$result  = array( 'posts' => array() );
		$get_ids = ( ! empty( $_GET['ids_only'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( function_exists( 'pll_current_language' ) ) {
			$args['lang'] = pll_current_language();
		}

		$query = new WP_Query( $args );

		$total              = $query->found_posts;
		$result['total']    = $total <= $offset + $pp;
		$result['found']    = $total;
		$result['per_page'] = (int) $pp;
		$result['pages']    = (int) ceil( $result['found'] / $result['per_page'] );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$id = get_the_ID();

				if ( $get_ids ) {
					$result['posts'][ $id ] = html_entity_decode( get_the_title( $id ) );
					continue;
				}

				$rating             = get_post_meta( $id, 'course_marks', true );
				$rates              = STM_LMS_Course::course_average_rate( $rating );
				$average            = $rates['average'];
				$percent            = $rates['percent'];
				$status             = get_post_status( $id );
				$price              = get_post_meta( $id, 'price', true );
				$availability       = get_post_meta( $id, 'coming_soon_status', true );
				$sale_price         = get_post_meta( $id, 'sale_price', true );
				$sale_price_active  = STM_LMS_Helpers::is_sale_price_active( $id );
				$single_sale        = get_post_meta( $id, 'single_sale', true );
				$not_in_membership  = get_post_meta( $id, 'not_membership', true );
				$course_free_status = masterstudy_lms_course_free_status( $id, $price );

				switch ( $status ) {
					case 'publish':
						$status_label = esc_html__( 'Published', 'masterstudy-lms-learning-management-system' );
						break;
					case 'private':
						$status_label = esc_html__( 'Private', 'masterstudy-lms-learning-management-system' );
						break;
					case 'pending':
						$status_label = esc_html__( 'Pending', 'masterstudy-lms-learning-management-system' );
						break;
					default:
						$status_label = esc_html__( 'Draft', 'masterstudy-lms-learning-management-system' );
						break;
				}

				$post_status  = STM_LMS_Course::get_post_status( $id );
				$image        = ( function_exists( 'stm_get_VC_img' ) ) ? html_entity_decode( stm_get_VC_img( get_post_thumbnail_id(), '272x161' ) ) : get_the_post_thumbnail( $id, 'img-300-225' );
				$image_small  = ( function_exists( 'stm_get_VC_img' ) ) ? html_entity_decode( stm_get_VC_img( get_post_thumbnail_id(), '50x50' ) ) : get_the_post_thumbnail( $id, 'img-300-225' );
				$is_featured  = get_post_meta( $id, 'featured', true );
				$rating_count = ( ! empty( $rating ) ) ? count( $rating ) : '';

				$post = array(
					'id'                          => $id,
					'time'                        => get_post_time( 'U', true ),
					'title'                       => html_entity_decode( get_the_title() ),
					/* translators: %s Last Updated */
					'updated'                     => stm_lms_time_elapsed_string( get_post( $id )->post_modified ),
					'link'                        => get_the_permalink(),
					'image'                       => $image,
					'image_small'                 => $image_small,
					'terms'                       => wp_get_post_terms( $id, 'stm_lms_course_taxonomy' ),
					'status'                      => $status,
					'status_label'                => $status_label,
					'availability'                => $availability,
					'percent'                     => $percent,
					'is_featured'                 => $is_featured,
					'average'                     => $average,
					'total'                       => $rating_count,
					'views'                       => STM_LMS_Course::get_course_views( $id ),
					'price'                       => STM_LMS_Helpers::display_price( $price ),
					'simple_price'                => $sale_price_active && $sale_price ? $sale_price : $price,
					'sale_price'                  => $sale_price ? STM_LMS_Helpers::display_price( $sale_price ) : 0,
					'single_sale'                 => $single_sale,
					'is_free'                     => $course_free_status['is_free'],
					'zero_price'                  => $course_free_status['zero_price'],
					'members_only'                => ! $single_sale && ! $not_in_membership,
					'edit_link'                   => ms_plugin_manage_course_url( $id ),
					'coming_soon_link'            => ms_plugin_manage_course_url( "$id/settings/access" ),
					'post_status'                 => $post_status,
					'manage_students_link'        => self::instructor_manage_students_url() . "/?course_id=$id",
					'can_instructor_add_students' => self::instructor_can_add_students(),
				);

				if ( STM_LMS_Helpers::is_pro_plus() && STM_LMS_Options::get_option( 'instructors_reports', true ) ) {
					$post = apply_filters( 'masterstudy_add_analytics_link', $post, $id );
				}

				if ( STM_LMS_Helpers::is_pro_plus() && is_ms_lms_addon_enabled( 'grades' ) ) {
					$post = apply_filters( 'masterstudy_add_grades_link', $post, $id );
				}

				$post['sale_price'] = ( $sale_price_active && ! empty( $sale_price ) ) ? STM_LMS_Helpers::display_price( $sale_price ) : '';
				$result['posts'][]  = $post;
			}
		}

		wp_reset_postdata();

		return $result;
	}

	public static function get_instructor_courses( $args = array(), $pp = 12 ): array {
		$result = array( 'posts' => array() );

		if ( function_exists( 'pll_current_language' ) ) {
			$args['lang'] = pll_current_language();
		}

		$pp = (int) $pp;

		if ( $pp > 0 ) {
			$args['posts_per_page'] = $pp;
		}

		$paged = 1;

		if ( ! empty( $args['paged'] ) ) {
			$paged = (int) $args['paged'];
		} elseif ( ! empty( $args['page'] ) ) {
			$paged = (int) $args['page'];
		}

		$paged = max( 1, $paged );

		$query = new WP_Query( $args );

		$total_posts = (int) $query->found_posts;

		$result['found']    = $total_posts;
		$result['per_page'] = $pp > 0 ? $pp : (int) $query->get( 'posts_per_page' );
		$result['pages']    = $result['per_page'] > 0 ? (int) ceil( $result['found'] / $result['per_page'] ) : 1;
		$result['total']    = ( $paged >= $result['pages'] );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$id = get_the_ID();

				$status            = get_post_status( $id );
				$price             = get_post_meta( $id, 'price', true );
				$sale_price        = STM_LMS_Course::get_sale_price( $id );
				$single_sale       = get_post_meta( $id, 'single_sale', true );
				$is_featured       = get_post_meta( $id, 'featured', true );
				$not_in_membership = get_post_meta( $id, 'not_membership', true );

				switch ( $status ) {
					case 'publish':
						$status_label = esc_html__( 'Published', 'masterstudy-lms-learning-management-system' );
						break;
					case 'private':
						$status_label = esc_html__( 'Private', 'masterstudy-lms-learning-management-system' );
						break;
					case 'pending':
						$status_label = esc_html__( 'Pending', 'masterstudy-lms-learning-management-system' );
						break;
					default:
						$status_label = esc_html__( 'Draft', 'masterstudy-lms-learning-management-system' );
						break;
				}

				$post = array(
					'id'                          => $id,
					'time'                        => get_post_time( 'U', true ),
					'post_author'                 => get_post_field( 'post_author', $id ),
					'post_title'                  => get_the_title( $id ),
					'course_marks'                => get_post_meta( $id, 'course_marks', true ),
					/* translators: %s Last Updated */
					'updated'                     => stm_lms_time_elapsed_string( get_post( $id )->post_modified ),
					'status'                      => $status,
					'status_label'                => $status_label,
					'featured'                    => $is_featured,
					'views'                       => STM_LMS_Course::get_course_views( $id ),
					'price'                       => $price,
					'sale_price'                  => $sale_price,
					'single_sale'                 => $single_sale,
					'edit_link'                   => ms_plugin_manage_course_url( $id ),
					'coming_soon_link'            => ms_plugin_manage_course_url( "$id/settings/access" ),
					'duration_info'               => get_post_meta( $id, 'duration_info', true ),
					'manage_students_link'        => self::instructor_manage_students_url() . "/?course_id=$id",
					'can_instructor_add_students' => self::instructor_can_add_students(),
					'not_in_membership'           => $not_in_membership,
				);

				if ( STM_LMS_Helpers::is_pro_plus() && STM_LMS_Options::get_option( 'instructors_reports', true ) ) {
					$post = apply_filters( 'masterstudy_add_analytics_link', $post, $id );
				}

				if ( STM_LMS_Helpers::is_pro_plus() && is_ms_lms_addon_enabled( 'grades' ) ) {
					$post = apply_filters( 'masterstudy_add_grades_link', $post, $id );
				}

				$result['posts'][] = $post;
			}
		}

		wp_reset_postdata();

		return $result;
	}

	public static function transient_name( $user_id, $name = '' ): string {
		return "stm_lms_instructor_{$user_id}_{$name}";
	}

	public static function my_rating_v2( $user = '' ): array {
		$user    = ( ! empty( $user ) ) ? $user : STM_LMS_User::get_current_user();
		$user_id = $user['id'];

		$sum_rating_key    = 'sum_rating';
		$total_reviews_key = 'total_reviews';
		$sum_rating        = ( ! empty( get_user_meta( $user_id, $sum_rating_key, true ) ) ) ? get_user_meta( $user_id, $sum_rating_key, true ) : 0;
		$total_reviews     = ( ! empty( get_user_meta( $user_id, $total_reviews_key, true ) ) ) ? get_user_meta( $user_id, $total_reviews_key, true ) : 0;

		if ( empty( $sum_rating ) || empty( $total_reviews ) ) {
			return array(
				'total'       => 0,
				'average'     => 0,
				'total_marks' => '',
				'percent'     => 0,
			);
		}

		$ratings['total']     = intval( $sum_rating );
		$ratings['average']   = floatval( number_format( $sum_rating / $total_reviews, 2 ) );
		$label                = _n( 'Review', 'Reviews', $total_reviews, 'masterstudy-lms-learning-management-system' );
		$ratings['marks_num'] = intval( $total_reviews );
		/* translators: %s Total Reviews, Label */
		$ratings['total_marks'] = sprintf( _x( '%1$s %2$s', '"1 Review" or "2 Reviews"', 'masterstudy-lms-learning-management-system' ), $total_reviews, $label );

		$ratings['percent'] = intval( ( $ratings['average'] * 100 ) / 5 );

		return $ratings;
	}

	public static function my_rating( $user = '' ) {
		$ratings = array();
		$user    = ( ! empty( $user ) ) ? $user : STM_LMS_User::get_current_user();
		$user_id = $user['id'];

		$transient_name = self::transient_name( $user_id, 'rating' );
		if ( ! empty( $transient_name ) ) {
			$ratings = get_transient( $transient_name );
		}
		if ( false === $ratings ) {
			$args = array(
				'post_type'      => 'stm-courses',
				'posts_per_page' => '-1',
				'author'         => $user_id,
			);

			$q = new WP_Query( $args );

			$ratings = array(
				'total_marks' => 0,
				'total'       => 0,
				'average'     => 0,
				'percent'     => 0,
			);

			if ( $q->have_posts() ) {
				while ( $q->have_posts() ) {
					$q->the_post();
					$marks = get_post_meta( get_the_ID(), 'course_marks', true );
					if ( ! empty( $marks ) ) {
						foreach ( $marks as $mark ) {
							++$ratings['total_marks'];
							$ratings['total'] += $mark;
						}
					}
				}

				$ratings['average'] = ( $ratings['total'] && $ratings['total_marks'] ) ? round( $ratings['total'] / $ratings['total_marks'], 2 ) : 0;

				$ratings['marks_num'] = $ratings['total_marks'];

				$ratings['percent'] = ( $ratings['average'] * 100 ) / 5;
			}

			wp_reset_postdata();

			set_transient( $transient_name, $ratings, 7 * 24 * 60 * 60 );
		}
		if ( empty( $ratings['marks_num'] ) ) {
			$ratings['marks_num'] = 0;
		}
		// phpcs:disable
		$label = _n( __( 'Review', 'masterstudy-lms-learning-management-system' ), __( 'Reviews', 'masterstudy-lms-learning-management-system' ), $ratings['marks_num'], 'masterstudy-lms-learning-management-system' ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralPlural
		$ratings['total_marks'] = sprintf( // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralPlural
		/* translators: %s: Review  %s: Reviews */
			__(
				'%1$s %2$s.',
				'masterstudy-lms-learning-management-system'
			),
			$ratings['marks_num'],
			$label
		);
		// phpcs:enable

		return $ratings;
	}

	public static function become_instructor( $data, $user_id ) {
		if ( ! empty( $data['become_instructor'] ) ) {
			$is_ai_enabled = self::get_is_ai_enabled_for_all();

			if ( ! empty( $data['fields_type'] ) && 'custom' === $data['fields_type'] ) {
				if ( ! empty( $data['fields'] ) ) {
					$subject    = esc_html__( 'New Instructor Application', 'masterstudy-lms-learning-management-system' );
					$user       = STM_LMS_User::get_current_user( $user_id );
					$user_login = $user['login'];

					$email_data = array(
						'user_login' => STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $user_id ),
						'user_id'    => $user_id,
						'date'       => current_time( 'mysql' ),
					);

					foreach ( $data['fields'] as $field ) {
						if ( ! empty( $field['slug'] ) ) {
							$email_data[ $field['slug'] ] = $field['value'];
						}
					}

					unset( $data['register_user_password'], $data['register_user_password_re'] );

					update_user_meta( $user_id, 'become_instructor', $data );
					update_user_meta( $user_id, 'submission_date', time() );
					update_user_meta( $user_id, 'submission_status', 'pending' );
					update_user_meta( $user_id, 'stm_lms_ai_enabled', $is_ai_enabled );

					$instructor_premoderation = STM_LMS_Options::instructor_premoderation_enabled();

					$message = sprintf(
						/* translators: %s User Login, User ID */
						__( 'User %1$s with id - %2$s, wants to become an Instructor.', 'masterstudy-lms-learning-management-system' ),
						$user_login,
						$user_id
					);

					if ( ! $instructor_premoderation ) {
						wp_update_user(
							array(
								'ID'   => $user_id,
								'role' => 'stm_lms_instructor',
							)
						);
						$message = sprintf(
							/* translators: %s User Login, User ID */
							__( 'User %1$s with id - %2$s, registered as Instructor.', 'masterstudy-lms-learning-management-system' ),
							$user_login,
							$user_id
						);
						update_user_meta( $user_id, 'submission_status', 'approved' );
					}

					STM_LMS_Helpers::send_email(
						'',
						$subject,
						$message,
						'stm_lms_become_instructor_email',
						$email_data
					);
				}
			} else {
				$degree    = ( ! empty( $data['degree'] ) ) ? sanitize_text_field( $data['degree'] ) : esc_html__( 'N/A', 'masterstudy-lms-learning-management-system' );
				$expertize = ( ! empty( $data['expertize'] ) ) ? sanitize_text_field( $data['expertize'] ) : esc_html__( 'N/A', 'masterstudy-lms-learning-management-system' );

				$subject = esc_html__( 'New Instructor Application', 'masterstudy-lms-learning-management-system' );
				$user    = STM_LMS_User::get_current_user( $user_id );

				unset( $data['register_user_password'], $data['register_user_password_re'] );

				update_user_meta( $user_id, 'become_instructor', $data );
				update_user_meta( $user_id, 'submission_date', time() );
				update_user_meta( $user_id, 'submission_status', 'pending' );
				update_user_meta( $user_id, 'stm_lms_ai_enabled', $is_ai_enabled );

				$user_info  = get_userdata( $user_id );
				$first_name = get_user_meta( $user_id, 'first_name', true );
				$last_name  = get_user_meta( $user_id, 'last_name', true );

				if ( ! empty( $first_name ) && ! empty( $last_name ) ) {
					// If both first name and last name are available, use both.
					$display_name = $first_name . ' ' . $last_name;
				} elseif ( ! empty( $first_name ) ) {
					// If only first name is available, use it.
					$display_name = $first_name;
				} elseif ( ! empty( $last_name ) ) {
					// If only last name is available, use it.
					$display_name = $last_name;
				} else {
					// If both are empty, fall back to the user login.
					$display_name = $user_info->user_login;
				}

				$user_login = $display_name ?? $user['login'];

				$instructor_premoderation = STM_LMS_Options::instructor_premoderation_enabled();

				$date       = current_time( 'mysql' );
				$user_email = $user['email'];

				$message = esc_html__( 'You have received a new instructor application from ', 'masterstudy-lms-learning-management-system-pro' ) . $user_login . ', <br/>' . // phpcs:disable
					esc_html__( 'Here are the details:', 'masterstudy-lms-learning-management-system-pro' ) . ' <br/>' .
					'<b>' . esc_html__( 'Name: ', 'masterstudy-lms-learning-management-system-pro' ) . '</b>' . $user_login . ' <br>' .
					'<b>' . esc_html__( 'ID: ', 'masterstudy-lms-learning-management-system-pro' ) . '</b>' . $user_id . ' <br>' .
					'<b>' . esc_html__( 'Email: ', 'masterstudy-lms-learning-management-system-pro' ) . '</b>' . $user_email . ' <br>' .
					'<b>' . esc_html__( 'Degree: ', 'masterstudy-lms-learning-management-system-pro' ) . '</b>' . $degree . ' <br>' .
					'<b>' . esc_html__( 'Expertize: ', 'masterstudy-lms-learning-management-system-pro' ) . '</b>' . $expertize . ' <br>' .
					'<b>' . esc_html__( 'Application Date: ', 'masterstudy-lms-learning-management-system-pro' ) . '</b>' . wp_date( 'Y-m-d H:i:s' ) . ' <br><br>' .
					esc_html__( 'Please review the application at your earliest convenience.', 'masterstudy-lms-learning-management-system-pro' ) . '</a> <br/><br/>'; // phpcs:enable

				if ( ! $instructor_premoderation ) {
					wp_update_user(
						array(
							'ID'   => $user_id,
							'role' => 'stm_lms_instructor',
						)
					);
					$message = sprintf(
						/* translators: %s User Login, User ID, Degree, Expertize */
						__( 'User %1$s with id - %2$s, registered as Instructor. Degree - %3$s. Expertize - %4$s', 'masterstudy-lms-learning-management-system' ),
						$user_login,
						$user_id,
						$degree,
						$expertize
					);
					update_user_meta( $user_id, 'submission_status', 'approved' );
				}

				$email_data = array(
					'user_login' => STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $user_id ),
					'user_id'    => $user_id,
					'user_email' => $user_email,
					'date'       => $date,
					'degree'     => $degree,
					'expertize'  => $expertize,
					'blog_name'  => \STM_LMS_Helpers::masterstudy_lms_get_site_name(),
					'site_url'   => \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_site_url() ),
				);

				STM_LMS_Helpers::send_email(
					'',
					$subject,
					$message,
					'stm_lms_become_instructor_email',
					$email_data
				);
			}
		}
	}

	public static function update_rating( $user_id, $mark ) {
		$sum_rating_key    = 'sum_rating';
		$total_reviews_key = 'total_reviews';
		$average_key       = 'average_rating';

		$sum_rating    = ( ! empty( get_user_meta( $user_id, $sum_rating_key, true ) ) ) ? get_user_meta( $user_id, $sum_rating_key, true ) : 0;
		$total_reviews = ( ! empty( get_user_meta( $user_id, $total_reviews_key, true ) ) ) ? get_user_meta( $user_id, $total_reviews_key, true ) : 0;
		update_user_meta( $user_id, $sum_rating_key, $sum_rating + $mark );
		update_user_meta( $user_id, $total_reviews_key, $total_reviews + 1 );
		update_user_meta( $user_id, $average_key, round( ( $sum_rating + $mark ) / ( $total_reviews + 1 ), 2 ) );
	}

	public static function get_instructors_url() {
		$page_id = STM_LMS_Options::instructors_page();

		return ( ! empty( $page_id ) ) ? get_permalink( $page_id ) : '';
	}

	public static function instructor_can_add_students() {
		return STM_LMS_Options::get_option( 'instructor_can_add_students', false );
	}

	public static function instructor_show_list_students() {
		return STM_LMS_Options::get_option( 'show_students_to_instructors', false );
	}

	public static function instructor_manage_students_url(): string {
		return STM_LMS_User::user_page_url() . 'manage-students';
	}

	public static function manage_course_students_admin_url( int $course_id ): string {
		return admin_url( 'admin.php?page=manage_courses&subpage=manage_course_students&course_id=' . absint( $course_id ) );
	}

	public static function add_student_to_course( $raw_courses, $raw_emails ) {
		$courses = array();
		$emails  = array();

		$data = array(
			'error'   => false,
			'message' => esc_html__( 'Student added to course', 'masterstudy-lms-learning-management-system' ),
		);

		$instructor_id = get_current_user_id();

		foreach ( $raw_emails as $email ) {
			if ( is_email( $email ) ) {
				$emails[] = $email;
			}
		}

		if ( empty( $emails ) ) {
			die;
		}

		foreach ( $raw_courses as $course ) {
			$course = intval( $course );
			if ( STM_LMS_Course::check_course_author( $course, $instructor_id ) || current_user_can( 'edit_post', $course ) ) {
				$courses[] = $course;
			}
		}

		if ( empty( $courses ) ) {
			return array(
				'error'   => true,
				'message' => esc_html__( 'Unauthorized request.', 'masterstudy-lms-learning-management-system' ),
			);
		}

		/*Now we checked all courses and emails, we can add users to site*/
		$user_ids = self::create_users_from_emails( $emails );

		foreach ( $courses as $course_id ) {
			foreach ( $user_ids as $user_id ) {
				$user_course = stm_lms_get_user_course( $user_id, $course_id );

				if ( ! empty( $user_course ) ) {
					continue;
				}

				STM_LMS_Course::add_user_course(
					$course_id,
					$user_id,
					STM_LMS_Lesson::get_first_lesson( $course_id ),
				);

				STM_LMS_Course::add_student( $course_id );
			}
		}

		if ( count( $courses ) > 1 || count( $emails ) > 1 ) {
			$courses_n  = _n( 'Course', 'Courses', count( $courses ), 'masterstudy-lms-learning-management-system' );
			$students_n = _n( 'Student', 'Students', count( $emails ), 'masterstudy-lms-learning-management-system' );
			/* translators: %s Students Count, Courses Count */
			$data['message'] = sprintf( esc_html__( '%1$s added to %2$s', 'masterstudy-lms-learning-management-system' ), $students_n, $courses_n );
		}

		return $data;
	}

	public static function add_student_manually() {
		check_ajax_referer( 'stm_lms_add_student_manually', 'nonce' );

		$raw_courses = $_POST['courses'] ?? array();
		$raw_emails  = $_POST['emails'];

		if ( empty( get_current_user_id() ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Unauthorized request.', 'masterstudy-lms-learning-management-system' ),
				),
				403
			);
		}

		if ( empty( $raw_courses ) || ! is_array( $raw_courses ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Invalid request.', 'masterstudy-lms-learning-management-system' ),
				),
				400
			);
		}

		$data = self::add_student_to_course( $raw_courses, $raw_emails );

		wp_send_json( $data );
	}

	public static function create_users_from_emails( $emails ): array {
		$users = array();

		foreach ( $emails as $email ) {
			$user = get_user_by( 'email', $email );

			if ( $user ) {
				$users[] = $user->ID;
				continue;
			}

			/*Create User*/
			$username = sanitize_title( $email );
			$password = wp_generate_password();
			$user_id  = wp_create_user( $username, $password, $email );
			$subject  = esc_html__( 'Welcome to {{blog_name}}. Your student account is ready', 'masterstudy-lms-learning-management-system' );

			$template = wp_kses_post(
				'
					Hello {{username}},
					Welcome to {{blog_name}}<br><br>
					Your instructor has created an account for you so you can access your courses and start learning.<br>
					Here are your login details:<br>
					<b>Username</b>: {{username}}<br>
					<b>Password</b>: {{password}}<br>
					<b>Login page</b>: {{login_url}}<br>
					To get started, visit the login page and sign in using the credentials above. For security reasons, we recommend changing your password after your first login.<br>
					If you have any questions or experience issues accessing your account, please contact your instructor or the site administrator.<br>
					We are glad to have you with us and wish you a great learning experience.<br><br>
					Best regards,<br>
					The {{blog_name}} Team'
			);

			$email_data = array(
				'username'  => $username,
				'password'  => $password,
				'site_url'  => \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_site_url() ),
				'blog_name' => STM_LMS_Helpers::masterstudy_lms_get_site_name(),
				'date'      => current_time( 'mysql' ),
				'login_url' => \MS_LMS_Email_Template_Helpers::link( STM_LMS_Helpers::masterstudy_lms_get_login_url() ),
			);

			$message = \MS_LMS_Email_Template_Helpers::render( $template, $email_data );
			$subject = \MS_LMS_Email_Template_Helpers::render( $subject, $email_data );

			STM_LMS_Helpers::send_email( $email, $subject, $message, 'stm_lms_user_added_via_manage_students', $email_data );

			if ( ! is_wp_error( $user_id ) ) {
				$users[] = $user_id;
			}
		}

		return $users;
	}

	public static function manage_instructors() {
		add_submenu_page(
			'stm-lms-settings',
			esc_html__( 'Instructors', 'masterstudy-lms-learning-management-system' ),
			'<span class="stm-lms-instructors-menu-title">' . esc_html__( 'Instructors', 'masterstudy-lms-learning-management-system' ) . '</span>',
			'manage_options',
			'manage_instructors',
			'masterstudy_lms_render_admin_react_page',
			stm_lms_addons_menu_position()
		);
	}

	public static function manage_instructors_template() {
		STM_LMS_Templates::show_lms_template( 'components/admin-react-app/main' );
	}

	public static function get_submissions() {
		check_ajax_referer( 'stm_lms_get_users_submissions', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array( 'message' => 'Forbidden' ),
				403
			);
		}

		$data = ( new AdminInstructorRepository() )->get_instructors(
			array(
				'page'     => isset( $_GET['page'] ) ? absint( $_GET['page'] ) : 1,
				'per_page' => 20,
				'order'    => 'DESC',
			)
		);

		wp_send_json(
			array(
				'total'              => $data['total'],
				'users'              => $data['instructors'],
				'ai_enabled_for_all' => $data['ai_enabled_for_all'],
			)
		);
	}

	public static function update_user_status() {
		check_ajax_referer( 'stm_lms_update_user_status', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array( 'message' => 'Forbidden' ),
				403
			);
		}

		$user_id = isset( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : 0;
		$status  = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '';

		if ( ! $user_id || '' === $status ) {
			wp_send_json( array() );
		}

		$result = ( new AdminInstructorRepository() )->update_status(
			$user_id,
			$status,
			(string) ( $_GET['message'] ?? '' )
		);

		wp_send_json( null === $result ? array() : $result['submission_history'] );
	}

	public static function ban_user(): void {
		check_admin_referer( 'stm_lms_ban_user', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array( 'message' => 'Forbidden' ),
				403
			);
		}

		$user_id = absint( $_GET['user_id'] ?? 0 );
		if ( ! $user_id || ! current_user_can( 'manage_options' ) ) {
			wp_send_json( 'unsaved' );
		}

		$banned = rest_sanitize_boolean( $_GET['banned'] ?? false );
		$saved  = ( new AdminInstructorRepository() )->update_ban( $user_id, $banned );

		wp_send_json( $saved ? 'saved' : 'unsaved' );
	}

	public static function toggle_user_ai_access() {
		check_ajax_referer( 'stm_lms_ban_user', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array( 'message' => 'Forbidden' ),
				403
			);
		}

		$user_id = absint( $_GET['user_id'] ?? 0 );
		if ( ! $user_id ) {
			wp_send_json( 'unsaved' );
		}

		$repository = new AdminInstructorRepository();

		if ( ! $repository->is_ai_lab_available() ) {
			wp_send_json_error(
				array( 'message' => 'Forbidden' ),
				403
			);
		}

		$ai_enabled = rest_sanitize_boolean( $_GET['ai_enabled'] ?? false );
		$saved      = $repository->update_ai_access( $user_id, $ai_enabled );

		wp_send_json( $saved ? 'saved' : 'unsaved' );
	}

	public static function toggle_users_ai_access() {
		check_ajax_referer( 'stm_lms_ban_user', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array( 'message' => 'Forbidden' ),
				403
			);
		}

		$repository = new AdminInstructorRepository();

		if ( ! $repository->is_ai_lab_available() ) {
			wp_send_json_error(
				array( 'message' => 'Forbidden' ),
				403
			);
		}

		$ai_enabled = rest_sanitize_boolean( $_GET['ai_enabled'] ?? false );
		$repository->update_all_ai_access( $ai_enabled );

		wp_send_json( 'saved' );
	}

	public static function get_instructors( $sort_args ): array {
		$args = array(
			'role' => self::role(),
		);
		if ( ! empty( $sort_args ) ) {
			$args = array_merge( $args, $sort_args );
		}

		return ( new WP_User_Query( $args ) )->get_results();
	}

	public static function get_course_quantity( $user_id ) {
		global $wpdb;
		$result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `$wpdb->posts` WHERE `post_type`= 'stm-courses' AND `post_author` = %d", $user_id ), ARRAY_A );
		if ( is_array( $result ) ) {
			return count( $result );
		}
		return 0;
	}

	public static function get_is_ai_enabled_for_all() {
		return ( new AdminInstructorRepository() )->is_ai_enabled_for_all();
	}

	public static function has_ai_access( $user_id ) {
		$repository = new AdminInstructorRepository();

		return $repository->is_ai_lab_available()
			&& ( current_user_can( 'administrator' ) || get_user_meta( $user_id, 'stm_lms_ai_enabled', true ) );
	}

	public static function restrict_media_to_own( $query ) {
		if ( self::has_instructor_role() && ! current_user_can( 'administrator' ) ) {
			$query['author'] = wp_get_current_user()->ID;
		}
		return $query;
	}

	public static function maybe_publish_elementor_template( $data, $postarr ) {
		if ( empty( $data['post_type'] ) || 'elementor_library' !== $data['post_type'] ) {
			return $data;
		}

		if ( ! self::is_instructor() ) {
			return $data;
		}

		if ( 'pending' === $data['post_status'] ) {
			$data['post_status'] = 'publish';
		}

		return $data;
	}
}
