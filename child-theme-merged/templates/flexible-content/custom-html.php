<section class="custom-html background-<?php echo wld_get( 'background_color' ) ?> padding-<?php echo wld_get( 'padding' ) ?> <?php echo wld_get( 'custom_class' ) ?>">
	<div class="inner">
		<?php if ( wld_get( 'full_width' ) == "1" ) {
			  echo '<div class="wrapper" style="max-width: inherit;">';
			} else {
			  echo '<div class="wrapper">';
			}; ?>
    	<?php wld_the( 'custom_html' ); ?>
		</div>
	</div>
</section>
