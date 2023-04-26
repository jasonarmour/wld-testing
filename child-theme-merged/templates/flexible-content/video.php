<section class="video-module background-<?php echo wld_get( 'background_color' ) ?> padding-<?php echo wld_get( 'padding' ) ?> <?php echo wld_get( 'custom_class' ) ?>">
  <div class="inner">
	  <?php if( wld_get('title') ): ?>
    	<h2><?php wld_the('title'); ?></h2>
    <?php endif; ?>
    <?php
		if ( wld_get( 'full_width' ) == "1" ) {
		  echo  '<div class="wrapper video-' . wld_get('size') .'" style="max-width: inherit;">';
		} else {
		  echo '<div class="wrapper video-' . wld_get('size') .'">';
		};
    ?>
	

	<?php while ( wld_loop( 'video' ) ) : ?>			
				  		<div class="video embed-container">
							<?php wld_the('video_url');?>
							<h3 class="sub-title"><?php echo wld_get('caption');?></h3>
						</div>
 
	  
	<?php endwhile; ?>
	  
  	</div><!- END WRAPPER -->
  </div>
</section>
	