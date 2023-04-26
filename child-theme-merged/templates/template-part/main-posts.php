<div class="item">
	<?php the_post_thumbnail( '400x400' ); ?>
	<div class="content-wrapper">
		<?php the_title( '<h5>', '</h5>' ); ?>

		<?php echo force_balance_tags( html_entity_decode( wp_trim_words( htmlentities( get_the_content() ), 8, '...' ) ) ); ?>

		<a href="<?php the_permalink(); ?>"><?php esc_html_e( 'learn more', 'parent-theme' ); ?></a>
	</div>
</div>
