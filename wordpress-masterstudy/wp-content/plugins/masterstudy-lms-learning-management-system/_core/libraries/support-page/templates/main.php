<?php
$data = STM_Support_Page::get_data( $textdomain );
?>
<div class="help-center">
	<header class="help-center__header">
	<?php
	foreach ( $data['header'] as $item ) :
		if ( ! empty( $item['title'] ) ) :
			?>
		<h1><?php echo esc_html( $item['title'] ); ?></h1>
			<?php
		endif;
		if ( ! empty( $item['description'] ) ) :
			?>
		<p><?php echo esc_html( $item['description'] ); ?></p>
			<?php
		endif;
	endforeach;
	?>
	</header>
	<section class="help-grid">
	<?php
	foreach ( $data['help_items'] as $item ) :
		$pro       = $item['has-pro'] ?? '';
		$pro_plus  = $item['has-pro-plus'] ?? '';
		$title_pro = $item['title_pro'] ?? '';
		if ( empty( $item ) ||
			! is_array( $item ) || ( empty( $item['has-pro-notice'] ) && ( isset( $pro_plus ) && true === $pro_plus ) )
		) {
			continue;
		}
		?>
		<div class="help-item <?php echo ! empty( $item['class'] ) ? esc_attr( $item['class'] ) : ''; ?>">
			<?php if ( ! empty( $item['icon'] ) ) : ?>
			<div class="icon <?php echo esc_attr( $item['icon'] ); ?>"></div>
				<?php
			endif;
			if ( ! empty( $item['title'] ) ) :
				?>
			<h3>
				<?php
				if ( $pro && ! empty( $title_pro ) ) {
					echo esc_html( $title_pro );
				} else {
					echo esc_html( $item['title'] );
				}
				?>
			</h3>
				<?php
			endif;
			if ( ! empty( $item['description'] ) ) :
				?>
			<p><?php echo wp_kses_post( $item['description'] ); ?></p>
				<?php
				if ( ! empty( $item['list'] ) && is_array( $item['list'] ) ) :
					?>
				<ul>
					<?php foreach ( $item['list'] as $list_item ) : ?>
					<li><?php echo esc_html( $list_item ); ?></li>
					<?php endforeach; ?>
				</ul>
					<?php
				endif;
			endif;
			if (
				! empty( $item['has-pro-notice'] ) &&
				! $pro_plus &&
				! $pro
			) {
				if ( ! empty( $item['has-pro-notice'] ) ) :
					?>
				<div class="support-page-button-pro-notice">
					<span class="support-page-icon-lock"></span>
					<?php echo esc_html( $item['has-pro-notice'] ); ?>
				</div>
					<?php
				endif;
			} else {
				?>
			<div class="support-button-group">
				<?php
				if ( ! empty( $item['buttons'] ) && is_array( $item['buttons'] ) ) {
					foreach ( $item['buttons'] as $button ) :
						$label_pro = $button['label_pro'] ?? '';
						if ( ! empty( $button['notice'] ) ) :
							?>
							<div class="support-page-button-notice"><?php echo esc_html( $button['notice'] ); ?></div>
							<?php
						endif;
						if ( ! empty( $button['href'] ) ) :
							?>
						<a href="<?php echo esc_url( $button['href'] ); ?>" class="support-page-button support-page-button-<?php echo esc_attr( $button['type'] ); ?> <?php echo esc_attr( ! empty( $button['icon'] ) ? 'has-icon' : '' ); ?>" target="_blank">
							<?php
							if ( ! empty( $button['icon'] ) ) :
								?>
							<span class="button-icon <?php echo esc_attr( $button['icon'] ); ?>"></span>
								<?php
							endif;
							if ( $pro && $label_pro ) {
								echo esc_html( $label_pro );
							} else {
								echo esc_html( $button['label'] );
							}
							?>
						</a>
							<?php
						endif;
					endforeach;
				}
				?>
			</div>
				<?php
			}
			if ( ! empty( $item['image'] ) ) :
				?>
			<div class="image"><img src="<?php echo esc_url( $item['image'] ); ?>" width="<?php echo esc_attr( $item['image-width'] ); ?>" alt="<?php echo esc_attr( $item['image-height'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>"></div>
			<?php endif; ?>
		</div>
		<?php
	endforeach;
	foreach ( $data['review'] as $item ) :
		?>
	<div class="help-item <?php echo ! empty( $item['class'] ) ? esc_attr( $item['class'] ) : ''; ?>">
		<div id="feedback-modal" class="feedback-modal <?php echo esc_attr( $item['has_review'] ? 'review-sended' : '' ); ?>">
			<div class="feedback-modal-content">
				<?php
				if ( ! empty( $item['title'] ) ) :
					?>
				<h2><?php echo esc_html( $item['title'] ); ?></h2>
					<?php
				endif;
				if ( ! empty( $item['description'] ) ) :
					?>
				<p><?php echo esc_html( $item['description'] ); ?></p>
					<?php
				endif;
				?>
				<div class="feedback-rating-stars">
					<ul id="feedback-stars" class="feedback-stars">
						<?php
						$ratings = array(
							1 => 'Poor',
							2 => 'Bad',
							3 => 'Fair',
							4 => 'Good',
							5 => 'Excellent!',
						);

						foreach ( $ratings as $value => $rating ) :
							?>
							<li class="star" title="<?php echo esc_attr( $rating ); ?>" data-value="<?php echo esc_attr( $value ); ?>">
								<i class="support-page-icon-star-solid feedback-star"></i>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<p class="feedback-review-text" style="display: none;"></p>
				<div class="feedback-extra">
					<textarea id="feedback-review" rows="5" placeholder="Describe your review"></textarea>
				</div>
				<?php
				if ( ! empty( $item['buttons'] ) && is_array( $item['buttons'] ) ) {
					foreach ( $item['buttons'] as $button ) :
						if ( ! empty( $button['href'] ) ) :
							?>
						<a href="<?php echo esc_url( $button['href'] ); ?>" class="support-page-button feedback-submit support-page-button-<?php echo esc_attr( $button['type'] ); ?>" target="_blank" style="display: none;">
							<span><?php echo esc_html( $button['label'] ); ?></span><span><?php echo esc_html__( 'Submit Review', 'support-page' ); ?></span>
						</a>
							<?php
						endif;
					endforeach;
				}
				?>
				<span class="feedback-extra-notice"><?php echo esc_html__( 'Indicate how many stars you want to give', 'support-page' ); ?></span>
			</div>
			<div class="feedback-modal-content-sent" style="display: none;">
				<span class="support-page-icon-check"></span>
				<h3><?php echo esc_html__( 'Thanks for review!', 'support-page' ); ?></h3>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
	</section>
	<section class="news-subscription">
	<?php
	foreach ( $data['news'] as $item ) :
		?>
		<div class="help-item <?php echo ! empty( $item['class'] ) ? esc_attr( $item['class'] ) : ''; ?>">
			<div class="news-header">
				<div class="news-header-title">
				<?php if ( $item['category_id'] ) : ?>
					<h3><?php echo esc_html( $item['title'] ); ?></h3>
					<?php
				endif;
				if ( $item['description'] ) :
					?>
					<p><?php echo esc_html( $item['description'] ); ?></p>
				<?php endif; ?>
				</div>
				<div class="news-header-button">
					<?php
					if ( ! empty( $item['buttons'] ) && is_array( $item['buttons'] ) ) :
						foreach ( $item['buttons'] as $button ) :
							if ( ! empty( $button['href'] ) ) :
								?>
							<a href="<?php echo esc_url( $button['href'] ); ?>" class="support-page-button support-page-button-<?php echo esc_attr( $button['type'] ); ?>" target="_blank">
								<?php echo esc_html( $button['label'] ); ?>
							</a>
								<?php
							endif;
						endforeach;
					endif;
					?>
				</div>
			</div>
			<?php
			$response = wp_remote_get( 'https://stylemixthemes.com/wp/wp-json/wp/v2/posts?categories=' . $item['category_id'] . '&per_page=' . $item['per_page'] . '&_embed' );

			if ( is_wp_error( $response ) ) {
				return;
			}

			$raw_body   = wp_remote_retrieve_body( $response );
			$json_start = strpos( $raw_body, '[' );

			if ( false === $json_start ) {
				return;
			}

			$json = substr( $raw_body, $json_start );
			$body = json_decode( $json, true );

			if ( is_array( $body ) && json_last_error() === JSON_ERROR_NONE ) {
				foreach ( $body as $news ) :
					$news_title = isset( $news['title']['rendered'] ) ? $news['title']['rendered'] : '';
					$news_link  = isset( $news['link'] ) ? $news['link'] : '#';
					$image      = isset( $news['_embedded']['wp:featuredmedia'][0]['source_url'] ) ? $news['_embedded']['wp:featuredmedia'][0]['source_url'] : '';
					?>
				<div class="news-preview">
					<?php if ( $image ) : ?>
					<div class="news-preview__image">
						<a href="<?php echo esc_url( $news_link ); ?>" target="_blank"><img src="<?php echo esc_url( $image ); ?>" width="146" height="80" alt="<?php echo esc_attr( $news_title ); ?>" /></a>
					</div>
						<?php
					endif;
					if ( $news_title ) :
						?>
					<div class="news-preview__content">
						<h3><a href="<?php echo esc_url( $news_link ); ?>" target="_blank"><?php echo esc_html( $news_title ); ?></a></h3>
					</div>
					<?php endif; ?>
				</div>
					<?php
				endforeach;
			}
			?>
		</div>
		<?php
	endforeach;
	foreach ( $data['newsletter'] as $item ) :
		?>
		<div class="help-item <?php echo ! empty( $item['class'] ) ? esc_attr( $item['class'] ) : ''; ?>">
			<?php
			if ( isset( $_GET['subscribed'] ) && 'success' === $_GET['subscribed'] ) :
				if ( ! empty( $item['newsletter-icon'] ) ) :
					?>
				<div class="icon <?php echo esc_attr( $item['newsletter-icon'] ); ?>"></div>
					<?php
				endif;
				if ( ! empty( $item['newsletter-title'] ) ) :
					?>
				<h3><?php echo wp_kses_post( $item['newsletter-title'] ); ?></h3>
					<?php
				endif;
				if ( ! empty( $item['newsletter-message'] ) ) :
					?>
				<p><?php echo wp_kses_post( $item['newsletter-message'] ); ?></p>
					<?php
				endif;
				else :
					if ( ! empty( $item['icon'] ) ) :
						?>
					<div class="icon <?php echo esc_attr( $item['icon'] ); ?>"></div>
						<?php
					endif;
					if ( ! empty( $item['title'] ) ) :
						?>
					<h3><?php echo wp_kses_post( $item['title'] ); ?></h3>
						<?php
					endif;
					?>
					<form method="post">
						<?php wp_nonce_field( 'subscribe_to_mailchimp', 'subscribe_nonce' ); ?>
						<input type="email" name="subscriber_email" placeholder="<?php echo esc_attr( $item['placeholder'] ); ?>" required>
						<label><input type="checkbox" name="agree_terms" required> <?php echo esc_html( $item['label'] ); ?></label>
						<button type="submit" name="subscribe_to_mailchimp" class="support-page-button support-page-button-primary">Subscribe</button>
					</form>
					<?php
			endif;
				?>
		</div>
			<?php
	endforeach;
	?>
	</section>
</div>
