<section class="section-search-form <?php echo is_search() ? 'form-result' : '';  ?>">
	<div class="inner">
		<?php if ( is_search() ) : ?>
			<?php wld_the( 'wld_search_form_image' ); ?>
		<?php endif; ?>
		<?php if ( wld_has( 'title' ) ) : ?>
			<?php wld_the( 'search_form_title', 'title' ); ?>
		<?php elseif ( isset( $args['title'] ) ) : ?>
			<?php echo $args['title']; ?>
		<?php endif; ?>
		<?php get_search_form(); ?>
	</div>
</section>
