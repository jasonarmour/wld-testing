<?php
global $wp_query;

$is_first_post = $wp_query->is_main_query() && 0 === $wp_query->current_post
?>

<div class="item">
	<div class="img">
		<?php the_post_thumbnail( $is_first_post ? '390x400' : '262x258' ); ?>
	</div>
	<div class="text">
		<?php if ( has_category() ) : ?>
			<div class="categories">
				<?php the_category( ' ' ); ?>
			</div>
		<?php endif; ?>
		<div class="posted">
			<?php
			printf( // translators: %s - posted date
				esc_html__( 'Posted - %s', 'parent-theme' ),
				get_the_date( 'F j, Y' )
			);
			?>
		</div>
		<h2 class="title"><?php the_title(); ?></h2>
		<?php the_excerpt(); ?>
		<div class="more">
			<a href="<?php the_permalink(); ?>"><?php esc_html_e( 'Learn More', 'parent-theme' ); ?></a>
		</div>
	</div>
</div>
