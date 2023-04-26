<?php $i = 1; ?>
<section
	class="section-categories-list background-<?php echo wld_get( 'background_color' ) ?> padding-<?php echo wld_get( 'padding' ) ?> <?php echo wld_get( 'custom_class' ) ?>">
	<div class="inner">
		<?php if ( wld_get( 'full_width' ) == "1" ) {
			echo '<div class="wrapper" style="max-width: inherit;">';
		} else {
			echo '<div class="wrapper">';
		}; ?>

		<?php
		while ( wld_loop( 'categories', '<div class="items">' ) ) :
			$term = get_term( (int) wld_get( 'category' ) );

			$posts = get_posts(
				array(
					'post_type'      => 'product',
					'posts_per_page' => 3,
					'tax_query'      => array(
						array(
							'taxonomy' => $term->taxonomy,
							'field'    => 'slug',
							'terms'    => $term->slug,
						),
					),
				)
			);
			if ( ! empty( $posts ) ):
				?>
				<div class="item">
					<div class="wrapper-category">
						<?php wld_the( 'link', '', '<h3>' ); ?>

						<?php
						$image = get_field( 'thumbnail', $term );
						if ( $image ) { ?>
							<div class="img">
								<?php
								echo wp_get_attachment_image( $image['ID'], '370x0' );
								?>
							</div>
							<?php
						}
						?>

					</div>

					<?php while ( wld_loop( $posts, '<ul class="wrapper-products">' ) ) : ?>
						<li class="product">
							<a href="<?php echo get_the_permalink(); ?>">
								<?php WLD_WC_BI_Loop::display_product_title_attribute(); ?>
								<h2><?php the_title(); ?></h2>
								<p>
									<?php echo wp_trim_words( get_the_excerpt(), 14, '...' ); ?>
								</p>
								<a href="<?php echo get_the_permalink(); ?>"><?php esc_html_e( 'Read more', 'parent-theme' ); ?></a>
							</a>
						</li>
					<?php endwhile; ?>
				</div>
				<?php
				if ( 5 === ++ $i ) {
					echo '<div class="line"> </div>';
				}

			endif;
			?>
		<?php endwhile; ?>
	</div>
	</div>
</section>



