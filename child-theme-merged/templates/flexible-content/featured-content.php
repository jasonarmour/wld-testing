<?php
$i              = 0;
$args           = WLD_WC_BI_Query::bi_products_query( 7 );
$featured_posts = get_posts( $args );
?>
<section class="section-featured-content custom-html background-<?php echo wld_get( 'background_color' ) ?> padding-<?php echo wld_get( 'padding' ) ?> <?php echo wld_get( 'custom_class' ) ?>">
	<div class="inner">
		<?php wld_the( 'title', 'title' ); ?>
		<div class="wrapper">
			<?php while ( wld_loop( $featured_posts, ) ) : ?>
				<?php get_template_part( 'templates/template-part/home', 'product-query', array( 'i' => ++ $i ) ); ?>
			<?php endwhile; ?>
		</div>
		<?php wld_the( 'btn', 'btn' ); ?>
	</div>
</section>


