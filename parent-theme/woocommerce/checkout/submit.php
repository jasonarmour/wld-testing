<div class="form-row place-order">
	<noscript>
		<?php
		printf( /* translators: $1 and $2 opening and closing emphasis tags respectively */
			esc_html__(
				'Since your browser does not support JavaScript, or it is disabled,
				please ensure you click the %1$sUpdate Totals%2$s button before placing your order.
				You may be charged more than the amount stated above if you fail to do so.',
				'woocommerce'
			),
			'<em>',
			'</em>'
		);
		?>
		<br/>
		<button type="submit"
				class="button alt"
				name="woocommerce_checkout_update_totals"
				value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>">
			<?php esc_html_e( 'Update totals', 'woocommerce' ); ?>
		</button>
	</noscript>
	<?php do_action( 'woocommerce_review_order_before_submit' ); ?>
	<?php
	$order_button_text = apply_filters(
		'woocommerce_order_button_text',
		__( 'Place order', 'woocommerce' )
	);
	echo apply_filters(
		'woocommerce_order_button_html',
		'<button
					type="submit"
					class="button alt"
					name="woocommerce_checkout_place_order"
					id="place_order"
					value="' . esc_attr( $order_button_text ) . '"
					data-value="' . esc_attr( $order_button_text ) . '">'
		. esc_html( $order_button_text ) .
		'</button>'
	);
	do_action( 'woocommerce_review_order_after_submit' );
	wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' );
	?>
</div>
