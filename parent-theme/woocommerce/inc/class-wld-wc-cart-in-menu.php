<?php /** @noinspection PhpUndefinedFunctionInspection */


class WLD_WC_Cart_In_Menu {
	protected static $show_total = false;
	protected static $show_count = false;
	protected static $show_full  = false;

	public static function init( array $args = array() ) : void {
		$args = array_merge(
			array(
				'show_total' => false,
				'show_count' => true,
				'show_full'  => true,
			),
			$args
		);

		static::$show_total = $args['show_total'];
		static::$show_count = $args['show_count'];
		static::$show_full  = $args['show_full'];

		if ( static::$show_full ) {
			add_filter(
				'walker_nav_menu_start_el',
				array( static::class, 'add_cart' ),
				10,
				2
			);
		}

		add_filter(
			'woocommerce_add_to_cart_fragments',
			array( static::class, 'add_fragments' )
		);
		add_filter(
			'nav_menu_item_title',
			array( static::class, 'add_span' ),
			10,
			2
		);
		add_filter(
			'woocommerce_get_checkout_url',
			array( static::class, 'set_checkout_url' )
		);
		add_action(
			'wld_the_cart_count',
			array( static::class, 'the_span_count' )
		);
	}

	public static function add_fragments( array $fragments ) : array {
		if ( static::$show_total ) {
			$fragments['.cart-total'] = self::get_span_total();
		}

		if ( static::$show_count ) {
			$fragments['.cart-count'] = self::get_span_count();
		}

		if ( static::$show_full ) {
			$fragments['.menu-item.cart .menu-cart'] = self::get_html( 'cart/menu-cart.php' );

			if (
				true === wc_string_to_bool( $_POST['is_menu_cart'] ?? '' ) && // phpcs:ignore
				0 === strpos( wp_get_referer(), wc_get_cart_url() )
			) {
				WC()->cart->calculate_totals();
				$cart                   = '.woocommerce-cart .default-page .inner > .woocommerce';
				$fragments[ $cart ]     = '';
				$request_url            = $_SERVER['REQUEST_URI'];
				$_SERVER['REQUEST_URI'] = wp_get_referer();

				$fragments[ $cart ] .= '<div class="woocommerce">';
				if ( WC()->cart->is_empty() ) {
					$fragments[ $cart ] .= self::get_html( 'cart/cart-empty.php' );
				} else {
					$fragments[ $cart ] .= self::get_html( 'cart/cart.php' );
				}
				$fragments[ $cart ] .= '</div>';

				$_SERVER['REQUEST_URI'] = $request_url;
			}
		}

		if ( isset( $fragments['div.widget_shopping_cart_content'] ) ) {
			unset( $fragments['div.widget_shopping_cart_content'] );
		}

		return $fragments;
	}

	public static function add_span( string $title, $item ) : string {
		if ( static::$show_total && in_array( 'cart', $item->classes, true ) ) {
			$title .= self::get_span_total( true );
		}

		if ( static::$show_count && in_array( 'cart', $item->classes, true ) ) {
			$title .= self::get_span_count( true );
		}

		return $title;
	}

	public static function get_span_total( bool $empty = false ) : string {
		$total = $empty ? '' : wc_price( WC()->cart->get_cart_contents_total() );

		return ' <span class="cart-total">' . $total . '</span>';
	}

	public static function get_span_count( bool $empty = false ) : string {
		return ' <span class="cart-count">' . ( $empty ? '' : WC()->cart->get_cart_contents_count() ) . '</span>';
	}

	public static function the_span_count() : void {
		echo self::get_span_count();
	}

	public static function add_cart( string $item_output, $item ) : string {
		if ( in_array( 'cart', $item->classes, true ) ) {
			$item_output .= '<div class="menu-cart"></div>';
		}

		return $item_output;
	}

	public static function set_checkout_url( $url ) : string {
		if ( is_user_logged_in() || is_account_page() ) {
			return $url;
		}

		if ( doing_action( 'woocommerce_widget_shopping_cart_buttons' ) ) {
			$myaccount_page_id = wc_get_page_id( 'myaccount' );
			if ( $myaccount_page_id > 0 ) {
				$url = add_query_arg(
					array(
						'_wp_http_referer' => $url,
					),
					wc_get_page_permalink( 'myaccount' )
				);
			}
		}

		return $url;
	}

	protected static function get_html( string $template_name ) : string {
		return wc_get_template_html( $template_name );
	}
}
