<section class="module-only-content background-<?php echo wld_get( 'background_color' ) ?> padding-<?php echo wld_get( 'padding' ) ?> <?php echo wld_get( 'custom-class' ) ?>"> 
  <!--    <img src="" alt="" class="object-fit object-fit-cover">-->
  <div class="inner">
	<?php if ( wld_get( 'full_width' ) == "1" ) {
	  echo  '<div class="wrapper" style="max-width: inherit;">';
	} else {
	  echo '<div class="wrapper">';
	}; 
 ?>
    <?php wld_the( 'headline', 'title' ); ?>
    <?php wld_the( 'subhead', 'sub-title' ); ?>
    <?php wld_the( 'text' ); ?>
    <?php wld_the( 'button', 'btn' ); ?>
  </div>
  </div>
</section>
