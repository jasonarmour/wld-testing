<section class="module-logos background-<?php echo wld_get( 'background_color' ) ?> padding-<?php echo wld_get( 'padding' ) ?> <?php echo wld_get( 'custom_class' ) ?>">
  <div class="inner">
      <?php wld_the( 'title', 'title' ); ?>
    <?php if( wld_get( 'text_under_the_headline' ) ): ?>
    <span>
    <?php wld_the( 'text_under_the_headline' ); ?>
    </span>
    <?php endif; ?>
    <?php wld_the( 'text' ); ?>
    <ul class="logos">
      <?php while ( wld_loop( 'logos' ) ) : ?>
      <li><a href="<?php echo wld_get('url');?>"> 
		  
		 <?php  $image = wld_the('logo');
				$size = 'full'; // (thumbnail, medium, large, full or custom size)
				if( $image ) {
					echo wp_get_attachment_image( $image_id, $size );
				}?>
		  
		  </a> </li>
      <?php endwhile; ?>
    </ul>
    <div class="btn-wrap">
      <?php wld_the( 'button_one', 'btn' ); ?>
      <?php wld_the( 'button_two', 'btn' ); ?>
    </div>
  </div>
</section>
