<?php


class WLD_WC_Cart {
	public static function init() : void {
		add_action(
			'woocommerce_before_cart_table',
			array( self::class, 'add_title' )
		);
		add_action(
			'woocommerce_before_shipping_calculator',
			static function () {
				ob_start();
			}
		);
		add_action(
			'woocommerce_after_shipping_calculator',
			array( self::class, 'add_address_field_in_shipping_calculator' )
		);
		add_action(
			'woocommerce_calculated_shipping',
			array( self::class, 'save_address_field_in_shipping_calculator' )
		);
		add_action(
			'woocommerce_after_cart_totals',
			array( self::class, 'the_card_icons' )
		);
	}

	public static function add_title() : void {
		echo '<h2>' . __( 'My Cart', 'parent-theme' ) . '</h2>';
	}

	public static function add_address_field_in_shipping_calculator() : void {
		$value       = WC()->customer->get_shipping_address_1();
		$placeholder = __( 'House number and street name', 'parent-theme' );
		echo str_replace(
			'<p><button type="submit"',
			'
			<p class="form-row form-row-wide" id="calc_shipping_address_1_field">
				<input type="text" class="input-text " value="' . $value . '" placeholder="' . $placeholder . '"
					name="calc_shipping_address_1" id="calc_shipping_address_1">
			</p>
			<p><button type="submit"',
			ob_get_clean()
		);
	}

	public static function save_address_field_in_shipping_calculator() : void {
		$address_1 = wc_clean( wp_unslash( $_POST['calc_shipping_address_1'] ?? '' ) ); // phpcs:ignore
		if ( $address_1 ) {
			WC()->customer->set_shipping_address_1( $address_1 );
			if ( ! WC()->customer->get_billing_first_name() ) {
				WC()->customer->set_billing_address_1( $address_1 );
			}
		}

		WC()->customer->save();
	}

	public static function the_card_icons() : void {
		/** @var WC_Payment_Gateway[] $available_gateways */
		$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
		$icons              = '';
		foreach ( $available_gateways as $gateway ) {
			$icons .= $gateway->get_icon();
		}

		if ( $icons ) {
			echo '<h3>' . esc_html__( 'We Accept', 'parent-theme' ) . '</h3>';
			echo '<div class="icons">' . $icons . '</div>';
		}
	}
}
