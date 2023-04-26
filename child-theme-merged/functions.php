<?php
$popup_right_side = get_field( 'wld_product_popup_right_side', 'option' );
if ( $popup_right_side[ 'button' ][ 'url' ] ) {
  define( 'BERYL_CRM_URL', $popup_right_side[ 'button' ][ 'url' ] );
} else {
  define( 'BERYL_CRM_URL', '' );
}

function wpdocs_theme_name_scripts() {
  wp_enqueue_script( 'custom', '/wp-content/themes/child-theme/js/custom.js', array(), '1.0.0', true );
}

add_action( 'wp_enqueue_scripts', 'wpdocs_theme_name_scripts' );

//Add Post Formats
function themename_post_formats_setup() {
  add_theme_support( 'post-formats', array( 'link' ) );
}

add_action( 'after_setup_theme', 'themename_post_formats_setup' );

//Page Slug Body Class
function add_slug_body_class( $classes ) {
  global $post;
  if ( isset( $post ) ) {
    $classes[] = $post->post_type . '-' . $post->post_name;
  }

  return $classes;
}

add_filter( 'body_class', 'add_slug_body_class' );

add_action(
  'wp_ajax_ajax_buying_product',
  static function () {
    $data = $_POST;
    if ( is_user_logged_in() && !wc_customer_bought_product( get_userdata( get_current_user_id() )->user_email, get_current_user_id(), $data[ 'prodId' ] ) ) {
      $price = get_user_meta( get_current_user_id(), 'member_price', true ) ?? 0;
      $order = wc_create_order(
        array(
          'customer_id' => get_current_user_id()
        ),
      );

      $order->add_product( wc_get_product( $data[ 'prodId' ] ) );
      $order->set_address(
        array(
          'email' => get_userdata( get_current_user_id() )->user_email
        ),
        'billing'
      );
      $order->set_total( $price );
      $order->update_status( 'completed' );
    }
    wp_send_json_success();
  }
);

function _hook_hide_admin_pages() {
  if ( WLD_DISABLE_COMMENTS ) {
    remove_menu_page( 'edit-comments.php' );
    remove_submenu_page( 'options-general.php', 'options-discussion.php' );
  }
  remove_submenu_page( 'plugins.php', 'plugin-editor.php' );
  remove_submenu_page( 'tools.php', 'tools.php' );
  remove_submenu_page( 'options-general.php', 'options-media.php' );
}

defined( 'DISALLOW_FILE_EDIT' ) || define( 'DISALLOW_FILE_EDIT', false );


add_filter( 'safe_style_css', 'add_display_to_safe_css', 10, 1 );

function add_display_to_safe_css( $css_attributes ) {
  $css_attributes[] = array(
    'left' => true,
    'top' => true,
  );

  return $css_attributes;
}


add_action( 'woocommerce_single_product_summary', function() {
  // Get the product object.
  $product = wc_get_product();

  // Get the product image.
  $image = $product->get_image('16x9');

  // Move the image to the left.
  echo '<div class="product-image">';
    echo $image;
  echo '</div>';
	
});

// Remove image from product pages
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
/* IMPORT SVG FILES */
function load_custom_template( $template ) {
  if ( is_page( 6547 ) ) {
    echo "Page 6547";
    include get_stylesheet_directory() . '/svg/pxpf.php';
  }
  if ( is_page( 4245 ) ) {
    echo "Page 4245";
    include get_stylesheet_directory() . '/svg/framework.php';
  }
  return $template;
}
add_filter( 'template_include', 'load_custom_template', 99 );

