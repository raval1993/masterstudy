<?php
use MasterStudy\Lms\Plugin\PostType;

STM_LMS_Reviews::reviews_init();

class STM_LMS_Reviews {


	private static $instance;

	public static function reviews_init() {
		add_action( 'save_post', 'STM_LMS_Reviews::save_post', 100, 1 );
		add_action( 'before_delete_post', 'STM_LMS_Reviews::delete_post', 100, 1 );

		add_action( 'wp_ajax_stm_lms_get_reviews', 'STM_LMS_Reviews::get_reviews', 100 );
		add_action( 'wp_ajax_nopriv_stm_lms_get_reviews', 'STM_LMS_Reviews::get_reviews', 100 );

		add_action( 'wp_ajax_stm_lms_add_review', 'STM_LMS_Reviews::add_review', 100 );
		add_action( 'wp_ajax_nopriv_stm_lms_add_review', 'STM_LMS_Reviews::add_review', 100 );
	}

	public static function save_post( $post_id ) {
		$post = get_post( $post_id );

		if ( empty( $post ) || PostType::REVIEW !== $post->post_type ) {
			return;
		}

		$course = get_post_meta( $post_id, 'review_course', true );
		$mark   = get_post_meta( $post_id, 'review_mark', true );
		$user   = get_post_meta( $post_id, 'review_user', true );

		$transient_name = STM_LMS_Instructor::transient_name( get_post_field( 'post_author', $course ), 'rating' );
		delete_transient( $transient_name );

		if ( ! empty( $mark ) && ! empty( $course ) && ! empty( $user ) ) {

			$marks = get_post_meta( $course, 'course_marks', true );

			if ( empty( $marks ) ) {
				$marks = array();
			}
			$marks[ $user ] = $mark;

			$rates = STM_LMS_Course::course_average_rate( $marks );

			update_post_meta( $course, 'course_mark_average', $rates['average'] );
			update_post_meta( $course, 'course_marks', $marks );

			/*Update Instructor Rating*/
			STM_LMS_Instructor::update_rating( get_post_field( 'post_author', $course ), $mark );

		}
	}

	public static function delete_post( $post_id ) {
		$post = get_post( $post_id );

		if ( empty( $post ) || PostType::REVIEW !== $post->post_type ) {
			return;
		}

		$course = get_post_meta( $post_id, 'review_course', true );
		$mark   = get_post_meta( $post_id, 'review_mark', true );
		$user   = get_post_meta( $post_id, 'review_user', true );

		$transient_name = STM_LMS_Instructor::transient_name( get_post_field( 'post_author', $course ), 'rating' );
		delete_transient( $transient_name );

		if ( ! empty( $mark ) && ! empty( $course ) && ! empty( $user ) ) {
			$marks = get_post_meta( $course, 'course_marks', true );

			if ( ! empty( $marks ) && isset( $marks[ $user ] ) ) {
				unset( $marks[ $user ] );

				$rates = STM_LMS_Course::course_average_rate( $marks );

				update_post_meta( $course, 'course_mark_average', $rates['average'] );
				update_post_meta( $course, 'course_marks', $marks );

				STM_LMS_Instructor::update_rating( get_post_field( 'post_author', $course ), null );
			}
		}
	}

	public static function get( $course_id, $offset = '', $pp = 5 ) {
		global $wpdb;

		$response = array(
			'posts' => array(),
			'total' => 0,
		);

		if ( empty( $course_id ) ) {
			return $response;
		}

		$pp       = $pp;
		$user_id  = get_current_user_id();
		$is_admin = current_user_can( 'administrator' ) || current_user_can( 'super_admin' );
		$offset   = $offset * $pp;

		$post_results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT p.ID, p.post_status
				FROM {$wpdb->posts} p
				LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
				WHERE pm.meta_key = 'review_course'
				AND pm.meta_value = %d
				AND (p.post_status = 'publish'
				OR (p.post_status = 'pending' AND (p.post_author = %d OR %d = 1)))
				ORDER BY p.post_date DESC
				LIMIT %d OFFSET %d",
				intval( $course_id ),
				$user_id,
				$is_admin ? 1 : 0,
				$pp,
				$offset
			)
		);

		$total = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*)
				FROM {$wpdb->posts} p
				LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
				WHERE pm.meta_key = 'review_course'
				AND pm.meta_value = %d
				AND (p.post_status = 'publish'
				OR (p.post_status = 'pending' AND (p.post_author = %d OR %d = 1)))",
				intval( $course_id ),
				$user_id,
				$is_admin ? 1 : 0,
			)
		);

		$response['total'] = $total <= $offset + $pp;

		if ( ! empty( $post_results ) ) {
			foreach ( $post_results as $post ) {
				$id   = $post->ID;
				$meta = self::convert_meta( $id );
				if ( ! empty( $meta['review_mark'] ) && ! empty( $meta['review_user'] ) ) {
					$mark = $meta['review_mark'];
					$user = $meta['review_user'];

					$user_class = STM_LMS_User::get_current_user( $user );
					$user_data  = get_user_by( 'id', $user );
					if ( is_wp_error( $user_data ) ) {
						continue;
					}
					$user_name = ( ! empty( $user_data->data->display_name ) ) ? $user_data->data->display_name : $user_data->data->user_nicename;
					$avatar    = STM_LMS_User::get_avatar_url( $user );

					if ( ! empty( $user_class['avatar_url'] ) ) {
						$avatar = $user_class['avatar_url'];
					}

					$time = ( 'pending' === $post->post_status ) ? get_the_date( 'U', $id ) : get_post_time( 'U', true, $id );

					$response['posts'][] = array(
						'user_url'   => STM_LMS_User::student_public_page_url( $user ),
						'user'       => $user_name,
						'avatar_url' => $avatar,
						'time'       => stm_lms_time_elapsed_string( '@' . $time ),
						'title'      => get_the_title( $id ),
						'content'    => apply_filters( 'the_content', get_post_field( 'post_content', $id ) ),
						'mark'       => intval( $mark ),
						'status'     => $post->post_status,
					);
				}
			}
		}

		return $response;
	}

	public static function get_reviews() {
		check_ajax_referer( 'stm_lms_get_reviews', 'nonce' );

		if ( empty( $_GET['post_id'] ) ) {
			die;
		}
		$course_id = intval( $_GET['post_id'] );

		$offset = ( ! empty( $_GET['offset'] ) ) ? intval( $_GET['offset'] ) : 0;
		$pp     = ( ! empty( $_GET['pp'] ) ) ? intval( $_GET['pp'] ) : 5;

		$r = self::get( $course_id, $offset, $pp );

		wp_send_json( $r );
	}

	public static function convert_meta( $post_id ) {
		$meta  = get_post_meta( $post_id );
		$metas = array();
		foreach ( $meta as $meta_name => $meta_value ) {
			$metas[ $meta_name ] = $meta_value[0];
		}

		return $metas;
	}

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function get_user_review_on_course( $course_id, $user_id ) {
		$args = array(
			'post_type'      => 'stm-reviews',
			'post_status'    => array( 'publish', 'pending' ),
			'posts_per_page' => 1,
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'     => 'review_course',
					'compare' => '=',
					'value'   => $course_id,
				),
				array(
					'key'     => 'review_user',
					'compare' => '=',
					'value'   => $user_id,
				),
			),
		);

		$q = new WP_Query( $args );
		wp_reset_postdata();
		return $q;
	}

	public static function create( $course_id, $mark, $review ) {
		global $wpdb;

		$current_user = STM_LMS_User::get_current_user();

		if ( empty( $current_user['id'] ) ) {
			die;
		}

		$user_id = (int) $current_user['id'];

		$r = array(
			'error'   => false,
			'status'  => 'success',
			'message' => esc_html__( 'Your review is moderating.', 'masterstudy-lms-learning-management-system' ),
		);

		if ( empty( $mark ) ) {
			return array(
				'error'   => true,
				'status'  => 'error',
				'message' => esc_html__( 'Please, check rating', 'masterstudy-lms-learning-management-system' ),
			);
		}

		if ( empty( $review ) ) {
			return array(
				'error'   => true,
				'status'  => 'error',
				'message' => esc_html__( 'Please, write review.', 'masterstudy-lms-learning-management-system' ),
			);
		}

		$lock_key   = sprintf( 'stm_reviews_%d_%d', (int) $course_id, (int) $user_id );
		$lock_taken = false;

		try {
			$lock_taken = (bool) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, %d)', $lock_key, 5 ) );
		} catch ( \Throwable $e ) {
			$lock_taken = false;
		}

		$prev_reviews = self::get_user_review_on_course( $course_id, $user_id );

		if ( $prev_reviews->found_posts ) {
			if ( $lock_taken ) {
				$wpdb->query( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock_key ) );
			}

			return array(
				'error'   => true,
				'status'  => 'error',
				'message' => esc_html__( 'You already left review.', 'masterstudy-lms-learning-management-system' ),
			);
		}

		if ( $mark > 5 ) {
			$mark = 5;
		}

		if ( $mark < 1 ) {
			$mark = 1;
		}

		$my_review = array(
			'post_type'    => 'stm-reviews',
			'post_title'   => wp_strip_all_tags(
				sprintf(
					/* translators: %s: string */
					esc_html__( 'Review on %1$s by %2$s', 'masterstudy-lms-learning-management-system' ),
					get_the_title( $course_id ),
					$current_user['login']
				)
			),
			'post_content' => $review,
			'post_status'  => 'pending',
		);

		$review_id = wp_insert_post( $my_review );

		$meta_fields = array(
			'review_course' => $course_id,
			'review_user'   => $user_id,
			'review_mark'   => $mark,
		);

		foreach ( $meta_fields as $meta_key => $meta_value ) {
			update_post_meta( $review_id, $meta_key, $meta_value );
		}

		if ( $lock_taken ) {
			$wpdb->query( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock_key ) );
		}

		$course_title = get_the_title( $course_id );
		$login        = $current_user['login'];

		STM_LMS_Helpers::send_email(
			'admin',
			esc_html__( 'New Review', 'masterstudy-lms-learning-management-system' ),
			sprintf(
				/* translators: %s: string */
				esc_html__( 'Check out new review on course %1$s by %2$s', 'masterstudy-lms-learning-management-system' ),
				$course_title,
				$login
			),
			'stm_lms_new_review',
			compact( 'course_title', 'login' )
		);

		delete_transient( STM_LMS_Instructor::transient_name( $current_user['id'], 'rating' ) );

		return $r;
	}

	public static function add_review() {
		check_ajax_referer( 'stm_lms_add_review', 'nonce' );

		if ( empty( $_POST['post_id'] ) ) {
			die;
		}
		$course_id = intval( $_POST['post_id'] );

		if ( ! is_user_logged_in() ) {
			$error_message = wp_kses_post(
				sprintf(
					__( 'Please, <a href="%s" class="masterstudy-single-course-reviews__login-link" target="_blank">login</a> to leave a review', 'masterstudy-lms-learning-management-system' ),
					esc_url( STM_LMS_User::login_page_url() )
				)
			);
		}

		if ( STM_LMS_Course::check_course_author( $course_id, get_current_user_id() ) ) {
			$error_message = esc_html__( 'You can\'t leave a review for your own course', 'masterstudy-lms-learning-management-system' );
		}

		if ( ! empty( $error_message ) ) {
			return wp_send_json(
				array(
					'error'   => true,
					'status'  => 'error',
					'message' => $error_message,
				)
			);
		}

		$mark   = ( ! empty( $_POST['mark'] ) ) ? intval( $_POST['mark'] ) : 0;
		$review = ( ! empty( $_POST['review'] ) ) ? wp_kses_post( $_POST['review'] ) : '';

		if ( ! STM_LMS_Options::get_option( 'course_allow_review', true ) ) {
			if ( ! STM_LMS_User::has_course_access( $course_id ) ) {
				$r = array(
					'error'   => true,
					'status'  => 'error',
					'message' => esc_html__( 'You must purchase the course to leave a review', 'masterstudy-lms-learning-management-system' ),
				);
			} else {
				$r = self::create( $course_id, $mark, $review );
			}
		} else {
			$r = self::create( $course_id, $mark, $review );
		}

		wp_send_json( $r );
	}
}
