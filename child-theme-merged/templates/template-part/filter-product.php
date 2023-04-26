<?php
global $product;
  $product_id = $product->get_id();
$product_categories = get_the_terms( $product_id, 'product_cat' );
$category_slugs = '';
foreach ( $product_categories as $category ) {
    $category_slugs .= $category->slug . ' ';
}
?>

<a href="<?php the_permalink(); ?>" class="item link-content <?php echo $category_slugs;?>">
	<div class="image">
		<?php the_post_thumbnail( '16x9' ); ?>
	</div>
	<div class="content">
		<div class="category "><?php WLD_WC_BI_Loop::display_product_title_cat_id( get_the_ID() ); ?></div>
		<h2 class="title"><?php the_title(); ?></h2>
		<p><?php echo force_balance_tags( html_entity_decode( wp_trim_words( htmlentities( get_the_content() ), 14, '...' ) ) ); ?></p>
		<div class="link"><?php esc_html_e( 'read more', 'parent-theme' ); ?></div>
	</div>
</a>
