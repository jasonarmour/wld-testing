<?php
$title      = get_sub_field( 'title' );
$categories = get_categories(
	array(
		'type'       => 'portfolio',
		'orderby'    => 'menu_order',
		'order'      => 'asc',
		'taxonomy'   => 'portfolio_category',
		'pad_counts' => false,
	)
);
if ( get_sub_field( 'all' ) ) {
	$posts = get_posts(
		array(
			'post_type'      => 'portfolio',
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
			'orderby'        => 'menu_order',
			'order'          => 'asc',
		)
	);
} else {
	$posts = get_sub_field( 'posts' );
}
?>
<section class="portfolio-section">
	<div class="inner">
		<?php wld_the_title( $title, 'section-title' ); ?>
		<?php if ( $categories ) : ?>
			<ul class="portfolio-nav">
				<li class="active" data-filter="all">All</li>
				<?php foreach ( $categories as $category ) : ?>
					<li data-filter="<?php echo $category->slug; ?>"><?php echo $category->name; ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<?php if ( $posts ) : ?>
			<div class="wrap">
				<?php foreach ( $posts as $post ) : ?>
					<?php
					setup_postdata( $post );
					$class       = '';
					$_categories = wp_get_object_terms( $post->ID, 'portfolio_category' );
					foreach ( $_categories as $category ) {
						$class .= ' ' . $category->slug;
					}
					?>
					<a href="<?php the_permalink(); ?>" class="item all <?php echo $class; ?>">
						<?php the_post_thumbnail( '300x300' ); ?>
					</a>
				<?php endforeach; ?>
				<?php wp_reset_postdata(); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
