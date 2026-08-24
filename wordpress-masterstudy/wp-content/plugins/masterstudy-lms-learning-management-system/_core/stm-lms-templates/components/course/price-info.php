<?php
/**
 * @var object $course
 */

use MasterStudy\Lms\Enums\PricingMode;
?>
<?php if ( PricingMode::FREE === $course->pricing_mode ) : ?>
	<div class="masterstudy-single-course-price-info">
		<?php echo esc_html( $course->free_price_info ); ?>
	</div>
<?php endif; ?>
