<?php /** @noinspection PhpUndefinedMethodInspection, PhpUndefinedClassInspection */

/**
 * @link    ./wp-content/woocommerce/templates/content-single-product.php
 *
 * Hook: woocommerce_before_single_product.
 * @see     woocommerce_output_all_notices - 10
 *
 * Hook: woocommerce_before_single_product_summary.
 * @see     woocommerce_show_product_sale_flash - 10
 * @see     woocommerce_show_product_images - 20
 *
 * Hook: woocommerce_single_product_summary.
 * @see     woocommerce_template_single_title - 5
 * @see     woocommerce_template_single_rating - 10
 * @see     woocommerce_template_single_price - 10
 * @see     woocommerce_template_single_excerpt - 20
 * @see     woocommerce_template_single_add_to_cart - 30
 * @see     woocommerce_template_single_meta - 40
 * @see     woocommerce_template_single_sharing - 50
 * @see     WC_Structured_Data::generate_product_data() - 60
 *
 * Hook: woocommerce_after_single_product_summary.
 * @see     woocommerce_output_product_data_tabs - 10
 * @see     woocommerce_upsell_display - 15
 * @see     woocommerce_output_related_products - 20
 *
 * Hook: woocommerce_after_single_product.
 *
 * @version 4.3.0
 */
class WLD_WC_Single_Product {
	public static $has_admin_fields = false;

	public static function init(): void {
		add_action(
			'after_setup_theme',
			array( static::class, 'add_support' )
		);
		add_action(
			'woocommerce_single_product_summary',
			array( static::class, 'radio_variations' ),
			5
		);
		add_filter(
			'woocommerce_product_get_gallery_image_ids',
			array( static::class, 'add_first_thumbnail_in_gallery' ),
			10,
			2
		);
		add_filter(
			'woocommerce_product_data_tabs',
			array( static::class, 'add_admin_tab' )
		);
		add_filter(
			'woocommerce_product_write_panel_tabs',
			array( static::class, 'add_admin_panel' ),
			PHP_INT_MAX
		);
		add_action(
			'admin_head',
			array( static::class, 'styles' )
		);
		add_filter(
			'woocommerce_get_availability_text',
			array( self::class, 'availability_text' ),
			10,
			2
		);
	}

	/**
	 * Do not use these scripts from WooCommerce, so as not to include unnecessary scripts and styles.
	 * Better do everything in init.js this also gives you complete freedom of action,
	 * since it may be difficult to change anything in js WooCommerce.
	 */
	public static function add_support(): void {
		// phpcs:disable Squiz.PHP.CommentedOutCode.Found
		//add_theme_support( 'wc-product-gallery-zoom' );
		//add_theme_support( 'wc-product-gallery-lightbox' );
		//add_theme_support( 'wc-product-gallery-slider' );
		// phpcs:enable Squiz.PHP.CommentedOutCode.Found
	}

	public static function add_first_thumbnail_in_gallery( $gallery_image_ids, WC_Product $product ): array {
		if ( empty( $gallery_image_ids ) ) {
			$gallery_image_ids = array();
		}

		if ( $product->get_image_id() && ! current_theme_supports( 'wc-product-gallery-slider' ) ) {
			array_unshift( $gallery_image_ids, $product->get_image_id() );
		}

		return $gallery_image_ids;
	}

	public static function add_admin_tab( array $tabs ): array {
		if ( self::$has_admin_fields ) {
			$tabs['theme'] = array(
				'label'    => esc_html__( 'Theme', 'parent-theme' ),
				'target'   => 'theme_product_data',
				'class'    => array(),
				'priority' => 0,
			);
		}

		return $tabs;
	}

	public static function add_admin_panel(): void {
		echo '</ul><ul>';
		echo '<div id="theme_product_data" class="panel woocommerce_options_panel"><div class="options_group">';
		do_action( 'woocommerce_product_options_theme' );
		echo '</div></div>';
	}

	public static function styles(): void {
		?>
		<style>
			#woocommerce-product-data ul.wc-tabs li.theme_options a::before {
				content: "\f111";
			}
		</style>
		<?php
	}

	public static function availability_text( string $text, WC_Product $product ): string {
		if ( '' === $text && $product->is_in_stock() ) {
			$text = esc_html__( 'In stock', 'parent-theme' );
		}

		return $text;
	}

	public static function radio_variations(): void {
		/** @var WC_Product_Variable $product */
		global $product;

		if ( $product->is_type( 'variable' ) ) {
			echo '<div class="radio-variations"></div>';
		}
	}
}
