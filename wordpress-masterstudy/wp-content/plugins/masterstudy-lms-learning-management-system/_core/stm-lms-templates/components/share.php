<?php
/**
 * @var $url
 * @var $label
 */

wp_enqueue_style( 'masterstudy-share' );
wp_enqueue_script( 'masterstudy-share' );
wp_localize_script(
	'masterstudy-share',
	'share_data',
	array(
		'copy_text' => __( 'Copied to clipboard!', 'masterstudy-lms-learning-management-system' ),
	)
);

$label = isset( $label ) ? $label : __( 'Share', 'masterstudy-lms-learning-management-system' );
?>

<div class="masterstudy-share">
	<span class="masterstudy-share__button">
		<?php echo esc_html( $label ); ?>
	</span>
</div>
<div class="masterstudy-share-modal" style="display:none">
	<div class="masterstudy-share-modal__wrapper">
		<div class="masterstudy-share-modal__container">
			<div class="masterstudy-share-modal__header">
				<span class="masterstudy-share-modal__header-title">
					<?php echo esc_html__( 'Share link', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
				<div class="masterstudy-share-modal__close"></div>
			</div>
			<div class="masterstudy-share-modal__content">
				<div class="masterstudy-share-modal__link-wrapper">
					<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode( $url ); ?>" target="_blank" class="masterstudy-share-modal__link masterstudy-share-modal__link_facebook">
						<?php echo esc_html__( 'Facebook', 'masterstudy-lms-learning-management-system' ); ?>
					</a>
				</div>
				<div class="masterstudy-share-modal__link-wrapper">
					<a href="https://twitter.com/intent/tweet?url=<?php echo rawurlencode( $url ); ?>" target="_blank" class="masterstudy-share-modal__link masterstudy-share-modal__link_twitter">
						<?php echo esc_html__( 'Twitter', 'masterstudy-lms-learning-management-system' ); ?>
					</a>
				</div>
				<div class="masterstudy-share-modal__link-wrapper">
				<a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo rawurlencode( $url ); ?>" target="_blank" class="masterstudy-share-modal__link masterstudy-share-modal__link_linkedin">
						<?php echo esc_html__( 'Linkedin', 'masterstudy-lms-learning-management-system' ); ?>
					</a>
				</div>
				<div class="masterstudy-share-modal__link-wrapper">
					<a href="https://t.me/share/url?url=<?php echo rawurlencode( $url ); ?>&amp;text=<?php echo rawurlencode( $course->title ?? '' ); ?>" target="_blank" class="masterstudy-share-modal__link masterstudy-share-modal__link_telegram">
						<?php echo esc_html__( 'Telegram', 'masterstudy-lms-learning-management-system' ); ?>
					</a>
				</div>
				<div class="masterstudy-share-modal__link-wrapper">
					<a href="https://api.whatsapp.com/send?text=<?php echo rawurlencode( $course->title ?? '' . "\n" . $url ); ?>" target="_blank" class="masterstudy-share-modal__link masterstudy-share-modal__link_whatsapp">
						<?php echo esc_html__( 'WhatsApp', 'masterstudy-lms-learning-management-system' ); ?>
					</a>
				</div>
				<div class="masterstudy-share-modal__link-wrapper">
					<a href="#" data-url="<?php echo esc_url( $url ); ?>" class="masterstudy-share-modal__link masterstudy-share-modal__link_copy">
						<?php echo esc_html__( 'Copy link', 'masterstudy-lms-learning-management-system' ); ?>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
