<?php

/**
 * @noinspection PhpUnused, UnknownInspectionInspection, PhpUndefinedFunctionInspection
 *
 * @link    ./wp-content/woocommerce/templates/archive-product.php
 * @link    ./wp-content/woocommerce/templates/content-product.php
 *
 * Hook: woocommerce_before_shop_loop.
 *
 * @see     woocommerce_output_all_notices - 10
 * @see     woocommerce_result_count - 20
 * @see     woocommerce_catalog_ordering - 30
 *
 * Hook: woocommerce_shop_loop.
 *
 * Hook: woocommerce_before_shop_loop_item.
 * @see     woocommerce_template_loop_product_link_open - 10
 *
 * Hook: woocommerce_before_shop_loop_item_title.
 *
 * @see     woocommerce_show_product_loop_sale_flash - 10
 * @see     woocommerce_template_loop_product_thumbnail - 10
 *
 *
 * Hook: woocommerce_shop_loop_item_title.
 * @see     woocommerce_template_loop_product_title - 10
 *
 * Hook: woocommerce_after_shop_loop_item_title.
 * @see     woocommerce_template_loop_rating - 5
 * @see     woocommerce_template_loop_price - 10
 *
 * Hook: woocommerce_after_shop_loop_item.
 * @see     woocommerce_template_loop_product_link_close - 5
 * @see     woocommerce_template_loop_add_to_cart - 10
 *
 * Hook: woocommerce_after_shop_loop.
 * @see     woocommerce_pagination - 10
 *
 * Hook: woocommerce_no_products_found.
 * @see     wc_no_products_found - 10
 *
 * @version 4.3.0
 */
class WLD_WC_Loop {
	public static function init(): void {
		add_filter(
			'loop_shop_per_page',
			array( static::class, 'shop_per_page' )
		);
		add_filter(
			'woocommerce_before_template_part',
			array( static::class, 'change_pagination_start' )
		);
		add_filter(
			'woocommerce_after_template_part',
			array( static::class, 'change_pagination_end' )
		);
	}

	public static function shop_per_page(): int {
		return 12;
	}

	public static function change_pagination_start( string $template ): void {
		if ( 'loop/pagination.php' === $template ) {
			ob_start();
		}
	}

	/** @noinspection HtmlUnknownTarget, DuplicatedCode */
	public static function change_pagination_end( string $template ): void {
		if ( 'loop/pagination.php' === $template ) {
			$total       = wc_get_loop_prop( 'total_pages' );
			$current     = wc_get_loop_prop( 'current_page' );
			$first_url   = get_pagenum_link();
			$first_text  = esc_html__( 'First Page', 'parent-theme' );
			$first_link  = sprintf( '<a href="%s" class="first-page">%s</a>', $first_url, $first_text );
			$last_url    = get_pagenum_link( $total );
			$last_text   = esc_html__( 'Last Page', 'parent-theme' );
			$last_link   = sprintf( '<a href="%s" class="last-page">%s</a>', $last_url, $last_text );
			$pattern     = array();
			$replacement = array();

			if ( $current > 1 ) {
				$pattern[]     = '/(<ul[^>]*>)/';
				$replacement[] = "$1<li>$first_link</li>";
			}

			if ( $current < $total ) {
				$pattern[]     = '/(<\/ul>)/';
				$replacement[] = "<li>$last_link</li>$1";
			}

			$pagination = preg_replace( $pattern, $replacement, ob_get_clean() );

			echo $pagination;
		}
	}
}
