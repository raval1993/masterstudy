<?php

namespace MasterStudy\Lms\Plugin;

use MasterStudy\Lms\Plugin;
use STM_LMS_Course;

if ( ! defined( 'ABSPATH' ) ) {
	exit; //Exit if accessed directly
}

class Taxonomy {
	public const COURSE_CATEGORY   = 'stm_lms_course_taxonomy';
	public const QUESTION_CATEGORY = 'stm_lms_question_taxonomy';

	public const COURSE_CATEGORY_DEFAULT_SLUG = 'stm_lms_course_category';

	public static function add_fields( $taxonomy ) {
		add_action( 'stm_lms_course_taxonomy_add_form_fields', array( __CLASS__, 'add_stm_lms_course_taxonomy_fields' ), 10, 2 );
		add_action( 'stm_lms_course_taxonomy_edit_form_fields', array( __CLASS__, 'edit_stm_lms_course_taxonomy_fields' ), 10, 2 );
		add_action( 'create_stm_lms_course_taxonomy', array( __CLASS__, 'save_stm_lms_course_taxonomy_fields' ), 10, 2 );
		add_action( 'edited_stm_lms_course_taxonomy', array( __CLASS__, 'save_stm_lms_course_taxonomy_fields' ), 10, 2 );
		add_filter(
			"manage_edit-{$taxonomy}_columns",
			function ( $columns ) use ( $taxonomy ) {
				return self::add_columns( $columns, $taxonomy );
			},
			10,
			2
		);
		add_action(
			"manage_{$taxonomy}_custom_column",
			function ( $content, $column_name, $term_id ) use ( $taxonomy ) {
				return self::fill_columns( $content, $column_name, $term_id, $taxonomy );
			},
			10,
			4
		);
		add_action(
			'pre_get_terms',
			function ( $query ) use ( $taxonomy ) {
				self::query_columns( $query, $taxonomy );
			},
			10,
			2
		);
		add_filter(
			"manage_edit-{$taxonomy}_sortable_columns",
			function ( $columns ) use ( $taxonomy ) {
				return self::sort_columns( $columns, $taxonomy );
			},
			10,
			2
		);
		add_filter( 'term_link', array( __CLASS__, 'overwrite_taxonomy_term_link' ), 10, 3 );

		if ( self::COURSE_CATEGORY === $taxonomy ) {
			add_filter( 'manage_' . PostType::COURSE . '_posts_columns', array( __CLASS__, 'normalize_course_category_column_header' ) );
		}

		if ( self::QUESTION_CATEGORY === $taxonomy ) {
			add_filter( 'manage_' . PostType::QUESTION . '_posts_columns', array( __CLASS__, 'normalize_question_category_column_header' ) );
		}
	}

	/**
	 * @return array[]
	 */
	public static function defaults( $course_category_slug ): array {
		// phpcs:disable WordPress.WP.I18n.NonSingularStringLiteralDomain
		return array(
			self::COURSE_CATEGORY   => array(
				'post_type' => PostType::COURSE,
				'args'      => array(
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => _x( '⤷ Course Category', 'taxonomy general name', Plugin::TRANSLATION_DOMAIN ),
						'singular_name'     => _x( 'Course Category', 'taxonomy singular name', Plugin::TRANSLATION_DOMAIN ),
						'search_items'      => __( 'Search Courses Category', Plugin::TRANSLATION_DOMAIN ),
						'all_items'         => __( 'All Courses Category', Plugin::TRANSLATION_DOMAIN ),
						'parent_item'       => __( 'Parent Course Category', Plugin::TRANSLATION_DOMAIN ),
						'parent_item_colon' => __( 'Parent Course Category:', Plugin::TRANSLATION_DOMAIN ),
						'edit_item'         => __( 'Edit Course Category', Plugin::TRANSLATION_DOMAIN ),
						'update_item'       => __( 'Update Course Category', Plugin::TRANSLATION_DOMAIN ),
						'add_new_item'      => __( 'Add New Course Category', Plugin::TRANSLATION_DOMAIN ),
						'new_item_name'     => __( 'New Course Category Name', Plugin::TRANSLATION_DOMAIN ),
						'menu_name'         => __( 'Course Category', Plugin::TRANSLATION_DOMAIN ),
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'hidden'            => true,
				),
			),
			self::QUESTION_CATEGORY => array(
				'post_type' => PostType::QUESTION,
				'args'      => array(
					'public'            => false,
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => _x( '⤷ Question Category', 'taxonomy general name', Plugin::TRANSLATION_DOMAIN ),
						'singular_name'     => _x( 'Question Category', 'taxonomy singular name', Plugin::TRANSLATION_DOMAIN ),
						'search_items'      => __( 'Search Questions Category', Plugin::TRANSLATION_DOMAIN ),
						'all_items'         => __( 'All Questions Category', Plugin::TRANSLATION_DOMAIN ),
						'parent_item'       => __( 'Parent Question Category', Plugin::TRANSLATION_DOMAIN ),
						'parent_item_colon' => __( 'Parent Question Category:', Plugin::TRANSLATION_DOMAIN ),
						'edit_item'         => __( 'Edit Question Category', Plugin::TRANSLATION_DOMAIN ),
						'update_item'       => __( 'Update Question Category', Plugin::TRANSLATION_DOMAIN ),
						'add_new_item'      => __( 'Add New Question Category', Plugin::TRANSLATION_DOMAIN ),
						'new_item_name'     => __( 'New Question Category Name', Plugin::TRANSLATION_DOMAIN ),
						'menu_name'         => __( 'Question Category', Plugin::TRANSLATION_DOMAIN ),
					),
					'show_ui'           => true,
					'show_in_menu'      => false,
					'show_admin_column' => true,
					'query_var'         => true,
				),
			),
		);
		// phpcs:enable WordPress.WP.I18n.NonSingularStringLiteralDomain
	}

	/**
	 * @return array<\WP_Term>
	 */
	public static function all_categories( \WP_REST_Request $request = null ): array {
		$args = array(
			'hide_empty' => false,
			'taxonomy'   => self::COURSE_CATEGORY,
		);

		$categories = get_terms( $args );

		if ( $request instanceof \WP_REST_Request ) {
			if ( ! empty( $request->get_param( 'children' ) ) ) {
				$categories = self::all_categories_tree( $categories );
			}

			if ( ! empty( $request->get_param( 'details' ) ) ) {
				$categories = self::all_categories_details( $categories );
			}
		}

		return $categories;
	}

	private static function all_categories_tree( $categories, $parent_id = 0 ) {
		$category_map = array();

		foreach ( $categories as $category ) {
			$category_map[ $category->parent ][] = $category;
		}

		$build_tree = function ( $parent_id ) use ( &$category_map, &$build_tree ) {
			$result = array();

			if ( isset( $category_map[ $parent_id ] ) ) {
				foreach ( $category_map[ $parent_id ] as $category ) {
					$category->children = $build_tree( $category->term_id );
					$result[]           = $category;
				}
			}

			return $result;
		};

		return $build_tree( $parent_id );
	}

	private static function all_categories_details( $categories ) {
		return array_map(
			function ( $category ) {
				$category->course_image = wp_get_attachment_image_url( get_term_meta( $category->term_id, 'course_image', true ), 'full' );
				$category->course_icon  = get_term_meta( $category->term_id, 'course_icon', true );
				$category->course_color = get_term_meta( $category->term_id, 'course_color', true );
				$category->course_count = get_term_by( 'id', $category->term_id, 'stm_lms_course_taxonomy' )->count;
				if ( ! empty( $category->children ) ) {
					$category->children = self::all_categories_details( $category->children );
				}

				return $category;
			},
			$categories
		);
	}

	public static function add_stm_lms_course_taxonomy_fields( $taxonomy ) {
		$nonce = wp_create_nonce( 'course_page_style_nonce' );
		?>
		<div class="form-field term-group">
			<label class="masterstudy-templates-choose-label" for="course_page_style">
				<?php echo esc_html__( 'Course Page Style', 'masterstudy-lms-learning-management-system' ); ?>
			</label>
			<div class="masterstudy-templates-choose-button" data-id="new_category" data-current-style>
				<span class="masterstudy-templates-choose-button__title">
					<?php echo esc_html__( 'None', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
			</div>
			<div data-id="new_category" class="masterstudy-templates-reset-button">
				<div class="masterstudy-hint">
					<div class="masterstudy-hint__popup">
						<div class="masterstudy-hint__text">
							<?php echo esc_html__( 'Reset', 'masterstudy-lms-learning-management-system' ); ?>
						</div>
					</div>
				</div>
			</div>
			<input type="hidden" id="course_page_style" name="course_page_style" />
			<input type="hidden" name="course_page_style_nonce" value="<?php echo esc_html( $nonce ); ?>">
		</div>
		<?php
	}

	public static function edit_stm_lms_course_taxonomy_fields( $term, $taxonomy ) {
		$current_style = get_term_meta( $term->term_id, 'course_page_style', true );
		$my_templates  = class_exists( '\Elementor\Plugin' ) ? masterstudy_lms_get_my_templates() : array();
		$page_styles   = array_merge(
			masterstudy_lms_get_native_templates(),
			$my_templates,
		);
		$style_label   = __( 'None', 'masterstudy-lms-learning-management-system' );
		$nonce         = wp_create_nonce( 'course_page_style_nonce' );

		if ( ! empty( $current_style ) && ! empty( $page_styles ) ) {
			$matched = array_filter(
				$page_styles,
				function ( $style ) use ( $current_style ) {
					return isset( $style['name'] ) && $style['name'] === $current_style;
				}
			);

			if ( ! empty( $matched ) ) {
				$style = reset( $matched );
				if ( isset( $style['title'] ) ) {
					$style_label = esc_html( $style['title'] );
				}
			}
		}
		?>
		<tr class="form-field term-group-wrap">
			<th scope="row">
				<label for="course_page_style"><?php echo esc_html__( 'Course Page Style', 'masterstudy-lms-learning-management-system' ); ?></label>
			</th>
			<td>
				<div class="masterstudy-templates-choose-button" data-id="edit_category_inside" data-term-id="<?php echo esc_attr( $term->term_id ); ?>" data-current-style="<?php echo esc_attr( $current_style ); ?>">
					<span class="masterstudy-templates-choose-button__title">
						<?php echo esc_html( $style_label ); ?>
					</span>
					<div class="masterstudy-hint">
						<div class="masterstudy-hint__popup">
							<div class="masterstudy-hint__text">
								<?php echo esc_html( $style_label ); ?>
							</div>
						</div>
					</div>
				</div>
				<div data-id="edit_category_inside" class="masterstudy-templates-reset-button">
					<div class="masterstudy-hint">
						<div class="masterstudy-hint__popup">
							<div class="masterstudy-hint__text">
								<?php echo esc_html__( 'Reset', 'masterstudy-lms-learning-management-system' ); ?>
							</div>
						</div>
					</div>
				</div>
				<input type="hidden" id="course_page_style" name="course_page_style" value="<?php echo esc_attr( $current_style ); ?>" />
				<input type="hidden" name="course_page_style_nonce" value="<?php echo esc_html( $nonce ); ?>">
			</td>
		</tr>
		<?php
	}

	public static function save_stm_lms_course_taxonomy_fields( $term_id, $tt_id ) {
		if ( ! isset( $_POST['course_page_style_nonce'] ) || ! wp_verify_nonce( $_POST['course_page_style_nonce'], 'course_page_style_nonce' ) ) {
			return;
		}

		if ( isset( $_POST['course_icon'] ) ) {
			update_term_meta( $term_id, 'course_icon', sanitize_text_field( $_POST['course_icon'] ) );
		}

		if ( isset( $_POST['course_page_style'] ) && 'none' !== $_POST['course_page_style'] ) {
			update_term_meta( $term_id, 'course_page_style', sanitize_text_field( $_POST['course_page_style'] ) );
		} else {
			update_term_meta( $term_id, 'course_page_style', '' );
		}
	}

	public static function add_columns( $columns, $taxonomy ) {
		unset( $columns['posts'] );

		if ( 'stm_lms_course_taxonomy' === $taxonomy ) {
			$columns['course_page_style']         = __( 'Course page style', 'masterstudy-lms-learning-management-system' );
			$columns['masterstudy_courses_count'] = __( 'Count', 'masterstudy-lms-learning-management-system' );
		} elseif ( 'stm_lms_question_taxonomy' === $taxonomy ) {
			$columns['masterstudy_questions_count'] = __( 'Count', 'masterstudy-lms-learning-management-system' );
		}

		return $columns;
	}

	public static function normalize_course_category_column_header( $columns ) {
		$taxonomy_column = 'taxonomy-' . self::COURSE_CATEGORY;

		if ( isset( $columns[ $taxonomy_column ] ) ) {
			$columns[ $taxonomy_column ] = __( 'Course Category', 'masterstudy-lms-learning-management-system' );
		}

		return $columns;
	}

	public static function normalize_question_category_column_header( $columns ) {
		$taxonomy_column = 'taxonomy-' . self::QUESTION_CATEGORY;

		if ( isset( $columns[ $taxonomy_column ] ) ) {
			$columns[ $taxonomy_column ] = __( 'Question Category', 'masterstudy-lms-learning-management-syste,' );
		}

		return $columns;
	}

	public static function fill_columns( $content, $column_name, $term_id, $taxonomy ) {
		if ( 'course_page_style' === $column_name && 'stm_lms_course_taxonomy' === $taxonomy ) {
			$my_templates  = class_exists( '\Elementor\Plugin' ) ? masterstudy_lms_get_my_templates() : array();
			$page_styles   = array_merge(
				masterstudy_lms_get_native_templates(),
				$my_templates,
			);
			$current_style = get_term_meta( $term_id, 'course_page_style', true );
			$style_label   = __( 'None', 'masterstudy-lms-learning-management-system' );

			if ( ! empty( $current_style ) && ! empty( $page_styles ) ) {
				$matched = array_filter(
					$page_styles,
					function ( $style ) use ( $current_style ) {
						return isset( $style['name'] ) && $style['name'] === $current_style;
					}
				);

				if ( ! empty( $matched ) ) {
					$style = reset( $matched );
					if ( isset( $style['title'] ) ) {
						$style_label = esc_html( $style['title'] );
					}
				}
			}

			$content = sprintf(
				'<div class="masterstudy-templates-choose-button" data-id="edit_category" data-term-id="%d" data-current-style="%s">
					<span class="masterstudy-templates-choose-button__title">%s</span>
					<div class="masterstudy-hint">
						<div class="masterstudy-hint__popup">
							<div class="masterstudy-hint__text">
								%s
							</div>
						</div>
					</div>
				</div><div data-id="edit_category" data-term-id="%d" class="masterstudy-templates-reset-button">
					<div class="masterstudy-hint">
						<div class="masterstudy-hint__popup">
							<div class="masterstudy-hint__text">
								%s
							</div>
						</div>
					</div>
				</div>',
				$term_id,
				esc_attr( $current_style ),
				esc_html( $style_label ),
				esc_html( $style_label ),
				$term_id,
				esc_html__( 'Reset', 'masterstudy-lms-learning-management-system' ),
			);
		}

		if ( in_array( $column_name, array( 'masterstudy_courses_count', 'masterstudy_questions_count' ), true ) ) {
			$post_type     = 'stm-courses';
			$taxonomy_slug = 'stm_lms_course_taxonomy';

			if ( 'masterstudy_questions_count' === $column_name ) {
				$post_type     = 'stm-questions';
				$taxonomy_slug = 'stm_lms_question_taxonomy';
			}

			$term    = get_term_by( 'id', $term_id, $taxonomy_slug );
			$new_url = add_query_arg(
				array(
					'post_type'    => $post_type,
					$taxonomy_slug => $term->slug,
				),
				admin_url( 'edit.php' )
			);

			$content = sprintf( '<a href="%s">%d</a>', esc_url( $new_url ), $term->count );
		}

		return $content;
	}

	public static function sort_columns( $columns, $taxonomy ) {
		if ( 'stm_lms_course_taxonomy' === $taxonomy ) {
			$columns['masterstudy_courses_count'] = 'masterstudy_courses_count';
		} elseif ( 'stm_lms_question_taxonomy' === $taxonomy ) {
			$columns['masterstudy_questions_count'] = 'masterstudy_questions_count';
		}

		return $columns;
	}

	public static function query_columns( $query, $taxonomy ) {
		if ( ! isset( $query->query_vars['taxonomy'] ) || ! in_array( $taxonomy, $query->query_vars['taxonomy'], true ) ) {
			return;
		}

		$order_by_key = 'stm_lms_course_taxonomy' === $taxonomy ? 'masterstudy_courses_count' : 'masterstudy_questions_count';

		if ( isset( $query->query_vars['orderby'] ) && $order_by_key === $query->query_vars['orderby'] ) {
			$order = isset( $query->query_vars['order'] ) && 'desc' === $query->query_vars['order'] ? 'desc' : 'asc';

			$query->query_vars['orderby'] = 'count';
			$query->query_vars['order']   = $order;
		}
	}

	public static function overwrite_taxonomy_term_link( $url, $term, $taxonomy ) {
		if ( self::COURSE_CATEGORY === $taxonomy ) {
			return esc_url( STM_LMS_Course::courses_page_url() . '?terms[]=' . $term->term_id . '&category[]=' . $term->term_id );
		}
		return $url;
	}
}
