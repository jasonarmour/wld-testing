<section class="module-links background-<?php echo wld_get( 'background_color' ) ?> padding-<?php echo wld_get( 'padding' ) ?> <?php echo wld_get( 'custom_class' ) ?>">
	<?php if ( wld_get( 'set_the_background' ) ) : ?>
		<?php wld_the( 'background', '1903x0', array( 'class' => 'object-fit object-fit-cover' ) ); ?>
	<?php endif; ?>
	<div class="inner">
		<?php while ( wld_loop( 'links', '<div class="wrapper">' ) ) : ?>
			<?php if ( 1 === get_row_index() ): ?>
				<?php wld_the( 'link', 'active' ); ?>
			<?php else: ?>
				<?php wld_the( 'link' ); ?>
			<?php endif; ?>
		<?php endwhile; ?>
	</div>
</section>



