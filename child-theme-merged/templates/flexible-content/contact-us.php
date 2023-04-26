<section class="section-contact-us background-<?php echo wld_get( 'background_color' ) ?> padding-<?php echo wld_get( 'padding' ) ?> <?php echo wld_get( 'custom_class' ) ?>">
  <?php wld_the( 'background', '1903x0', array( 'class' => 'object-fit object-fit-cover' ) ); ?>
  <div class="inner">
    		<?php if ( wld_get( 'full_width' ) == "1" ) {
			  echo '<div class="wrapper" style="max-width: inherit;">';
			} else {
			  echo '<div class="wrapper">';
		}; ?>

    <div class="left">
      <?php wld_the( 'title', 'title' ); ?>
      <?php wld_the( 'text', '' ); ?>
    </div>
    <div class="right">
      <?php wld_the( 'form' ); ?>
    </div>
  </div>
  </div>
</section>
