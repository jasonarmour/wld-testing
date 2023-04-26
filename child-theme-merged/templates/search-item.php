<a href="<?php the_permalink(); ?>" class="item">
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="img">
			<?php the_post_thumbnail( '16x9' ); ?>
		</div>
	<?php endif; ?>
	<div class="text">
		<?php the_title( '<h2 class="title">', '</h2>' ); ?>
		<p><?php wld_the_excerpt( 137 ); ?></p>
		<div class="more">
			<span>
				<?php esc_html_e( 'Learn More', 'parent-theme' ); ?>
			</span>
		</div>
	</div>
</a>
