<section class="module-products-three-columns individual-content">
	<div class="inner">
		<h2 class="title"><?php esc_html_e( 'RELATED CONTENT', 'parent-theme' ); ?></h2>
		<?php $related_posts = get_field( 'related_content_products' ); ?>
		<?php if ( $related_posts ) : ?>
			<div class="wrapper">
				<?php
				foreach ( $related_posts as $post ) :
					setup_postdata( $post );
					?>

					<div class="item">

						<div class="image">
							<?php the_post_thumbnail( '370x0' ); ?>
						</div>
						<div class="content">
							<?php WLD_WC_BI_Single_Product::display_product_title_cat(); ?>
							<h3 class="sub-title"><?php the_title(); ?></h3>
							<?php the_excerpt(); ?>
							<a href="<?php the_permalink(); ?>"
							   class="link"><?php esc_html_e( 'learn more', 'parent-theme' ); ?></a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<?php wp_reset_postdata(); ?>
		<?php endif; ?>
	</div>
</section>
