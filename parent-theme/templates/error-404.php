<section class="section-error-404">
	<div class="inner">
		<?php while ( wld_loop( 'wld_404' ) ) : ?>
			<?php wld_the( 'image', '500x0', '<div class="img">' ); ?>
			<?php wld_the( 'title', 'title' ); ?>
			<?php wld_the( 'text' ); ?>
			<?php while ( wld_loop( 'links', '<div class="block-more">' ) ) : ?>
				<?php wld_the( 'link' ); ?>
				<?php if ( 0 === get_row_index() % 3 ) : ?>
					<?php echo '<br>'; ?>
				<?php endif; ?>
			<?php endwhile; ?>
			<?php wld_the( 'button', 'btn', '<p>' ); ?>
			<?php if ( wld_get( 'search_form_enabled' ) ) : ?>
				<?php get_template_part( 'templates/search-form' ); ?>
			<?php endif; ?>
		<?php endwhile; ?>
	</div>
</section>
