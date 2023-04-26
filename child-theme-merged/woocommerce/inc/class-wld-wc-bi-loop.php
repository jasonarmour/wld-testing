<?php

class WLD_WC_BI_Loop {
	public static function init(): void {
		add_action(
			'woocommerce_after_shop_loop_item_title',
			array( self::class, 'display_excerpt_in_loop' ),
			50
		);

		add_action(
			'woocommerce_shop_loop_item_title',
			array( self::class, 'display_product_title_cat' ),
			8
		);

		add_action(
			'woocommerce_after_shop_loop_item',
			array( self::class, 'display_product_see_more' )
		);

		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price' );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
	}

	public static function display_excerpt_in_loop(): void {
		the_excerpt();

	}

	public static function display_product_see_more(): void {
		echo '<a class="link" href="' . get_the_permalink() . '">' . esc_html__( 'Learn more', 'parent-theme' ) . '</a>';
	}

	public static function display_product_title_cat(): void {
		global $post;

		$terms = get_the_terms( $post->ID, 'topic' );

		if ( $terms && ! is_wp_error( $terms ) ) :

			$cat_links = array();

			foreach ( $terms as $term ) {

				$cat_links[] = $term->name;

			}

			$on_cat = implode( ' | ', $cat_links );

			?>

			<div class="label-group">
				<?php echo $on_cat; ?>

			</div>
		<?php
		endif;
	}

	public static function display_product_title_attribute(): void {
		global $post;

		$attr_type = get_post_meta( $post->ID, '_type', true );

		$attr_type_name = '';

		$meta_options = get_field( 'wld_meta_attributes', 'option' );

		foreach ( $meta_options as $meta ) {
			if ( '_type' === '_' . $meta['name'] ) {
				$opt_str = explode( PHP_EOL, $meta['options'] );
				foreach ( $opt_str as $option ) {
					$lines = explode( ' : ', $option );
					if ( $lines[0] === $attr_type ) {
						$attr_type_name = $lines[1];
					}
				}
			}
		}
		$on_attr = $attr_type_name ?? '';

		?>


		<div class="label-group">
			<?php echo $on_attr; ?>

		</div>
		<?php
	}

	public static function display_product_title_cat_id( int $id ): void {

		$terms = get_the_terms( $id, 'topic' );

		if ( $terms && ! is_wp_error( $terms ) ) :

			$cat_links = array();

			foreach ( $terms as $term ) {

				$cat_links[] = $term->name;

			}

			$on_cat = implode( ' | ', $cat_links );

			?>

			<div class="label-group">
				<?php echo $on_cat; ?>

			</div>

		<?php
		endif;
	}
}

