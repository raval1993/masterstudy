<?php
$paginate = paginate_links(
	array(
		'type'      => 'array',
		'base'      => add_query_arg( 'page', '%#%' ),
		'format'    => '?page=%#%',
		'current'   => $pagination_data['current_page'],
		'total'     => $pagination_data['total_pages'],
		'mid_size'  => 2,
		'end_size'  => 1,
		'prev_text' => esc_html__( 'Prev', 'masterstudy-lms-learning-management-system' ),
		'next_text' => esc_html__( 'Next', 'masterstudy-lms-learning-management-system' ),
	)
);
if ( is_array( $paginate ) ) {
	$blog_style = ! empty( $pagination_data['blog_style'] ) ? $pagination_data['blog_style'] : 'cards';
	?>
	<ul class="masterstudy-post-template__pagination_list" data-per-page="<?php echo esc_attr( $pagination_data['posts_per_page'] ); ?>" data-current-page="<?php echo esc_attr( $pagination_data['current_page'] ); ?>" data-blog-style="<?php echo esc_attr( $blog_style ); ?>">
	<?php foreach ( $paginate as $item ) { ?>
		<li class="masterstudy-post-template__pagination_list_item">
			<?php echo wp_kses_post( $item ); ?>
		</li>
	<?php } ?>
	</ul>
	<?php
}
