<?php
/**
 * @var array $args array with data about one event
 */
?>
<?php $j = $args['i']; ?>
<li class="product <?php echo 1 === $j ? 'first-product' : ''; ?> ">
	<a href="<?php echo get_the_permalink(); ?>">
		<?php
		if ( 1 === $j ) {
			the_post_thumbnail( '670x0' );
		} else {
			the_post_thumbnail( '0x90' );
		}
		?>
	</a>
	<div class="content">
		<?php WLD_WC_BI_Loop::display_product_title_cat(); ?>
		<?php the_title( '<h2>', '</h2>' ); ?>
		<?php if ( 1 === $j ) : ?>
			<?php the_excerpt(); ?>
		<?php endif; ?>
		<a href="<?php echo get_the_permalink(); ?>"><?php esc_html_e( 'Read more', 'parent-theme' ); ?></a>
	</div>
</li>
