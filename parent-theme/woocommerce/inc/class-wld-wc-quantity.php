<?php /** @noinspection UnknownInspectionInspection, PhpUnused, PhpUndefinedClassInspection, PhpUndefinedMethodInspection */


class WLD_WC_Quantity {
	protected static $args;

	public static function init( array $args = array() ) : void {
		static::$args = array_merge(
			array(
				'hide_product_name'     => true,
				'allow_in_product_page' => false,
				'label'                 => 'Qty',
			),
			$args
		);

		add_action(
			'woocommerce_before_quantity_input_field',
			array( static::class, 'before' )
		);
		add_action(
			'woocommerce_after_quantity_input_field',
			array( static::class, 'after' )
		);
		add_filter(
			'woocommerce_quantity_input_args',
			array( static::class, 'change_args' ),
			PHP_INT_MAX
		);
		add_filter(
			'woocommerce_cart_item_quantity',
			array( static::class, 'replace_label' ),
			PHP_INT_MAX
		);
	}

	public static function change_args( array $args ) : array {
		if ( true === static::$args['hide_product_name'] ) {
			$args['product_name'] = '';
		}

		if ( false === static::$args['allow_in_product_page'] && is_single() && empty( wc_get_loop_prop( 'name' ) ) ) {
			$args['max_value'] = 1;
			$args['min_value'] = 1;
		}

		return $args;
	}

	public static function before() : void {
		$title = __( 'Minus Quantity', 'parent-theme' );
		?>
		<button type="button" class="qty-btn minus" title="<?php echo esc_attr( $title ); ?>">-</button>
		<?php
	}

	public static function after() : void {
		$title = __( 'Plus Quantity', 'parent-theme' );
		?>
		<button type="button" class="qty-btn plus" title="<?php echo esc_attr( $title ); ?>">+</button>
		<?php
	}

	public static function replace_label( string $product_quantity ) : string {
		if ( empty( static::$args['label'] ) ) {
			return $product_quantity;
		}

		return preg_replace(
			array( '/(>[^>]+<\/label>)/s', '/(screen-reader-text)/' ),
			array( '>' . static::$args['label'] . '</label>', '' ),
			$product_quantity
		);
	}
}
