<?php
$i = 0;

$args           = WLD_WC_BI_Query::bi_products_query( 4 );
$trending_posts = get_posts( $args );

?>
<section class="section-trending">
	<div class="inner">
		<div class="wrapper">
			<?php wld_the( 'title', 'title' ); ?>
			<?php while ( wld_loop( $trending_posts, '<ul class="items">' ) ) : ?>
				<?php get_template_part( 'templates/template-part/main', 'trending', array( 'i' => ++ $i ) ); ?>
			<?php endwhile; ?>
			<?php wld_the( 'button', 'btn' ); ?>
		</div>
	</div>
</section>



