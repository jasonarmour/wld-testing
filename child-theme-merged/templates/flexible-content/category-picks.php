<?php
$i                    = 0;
$args                 = WLD_WC_BI_Query::bi_products_query( 3 );
$category_picks_posts = get_posts( $args );

?>
<section class="module-category-picks background-<?php echo wld_get( 'background_color' ) ?> padding-<?php echo wld_get( 'padding' ) ?> <?php echo wld_get( 'custom_class' ) ?>">

<?php if ( wld_get( 'set_the_background' ) ) : ?>
	<?php wld_the( 'background', '1903x0', array( 'class' => 'object-fit object-fit-cover' ) ); ?>
<?php endif; ?>
<div class="inner">
	<?php wld_the( 'title', 'title' ); ?>
			<?php if ( wld_get( 'full_width' ) == "1" ) {
			  echo '<div class="wrapper" style="max-width: inherit;">';
			} else {
			  echo '<div class="wrapper">';
		}; ?>

		<?php while ( wld_loop( $category_picks_posts, '<div class="left">' ) ) : ?>
			<?php if ( 0 === $i ) : ?>
				<div class="item">
					<?php if ( has_post_thumbnail() ): ?>
						<div class="image">
							<?php the_post_thumbnail( '770x0' ); ?>
						</div>
					<?php endif; ?>
					<?php WLD_WC_BI_Loop::display_product_title_attribute(); ?>
					<h3 class="sub-title"><?php echo get_the_title(); ?></h3>
					<?php echo '<p>'.wp_trim_words( get_the_content(), 37, '' ).'</p>'; ?>
					<a href="<?php echo get_permalink(); ?>"><?php esc_html_e( 'Read more', 'parent-theme' ); ?></a>
				</div>
				<?php $i ++ ?>
			<?php endif; ?>
		<?php endwhile; ?>
		<?php $i = 0; ?>
		<?php while ( wld_loop( $category_picks_posts, '<div class="right">' ) ) : ?>
			<?php if ( 0 !== $i ) : ?>
				<div class="item">
					<?php if ( has_post_thumbnail() ): ?>
						<div class="image">
							<?php the_post_thumbnail( '300x0' ); ?>
						</div>
					<?php endif; ?>
					<?php WLD_WC_BI_Loop::display_product_title_attribute(); ?>
					<h3 class="sub-title"><?php echo get_the_title(); ?></h3>
					<?php echo '<p>'.wp_trim_words( get_the_content(), 16, '' ).'</p>'; ?>
					<a class="link"
					   href="<?php echo get_permalink(); ?>"><?php esc_html_e( 'Read more', 'parent-theme' ); ?></a>
				</div>
			<?php endif; ?>
			<?php $i ++; ?>
		<?php endwhile; ?>
	</div>
</div>
</section>
