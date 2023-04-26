<?php
$title = get_sub_field( 'title' );
$text  = get_sub_field( 'text' );
if ( get_sub_field( 'all' ) ) {
	$posts = get_posts(
		array(
			'post_type'      => 'testimonial',
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
<section class="testimonials-section">
	<div class="inner">
		<div class="wrap">
			<div class="left">
				<?php wld_the_title( $title, 'section-title' ); ?>
				<?php echo $text; ?>
			</div>
			<?php if ( $posts ) : ?>
				<div class="right">
					<div class="testimonials-wrap">
						<?php foreach ( $posts as $post ) : ?>
							<?php
							setup_postdata( $post );
							$info  = get_field( 'info' );
							$title = get_the_title();
							if ( $info ) {
								$title .= ' - <em>' . $info . '</em>';
							}
							?>
							<div class="item">
								<div class="text-content"><?php the_content(); ?></div>
								<?php wld_the_title( $title, 'author' ); ?>
							</div>
						<?php endforeach; ?>
						<?php wp_reset_postdata(); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
