<?php
$i              = 0;
$args           = WLD_WC_BI_Query::bi_products_query( 3 );
$featured_posts = get_posts( $args );

?>
<section class="section-featured background-<?php echo wld_get( 'background_color' ) ?> padding-<?php echo wld_get( 'padding' ) ?> <?php echo wld_get( 'custom_class' ) ?>">
	<div class="inner">
		<div class="wrapper">
			<div class="left">
				<?php wld_the( 'title', 'title' ); ?>
				<ul class="items">
					<?php while ( wld_loop( $featured_posts, ) ) : ?>
						<?php get_template_part( 'templates/template-part/main', 'product-query', array( 'i' => ++ $i ) ); ?>
					<?php endwhile; ?>
				</ul>
				<?php wld_the( 'button-left', 'btn' ); ?>
			</div>
			<div class="right">
				<div class="item">
					<?php wld_the( 'right-title-top', 'title' ); ?>
					<div class="wrapper">
						<?php while ( wld_loop( 'upcoming-events' ) ) : ?>
							<?php get_template_part( 'templates/template-part/main', 'posts' ); ?>
						<?php endwhile; ?>
						<?php wld_the( 'button-top', 'btn' ); ?>
					</div>
				</div>
				<div class="item">
					<?php wld_the( 'right-title-bottom', 'title' ); ?>
					<div class="wrapper">
						<?php while ( wld_loop( 'recent-news' ) ) : ?>
							<?php get_template_part( 'templates/template-part/main', 'posts-news' ); ?>
						<?php endwhile; ?>
						<?php wld_the( 'button-bottom', 'btn' ); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>



