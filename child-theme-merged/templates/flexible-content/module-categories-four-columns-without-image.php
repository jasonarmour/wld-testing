<section class="module-categories-four-columns-without-image background-<?php echo wld_get( 'background_color' ) ?> padding-<?php echo wld_get( 'padding' ) ?> <?php echo wld_get( 'custom_class' ) ?>">
	<div class="inner">
		<?php while ( wld_loop( 'categories', '<div class="wrapper">' ) ) : ?>
			<div class="item">
				<?php wld_the( 'category','<div class="category">' ); ?>
				<?php wld_the( 'title', 'title' ); ?>
				<?php wld_the( 'text' ); ?>
				<?php wld_the( 'link', 'link' ); ?>
			</div>
		<?php endwhile; ?>
	</div>
</section>



