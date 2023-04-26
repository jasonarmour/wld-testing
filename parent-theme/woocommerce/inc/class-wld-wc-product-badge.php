<?php /** @noinspection PhpUndefinedMethodInspection, PhpUndefinedClassInspection, PhpUndefinedFunctionInspection */


class WLD_WC_Product_Badge {
	public const CUSTOM      = 'custom';
	public const LOOP_ACTION = 'wld_show_badges_in_loop';
	public const PAGE_ACTION = 'wld_show_badges_in_page';

	protected $title;
	protected $key;
	protected $prefix;
	protected $is_custom_title;
	protected $allow_in_page;
	protected $allow_in_loop;

	public function __construct( string $title = '', bool $allow_in_page = true, bool $allow_in_loop = true ) {
		$this->title           = ucwords( wp_strip_all_tags( $title ) );
		$this->key             = sanitize_key( $this->title ) ?: static::CUSTOM;
		$this->prefix          = '_badge_' . $this->key;
		$this->is_custom_title = static::CUSTOM === $this->key;
		$this->allow_in_page   = $allow_in_page;
		$this->allow_in_loop   = $allow_in_loop;

		WLD_WC_Single_Product::$has_admin_fields = true;

		$this->hooks();
	}

	protected function hooks(): void {
		add_action(
			'woocommerce_product_options_theme',
			array( $this, 'add' )
		);
		add_action(
			'woocommerce_admin_process_product_object',
			array( $this, 'save' )
		);
		add_action(
			'woocommerce_before_shop_loop_item_title',
			array( $this, 'show' ),
			8
		);
		add_action(
			'woocommerce_before_single_product_summary',
			array( $this, 'show' ),
			8
		);
		add_action(
			static::LOOP_ACTION,
			array( $this, 'show' ),
			8
		);
		add_action(
			static::PAGE_ACTION,
			array( $this, 'show' ),
			8
		);
	}

	public function add(): void {
		$title = $this->is_custom_title ? esc_html__( 'Custom', 'parent-theme' ) : $this->title;
		$badge = esc_html__( 'Badge', 'parent-theme' );

		echo '<div class="options_group">';
		echo '<h2><strong>' . $title . ' ' . $badge . '</strong></h2>';
		if ( $this->is_custom_title ) {
			woocommerce_wp_text_input(
				array(
					'id'    => $this->prefix . '_title',
					'label' => esc_html__( 'Title', 'parent-theme' ),
				)
			);
		}
		if ( $this->allow_in_page ) {
			woocommerce_wp_checkbox(
				array(
					'id'    => $this->prefix . '_show_in_page',
					'label' => esc_html__( 'Show in Page', 'parent-theme' ),
				)
			);
		}
		if ( $this->allow_in_loop ) {
			woocommerce_wp_checkbox(
				array(
					'id'    => $this->prefix . '_show_in_loop',
					'label' => esc_html__( 'Show in Loop', 'parent-theme' ),
				)
			);
		}
		echo '</div>';
	}

	public function save( WC_Product $product ): void {
		if ( $this->is_custom_title ) {
			$key = $this->prefix . '_title';
			$product->update_meta_data( $key, wc_clean( $_POST[ $key ] ?? '' ) ); // phpcs:ignore
		}
		if ( $this->allow_in_page ) {
			$key = $this->prefix . '_show_in_loop';
			$product->update_meta_data( $key, isset( $_POST[ $key ] ) ? 'yes' : 'no' ); // phpcs:ignore
		}
		if ( $this->allow_in_loop ) {
			$key = $this->prefix . '_show_in_loop';
			$product->update_meta_data( $key, isset( $_POST[ $key ] ) ? 'yes' : 'no' ); // phpcs:ignore
		}
	}

	public function show(): void {
		/** @var WC_Product $product */
		global $product;

		$current = $product;
		if ( $current->get_parent_id() ) {
			$current = wc_get_product( $current->get_parent_id() );
		}

		$action       = current_action();
		$title        = $this->is_custom_title ? $current->get_meta( $this->prefix . '_title' ) : $this->title;
		$show_in_page = $this->allow_in_page ? $current->get_meta( $this->prefix . '_show_in_page' ) : 'no';
		$show_in_loop = $this->allow_in_loop ? $current->get_meta( $this->prefix . '_show_in_loop' ) : 'no';

		if (
			$title && (
				(
					'yes' === $show_in_page && (
						'woocommerce_before_single_product_summary' === $action ||
						static::PAGE_ACTION === $action ||
						static::PAGE_ACTION . '_' . $this->key === $action
					)
				) || (
					'yes' === $show_in_loop && (
						'woocommerce_before_shop_loop_item_title' === $action ||
						static::LOOP_ACTION === $action ||
						static::LOOP_ACTION . '_' . $this->key === $action
					)
				)
			)
		) {
			echo '<span class="badge badge-' . $this->key . '">' . esc_html( $title ) . '</span>';
		}
	}
}
