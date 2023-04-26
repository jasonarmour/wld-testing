<?php
$i = 0;
?>
<section class="module-category-picks background-<?php echo wld_get( 'background_color' ) ?> padding-<?php echo wld_get( 'padding' ) ?> <?php echo wld_get( 'custom_class' ) ?>">
  <div class="inner">
    <?php wld_the('title', 'title'); ?>
    <?php
    if ( wld_get( 'full_width' ) == "1" ) {
      echo '<div class="wrapper" style="max-width: inherit;">';
    } else {
      echo '<div class="wrapper">';
    };

    global $wp_query;


    while ( wld_loop( 'products' ) ):
	  
	global $product;
  $product_id = $product->get_id();
$product_categories = get_the_terms( $product_id, 'product_cat' );
$category_slugs = '';
foreach ( $product_categories as $category ) {
    $category_slugs .= $category->slug . ' ';
}  
	  
	  
      if ( 0 === $i ):
        ?>
    <div class="left"> <a href="<?php the_permalink(); ?>" class="item <?php echo $category_slugs;?>">
      <?php if (has_post_thumbnail()): ?>
      <div class="image">
        <?php the_post_thumbnail('992x0'); ?>
      </div>
      <?php endif; ?>
      <?php WLD_WC_BI_Loop::display_product_title_attribute(); ?>
      <h3 class="sub-title">
        <?php the_title(); ?>
      </h3>
      <p> <?php echo wp_trim_words(get_the_excerpt(), 37, ''); ?></p>
      <div class="link">
        <?php esc_html_e('Learn more', 'parent-theme'); ?>
      </div>
      </a> </div>
    <div class="right">
      <?php
      $i++;
      elseif ( $i <= 2 ):
        ?>
      <a href="<?php the_permalink(); ?>" class="item <?php echo $category_slugs;?>">
        <?php if (has_post_thumbnail()): ?>
        <div class="image">
          <?php the_post_thumbnail('300x0'); ?>
        </div>
        <?php endif; ?>
        <?php WLD_WC_BI_Loop::display_product_title_attribute(); ?>
        <h3 class="sub-title">
          <?php the_title(); ?>
        </h3>
        <p> <?php echo wp_trim_words(get_the_excerpt(), 16, ''); ?></p>
        <div class="link">
        <?php esc_html_e('Learn more', 'parent-theme'); ?>
        </div>
        <?php $i++; ?>
      </a>
      <?php
      else :
        if ( $i === 3 ) {
          echo '</div></div><div class="bottom">';
        }
        ?>
      <a href="<?php the_permalink(); ?>" class="item <?php echo $category_slugs;?>">
        <?php if (has_post_thumbnail()): ?>
        <div class="image">
          <?php the_post_thumbnail('300x0'); ?>
        </div>
        <?php endif; ?>
        <?php WLD_WC_BI_Loop::display_product_title_attribute(); ?>
        <h3 class="sub-title">
          <?php the_title(); ?>
        </h3>
        <p> <?php echo wp_trim_words(get_the_excerpt(), 16, ''); ?></p>
        <div class="link">
        <?php esc_html_e('Learn more', 'parent-theme'); ?>
        </div> </a>
      <?php
      $i++;
      $loop_has_next = ( $wp_query->current_post + 1 ) !== $wp_query->post_count;
      if ( $loop_has_next && $i > 3 && $i % 3 === 1 ) {
        echo '</div><div class="bottom">';
      }
      ?>
      <?php endif; ?>
      <?php endwhile; ?>
    </div>
  </div>
  <?php
  if ( $i > 2 ) {
    echo '</div>';
  }
  ?>
  </div>
  </div>
  </div>
</section>
