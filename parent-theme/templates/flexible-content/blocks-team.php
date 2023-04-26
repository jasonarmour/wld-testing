<?php
$title = get_sub_field( 'title' );
$text  = get_sub_field( 'text' );
if ( get_sub_field( 'all' ) ) {
	$posts = get_posts(
		array(
			'post_type'      => 'team',
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
<section class="our-team-section">
	<div class="inner">
		<?php wld_the_title( $title, 'section-title' ); ?>
		<div class="text-content"><?php echo $text; ?></div>
		<?php if ( $posts ) : ?>
			<div class="wrap">
				<?php foreach ( $posts as $post ) : ?>
					<?php setup_postdata( $post ); ?>
					<div class="item">
						<div class="image">
							<?php if ( has_post_thumbnail() ) : ?>
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( '150x150' ); ?>
								</a>
							<?php endif; ?>
						</div>
						<div class="text-content">
							<?php wld_the_title( get_the_title(), 'name' ); ?>
						</div>
						<a href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read More', 'parent-theme' ); ?></a>
					</div>
				<?php endforeach; ?>
				<?php wp_reset_postdata(); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
