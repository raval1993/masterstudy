<?php
/**
 * @var int $bundle_id
 */

use MasterStudy\Lms\Pro\addons\CourseBundle\Repository\CourseBundleRepository;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Enums\ReccuringInterval;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;

wp_enqueue_style( 'masterstudy-pricing-subscription' );
wp_enqueue_script( 'masterstudy-pricing-subscription' );

$plans                = array();
$subscription_enabled = CourseBundleRepository::get_subscription_enabled( $bundle_id );

if ( $bundle_id ) {
	$plans = ( new SubscriptionPlanRepository() )->list_bundle_plans( $bundle_id );
}

$currency_symbol  = \STM_LMS_Options::get_option( 'currency_symbol', '$' );
$interval_options = ReccuringInterval::get_translate_options();

wp_localize_script(
	'masterstudy-pricing-subscription',
	'_subscription_data',
	array(
		'currency_symbol' => $currency_symbol,
		'translations'    => array(
			'edit'                  => esc_html__( 'Edit', 'masterstudy-lms-learning-management-system' ),
			'new_subscription_plan' => esc_html__( 'New Subscription Plan', 'masterstudy-lms-learning-management-system' ),
			'subscription_plan'     => esc_html__( 'Subscription Plan', 'masterstudy-lms-learning-management-system' ),
			'delete_message'        => esc_html__( 'Do you want to delete this plan?', 'masterstudy-lms-learning-management-system' ),
		),
	)
)
?>

<div class="masterstudy-pricing-item masterstudy-pricing-subscription">
	<div class="masterstudy-pricing-item__header">
		<?php
		STM_LMS_Templates::show_lms_template(
			'components/switcher',
			array(
				'name'  => 'subscription',
				'class' => 'masterstudy-switcher-toggleable',
				'on'    => 'true' === $subscription_enabled,
			)
		);
		?>
		<span class="masterstudy-pricing-item__title">
			<?php echo esc_html__( 'Subscription', 'masterstudy-lms-learning-management-system' ); ?>
		</span>
	</div>
	<div
		class="masterstudy-pricing-item__content <?php echo esc_attr( ! empty( $plans ) ? 'masterstudy-pricing-item__content_open' : '' ); ?>">
		<div class="masterstudy-pricing-item__subscription-plans-container">
			<div class="masterstudy-pricing-item__subscription-plans">
				<?php if ( ! empty( $plans ) ) : ?>
					<?php foreach ( $plans as $plan ) : ?>
					<div
						class="masterstudy-pricing-item__subscription-plan"
						data-name="<?php echo esc_attr( $plan['name'] ); ?>"
						data-id="<?php echo esc_attr( $plan['id'] ); ?>"
						data-price="<?php echo esc_attr( $plan['price'] ); ?>"
						data-is_featured="<?php echo esc_attr( $plan['is_featured'] ); ?>"
						data-featured_text="<?php echo esc_attr( $plan['featured_text'] ); ?>"
						data-recurring_value="<?php echo esc_attr( $plan['recurring_value'] ); ?>"
						data-recurring_interval="<?php echo esc_attr( $plan['recurring_interval'] ); ?>"
					>
						<div class="masterstudy-pricing-item__subscription-plan-title">
							<?php echo esc_attr( $plan['name'] ); ?>
							<?php if ( $plan['is_featured'] ) : ?>
								<span class="masterstudy-pricing-item__subscription-plan-featured-text"><?php echo esc_attr( $plan['featured_text'] ); ?></span>
							<?php endif; ?>
						</div>
						<div class="masterstudy-pricing-item__subscription-plan-info">
							<div class="masterstudy-pricing-item__subscription-plan-cost">
								<span><?php echo esc_attr( $currency_symbol ); ?><?php echo esc_attr( $plan['price'] ); ?></span> /<?php echo esc_attr( strtolower( $interval_options[ $plan['recurring_interval'] ] ?? $plan['recurring_interval'] ) ); ?>
							</div>
							<div class="masterstudy-pricing-item__subscription-plan-btns-container">
								<button class="masterstudy-button masterstudy-button_style-secondary masterstudy-button_size-sm masterstudy-pricing-item__subscription-plan-edit-btn">
									<span class="masterstudy-button__title"><?php echo esc_html__( 'Edit', 'masterstudy-lms-learning-management-system' ); ?></span>
								</button>
								<button class="masterstudy-button masterstudy-button_style-danger masterstudy-button_size-sm masterstudy-pricing-item__subscription-plan-delete-btn">
									<span class="stmlms-trash1"></span>
								</button>
							</div>
						</div>
					</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/button',
				array(
					'class' => 'masterstudy-pricing-item__subscription-plan-add-btn',
					'style' => 'tertiary',
					'title' => esc_html__( 'Add New', 'masterstudy-lms-learning-management-system' ),
					'size'  => 'md',
				)
			);
			?>
		</div>

		<?php
		STM_LMS_Templates::show_lms_template(
			'components/bundle/create-edit-subscription-drawer',
		);
		?>
	</div>
</div>
