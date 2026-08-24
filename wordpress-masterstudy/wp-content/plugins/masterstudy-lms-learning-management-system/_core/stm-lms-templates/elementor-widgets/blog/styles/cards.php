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
			<div class="masterstudy-post-title">
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a>
			</div>
			<div class="masterstudy-post-category-list">
				<?php
				$categories     = get_the_category();
				$category_links = array();
				foreach ( $categories as $category ) {
					$category_links[] = '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" title="' . esc_attr( $category->name ) . '">' . esc_html( $category->name ) . '</a>';
				}
				echo wp_kses_post( implode( ', ', $category_links ) );
				?>
			</div>
		</div>
	</div>
</section>
