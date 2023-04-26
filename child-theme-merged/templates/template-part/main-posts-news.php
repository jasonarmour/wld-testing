<div class="item">
	<?php the_post_thumbnail( '400x400' ); ?>
	<div class="content-wrapper">
		<?php the_title( '<h5>', '</h5>' ); ?>
		<a href="<?php the_permalink(); ?>"><?php esc_html_e( 'learn more', 'parent-theme' ); ?></a>
	</div>
</div>
