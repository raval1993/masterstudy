<?php
/**
 * @var $item
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Enums\SubscriptionPlanType;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;

$plan           = ( new SubscriptionPlanRepository() )->get( $item['item_id'] );
$course_id      = SubscriptionPlanType::COURSE === $plan['type'] ? ( $plan['items'][0]['object_id'] ?? null ) : null;
$category_ids   = SubscriptionPlanType::CATEGORY === $plan['type']
	? array_column( $plan['items'], 'object_id' )
	: array();
$categories     = wp_get_post_terms( $course_id, 'stm_lms_course_taxonomy' );
$billing_cycles = 0;
$price          = SubscriptionPlanRepository::get_actual_price( $plan );

$is_trial           = ! empty( $plan['trial_period'] );
$trial_period_days  = intval( $plan['trial_period'] ?? 0 );
$recurring_interval = '';

?>
<div class="masterstudy-checkout-table__body-row memberships">
	<?php if ( ! empty( $course_id ) ) { ?>
		<div class="masterstudy-checkout-course-info__image">
			<a href="<?php echo esc_url( get_the_permalink( $course_id ) ); ?>">
				<img src="<?php echo esc_url( get_the_post_thumbnail_url( $course_id, 'thumbnail' ) ); ?>">
				<div class="masterstudy-checkout-course-info__title">
					<?php echo ! empty( $course_id ) ? esc_html( get_the_title( $course_id ) ) : ''; ?>
					<span>
						<?php
						echo esc_html( implode( ', ', wp_list_pluck( $categories, 'name' ) ) );
						?>
					</span>
				</div>
				<?php if ( ! empty( $plan['billing_cycles'] ) ) : ?>
					<div class="masterstudy-checkout-course-info__price">
						<?php
						echo esc_html(
							STM_LMS_Helpers::display_price_with_taxes(
								$is_trial ? 0 : (float) SubscriptionPlanRepository::get_actual_price( $plan )
							)
						);
						?>
					</div>
				<?php endif; ?>
			</a>
		</div>
	<?php } ?>
	<div class="masterstudy-checkout-course-info">
		<div class="masterstudy-checkout-course-info__common">
			<div class="masterstudy-checkout-course-info__title">
				<?php echo ! empty( $plan['name'] ) ? esc_html( $plan['name'] ) : ''; ?>
			</div>
			<?php if ( ! empty( $plan['description'] ) ) : ?>
				<div class="masterstudy-checkout-course-info__desc">
					<?php echo esc_html( $plan['description'] ); ?>
				</div>
			<?php endif; ?>
			<?php if ( ! empty( $plan['trial_period'] ) ) : ?>
				<div class="masterstudy-checkout-course-info__trial">
					<?php
					printf(
						esc_html(
							_n(
								'%s-Day Free Trial',
								'%s-Days Free Trial',
								$plan['trial_period'],
								'masterstudy-lms-learning-management-system'
							)
						),
						esc_html( $plan['trial_period'] )
					);
					?>
				</div>
			<?php endif; ?>

			<div class="masterstudy-checkout-course-info__category">
			<?php
			if ( ! empty( $category_ids ) ) {
				STM_LMS_Templates::show_lms_template(
					'components/course/categories',
					array(
						'term_ids' => $category_ids,
						'only_one' => false,
						'inline'   => true,
					)
				);
			}
			?>
			</div>
		</div>
		<?php if ( ! empty( $plan['price'] ) ) : ?>
			<div class="masterstudy-checkout-course-info__cost">
				<div class="masterstudy-checkout-course-info__cost-price">
					<div class="masterstudy-checkout-course-info__cost-price-title">
						<?php if ( $is_trial ) : ?>
							<?php esc_html_e( 'Price after trial:', 'masterstudy-lms-learning-management-system' ); ?>
						<?php else : ?>
							<?php esc_html_e( 'Price:', 'masterstudy-lms-learning-management-system' ); ?>
						<?php endif; ?>
					</div>

					<div class="masterstudy-checkout-course-info__cost-price-wrapper">
						<div data-price-current="<?php echo esc_attr( $price ); ?>" class="masterstudy-checkout-course-info__cost-price-current">
							<span><?php echo esc_html( STM_LMS_Helpers::display_price_with_taxes( $price ) ); ?></span>
							<?php if ( ! empty( $plan['recurring_interval'] ) ) : ?>
								<span class="masterstudy-checkout-course-info__cost-price-interval">
									/<?php echo esc_html( $plan['recurring_interval'] ); ?>
								</span>
							<?php endif; ?>
						</div>

						<?php
						if ( ! empty( $plan['sale_price'] ) ) {
							$show_sale_price = false;
							if ( ! empty( $plan['sale_price_from'] ) && ! empty( $plan['sale_price_to'] ) ) {
								$sale_price_from = strtotime( $plan['sale_price_from'] );
								$sale_price_to   = strtotime( $plan['sale_price_to'] );
								$current_time    = time();

								if ( $current_time >= $sale_price_from && $current_time <= $sale_price_to ) {
									$show_sale_price = true;
									$price           = (float) $plan['price'];
								}
							} else {
								$show_sale_price = true;
								$price           = (float) $plan['price'];
							}
							?>
							<?php if ( ! empty( $show_sale_price ) ) : ?>
								<span data-price-current-sale="<?php echo esc_attr( $price ); ?>" class="masterstudy-checkout-course-info__cost-sale-price">
									<?php echo esc_html( STM_LMS_Helpers::display_price_with_taxes( $price ) ); ?>
								</span>
							<?php endif; ?>
						<?php } ?>
					</div>
				</div>

				<?php if ( ! empty( $plan['enrollment_fee'] ) && intval( $plan['enrollment_fee'] ) > 0 ) : ?>
					<div class="masterstudy-checkout-course-info__cost-price">
						<div class="masterstudy-checkout-course-info__cost-price-title"><?php esc_html_e( 'Enrollment fee:', 'masterstudy-lms-learning-management-system' ); ?></div>
						<div data-enrollment-fee="<?php echo esc_attr( $plan['enrollment_fee'] ); ?>" class="masterstudy-checkout-course-info__cost-price-current">
							<?php echo esc_html( STM_LMS_Helpers::display_price_with_taxes( $plan['enrollment_fee'] ) ); ?>
						</div>
					</div>
				<?php endif; ?>

			</div>
		<?php endif; ?>
		<?php if ( ! empty( $plan['billing_cycles'] ) ) : ?>
			<div class="masterstudy-checkout-course-info__timeline">
				<div class="masterstudy-checkout-course-info__cost-price-title">
					<?php esc_html_e( 'Payment Plan:', 'masterstudy-lms-learning-management-system' ); ?>
					<?php
					if ( ! empty( $plan['recurring_interval'] ) ) {
						$recurring_interval = STM_LMS_Helpers::masterstudy_lms_get_subscription_interval_label( $plan['recurring_interval'] );
					}
					?>
					<span>
						<?php
						$billing_cycles_limit = function_exists( 'masterstudy_lms_subscription_plan_billing_cycles_limit' )
							? masterstudy_lms_subscription_plan_billing_cycles_limit( $plan )
							: '';
						printf(
							esc_html__( '%1$s %2$s', 'masterstudy-lms-learning-management-system' ),
							esc_html( $billing_cycles_limit ),
							esc_html( $recurring_interval )
						);
						?>
					</span>
				</div>
				<?php
				$billing_cycles_limit = function_exists( 'masterstudy_lms_subscription_plan_billing_cycles_limit' )
					? masterstudy_lms_subscription_plan_billing_cycles_limit( $plan )
					: $plan['billing_cycles'];

				for ( $i = 1; $i <= $billing_cycles_limit; $i++ ) :

					$billing_cycles_total = $is_trial ? 0 : (float) SubscriptionPlanRepository::get_actual_price( $plan );
					if ( $is_trial && 1 !== $i ) {
						$billing_cycles_total = SubscriptionPlanRepository::get_actual_price( $plan );
					}
					?>
				<div class="masterstudy-checkout-course-info__timeline-step <?php echo 1 === $i ? 'active' : ''; ?>">
					<div class="masterstudy-checkout-course-info__timeline-circle"></div>
					<div class="masterstudy-checkout-course-info__timeline-content">
						<span class="masterstudy-checkout-course-info__timeline-title">
							<?php
							if ( $is_trial && 1 === $i ) {
								printf(
								// translators: %s - trial period days
									esc_html__( 'Trial %s day(s)', 'masterstudy-lms-learning-management-system' ),
									esc_html( $trial_period_days )
								);
							} else {
								printf(
								// translators: %s - payment number
									esc_html__( '%s payment', 'masterstudy-lms-learning-management-system' ),
									esc_html( $i )
								);
							}
							?>
						</span>
						<span class="masterstudy-checkout-course-info__timeline-date">
							<?php
							$start_date        = current_time( 'Y-m-d' );
							$interval          = ! empty( $plan['recurring_interval'] ) ? $plan['recurring_interval'] : 'month';
							$allowed_intervals = array( 'day', 'week', 'month', 'year' );
							$billing_cycles   += $billing_cycles_total;

							if ( ! in_array( $interval, $allowed_intervals, true ) ) {
								$interval = 'month';
							}

							if ( $is_trial ) {
								if ( 1 === $i ) {
									$timestamp = strtotime( $start_date );
								} else {
									$timestamp = strtotime( "$start_date +" . ( $i - 2 ) . " $interval +$trial_period_days days" );
								}
							} else {
								$timestamp = strtotime( "$start_date +" . ( $i - 1 ) . " $interval" );
							}

							echo esc_html( date( 'd F Y', $timestamp ) );
							?>

						</span>
						<span data-timeline-amount="<?php echo esc_attr( $billing_cycles_total ); ?>" class="masterstudy-checkout-course-info__timeline-amount">
							<?php echo esc_html( STM_LMS_Helpers::display_price_with_taxes( $billing_cycles_total ) ); ?>
						</span>
					</div>
				</div>
				<?php endfor; ?>
			</div>
			<div data-timeline-total="<?php echo esc_attr( $billing_cycles ); ?>" class="masterstudy-checkout-course-info__timeline-total">
				<span><?php esc_html_e( 'Total:', 'masterstudy-lms-learning-management-system' ); ?></span>
				<strong><?php echo esc_html( STM_LMS_Helpers::display_price_with_taxes( $billing_cycles ) ); ?></strong>
			</div>
		<?php endif; ?>
		<?php if ( ! empty( $plan['is_certified'] ) && is_ms_lms_addon_enabled( 'certificate_builder' ) ) : ?>
			<div class="masterstudy-checkout-course-info__certificates">
				<?php esc_html_e( 'Certificate included', 'masterstudy-lms-learning-management-system' ); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
