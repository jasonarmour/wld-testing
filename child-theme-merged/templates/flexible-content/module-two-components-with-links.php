<section class="module-two-components-with-links background-<?php echo wld_get( 'background_color' ) ?> padding-<?php echo wld_get( 'padding' ) ?> <?php echo wld_get( 'custom_class' ) ?>">
	<?php /*if ( wld_get( 'set_the_background' ) ) : ?>
		<?php wld_the( 'background', '1903x0', array( 'class' => 'object-fit object-fit-cover' ) ); ?>
	<?php endif; */?>
	<div class="inner">
		<?php while ( wld_loop( 'components', '<div class="wrapper">' ) ) : ?>
			<div class="item">
				<?php wld_the( 'title', 'title' ); ?>
				<?php wld_the( 'text' ); ?>
				<?php while ( wld_loop( 'links' ) ) : ?>
					<?php wld_the( 'link' ); ?>
				<?php endwhile; ?>
			</div>
		<?php endwhile; ?>
	</div>
</section>



