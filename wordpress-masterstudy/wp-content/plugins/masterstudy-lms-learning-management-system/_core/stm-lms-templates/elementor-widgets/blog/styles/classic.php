<section id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="masterstudy-post-template-main">
		<?php if ( has_post_thumbnail() ) : ?>
		<div class="masterstudy-post-thumbnail">
			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
				<?php the_post_thumbnail( 'img-480-380' ); ?>
			</a>
		</div>
		<?php endif; ?>
		<div class="masterstudy-post-template-main-info 
			<?php
			if ( ! has_post_thumbnail() ) {
				echo 'masterstudy-post-thumbnail-none';
			}
			?>
		">
			<div class="masterstudy-post-date">
				<?php echo esc_html( strtoupper( get_the_date( 'M j' ) ) ); ?>
				<span><?php echo esc_html( strtoupper( get_the_date( 'Y' ) ) ); ?></span>
			</div>
			<div class="masterstudy-post-title">
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a>
			</div>
			<div class="masterstudy-post-category-list">
				<?php foreach ( get_the_category() as $category ) : ?>
				<a href="<?php echo esc_url( get_tag_link( $category ) ); ?>" title="<?php echo esc_attr( $category->name ); ?>">
					<?php echo esc_attr( $category->name ); ?>
				</a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
