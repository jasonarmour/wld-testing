<section class="module-two-components-with-buttons background-<?php echo wld_get( 'background_color' ) ?> padding-<?php echo wld_get( 'padding' ) ?> <?php echo wld_get( 'custom_class' ) ?>">
	<div class="inner">
		<?php while ( wld_loop( 'components', '<div class="wrapper">' ) ) : ?>
			<div class="item">
				<?php wld_the( 'title', 'title' ); ?>
				<?php wld_the( 'text' ); ?>
				<?php wld_the( 'button', 'btn' ); ?>
			</div>
			<?php endwhile; ?>
	</div>
</section>



