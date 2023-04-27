<section class="section-featured-content custom-html background-<?php echo wld_get( 'background_color' ) ?> padding-<?php echo wld_get( 'padding' ) ?> <?php echo wld_get( 'custom_class' ) ?>">
	<div class="inner">
		
		<div class="wrapper">
			<?php $product_type = get_field( 'product_type' ); ?>
			<?php if ( $product_type ) : ?>
				<?php $get_terms_args = array(
					'taxonomy' => 'product_cat',
					'hide_empty' => 0,
					'include' => $product_type,
				); ?>
				<?php $terms = get_terms( $get_terms_args ); ?>
				<?php if ( $terms ) : ?>
					<?php foreach ( $terms as $term ) : ?>
						<a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
					<?php endforeach; ?>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php wld_the( 'button', 'btn' ); ?>
	</div>
</section>


