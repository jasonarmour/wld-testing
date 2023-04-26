<?php

/**
 * @link    ./wp-content/woocommerce/templates/archive-product.php
 * @link    ./wp-content/woocommerce/templates/single-product.php
 *
 * Hook: woocommerce_before_main_content.
 * @see     woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @see     woocommerce_breadcrumb - 20
 * @see     WC_Structured_Data::generate_website_data() - 30
 *
 * Hook: woocommerce_archive_description.
 * @see     woocommerce_taxonomy_archive_description - 10
 * @see     woocommerce_product_archive_description - 10
 *
 * Hook: woocommerce_after_main_content.
 * @see     woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 *
 * Hook: woocommerce_sidebar.
 * @see     woocommerce_get_sidebar - 10
 *
 * @version 4.3.0
 */
class WLD_WC_Global {
	public static function init() : void {
		add_filter(
			'page_template_hierarchy',
			array( self::class, 'set_default_template' )
		);
		add_filter(
			'wp_enqueue_scripts',
			array( self::class, 'disable_block_styles' )
		);
		add_filter(
			'woocommerce_enqueue_styles',
			'__return_false'
		);
		add_filter(
			'do_shortcode_tag',
			array( self::class, 'wrapping_shortcodes' ),
			10,
			2
		);
		add_filter(
			'woocommerce_widget_cart_item_quantity',
			array( self::class, 'change_cart_item_quantity' ),
			10,
			3
		);
		add_action(
			'admin_print_scripts-woocommerce_page_wc-settings',
			array( static::class, 'enable_wysiwyg_for_terms_fields' )
		);
		remove_action(
			'woocommerce_before_main_content',
			'woocommerce_breadcrumb',
			20
		);
		remove_action(
			'woocommerce_before_main_content',
			'woocommerce_output_content_wrapper'
		);
		remove_action(
			'woocommerce_after_main_content',
			'woocommerce_output_content_wrapper_end'
		);
	}

	public static function set_default_template( array $templates ) : array {
		if ( is_account_page() || is_cart() || is_checkout() ) {
			array_splice( $templates, - 1, 0, 'woocommerce/page.php' );
		}

		return $templates;
	}

	public static function disable_block_styles() : void {
		wp_dequeue_style( 'wc-block-style' );
	}

	public static function wrapping_shortcodes( $output, $tag ) : string {
		$section = '';
		if ( 'woocommerce_cart' === $tag ) {
			$section = 'cart';
		} elseif ( 'woocommerce_checkout' === $tag ) {
			$section = 'checkout';
		} elseif ( 'woocommerce_my_account' === $tag ) {
			$section = is_user_logged_in() ? 'my-account' : 'login';
		}

		if ( $section ) {
			$output = '<div class="woocommerce-section ' . $section . '-section"><div class="inner">' . $output . '</div></div>';
		}

		return $output;
	}

	/** @noinspection PhpUnusedParameterInspection */
	public static function change_cart_item_quantity( $qty, $cart_item, $cart_item_key ) : string {
		$label         = __( 'Qty:', 'parent-theme' );
		$_product      = apply_filters(
			'woocommerce_cart_item_product',
			$cart_item['data'],
			$cart_item,
			$cart_item_key
		);
		$product_price = apply_filters(
			'woocommerce_cart_item_price',
			WC()->cart->get_product_price( $_product ),
			$cart_item,
			$cart_item_key
		);

		return sprintf(
			'<div class="qty">%s %s</div><div class="price">%s</div>',
			esc_html( $label ),
			(int) $cart_item['quantity'],
			wp_kses_post( $product_price )
		);
	}

	public static function enable_wysiwyg_for_terms_fields() : void {
		wp_enqueue_editor();
		add_action(
			'print_default_editor_scripts',
			static function () {
				?>
				<script>
					wp.editor.initialize( 'woocommerce_registration_privacy_policy_text' );
					wp.editor.initialize( 'woocommerce_checkout_privacy_policy_text' );
				</script>
				<?php
			}
		);
	}
}
