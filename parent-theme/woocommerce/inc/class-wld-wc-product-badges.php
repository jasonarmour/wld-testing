<?php /** @noinspection PhpUnused, UnknownInspectionInspection, PhpUndefinedFunctionInspection, PhpUndefinedMethodInspection, PhpUndefinedClassInspection */


class WLD_WC_Product_Badges {
	public static function init( array $args = array() ): void {
		$args = array_merge(
			array(
				'sale_allow_in_page' => false,
				'sale_allow_in_loop' => true,
				'sale_percent'       => true,
			),
			$args
		);

		if ( true !== $args['sale_allow_in_page'] ) {
			remove_action(
				'woocommerce_before_single_product_summary',
				'woocommerce_show_product_sale_flash'
			);
		}

		if ( true !== $args['sale_allow_in_loop'] ) {
			remove_action(
				'woocommerce_before_shop_loop_item_title',
				'woocommerce_show_product_loop_sale_flash'
			);
		}

		if ( true === $args['sale_percent'] ) {
			add_filter(
				'woocommerce_sale_flash',
				array( static::class, 'sale_badge' )
			);
		}

		add_action(
			'woocommerce_before_shop_loop_item_title',
			array( static::class, 'start_wrap' ),
			7
		);
		add_action(
			'woocommerce_before_single_product_summary',
			array( static::class, 'start_wrap' ),
			7
		);
		add_action(
			WLD_WC_Product_Badge::LOOP_ACTION,
			array( static::class, 'start_wrap' ),
			7
		);
		add_action(
			WLD_WC_Product_Badge::PAGE_ACTION,
			array( static::class, 'start_wrap' ),
			7
		);
		add_action(
			'woocommerce_before_shop_loop_item_title',
			array( static::class, 'end_wrap' ),
			9
		);
		add_action(
			'woocommerce_before_single_product_summary',
			array( static::class, 'end_wrap' ),
			9
		);
		add_action(
			WLD_WC_Product_Badge::LOOP_ACTION,
			array( static::class, 'end_wrap' ),
			9
		);
		add_action(
			WLD_WC_Product_Badge::PAGE_ACTION,
			array( static::class, 'end_wrap' ),
			9
		);

		// phpcs:disable Squiz.PHP.CommentedOutCode.Found
		//new WLD_WC_Product_Badge();
		//new WLD_WC_Product_Badge( esc_html__( 'Other', 'parent-theme' ) );
	}

	public static function start_wrap(): void {
		echo '<div class="badges">';
	}

	public static function end_wrap(): void {
		echo '</div>';
	}

	public static function sale_badge(): string {
		/** @var WC_Product $product */
		global $product;

		$sale          = '';
		$sale_price    = (float) $product->get_sale_price();
		$regular_price = (float) $product->get_regular_price();

		if ( $product->is_type( 'variable' ) ) {
			/** @var WC_Product_Variable $product */
			$min_sale_price    = $product->get_variation_sale_price( 'min' );
			$max_sale_price    = $product->get_variation_sale_price( 'max' );
			$min_regular_price = $product->get_variation_regular_price( 'min' );
			$max_regular_price = $product->get_variation_regular_price( 'max' );

			if ( $min_sale_price === $max_sale_price && $min_regular_price === $max_regular_price ) {
				$sale_price    = (float) $min_sale_price;
				$regular_price = (float) $min_regular_price;
			} else {
				/** @var WC_Product_Variation[] $variations */
				$variations    = array();
				$variation_ids = $product->get_children();
				foreach ( $variation_ids as $variation_id ) {
					/** @var WC_Product_Variation $variation */
					$variation = wc_get_product( $variation_id );
					if (
						! $variation ||
						! $variation->exists() ||
						(
							'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) &&
							! $variation->is_in_stock()
						) ||
						(
							apply_filters( 'woocommerce_hide_invisible_variations', true, $product->get_id(), $variation ) &&
							! $variation->variation_is_visible()
						)
					) {
						continue;
					}

					$variations[] = $variation;
				}

				$sales = array();
				if ( $variations ) {
					foreach ( $variations as $variation ) {
						$sale_price    = (float) $variation->get_sale_price();
						$regular_price = (float) $variation->get_regular_price();
						if ( $sale_price && $regular_price ) {
							$sales[] = round( ( $regular_price - $sale_price ) / $regular_price * 100, 2 );
						}
					}
				}

				if ( $sales ) {
					$min = min( $sales );
					$max = max( $sales );

					if ( $min === $max ) {
						$sale = (string) ( - 1 * $min );
					} else {
						$sale = ( - 1 * $min ) . '% to ' . ( - 1 * $max );
					}
				}
			}
		}

		if ( $sale_price && $regular_price && empty( $sale ) ) {
			$sale = (string) ( - 1 * round( ( $regular_price - $sale_price ) / $regular_price * 100, 2 ) );
		}

		if ( $sale ) {
			$sale = '<span class="onsale">' . $sale . '%</span>';
		}

		return $sale;
	}
}
