<?php
global $post;
$j         = $args['i'];
$attr_type = get_post_meta( $post->ID, '_type', true );
?>
<li class="product
<?php
if ( 'podcasts' === $attr_type ) {
	echo 'listen';
}

if ( 'webinars' === $attr_type ) {
	echo 'watch';
}
?>
">
	<a href="<?php echo get_permalink(); ?>">
		<?php the_post_thumbnail( '767x0' ); ?>
		<?php WLD_WC_BI_Loop::display_product_title_attribute(); ?>
		<h2 class="woocommerce-loop-product__title"><?php echo get_the_title(); ?></h2>
		<p>
			<?php echo 1 === $j ? get_the_excerpt() : wp_trim_words( get_the_excerpt(), 14, '...' ); ?>
		</p>
	</a>
	<a href="<?php echo get_permalink(); ?>">
		<?php
		if ( 'podcasts' === $attr_type ) {
			esc_html_e( 'Listen to the Podcast', 'parent-theme' );
		} elseif ( 'webinars' === $attr_type ) {
			esc_html_e( 'Watch The Webinar', 'parent-theme' );
		} else {
			esc_html_e( 'Learn more', 'parent-theme' );
		}
		?>
	</a>
</li>
