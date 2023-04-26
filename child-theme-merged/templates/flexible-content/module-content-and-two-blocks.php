<section class="module-content-and-two-blocks background-<?php echo wld_get( 'background_color' ) ?> padding-<?php echo wld_get( 'padding' ) ?> <?php echo wld_get( 'custom_class' ) ?>">
	<div class="inner">
		<div class="wrapper">
			<?php while ( wld_loop( 'content-left', '<div class="left">' ) ) : ?>
				<?php wld_the( 'title', 'title' ); ?>
				<?php wld_the( 'text' ); ?>
			<?php endwhile; ?>
			<?php while ( wld_loop( 'blocks', '<div class="right">' ) ) : ?>
				<div class="item">
					<?php wld_the( 'subtitle', 'sub-title' ); ?>
					<?php while ( wld_loop( 'links' ) ) : ?>
						<?php wld_the( 'link' ); ?>
					<?php endwhile; ?>
				</div>
			<?php endwhile; ?>
		</div>
	</div>
</section>



