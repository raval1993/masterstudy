<?php
/**
 * @var $instructor_id
 */

use MasterStudy\Lms\Plugin\Addons;

$instructor = STM_LMS_User::get_current_user( $instructor_id, false, true );

if ( empty( $instructor['id'] ) ) {
	return;
}

STM_LMS_Templates::show_lms_template( 'header' );

$settings                       = get_option( 'stm_lms_settings' );
$settings['course_tab_reviews'] = $settings['course_tab_reviews'] ?? true;
$profile_active                 = $settings['instructor_public_profile'] ?? true;
$profile_style                  = $_GET['public'] ?? $settings['instructor_public_profile_style'] ?? 'compact';
$show_reviews                   = $settings['instructor_reviews_public_profile'] ?? true;

if ( ! STM_LMS_Instructor::is_instructor( $instructor['id'] ) || ! $profile_active ) {
	echo esc_html__( 'This page does not exist.', 'masterstudy-lms-learning-management-system' );

	return;
}

wp_enqueue_style( 'masterstudy-review-card' );
wp_enqueue_style( 'masterstudy-bundle-card-default' );
wp_enqueue_style( 'masterstudy-instructor-public-account' );
wp_enqueue_script( 'masterstudy-instructor-public-account' );
wp_localize_script(
	'masterstudy-instructor-public-account',
	'instructor_data',
	array(
		'user'              => $instructor_id,
		'user_login'        => $instructor['login'],
		'courses_per_page'  => 12,
		'bundles_per_page'  => 6,
		'reviews_per_page'  => 5,
		'co_owned_per_page' => 12,
	)
);

$args                   = array(
	'posts_per_page' => 12,
	'author__in'     => array( $instructor_id ),
	'paged'          => 1,
);
$logged_in              = is_user_logged_in();
$courses                = STM_LMS_Courses::get_all_courses( $args );
$position               = ! empty( $instructor['meta']['position'] ) ? $instructor['meta']['position'] : esc_html__( 'Instructor', 'masterstudy-lms-learning-management-system' );
$description            = ! empty( $instructor['meta']['description'] ) ? $instructor['meta']['description'] : '';
$rating                 = STM_LMS_Instructor::my_rating( $instructor );
$user_info              = get_userdata( $instructor['id'] );
$stars                  = range( 1, 5 );
$socials                = array( 'facebook', 'twitter', 'linkedin', 'instagram' );
$is_multi_instructor_on = is_ms_lms_addon_enabled( 'multi_instructors' );
$instructor_tabs        = array(
	array(
		'id'         => 'courses',
		'title'      => esc_html__( 'Courses', 'masterstudy-lms-learning-management-system' ),
		'is_visible' => true,
	),
	array(
		'id'         => 'co-owned',
		'title'      => esc_html__( 'Co-owned courses', 'masterstudy-lms-learning-management-system' ),
		'is_visible' => $is_multi_instructor_on,
	),
	array(
		'id'         => 'bundles',
		'title'      => esc_html__( 'Bundles', 'masterstudy-lms-learning-management-system' ),
		'is_visible' => is_ms_lms_addon_enabled( Addons::COURSE_BUNDLE ),
	),
	array(
		'id'         => 'reviews',
		'title'      => esc_html__( 'Reviews', 'masterstudy-lms-learning-management-system' ),
		'is_visible' => $show_reviews && $settings['course_tab_reviews'],
	),
);

$instructor_tabs = array_filter(
	$instructor_tabs,
	function( $tab ) {
		return $tab['is_visible'];
	}
);

STM_LMS_Templates::show_lms_template(
	'components/modals/message',
	array(
		'username'  => $instructor['login'],
		'user_id'   => $instructor_id,
		'logged_in' => $logged_in,
	)
);
?>

<div class="masterstudy-instructor-public <?php echo esc_attr( 'masterstudy-instructor-public_' . $profile_style ); ?>">
	<div class="masterstudy-instructor-public__profile">
		<?php if ( ! empty( $instructor['cover'] ) ) { ?>
			<div class="masterstudy-instructor-public__cover">
				<img src="<?php echo esc_url( $instructor['cover'] ); ?>" class="masterstudy-instructor-public__cover-image">
			</div>
		<?php } ?>
		<div class="masterstudy-instructor-public__profile-container">
			<div class="masterstudy-instructor-public__avatar <?php echo empty( $instructor['cover'] ) ? 'masterstudy-instructor-public__avatar_empty' : ''; ?>">
				<?php
				if ( ! empty( $instructor['avatar'] ) ) {
					echo wp_kses_post( $instructor['avatar'] );
				}
				?>
			</div>
			<div class="masterstudy-instructor-public__name">
				<?php echo esc_html( $instructor['login'] ); ?>
			</div>
			<div class="masterstudy-instructor-public__position">
				<?php echo wp_kses_post( $position ); ?>
			</div>
			<div class="masterstudy-instructor-public__details-wrapper">
				<div class="masterstudy-instructor-public__stats">
					<div class="masterstudy-instructor-public__stats-row">
						<div id="total-courses" class="masterstudy-instructor-public__stats-value">
							<?php echo esc_html( ! empty( $courses['posts'] ) ? $courses['total_posts'] : 0 ); ?>
						</div>
						<div class="masterstudy-instructor-public__stats-label">
							<?php echo esc_html__( 'courses', 'masterstudy-lms-learning-management-system' ); ?>
						</div>
					</div>
				</div>
				<?php if ( $settings['course_tab_reviews'] ) { ?>
				<div class="masterstudy-instructor-public__rating">
					<?php foreach ( $stars as $star ) { ?>
						<span class="masterstudy-instructor-public__rating-star <?php echo esc_attr( $star <= floor( $rating['average'] ) ? 'masterstudy-instructor-public__rating-star_filled ' : '' ); ?>"></span>
					<?php } ?>
					<div class="masterstudy-instructor-public__rating-count">
						<?php echo (float) $rating['average'] === (int) $rating['average'] ? number_format( (int) $rating['average'], 1 ) : esc_html( round( $rating['average'], 1 ) ); ?>
					</div>
				</div>
				<?php } ?>
				<div class="masterstudy-instructor-public__actions">
					<?php
					$button_args = array(
						'title' => esc_html__( 'Send message', 'masterstudy-lms-learning-management-system' ),
						'link'  => '#',
						'style' => 'primary',
						'size'  => 'sm',
						'id'    => 'masterstudy-instructor-message-send',
					);

					if ( ! $logged_in ) {
						$button_args['login'] = 'login';
					}

					STM_LMS_Templates::show_lms_template( 'components/button', $button_args );
					if ( 'extended' === $profile_style ) {
						?>
						<div class="masterstudy-instructor-public__details">
							<span class="masterstudy-instructor-public__details-show">
								<?php echo esc_html__( 'Show Details', 'masterstudy-lms-learning-management-system' ); ?>
							</span>
							<span class="masterstudy-instructor-public__details-hide">
								<?php echo esc_html__( 'Hide Details', 'masterstudy-lms-learning-management-system' ); ?>
							</span>
						</div>
					<?php } ?>
				</div>
				<?php if ( ! empty( $description ) ) { ?>
					<div class="masterstudy-instructor-public__description">
						<?php echo wp_kses_post( $description ); ?>
					</div>
					<?php
				}
				STM_LMS_Templates::show_lms_template( 'components/form-builder-fields/public-fields', array( 'user_id' => $instructor_id ) );
				?>
				<div class="masterstudy-instructor-public__socials">
					<?php
					foreach ( $socials as $social ) {
						if ( ! empty( $instructor['meta'][ $social ] ) ) {
							?>
							<a href="<?php echo esc_url( $instructor['meta'][ $social ] ); ?>" class="masterstudy-instructor-public__socials-link" data-id="<?php echo esc_attr( $social ); ?>" target="_blank"></a>
							<?php
						}
					}
					?>
				</div>
				<div class="masterstudy-instructor-public__member">
					<?php
					echo esc_html__( 'Member since', 'masterstudy-lms-learning-management-system' );
					echo esc_html( ' ' . date_i18n( 'F Y', strtotime( $user_info->user_registered ) ) );
					?>
				</div>
			</div>
		</div>
	</div>
	<div class="masterstudy-instructor-public__content">
		<div class="masterstudy-instructor-public__tabs">
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/tabs',
				array(
					'items'            => $instructor_tabs,
					'style'            => 'buttons',
					'active_tab_index' => 0,
					'dark_mode'        => false,
				)
			);
			?>
		</div>
		<div class="masterstudy-instructor-public__list-header">
			<div class="masterstudy-instructor-public__list-header-title">
				<?php echo esc_html__( 'Courses reviews', 'masterstudy-lms-learning-management-system' ); ?>
				<span class="masterstudy-instructor-public__list-header-total"></span>
			</div>
			<div class="masterstudy-instructor-public__filters">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/search',
					array(
						'search_name'  => 'reviews-search',
						'is_queryable' => false,
						'placeholder'  => esc_html__( 'Search by course name', 'masterstudy-lms-learning-management-system' ),
					)
				);
				STM_LMS_Templates::show_lms_template(
					'components/select',
					array(
						'select_id'    => 'reviews-rating',
						'select_name'  => 'reviews-rating',
						'placeholder'  => esc_html__( 'All ratings', 'masterstudy-lms-learning-management-system' ),
						'default'      => 'all',
						'is_queryable' => false,
						'options'      => array(
							'5' => esc_html__( '5 stars', 'masterstudy-lms-learning-management-system' ),
							'4' => esc_html__( '4 stars', 'masterstudy-lms-learning-management-system' ),
							'3' => esc_html__( '3 stars', 'masterstudy-lms-learning-management-system' ),
							'2' => esc_html__( '2 stars', 'masterstudy-lms-learning-management-system' ),
							'1' => esc_html__( '1 stars', 'masterstudy-lms-learning-management-system' ),
						),
					)
				);
				?>
			</div>
		</div>
		<div class="masterstudy-instructor-public__list">
			<?php
			if ( ! empty( $courses['posts'] ) ) {
				foreach ( $courses['posts'] as $course ) {
					STM_LMS_Templates::show_lms_template(
						'components/course/card/default',
						array(
							'course'  => $course,
							'public'  => true,
							'reviews' => $settings['course_tab_reviews'],
						)
					);
				}
			}
			?>
		</div>
		<div class="masterstudy-instructor-public__loader">
			<div class="masterstudy-instructor-public__loader-body"></div>
		</div>
		<div class="masterstudy-instructor-public__empty <?php echo esc_attr( empty( $courses['posts'] ) ? 'masterstudy-instructor-public__empty_show' : '' ); ?>">
			<div class="masterstudy-instructor-public__empty-block">
				<span class="masterstudy-instructor-public__empty-icon"></span>
				<span class="masterstudy-instructor-public__empty-text">
					<?php echo esc_html__( 'Nothing to show yet', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
			</div>
		</div>
		<div class="masterstudy-instructor-public__list-pagination">
			<?php
			if ( ! empty( $courses ) && $courses['total_pages'] > 1 ) {
				STM_LMS_Templates::show_lms_template(
					'components/pagination',
					array(
						'max_visible_pages' => 5,
						'total_pages'       => $courses['total_pages'],
						'current_page'      => 1,
						'dark_mode'         => false,
						'is_queryable'      => false,
						'done_indicator'    => false,
						'is_api'            => true,
					)
				);
			}
			?>
		</div>
	</div>
</div>
<?php
STM_LMS_Templates::show_lms_template( 'footer' );
