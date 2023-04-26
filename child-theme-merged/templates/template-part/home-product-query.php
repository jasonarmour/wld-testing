<?php $j = $args['i']; ?>
<a href="<?php the_permalink(); ?>" class="link-content item <?php echo 1 === $j ? 'first' : ''; ?> ">
	<div class="image">
		<?php 1 == $j ? the_post_thumbnail( '992x0' ) : the_post_thumbnail( '767x0' ); ?>
	</div>
	<div class="content">
		<?php WLD_WC_BI_Loop::display_product_title_attribute(); ?>
		<h3><?php the_title(); ?></h3>
		<?php the_excerpt(); ?>
		<div class="link"><?php esc_html_e( 'Read more', 'parent-theme' ); ?></div>
	</div>
</a>
