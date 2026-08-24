<?php
/**
 * @var string $title
 * @var array  $items Array of [question, answer]
 * @var array  $icon_collapsed
 * @var array  $icon_opened
 */
?>
<div class="masterstudy-lms-faq">
	<div class="masterstudy-lms-faq__header">
		<h2 class="masterstudy-lms-faq__title"><?php echo esc_html( $title ); ?></h2>
	</div>
	<div class="masterstudy-lms-faq__list">
		<?php foreach ( $items as $item ) : ?>
			<?php
			$question = isset( $item['question'] ) ? $item['question'] : '';
			$answer   = isset( $item['answer'] ) ? $item['answer'] : '';
			if ( empty( $question ) ) {
				continue;
			}
			?>
			<div class="masterstudy-lms-faq__item">
				<div class="masterstudy-lms-faq__container">
					<div class="masterstudy-lms-faq__container-wrapper">
						<div class="masterstudy-lms-faq__question"><?php echo esc_html( $question ); ?></div>
						<span class="masterstudy-lms-faq__answer-toggler">
							<span class="masterstudy-lms-faq__answer-toggler-icon masterstudy-lms-faq__answer-toggler-icon_collapsed">
								<?php if ( ! empty( $icon_collapsed['value'] ) ) : ?>
									<?php \Elementor\Icons_Manager::render_icon( $icon_collapsed, array( 'aria-hidden' => 'true' ) ); ?>
								<?php else : ?>
									+
								<?php endif; ?>
							</span>
							<span class="masterstudy-lms-faq__answer-toggler-icon masterstudy-lms-faq__answer-toggler-icon_opened">
								<?php if ( ! empty( $icon_opened['value'] ) ) : ?>
									<?php \Elementor\Icons_Manager::render_icon( $icon_opened, array( 'aria-hidden' => 'true' ) ); ?>
								<?php else : ?>
									−
								<?php endif; ?>
							</span>
						</span>
					</div>
					<?php if ( ! empty( $answer ) ) : ?>
						<div class="masterstudy-lms-faq__answer">
							<div class="masterstudy-lms-faq__answer-wrapper"><?php echo wp_kses_post( wpautop( $answer ) ); ?></div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
