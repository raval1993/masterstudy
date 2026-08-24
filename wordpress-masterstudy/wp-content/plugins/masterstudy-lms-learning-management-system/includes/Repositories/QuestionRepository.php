<?php

namespace MasterStudy\Lms\Repositories;

use MasterStudy\Lms\Enums\BulkQuestionAction;
use MasterStudy\Lms\Enums\LessonVideoType;
use MasterStudy\Lms\Enums\QuestionType;
use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Plugin\Taxonomy;
use MasterStudy\Lms\Utility\Traits\VideoTrait;
use RuntimeException;
use WP_Query;

final class QuestionRepository extends AbstractRepository {
	private const ERROR_BAD_REQUEST = 400;
	private const ERROR_FORBIDDEN   = 403;
	private const MAX_PER_PAGE      = 100;

	protected static string $post_type = PostType::QUESTION;

	use VideoTrait;

	protected static array $fields_meta_map = array(
		'answers'           => 'answers',
		'explanation'       => 'question_explanation',
		'image'             => 'image',
		'hint'              => 'question_hint',
		'type'              => 'type',
		'view_type'         => 'question_view_type',
		'embed_ctx'         => 'question_embed_ctx', // @TODO move to filter
		'external_url'      => 'question_ext_link_url',
		'video_poster'      => 'question_video_poster',
		'video'             => 'question_video',
		'video_type'        => 'video_type',
		'presto_player_idx' => 'presto_player_idx',
		'vdocipher_id'      => 'vdocipher_id',
		'shortcode'         => 'question_shortcode',
		'vimeo_url'         => 'question_vimeo_url',
		'youtube_url'       => 'question_youtube_url',
	);

	protected static array $fields_post_map = array(
		'question' => 'post_title',
		'content'  => 'content',
	);

	protected static array $fields_taxonomy_map = array(
		'categories' => Taxonomy::QUESTION_CATEGORY,
	);

	protected static array $casts = array(
		'answers' => 'list',
		'image'   => 'nullable',
	);

	public function get( $post_id ): ?array {
		$post = parent::get( $post_id );

		if ( null === $post ) {
			return null;
		}

		$meta = get_post_meta( $post['id'] );

		$post = $this->hydrate_video( $post, $meta, 'stm-questions' );

		return apply_filters( 'masterstudy_lms_question_hydrate', $post, array() );
	}

	public function get_all( array $questions ) {
		$list_types = array( QuestionType::SINGLE_CHOICE, QuestionType::MULTI_CHOICE, QuestionType::IMAGE_MATCH );
		$questions  = array_map(
			function ( $question ) use ( $list_types ) {
				$question = $this->get( $question );

				if ( isset( $question['type'] ) && empty( $question['type'] ) ) {
					$question['type'] = QuestionType::SINGLE_CHOICE;
				}

				if ( empty( $question['view_type'] ) && in_array( $question['type'] ?? '', $list_types, true ) ) {
					$question['view_type'] = 'list';
				}

				return $question;
			},
			$questions
		);

		return array_filter( $questions );
	}

	public function create( array $data ): int {
		$data = $this->resolve_bank_categories( $data );
		return parent::create( $data );
	}

	public function update( int $question_id, array $data ): void {
		$data = $this->resolve_bank_categories( $data );

		// Replace <em> tags with <i> tags because of front question rendering issue
		$data['answers'][0]['text'] = preg_replace_callback(
			'/<em>(.*?)<\/em>/',
			static function ( $matches ) {
				return "<i>{$matches[1]}</i>";
			},
			$data['answers'][0]['text']
		);

		parent::update( $question_id, $data );
	}

	public function get_list( array $params ): array {
		$per_page = min( self::MAX_PER_PAGE, max( 1, (int) ( $params['per_page'] ?? 10 ) ) );
		$page     = max( 1, (int) ( $params['page'] ?? 1 ) );
		$search   = isset( $params['search'] ) ? trim( (string) $params['search'] ) : '';
		$category = isset( $params['category'] ) ? trim( (string) $params['category'] ) : '';
		$status   = isset( $params['status'] ) ? (string) $params['status'] : 'any';

		$query_args = array(
			'post_type'              => PostType::QUESTION,
			'post_status'            => 'any' === $status ? array( 'publish', 'pending', 'draft', 'trash', 'private' ) : $status,
			'ignore_sticky_posts'    => true,
			'posts_per_page'         => $per_page,
			'paged'                  => $page,
			'no_found_rows'          => false,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		if ( '' !== $search ) {
			$query_args['s'] = $search;
		}

		if ( '' !== $category ) {
			$category_ids = $this->resolve_category_ids( $category );

			$query_args['tax_query'] = array(
				array(
					'taxonomy'         => Taxonomy::QUESTION_CATEGORY,
					'field'            => 'term_id',
					'terms'            => ! empty( $category_ids ) ? $category_ids : array( 0 ),
					'include_children' => true,
				),
			);
		}

		$post_type = get_post_type_object( PostType::QUESTION );
		if ( $post_type && ! current_user_can( $post_type->cap->edit_others_posts ) ) {
			$query_args['author'] = get_current_user_id();
		}

		$date_range = isset( $params['date_range'] ) ? trim( (string) $params['date_range'] ) : '';
		if ( '' !== $date_range ) {
			$dates = explode( ',', $date_range );
			$from  = ! empty( $dates[0] ) ? trim( $dates[0] ) : '';
			$end   = ! empty( $dates[1] ) ? trim( $dates[1] ) : '';

			if ( $from || $end ) {
				$date_query = array(
					'inclusive' => true,
					'column'    => 'post_date',
				);

				if ( $from ) {
					$date_query['after'] = $from . ' 00:00:00';
				}

				if ( $end ) {
					$date_query['before'] = $end . ' 23:59:59';
				}

				$query_args['date_query'] = array( $date_query );
			}
		}

		$sort = isset( $params['sort'] ) ? trim( (string) $params['sort'] ) : '';
		if ( '' !== $sort && class_exists( 'STM_LMS_Helpers' ) ) {
			$sort_params = \STM_LMS_Helpers::get_sort_params_by_string( $sort );
			$key         = $sort_params['key'] ?? '';
			$direction   = strtoupper( (string) ( $sort_params['direction'] ?? 'desc' ) );
			$direction   = in_array( $direction, array( 'ASC', 'DESC' ), true ) ? $direction : 'DESC';

			switch ( $key ) {
				case 'id':
					$query_args['orderby'] = 'ID';
					$query_args['order']   = $direction;
					break;
				case 'title':
					$query_args['orderby'] = 'title';
					$query_args['order']   = $direction;
					break;
				case 'status':
					$query_args['orderby'] = 'post_status';
					$query_args['order']   = $direction;
					break;
				case 'date':
				default:
					$query_args['orderby'] = 'date';
					$query_args['order']   = $direction;
			}
		} else {
			$query_args['orderby'] = 'date';
			$query_args['order']   = 'DESC';
		}

		$query = new WP_Query( $query_args );

		return array(
			'posts'           => $query->posts,
			'pages'           => (int) $query->max_num_pages,
			'current_page'    => $page,
			'total_questions' => (int) $query->found_posts,
		);
	}

	private function resolve_category_ids( string $category ): array {
		$category_ids = array();
		$values       = array_filter(
			array_map( 'trim', explode( ',', $category ) )
		);

		foreach ( $values as $value ) {
			if ( is_numeric( $value ) ) {
				$term = get_term( (int) $value, Taxonomy::QUESTION_CATEGORY );

				if ( $term && ! is_wp_error( $term ) ) {
					$category_ids[] = (int) $term->term_id;
				}
			}

			$term = get_term_by( 'slug', $value, Taxonomy::QUESTION_CATEGORY );

			if ( ! $term ) {
				$term = get_term_by( 'slug', sanitize_title( $value ), Taxonomy::QUESTION_CATEGORY );
			}

			if ( ! $term ) {
				$term = get_term_by( 'name', $value, Taxonomy::QUESTION_CATEGORY );
			}

			if ( $term && ! is_wp_error( $term ) ) {
				$category_ids[] = (int) $term->term_id;
			}
		}

		return array_values( array_unique( $category_ids ) );
	}

	public function update_status( int $question_id, string $status ): void {
		if ( PostType::QUESTION !== get_post_type( $question_id ) ) {
			throw new RuntimeException(
				esc_html__( 'Invalid question provided for status update.', 'masterstudy-lms-learning-management-system' ),
				self::ERROR_BAD_REQUEST
			);
		}

		if ( ! $this->is_allowed_status( $status ) ) {
			throw new RuntimeException(
				esc_html__( 'Invalid status provided for question update.', 'masterstudy-lms-learning-management-system' ),
				self::ERROR_BAD_REQUEST
			);
		}

		if ( 'trash' === $status ) {
			$result = wp_trash_post( $question_id );
			if ( false === $result ) {
				throw new RuntimeException(
					esc_html__( 'Unable to move question to trash.', 'masterstudy-lms-learning-management-system' )
				);
			}

			return;
		}

		if ( 'trash' === get_post_status( $question_id ) ) {
			$untrash = wp_untrash_post( $question_id );
			if ( ! $untrash ) {
				throw new RuntimeException(
					esc_html__( 'Unable to restore question from trash.', 'masterstudy-lms-learning-management-system' )
				);
			}
		}

		$updated = wp_update_post(
			array(
				'ID'          => $question_id,
				'post_status' => $status,
			),
			true
		);

		if ( is_wp_error( $updated ) ) {
			throw new RuntimeException( $updated->get_error_message() );
		}
	}

	public function bulk_update( string $action, array $questions, ?string $status ): void {
		if ( BulkQuestionAction::UPDATE_STATUS === $action && empty( $status ) ) {
			throw new RuntimeException(
				esc_html__( 'Status is required for update_status action.', 'masterstudy-lms-learning-management-system' ),
				self::ERROR_BAD_REQUEST
			);
		}

		if ( BulkQuestionAction::UPDATE_STATUS === $action && ! $this->is_allowed_status( (string) $status ) ) {
			throw new RuntimeException(
				esc_html__( 'Invalid status provided for update_status action.', 'masterstudy-lms-learning-management-system' ),
				self::ERROR_BAD_REQUEST
			);
		}

		foreach ( $questions as $question ) {
			$question_id = $this->resolve_question_id( $question );
			if ( ! $question_id || PostType::QUESTION !== get_post_type( $question_id ) ) {
				throw new RuntimeException(
					esc_html__( 'Invalid question provided for bulk action.', 'masterstudy-lms-learning-management-system' ),
					self::ERROR_BAD_REQUEST
				);
			}

			if ( BulkQuestionAction::DELETE === $action ) {
				if ( ! current_user_can( 'delete_post', $question_id ) ) {
					throw new RuntimeException(
						esc_html__( 'You do not have permission to delete this question.', 'masterstudy-lms-learning-management-system' ),
						self::ERROR_FORBIDDEN
					);
				}

				try {
					$this->delete( $question_id );
				} catch ( RuntimeException $e ) {
					throw new RuntimeException(
						sprintf(
							/* translators: %d: question ID. */
							esc_html__( 'Unable to delete question %d.', 'masterstudy-lms-learning-management-system' ),
							$question_id
						)
					);
				}

				continue;
			}

			if ( ! current_user_can( 'edit_post', $question_id ) ) {
				throw new RuntimeException(
					esc_html__( 'You do not have permission to edit this question.', 'masterstudy-lms-learning-management-system' ),
					self::ERROR_FORBIDDEN
				);
			}

			if ( 'trash' === $status && ! current_user_can( 'delete_post', $question_id ) ) {
				throw new RuntimeException(
					esc_html__( 'You do not have permission to move this question to trash.', 'masterstudy-lms-learning-management-system' ),
					self::ERROR_FORBIDDEN
				);
			}

			$this->update_status( $question_id, (string) $status );
		}
	}

	private function resolve_bank_categories( array $data ) {
		if ( QuestionType::QUESTION_BANK !== $data['type'] || empty( $data['categories'][0] ) ) {
			return $data;
		}

		$categories = (array) $data['categories'];
		if ( ! is_numeric( $categories[0] ) ) {
			return $data;
		}

		$terms = get_terms(
			array(
				'taxonomy' => Taxonomy::QUESTION_CATEGORY,
				'include'  => wp_parse_id_list( $categories ),
			)
		);

		$data['answers'][0]['categories'] = array_map(
			function ( \WP_Term $term ) {
				return $term->to_array();
			},
			$terms
		);

		$data['categories'] = array();

		return $data;
	}

	/**
	 * @param mixed $question
	 */
	private function resolve_question_id( $question ): int {
		if ( is_numeric( $question ) ) {
			return (int) $question;
		}

		if ( is_array( $question ) && isset( $question['id'] ) ) {
			return (int) $question['id'];
		}

		return 0;
	}

	private function is_allowed_status( string $status ): bool {
		return in_array( $status, array( 'publish', 'pending', 'draft', 'trash', 'private' ), true );
	}

	public static function fill_the_gap_output_data( array $data, bool $show_answers ): array {
		$data = array(
			'id'                       => $data['id'],
			'user_answer'              => ! empty( $data['last_answers']['user_answer'] ) ? explode( ',', $data['last_answers']['user_answer'] ) : array(),
			'answer_text'              => $data['answers'][0]['text'],
			'matches'                  => stm_lms_get_string_between( $data['answers'][0]['text'], '|', '|' ),
			'answer_field'             => array(),
			'correct_answer'           => array(),
			'correct_user_answer'      => array(),
			'show_correct_user_answer' => array(),
			'show_correct_answer'      => $data['show_correct_answer'],
			'is_correct'               => $data['is_correct'],
		);

		if ( ! empty( $data['matches'] ) ) {
			$data_question = array_map(
				function ( $answer ) {
					return "|{$answer['answer']}|";
				},
				$data['matches']
			);

			foreach ( $data_question as $match_index => $match ) {
				$width                                = 'width: ' . ( strlen( $match ) * 8 + 16 ) . 'px';
				$width                               .= '; min-width: ' . ( strlen( $match ) * 8 + 16 ) . 'px';
				$name                                 = "{$data['id']}[{$match_index}]";
				$data['answer_field'][ $match_index ] = "<input type='text' name='{$name}' style='{$width}' />";
			}

			if ( $show_answers ) {
				foreach ( $data['matches'] as $match_index => $match ) {
					$match_index                         = (int) $match_index;
					$match_answer                        = stripslashes( rawurldecode( $match['answer'] ) );
					$data['user_answer'][ $match_index ] = isset( $data['user_answer'][ $match_index ] )
						? stripslashes( rawurldecode( $data['user_answer'][ $match_index ] ) )
						: null;

					$user_answer  = trim( strtolower( stripslashes( rawurldecode( $data['user_answer'][ $match_index ] ) ) ) );
					$match_answer = trim( strtolower( stripslashes( rawurldecode( html_entity_decode( $match_answer, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) ) ) );

					$correct = ( isset( $data['user_answer'][ $match_index ] ) && $match_answer === $user_answer || $data['is_correct'] )
						? 'masterstudy-course-player-fill-the-gap__check-correct'
						: 'masterstudy-course-player-fill-the-gap__check-incorrect';

					$data['correct_answer'][ $match_index ]           = "{$correct}";
					$data['correct_user_answer'][ $match_index ]      = $data['is_correct'] ? $match['answer'] : "{$data['user_answer'][ $match_index ]}";
					$data['show_correct_user_answer'][ $match_index ] = "{$match_answer}";
				}
			}
		}

		return apply_filters( 'masterstudy_lms_fill_gap_question_output_data', $data );
	}
}
