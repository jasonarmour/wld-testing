<?php
global $wp_query;

$per_page = $wp_query->get( 'posts_per_page' );
$paged    = $wp_query->get( 'paged' );
$base     = $paged ? $per_page * ( $paged - 1 ) : 0;
$showing  = sprintf( // translators: %1$d min number showing posts, %2$d max number showing posts, %3$d all count found posts
	esc_html__( 'Showing %1$d to %2$d of %3$d results', 'parent-theme' ),
	$base + 1,
	$base + $wp_query->post_count,
	$wp_query->found_posts
);
?>
<section class="section-search-results">
	<div class="inner">
		<div class="results">
			<h2>
				<?php esc_html_e( 'Search Results for: ', 'parent-theme' ); ?>
				<span><?php echo esc_html( get_search_query( false ) ); ?></span>
			</h2>
			<p><?php echo esc_html( $showing ); ?></p>
		</div>
		<div class="wrapper">
			<div class="content">
				<?php if ( have_posts() ) : ?>
					<?php while ( have_posts() ) : ?>
						<?php the_post(); ?>
						<?php get_template_part( 'templates/search-item' ); ?>
					<?php endwhile; ?>
				<?php else : ?>
					<p><?php esc_html_e( 'Nothing found', 'parent-theme' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php wld_the_pagination(); ?>
	</div>
</section>
