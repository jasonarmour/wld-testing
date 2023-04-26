<section class="image-gallery background-<?php echo wld_get( 'background_color' ) ?> padding-<?php echo wld_get( 'padding' ) ?> <?php echo wld_get( 'custom_class' ) ?>">
  <div class="inner">
    <?php if( wld_get('title') ): ?>
    	<h2><?php wld_the('title'); ?></h2>
    <?php endif; ?>
    <?php
	    $image_size = wld_get('size');
		if ( wld_get( 'full_width' ) == "1" ) {
		  echo  '<div class="wrapper "wrapper ' . $image_size . ' style="max-width: inherit;">';
		} else {
		  echo '<div class="wrapper ' . $image_size . '">';
		};
    ?>
    
	 <?php
		$images = get_sub_field( 'gallery' );
	    $size = "full";
		if ( $images ): ?>
		<ul>
		  <?php foreach( $images as $image_id ): ?>
		  	<li><?php echo wp_get_attachment_image( $image_id, $size ); ?></li>
		  <?php endforeach; ?>
		</ul>
    <?php endif; ?>
	  
	  
  </div>
  </div>
</section>
