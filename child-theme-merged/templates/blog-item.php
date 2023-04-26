<?php
global $wp_query;

$is_first_post = $wp_query->is_main_query() && 0 === $wp_query->current_post
?>
<?php if (is_category('blog')) : ?>
	<div class="item category-blog">
<?php elseif (is_category('press-release')) : ?>
	<div class="item category-press-release">
<?php elseif (is_category('recent-news')) : ?>
	<div class="item section-blog category-recent-news">
<?php elseif (is_category('upcoming-events')) : ?>
	<div class="item section-blog category-upcoming-events">
<?php endif; ?>

	<div class="img">
		<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( $is_first_post ? '767x431' : '370x208' ); ?></a>
	</div>
	<div class="text">
		
		<div class="posted">
			
			<?php
			printf( // translators: %s - posted date
				esc_html__( '%s', 'parent-theme' ),
				get_the_date( 'F j, Y' )
			);
			?>
		</div>
		<h2 class="title"><?php the_title(); ?></h2>
		
		<div class="more">
			<?php 
				$format = get_post_format() ? : 'standard';
				if ( $format == 'link' ) : {
					  echo '<a href="'. get_the_excerpt(). '" class="link">Learn More</a>';
				  }
				endif;
				if ( $format != 'link' ) : { ?>
			<?php the_excerpt(); ?>
					  <a href="<?php the_permalink(); ?>" class="link"><?php esc_html_e( 'Learn More', 'parent-theme' ); ?></a>
				 <?php };
				endif;
			?>
			
			
		</div>
	</div>
</div>
