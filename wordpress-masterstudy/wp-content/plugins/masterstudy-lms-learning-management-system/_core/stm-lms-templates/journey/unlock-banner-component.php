<?php
/**
 * @var $field
 * @var $field_name
 * @var $section_name
 */

use MasterStudy\Lms\Plugin\Addons;

$is_enable   = $field_data['is_enable'] ?? false;
$is_pro_plus = $field_data['is_pro_plus'] ?? false;
$is_pro      = STM_LMS_Helpers::is_pro();

if ( $is_pro && ! $is_enable && ! $is_pro_plus ) {
	return;
}

$version = ( WP_DEBUG ) ? time() : STM_LMS_VERSION;
wp_enqueue_style( 'stm_lms_unlock_addons', STM_LMS_URL . 'assets/css/stm_lms_unlock_addons.css', null, $version );
wp_enqueue_style( 'stm_lms_unlock_nuxy_banners', STM_LMS_URL . 'assets/css/stm_lms_unlock_nuxy_banners.css', null, $version );
wp_enqueue_script( 'masterstudy-analytics-preview-page' );

$label         = $field_data['label'] ?? '';
$img           = $field_data['img'] ?? '';
$description   = $field_data['desc'] ?? '';
$search_addon  = $field_data['search'] ?? '';
$utm_url       = $field_data['utm_url'] ?? '';
$slug          = str_replace( ' ', '-', mb_strtolower( $label ) );
$video_url     = '';

foreach ( Addons::list() as $addon_data ) {
	if ( mb_strtolower( (string) ( $addon_data['name'] ?? '' ) ) === mb_strtolower( (string) $label ) ) {
		$video_url = $addon_data['video_url'] ?? '';
		break;
	}
}

if ( empty( $video_url ) ) {
	switch ( trim( html_entity_decode( (string) $label, ENT_QUOTES, 'UTF-8' ) ) ) {
		case 'Reports & Analytics':
			$video_url = 'https://www.youtube.com/embed/7NqPcDGVOZM?rel=0&autoplay=1';
			break;
		case 'Audio Lesson Addon':
		case 'Audio Lesson':
			$video_url = 'https://www.youtube.com/embed/sNmXeINHDFI?rel=0&autoplay=1';
			break;
		case 'Woocommerce Checkout':
			$video_url = 'https://www.youtube.com/embed/Vg5Ex82FEdQ?rel=0&autoplay=1';
			break;
		case 'Upcoming Course Status Addon':
			$video_url = 'https://www.youtube.com/embed/wlZ3BnmpHkk?rel=0&autoplay=1';
			break;
		case 'Payouts':
			$video_url = 'https://www.youtube.com/embed/98LKgphFSCY?start=1023&rel=0&autoplay=1';
			break;
		case 'Social Login':
			$video_url = 'https://www.youtube.com/embed/NCE3ynXKzGI?rel=0&autoplay=1';
			break;
		case 'Grades':
			$video_url = 'https://www.youtube.com/embed/OfDtaP-u6SE?rel=0&autoplay=1';
			break;
	}
}

$redirect_link = admin_url( 'admin.php?page=' . ( $is_enable ? "stm-addons&search={$search_addon}" : "stm-lms-go-pro&source=button-{$slug}-settings" ) );
$redirect_link = ! $is_enable && $is_pro_plus && $utm_url && $is_pro ? $utm_url : $redirect_link;

$pro_plus_icon_url = STM_LMS_URL . 'assets/img/pro-features/unlock-pro-logo.svg';
$text              = esc_html__( 'Upgrade to PRO', 'masterstudy-lms-learning-management-system' );
if ( STM_LMS_Helpers::is_pro() && ! STM_LMS_Helpers::is_pro_plus() ) {
	$pro_plus_icon_url = STM_LMS_URL . 'assets/img/pro-features/pro_plus.svg';
	$text              = esc_html__( 'Upgrade to PRO PLUS', 'masterstudy-lms-learning-management-system' );
	$redirect_link     = $utm_url;
}

$link_text = $is_enable ? esc_html__( 'Enable addon', 'masterstudy-lms-learning-management-system' ) : $text;
?>
<?php if ( ! empty( $video_url ) ) : ?>
	<div class="masterstudy-analytics-preview-page__popup">
		<div class="masterstudy-analytics-preview-page__popup-video-wrapper">
			<div class="masterstudy-analytics-preview-page__popup-video">
				<iframe id="masterstudy-analytics-preview-video" frameborder="0" allowfullscreen="" data-original-src="<?php echo esc_url( $video_url ); ?>"></iframe>
			</div>
		</div>
	</div>
<?php endif; ?>
<div
	class="stm-lms-unlock-pro-banner<?php echo esc_attr( $is_enable || ! $is_enable && $is_pro_plus ? ' addon_disabled' : '' ); ?>">
	<div class="stm-lms-unlock-banner-wrapper">
		<?php
		if ( ! empty( $field_data['hint'] ) && 'slider' === $field_data['hint'] ) {
			$image_sources = array(
				STM_LMS_URL . 'assets/img/course/course-slider/classic.png',
				STM_LMS_URL . 'assets/img/course/course-slider/industrial.png',
				STM_LMS_URL . 'assets/img/course/course-slider/modern-curriculum.png',
				STM_LMS_URL . 'assets/img/course/course-slider/timeless.png',
				STM_LMS_URL . 'assets/img/course/course-slider/minimalistic.png',
				STM_LMS_URL . 'assets/img/course/course-slider/sleek-sidebar.png',
				STM_LMS_URL . 'assets/img/course/course-slider/dynamic.png',
				STM_LMS_URL . 'assets/img/course/course-slider/dynamic-sidebar.png',
			);
			?>
			<div class="unlock-pro-banner-slider">
				<div class="unlock-slider-container">
					<div class="unlock-slider-slide-window">
						<div class="unlock-slider-slide-holder" id="unlock-slider-slide-holder">
							<?php foreach ( $image_sources as $image_src ) : ?>
								<div class="unlock-slider-slide">
									<img src="<?php echo esc_attr( $image_src ); ?>">
								</div>
							<?php endforeach; ?>
						</div>
						<div class="unlock-slider-slide-nav" id="unlock-slider-slide-nav">
						</div>
					</div>
				</div>
			</div>
		<?php } else { ?>
			<div class="unlock-banner-image">
				<img src="<?php echo esc_url( $img ); ?>">
				<?php if ( ! empty( $video_url ) ) : ?>
					<a href="#" class="play-btn" data-id="analytics-watch-video" aria-label="">
						<span class="play-btn__icon" aria-hidden="true"></span>
					</a>
				<?php endif; ?>
			</div>
			<?php
		}
		?>
		<div class="unlock-wrapper-content">
			<h2>
				<?php
				if ( isset( $field_data['hint'] ) && 'slider' !== $field_data['hint'] ) {
					echo esc_html( $field_data['hint'] );
				} else {
					echo $is_enable ? esc_html__( 'Enable', 'masterstudy-lms-learning-management-system' ) : esc_html__( 'Unlock', 'masterstudy-lms-learning-management-system' );
				}
				?>
				<span class="unlock-addon-name">
					<?php
					if ( isset( $field_data['hint'] ) && ( 'Enable' === $field_data['hint'] ) ) {
						echo '<br/>';
					}
					echo esc_html( $label );
					?>
				</span>
				<?php
				if ( isset( $field_data['hint'] ) && ( 'Enable' === $field_data['hint'] || 'Automate' === $field_data['hint'] ) ) {
					echo '<br/>';
				}
				echo esc_html__( 'with', 'masterstudy-lms-learning-management-system' );
				?>
				<div class="unlock-pro-logo-wrapper">
					<span
						class="unlock-pro-logo"><?php echo esc_html__( 'MasterStudy', 'masterstudy-lms-learning-management-system' ); ?></span>
					<img src="<?php echo esc_url( $pro_plus_icon_url ); ?>">
				</div>
			</h2>
			<p><?php echo esc_html( $description ); ?> </p>
			<div class="unlock-pro-banner-footer">
				<div class="unlock-addons-buttons">
					<a href="<?php echo esc_url( $redirect_link ); ?>" target="_blank" class="primary button btn">
						<?php echo esc_html( $link_text ); ?>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
