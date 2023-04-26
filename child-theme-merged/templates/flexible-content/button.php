<?php 
	if ( wld_get( 'appearance' ) == "inline" ) {
	  $button_style = "button-inline";
	} else {
	  $button_style = "button-stacked";
	};
	$alignment = wld_get('alignment');


?>

<section class="button-module button-module-<?php echo $alignment; ?> <?php echo $button_style; ?> background-<?php echo wld_get( 'background_color' ) ?> padding-<?php echo wld_get( 'padding' ) ?> <?php echo wld_get( 'custom_class' ) ?>">
  <div class="inner">
   		<?php if ( wld_get( 'full_width' ) == "1" ) {
			  echo '<div class="wrapper" style="max-width: inherit;">';
			} else {
			  echo '<div class="wrapper">';
		}; ?>

	 
	<?php while ( wld_loop( 'buttons' ) ) : ?>			
				
	  			<?php 
	  
	  				$button_text = wld_get('button_text');
					if ( wld_get( 'custom_url' ) != "" ) {
				  		echo '<a href="' . wld_get( 'custom_url' ) . '" class="btn">' . $button_text . '</a>';
					} ?>
	  
	  			
	  			<?php wld_the( 'site_link', 'btn' ); ?>
	  
	<?php endwhile; ?>
	  
  	</div><!- END WRAPPER -->
  </div>
</section>
