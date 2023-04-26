<?php

class WLD_WC_BI_Single_Product extends WLD_WC_Single_Product {
  protected static array $product_flow = array(
    'open_access_no_tracking' => array(
      'blog', 'podcast', 'pxj-article', 'lpd', 'learning-bite', 'on-the-road',
    ),
    'open_access_yes_tracking' => array(
      'research-report',
    ),
    'member_free__non_member_buy' => array(
      'px-paper', 'webinar',
    ),
    'member_only' => array(
      'case-study', 'grant-report', 'connection-calls',
    ),
  );

  public static function init(): void {
    //remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
    remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs' );
    remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title' );
    remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );

    add_action( 'woocommerce_single_product_summary', array( self::class, 'woocommerce_template_single_before_title' ), 4 );
    add_action( 'woocommerce_before_single_product', array( self::class, 'woocommerce_before_single_product_summary_html' ) );
    add_action( 'woocommerce_shop_loop_item_title', array( self::class, 'woocommerce_template_loop_product_title_custom' ), 10 );
    add_action( 'woocommerce_single_product_summary', array( self::class, 'woocommerce_template_add_button' ), 14 );
    add_action( 'woocommerce_single_product_summary', array( self::class, 'woocommerce_template_single_long_description' ), 20 );
    add_action( 'woocommerce_single_product_summary', array( self::class, 'woocommerce_template_add_share_links' ), 22 );
    add_action( 'woocommerce_after_single_product', array( self::class, 'section_upsels' ), 5 ); // the buttons
    add_action( 'woocommerce_after_single_product', 'woocommerce_output_related_products', 3 ); // related content
    add_action( 'woocommerce_after_single_product', 'woocommerce_upsell_display', 10 );
    add_action( 'woocommerce_after_single_product', array( self::class, 'add_product_advance_blocks' ), 15 );
    add_action( 'woocommerce_after_single_product', array( self::class, 'add_product_popup' ), 20 );
    add_filter( 'woocommerce_single_product_image_thumbnail_html', array( self::class, 'custom_remove_product_link' ) );
    add_filter( 'gettext', array( self::class, 'rename_upsels' ) );
    add_filter( 'woocommerce_related_products', array( self::class, '_related_products_by_topic' ), 9999, 3 );
    add_filter( 'woocommerce_upsell_display_args', array( self::class, 'wc_change_number_related_products' ), 20 );

  }

  public static function _related_products_by_topic( $related_posts, $product_id, $args ) {

    $args = array(
      'post_type' => 'product',
      'post_status' => 'publish',
      'posts_per_page' => 3,
      'post__not_in' => array( $product_id ),
      'order' => 'DESC',
      'orderby' => 'meta_value',
      'meta_key' => '_date_published',
    );

    $terms_topic = get_the_terms( $product_id, 'topic' );

    if ( $terms_topic ) {
      $topics = array();
      $topics[ 'relation' ] = 'OR';
      foreach ( $terms_topic as $item ) {
        $topics[] = array(
          'taxonomy' => 'topic',
          'field' => 'term_id',
          'terms' => $item,
        );
      }
      $args[ 'tax_query' ][] = $topics;
    } else {
      return array();
    }

    $related_posts = get_posts( $args );
    return $related_posts;
  }

  public static function wc_change_number_related_products( $args ) {
    $args[ 'columns' ] = 3;

    return $args;
  }

  public static function woocommerce_template_loop_product_title_custom(): void {
    ?>
<h3 class="sub-title">
  <?php the_title(); ?>
</h3>
<?php
}

public static function section_upsels(): void {
  echo '</div></section>';
}

public static function rename_upsels( $translated ): string {
  $translated = str_ireplace( 'You may also like', 'Related content', $translated );

  return $translated;
}

public static function custom_remove_product_link( $html ): string {
  return strip_tags( $html, '<div><img>' );
}

public static function get_product_links( $product_id ): string | null {
  $downloadable = 'yes' === get_post_meta( $product_id, '_downloadable', true );
  $links = null;

  if ( $downloadable ) {
    global $product;
    $orders_ids = self::get_orders_ids_by_product_id( $product_id );
    $order = isset( $orders_ids[ 0 ] ) ? wc_get_order( $orders_ids[ 0 ] ) : null;

    // Check if $order is not null before calling get_downloadable_items()
    if ( $order !== null ) {
      $downloads = $order->get_downloadable_items();
	  
      $links .= '<div class="btn-wrap-container">'; // Add the container element with the desired class

      foreach ( $downloads as $download ) {
        $links .= '<div class="btn-wrap"><a href="' . esc_url( esc_url( $download[ 'download_url' ] ) ) . '" class="btn">' . esc_html( $download[ 'download_name' ] ) . '</a></div>';
      }
		
      $links .= '</div>'; // Close the container element
	  
    } else {
      $links = ''; // Set $links to an empty string if $order is null
    }
  } else {
    $event_link = get_post_meta( $product_id, '_link_view_event', true );
    $links .= '<div class="btn-wrap-container">'; // Add the container element with the desired class
    $links .= '<div class="btn-wrap"><a href="' . esc_url( $event_link ) . '" class="btn">' . esc_html__( 'View', 'parent-theme' ) . '</a></div>';
    $links .= '</div>'; // Close the container element
  }
	
  return $links;
}

public static function get_orders_ids_by_product_id( $product_id ): array {

  global $wpdb;
  $order_status = [ 'wc-completed' ];

  return $wpdb->get_col( "
			SELECT order_items.order_id
			FROM {$wpdb->prefix}woocommerce_order_items as order_items
			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
			LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
			WHERE posts.post_type = 'shop_order'
			AND posts.post_status IN ( '" . implode( "','", $order_status ) . "' )
			AND order_items.order_item_type = 'line_item'
			AND order_item_meta.meta_key = '_product_id'
			AND order_item_meta.meta_value = '" . $product_id . "'
			ORDER BY order_items.order_id DESC
			" );
}
public static function get_custom_button( $text = '', $href = '', $class = 'btn', $attrs = '' ): string {

  // Change button based on product type
  global $product;
  $product_id = $product->get_id();

  $product_categories = get_the_terms( $product_id, 'product_cat' );
  $custom_button = '';
  $category_name = '';

  if ( !empty( $product_categories ) && !is_wp_error( $product_categories ) ) {
    foreach ( $product_categories as $category ) {
      $category_name = $category->name;

      if ( $category_name == 'Webinar' ) {
        $custom_button = 'Watch Now';
        break;
      } elseif ( $category_name == 'Research Report' ) {
        $custom_button = 'Read Now';
        break;
      } elseif ( $category_name == 'PX Paper' ) {
        $custom_button = 'Read Now';
        break;
      /*} elseif ( $category_name == 'PXJ Article' ) {
        $custom_button = 'Visit Article';
        break; */
      } elseif ( $category_name == 'Case Study' ) {
        $custom_button = 'Read Now';
        break;
      } elseif ( $category_name == 'Learning Bite' ) {
        $custom_button = 'Watch Now';
        break;
      } elseif ( $category_name == 'Connection Calls' ) {
        $custom_button = 'Watch Now';
        break;
      } elseif ( $category_name == 'Grant Report' ) {
        $custom_button = 'Read Now';
        break;
        /*} elseif ($category_name == 'Blog') {
          $custom_button = 'Read Now';
          break;
        }
		  } elseif ( $category_name == 'Podcast' ) {
			$custom_button = 'Listen Now';
			break;*/
		  }
		  else {
        $custom_button = esc_html__( $text, 'parent-theme' );
        break;
      }
    }
  }

  // Add the category name to the class
  $class .= ' ' . sanitize_html_class( $category_name );

  return '<div class="btn-wrap">
            <a href="' . $href . '" class="' . $class . '" ' . $attrs . '>' . $custom_button . '</a>
          </div>';
}

public static function woocommerce_template_add_button(): void {
  global $product;
  $date_published = get_post_meta( $product->get_id(), '_date_published', true );
  $product_type = get_post_meta( $product->get_id(), '_type', true );
  $event_link = get_post_meta( $product->get_id(), '_link_view_event', true );
  $downloadable = 'yes' === get_post_meta( $product->get_id(), '_downloadable', true );
  $future_date = null;
  $is_member = null;

  if ( $date_published ) {
    $current_date = gmdate( 'Y-m-d' );
    $future_date = strtotime( $date_published ) > strtotime( $current_date );
  }

  if ( is_user_logged_in() ) {
    $is_member = get_user_meta( get_current_user_id(), 'is_member', true );
  }

  # Checking events
  if ( $future_date && 'connection-calls' === $product_type ) {
    if ( !$is_member ) {
      if ( $downloadable ) {
        echo self::get_custom_button( 'Download/View', '#become-member-popup' );
      } else {
        echo self::get_custom_button( 'View', '#become-member-popup' );
      }
      return;
    }
    echo self::get_custom_button( 'Register', $event_link );
    return;
  }

  # If the product is purchased
  if ( is_user_logged_in() && wc_customer_bought_product( get_userdata( get_current_user_id() )->user_email, get_current_user_id(), $product->get_id() ) ) {
    echo self::get_product_links( $product->get_id() );
    while ( wld_loop( 'links' ) ):
      wld_the( 'link', 'btn', '<div class="btn-wrap">' );
    endwhile;
    return;
  }

  # If the product is not purchased
  $flow_key = null;
  foreach ( self::$product_flow as $key => $flow ) {
    $type = get_post_meta( $product->get_id(), '_type', true );

    if ( in_array( $type, $flow, true ) ) {
      $flow_key = $key;
      break;
    }
  }

  switch ( $flow_key ) {
    case 'open_access_no_tracking':
    $product_id = $product->get_id();
    $product_categories = get_the_terms($product_id, 'product_cat');
    $downloads = $product->get_downloads();

    if ($product_categories && !is_wp_error($product_categories) && !empty($downloads)) {
        $download = array_shift($downloads); // Get the first download if there are multiple
        $file_url = $download->get_file();

        foreach ($product_categories as $category) {
            if ($category->name === 'Podcast') { // Replace with the first category name
                echo '<div class="podcast-player">
					  <iframe style="border: none;" title="Embed Player" src="' . $file_url . '"
							width="100%" height="32" scrolling="no" allowfullscreen="allowfullscreen"></iframe>
					 </div>' ;
					
            } elseif ($category->name === 'Learning Bite') { // Replace with the second category name
                echo '<div class="video-player responsive-video-wrapper">' . wp_oembed_get($file_url) . '</div>';
				 
            } elseif ($category->name === 'PXJ Article') { // Replace with the third category name
				echo '<div class="btn-wrap"><a href="' . $file_url . '" class="btn">Go to PX Journal</a></div>';
            }
        }
    }
break;


    case 'member_free__non_member_buy':
      if ( $is_member ) {
        echo self::get_custom_button( 'Download/View', '#', 'download-content-product btn', 'data-post-id=' . $product->get_id() );
        while ( wld_loop( 'links' ) ):
          wld_the( 'link', 'btn', '<div class="btn-wrap">' );
        endwhile;
      } else {
        echo self::get_custom_button( 'Download/View', '#member_free__non_member_buy', 'btn' );
        while ( wld_loop( 'links' ) ):
          $link_text = strip_tags( wld_get( 'link' ) );
        echo '<div class="btn-wrap"><a href="#become-member-popup" class="btn">' . $link_text . '</a></div>';
        endwhile;
      }
      break;
    case 'open_access_yes_tracking':
      if ( !is_user_logged_in() ) {
        echo self::get_custom_button( 'Download/View', '#become-member-popup' );
        while ( wld_loop( 'links' ) ):
          $link_text = strip_tags( wld_get( 'link' ) );
        echo '<div class="btn-wrap"><a href="#become-member-popup" class="btn">' . $link_text . '</a></div>';
        endwhile;
      } else {
        echo self::get_custom_button( 'Download/View', '#', 'download-content-product btn', 'data-post-id=' . $product->get_id() );
        while ( wld_loop( 'links' ) ):
          wld_the( 'link', 'btn', '<div class="btn-wrap">' );
        endwhile;
      }
      break;
    case 'member_only':
      if ( !$is_member ) {
        echo self::get_custom_button( 'Download/View', '#become-member-popup' );
        while ( wld_loop( 'links' ) ):
          $link_text = strip_tags( wld_get( 'link' ) );
        echo '<div class="btn-wrap"><a href="#become-member-popup" class="btn">' . $link_text . '</a></div>';
        endwhile;
      } else {
        echo self::get_custom_button( 'Download/View', '#', 'download-content-product btn', 'data-post-id=' . $product->get_id() );
        while ( wld_loop( 'links' ) ):
          wld_the( 'link', 'btn', '<div class="btn-wrap">' );
        endwhile;
      }
      break;
  }
}

public static function woocommerce_template_single_before_title(): void {
  self::display_product_title_cat( 3 );
}
//short description
public static function woocommerce_template_single_long_description(): void {
  if ( get_the_content() ):
    echo '<div class="woocommerce-product-details__short-description">';
  the_content();
  echo '</div>';
  endif;
}

public static function woocommerce_template_add_share_links(): void {
  get_template_part( 'templates/product-share-links' );
}

public static function add_product_advance_blocks(): void {
  //get_template_part( 'templates/template-part/single-product-relative' );
  get_template_part( 'templates/template-part/single-product-share-community-message' );
}

public static function woocommerce_before_single_product_summary_html(): void {
  ?>
<section class="product-wrapper" >
<div class="inner">
<?php
}

public static function display_product_title_cat( $count = 0 ): void {
  global $post;

  $terms = get_the_terms( $post->ID, 'topic' );

  if ( 3 === $count ) {
    $topics = get_the_terms( $post->ID, 'topic' );
    $focuses = get_the_terms( $post->ID, 'focus' );
    if ( $topics && $focuses ) {
      $terms = array_merge( $topics, $focuses );
    } elseif ( $topics ) {
      $terms = $topics;
    } elseif ( $focuses ) {
      $terms = $focuses;
    }
  }

  if ( $terms && !is_wp_error( $terms ) ):

    $cat_links = array();

  foreach ( $terms as $term ) {

    $cat_links[] = $term->name;

  }

  $on_cat = implode( ' | ', $cat_links );

  $attr_type = get_post_meta( $post->ID, '_type', true );
  $attr_top = get_post_meta( $post->ID, '_topic', true );

  $attr_type_name = '';
  $attr_top_name = '';

  $meta_options = get_field( 'wld_meta_attributes', 'option' );

  foreach ( $meta_options as $meta ) {
    if ( '_type' === '_' . $meta[ 'name' ] ) {
      $opt_str = explode( PHP_EOL, $meta[ 'options' ] );
      foreach ( $opt_str as $option ) {
        $lines = explode( ' : ', $option );
        if ( $lines[ 0 ] === $attr_type ) {
          $attr_type_name = $lines[ 1 ];
        }
      }
    }
    if ( '_topic' === '_' . $meta[ 'name' ] ) {
      $opt_str = explode( PHP_EOL, $meta[ 'options' ] );
      foreach ( $opt_str as $option ) {
        $lines = explode( ' : ', $option );
        if ( $lines[ 0 ] === $attr_top ) {
          $attr_top_name = $lines[ 1 ];
        }
      }
    }
  }

  $on_attr = '';
  if ( 2 === $count ) {
    if ( isset( $attr_type ) ) {
      $on_attr .= $attr_type_name;
    }
    if ( isset( $attr_top ) ) {
      $on_attr .= ' | ' . $attr_top_name;
    }
  } else {
    if ( isset( $attr_top ) ) {
      $on_attr = $attr_top_name;
    }
  }
  ?>
<div class="category">
  <?php if ( ''!==$on_attr ) :?>
  <?php echo $on_attr . ' | ' . $on_cat; ?>
  <?php else :?>
  <?php echo $on_cat; ?>
  <?php endif;?>
</div>
<?php
endif;
}

public static function add_product_popup(): void {
  global $product;

  get_template_part(
    'templates/template-part/product-popup',
    '',
    array(
      'product' => $product,
    ),
  );

  get_template_part(
    'templates/template-part/member_free_non_member_buy',
    '',
    array(
      'product' => $product,
    ),
  );
}
}
