<?php $obj = get_sub_field( 'product' ); ?>
<li class="product <?php echo wld_get( 'type' ); ?>">
	<a href="<?php echo get_permalink( $obj->ID ); ?>">
		<?php echo get_the_post_thumbnail( $obj->ID, '767x0' ); ?>
		<?php WLD_WC_BI_Loop::display_product_title_cat_id( $obj->ID ); ?>
		<h2 class="woocommerce-loop-product__title"><?php echo get_the_title( $obj->ID ); ?></h2>
		<p>
			<?php echo 1 === get_row_index() ? get_the_excerpt( $obj->ID ) : wp_trim_words( get_the_excerpt( $obj->ID ), 14, '...' ); ?>
		</p>
	</a>
	<a href="<?php echo get_permalink( $obj->ID ); ?>"><?php wld_the( 'text' ); ?></a>
</li>
