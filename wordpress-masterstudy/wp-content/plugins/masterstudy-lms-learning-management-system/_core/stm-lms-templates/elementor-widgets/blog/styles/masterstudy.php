<section id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="masterstudy-post-template-main">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="masterstudy-post-thumbnail">
				<a href="<?php the_permalink(); ?>" title="<?php esc_attr_e( 'View post details', 'masterstudy-lms-learning-management-system' ); ?>">
					<?php the_post_thumbnail( 'large', array( 'class' => 'img-responsive' ) ); ?>
				</a>
			</div>
		<?php endif; ?>
		<div class="masterstudy-post-template-main-info <?php echo ! has_post_thumbnail() ? 'masterstudy-post-thumbnail-none' : ''; ?>">
			<div class="masterstudy-post-content-row">
				<div class="masterstudy-post-date">
					<div class="date-d"><?php echo esc_html( get_the_date( 'd' ) ); ?></div>
					<div class="date-m"><?php echo esc_html( get_the_date( 'M' ) ); ?></div>
					<?php if ( get_comments_number( get_the_ID() ) ) : ?>
						<div class="masterstudy-post-comments">
							<span><?php echo esc_html( get_comments_number( get_the_ID() ) ); ?></span><i class="fa fa-comment-o" aria-hidden="true"></i>
						</div>
					<?php endif; ?>
				</div>

				<div class="masterstudy-post-content">
					<div class="masterstudy-post-title h3">
						<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
							<?php echo get_the_title() ? esc_html( get_the_title() ) : esc_html__( 'No title', 'masterstudy-lms-learning-management-system' ); ?>
						</a>
					</div>

					<?php if ( get_the_excerpt() ) : ?>
						<div class="masterstudy-post-excerpt"><?php the_excerpt(); ?></div>
					<?php endif; ?>

					<div class="masterstudy-post-short-separator"></div>

					<?php $categories = get_the_category(); ?>
					<?php if ( ! empty( $categories ) ) : ?>
						<div class="masterstudy-post-category-list">
							<span><?php esc_html_e( 'Posted in:', 'masterstudy-lms-learning-management-system' ); ?></span>
							<?php foreach ( $categories as $category ) : ?>
								<a href="<?php echo esc_url( get_category_link( $category ) ); ?>" title="<?php echo esc_attr( $category->name ); ?>"><?php echo esc_html( $category->name ); ?></a><span class="masterstudy-post-divider">,</span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php $tags = get_the_tags(); ?>
					<?php if ( $tags ) : ?>
						<div class="masterstudy-post-tags">
							<span><?php esc_html_e( 'Tags:', 'masterstudy-lms-learning-management-system' ); ?></span>
							<?php foreach ( $tags as $tag ) : ?>
								<a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" title="<?php echo esc_attr( $tag->name ); ?>"><?php echo esc_html( $tag->name ); ?></a><span class="masterstudy-post-divider">,</span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
