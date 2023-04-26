<?php /** @noinspection PhpUndefinedFunctionInspection */

/**
 * @link    ./wp-content/woocommerce/templates/archive-product.php
 * @link    ./wp-content/woocommerce/templates/single-product.php
 *
 * Hook: woocommerce_sidebar.
 * @see     woocommerce_get_sidebar - 10
 *
 * @version 4.3.0
 */
class WLD_WC_Sidebar {
	public static function init( $enable_widgets = false ): void {
		remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar' );

		if ( $enable_widgets ) {
			add_action(
				'widgets_init',
				array( static::class, 'register_sidebar' ),
				5
			);

			add_action(
				'woocommerce_after_main_content',
				array( static::class, 'the_sidebar' )
			);
		}
	}

	public static function the_sidebar(): void {
		if ( ! is_product() ) {
			dynamic_sidebar( 'shop' );
		}
	}

	public static function register_sidebar(): void {
		register_sidebar(
			array(
				'id'            => 'shop',
				'name'          => __( 'Shop Sidebar', 'parent-theme' ),
				'description'   => __( 'This is a sidebar for shop widgets', 'parent-theme' ),
				'before_widget' => '',
				'after_widget'  => '',
				'before_title'  => '<h3>',
				'after_title'   => '</h3>',
			)
		);
	}
}
