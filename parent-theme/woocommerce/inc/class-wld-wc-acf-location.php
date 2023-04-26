<?php /** @noinspection PhpUnused, UnknownInspectionInspection */


class WLD_WC_ACF_Location {
	public static function init(): void {
		add_filter(
			'acf/location/rule_types',
			array( static::class, 'add_rule_type' )
		);
		add_filter(
			'acf/location/rule_values/woo_page_type',
			array( static::class, 'add_rule_values' )
		);
		add_filter(
			'acf/location/rule_match/woo_page_type',
			array( static::class, 'add_rule_match' ),
			10,
			3
		);
	}

	public static function add_rule_type( array $groups ): array {
		$group                             = __( 'WooCommerce', 'parent-theme' );
		$groups[ $group ]['woo_page_type'] = __( 'Woo Page Type', 'parent-theme' );

		return $groups;
	}

	public static function add_rule_values( array $choices ): array {
		$types = array(
			'shop'      => __( 'Shop', 'parent-theme' ),
			'cart'      => __( 'Cart', 'parent-theme' ),
			'checkout'  => __( 'Checkout', 'parent-theme' ),
			'myaccount' => __( 'My Account', 'parent-theme' ),
		);

		foreach ( $types as $key => $title ) {
			$choices[ $key ] = $title;
		}

		return $choices;
	}

	public static function add_rule_match( bool $result, array $rule, array $screen ): bool {
		$post_id = absint( $screen['post_id'] ?? 0 );
		if ( $post_id && 'woo_page_type' === $rule['param'] ) {
			if ( '==' === $rule['operator'] ) {
				$result = self::is_page( $post_id, $rule['value'] );
			} else {
				$result = ! self::is_page( $post_id, $rule['value'] );
			}
		}

		return $result;
	}

	public static function is_page( int $post_id, string $value ): bool {
		return function_exists( 'wc_get_page_id' ) ? wc_get_page_id( $value ) === $post_id : false;
	}
}
