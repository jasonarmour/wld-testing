<?php /** @noinspection UnknownInspectionInspection, PhpUnused */


class WLD_WC_Search_In_Menu {
	public static $old_after;

	public static function init(): void {
		add_filter(
			'nav_menu_item_title',
			array( static::class, 'add_form_in_item' ),
			10,
			3
		);
	}

	public static function add_form_in_item( string $title, $item, $args ): string {
		if ( is_string( self::$old_after ) ) {
			$args->after     = self::$old_after;
			self::$old_after = null;
		}

		if ( in_array( 'search', $item->classes, true ) ) {
			self::$old_after = $args->after;

			$args->after .= static::get_form();
		}

		return $title;
	}

	public static function get_form(): string {
		// YITH WooCommerce Ajax Search https://wordpress.org/plugins/yith-woocommerce-ajax-search/
		if ( shortcode_exists( 'yith_woocommerce_ajax_search' ) ) {
			return apply_shortcodes( '[yith_woocommerce_ajax_search]' );
		}

		return (string) get_search_form( array( 'echo' => false ) );
	}
}
