<?php


class WLD_WC_Checkout_Blocks {
	protected static $start_blocks   = 0;
	protected static $current_block  = 0;
	protected static $editable_block = 1;
	protected static $ajax_fragments = false;

	public static function init() : void {
		add_action(
			'woocommerce_checkout_before_customer_details',
			array( static::class, 'the_blocks' )
		);
		add_action(
			'block_customer_details',
			array( static::class, 'the_customer_details_fields' )
		);
		add_action(
			'block_shipping_details',
			array( WC_Checkout::instance(), 'checkout_form_shipping' )
		);
		add_action(
			'block_shipping_options',
			array( static::class, 'the_shipping_options' )
		);
		add_action(
			'block_billing_details',
			array( WC_Checkout::instance(), 'checkout_form_billing' )
		);
		add_action(
			'block_payment_type',
			'woocommerce_checkout_payment'
		);
		add_action(
			'block_create_account',
			array( static::class, 'the_customer_create_fields' )
		);
		add_action(
			'wp_ajax_get_block_format_content',
			array( self::class, 'ajax_get_block_format_content' )
		);
		add_action(
			'wp_ajax_nopriv_get_block_format_content',
			array( self::class, 'ajax_get_block_format_content' )
		);
		add_filter(
			'woocommerce_update_order_review_fragments',
			array( static::class, 'add_fragments' )
		);
		add_action(
			'woocommerce_checkout_before_order_review_heading',
			array( self::class, 'order_review_wrapper_start' ),
			1
		);
		add_action(
			'woocommerce_checkout_after_order_review',
			array( self::class, 'order_review_wrapper_end' ),
			PHP_INT_MAX
		);
		add_filter(
			'woocommerce_checkout_registration_enabled',
			array( self::class, 'disable_registration_in_billing' )
		);
		add_filter(
			'woocommerce_checkout_posted_data',
			array( static::class, 'set_email_in_checkout_posted_data' )
		);
		add_action(
			'woocommerce_review_order_before_cart_contents',
			static function () {
				ob_start();
			},
			1
		);
		add_action(
			'woocommerce_review_order_after_cart_contents',
			array( self::class, 'replace_products_in_review_order' ),
			PHP_INT_MAX
		);
		add_action(
			'woocommerce_review_order_before_shipping',
			static function () {
				// phpcs:disable
				$is_woo_ajax = doing_action( 'wp_ajax_woocommerce_update_order_review' ) ||
							   doing_action( 'wp_ajax_nopriv_woocommerce_update_order_review' ) ||
							   doing_action( 'wc_ajax_update_order_review' );
				// phpcs:enable

				if ( ( $is_woo_ajax && ! self::$ajax_fragments ) || doing_action( 'woocommerce_checkout_order_review' ) ) {
					ob_start();
				}
			},
			1
		);
		add_action(
			'woocommerce_review_order_after_shipping',
			static function () {
				// phpcs:disable
				$is_woo_ajax = doing_action( 'wp_ajax_woocommerce_update_order_review' ) ||
							   doing_action( 'wp_ajax_nopriv_woocommerce_update_order_review' ) ||
							   doing_action( 'wc_ajax_update_order_review' );
				// phpcs:enable

				if ( ( $is_woo_ajax && ! self::$ajax_fragments ) || doing_action( 'woocommerce_checkout_order_review' ) ) {
					ob_end_clean();
				}
			},
			PHP_INT_MAX
		);
		add_filter(
			'woocommerce_cart_needs_shipping_address',
			'__return_true'
		);
		add_filter(
			'woocommerce_enable_order_notes_field',
			'__return_false'
		);
		remove_action(
			'woocommerce_checkout_billing',
			array( WC_Checkout::instance(), 'checkout_form_billing' )
		);
		remove_action(
			'woocommerce_checkout_shipping',
			array( WC_Checkout::instance(), 'checkout_form_shipping' )
		);
		remove_action(
			'woocommerce_checkout_order_review',
			'woocommerce_checkout_payment', 20
		);
		remove_action(
			'woocommerce_thankyou',
			'woocommerce_order_details_table'
		);
	}

	public static function the_blocks() : void {
		echo '<div class="checkout-blocks">';
		self::the_block_customer_details();
		self::the_block_shipping_details();
		self::the_block_shipping_options();
		while ( self::wrap_blocks( esc_html__( 'Payment', 'parent-theme' ) ) ) {
			self::the_block_billing_details();
			self::the_block_payment_type();
		}
		self::the_block_create_account();
		wc_get_template( 'checkout/submit.php' );
		wc_get_template( 'checkout/terms.php' );
		echo '</div>';
	}

	public static function the_customer_details_fields() : void {
		$key   = 'billing_email';
		$field = array(
			'type'         => 'email',
			'label'        => esc_html__( 'Email address', 'woocommerce' ),
			'placeholder'  => esc_html__( 'Email address', 'woocommerce' ),
			'required'     => true,
			'autocomplete' => 'email',
			'class'        => array( 'form-row-wide' ),
			'label_class'  => array( 'screen-reader-text' ),
			'validate'     => array( 'email' ),
		);

		woocommerce_form_field( $key, $field, WC()->checkout()->get_value( $key ) );
	}

	public static function the_shipping_options() : void {
		if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) {
			echo '<table>';
			do_action( 'woocommerce_review_order_before_shipping' );
			wc_cart_totals_shipping_html();
			do_action( 'woocommerce_review_order_after_shipping' );
			echo '</table>';
		}
	}

	public static function the_customer_create_fields() : void {
		$checkout = WC()->checkout();

		do_action( 'woocommerce_before_checkout_registration_form', $checkout );
		if ( $checkout->get_checkout_fields( 'account' ) ) {
			echo '<div class="create-account">';
			foreach ( $checkout->get_checkout_fields( 'account' ) as $key => $field ) {
				woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
			}
			echo '</div>';
		}
		do_action( 'woocommerce_after_checkout_registration_form', $checkout );
	}

	public static function the_block( array $args ) : void {
		self::$current_block ++;

		$args = array_merge(
			array(
				'type'      => '',
				'title'     => '',
				'action'    => '',
				'content'   => '',
				'static'    => false,
				'sub_block' => false,
				'save_text' => '',
			),
			$args
		);

		if ( self::$current_block === self::$editable_block && ( $args['content'] || $args['static'] ) ) {
			self::$editable_block ++;
			$class = 'block-done';
		} elseif ( self::$current_block > self::$editable_block ) {
			$class = 'block-empty';
		} else {
			$class = 'block-edited';
		}

		$edit_text = esc_html__( 'Change', 'parent-theme' );
		$classes   = array(
			'block',
			'block-' . $args['type'],
			$class,
			$args['content'] ? 'block-has-content' : 'block-empty-content',
			$args['static'] ? 'block-static' : 'block-mutable',
		);
		?>
		<div class="<?php echo implode( ' ', $classes ); ?>"
			 data-block-type="<?php echo str_replace( '-', '_', $args['type'] ); ?>">
			<?php if ( $args['sub_block'] ) : ?>
				<h3 class="subtitle"><?php echo $args['title']; ?></h3>
			<?php else : ?>
				<h2 class="title"><?php echo $args['title']; ?></h2>
			<?php endif; ?>
			<?php if ( $args['static'] ) : ?>
				<div class="block-format-content">
					<div class="block-content"><?php do_action( $args['action'] ); ?></div>
				</div>
			<?php else : ?>
				<div class="block-edit-content">
					<div class="content"><?php do_action( $args['action'] ); ?></div>
					<?php if ( $args['save_text'] ) : ?>
						<div class="actions">
							<button type="button" class="btn btn-save"><?php echo $args['save_text']; ?></button>
						</div>
					<?php endif; ?>
				</div>
				<div class="block-format-content">
					<div class="content"><?php echo $args['content']; ?></div>
					<div class="actions">
						<button type="button" class="btn btn-edit"><?php echo $edit_text; ?></button>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	public static function wrap_blocks( string $title ) : bool {
		if ( 0 === self::$start_blocks ) {
			self::$start_blocks = self::$current_block;
			ob_start();

			return true;
		}

		$blocks = ob_get_clean();

		if ( self::$start_blocks > self::$editable_block ) {
			$class = 'block-empty';
		} elseif ( 0 === self::$editable_block ) {
			$class = 'block-done';
		} else {
			$class = 'block-edited';
		}

		echo '<div class="blocks ' . $class . '"><h2 class="title">' . $title . '</h2>' . $blocks . '</div>';

		self::$start_blocks = false;

		return false;
	}

	public static function the_block_customer_details() : void {
		self::the_block(
			array(
				'type'      => 'customer-details',
				'title'     => esc_html__( 'Email Address', 'parent-theme' ),
				'action'    => 'block_customer_details',
				'content'   => wpautop( WC()->checkout()->get_value( 'billing_email' ) ),
				'save_text' => esc_html__( 'Add my Email', 'parent-theme' ),
			)
		);
	}

	public static function the_block_shipping_details() : void {
		ob_start();
		self::the_block(
			array(
				'type'      => 'shipping-details',
				'title'     => esc_html__( 'Delivery Address', 'parent-theme' ),
				'action'    => 'block_shipping_details',
				'content'   => self::get_formatted_address( 'shipping' ),
				'save_text' => esc_html__( 'Ship to this Address', 'parent-theme' ),
			)
		);

		echo preg_replace(
			'/<h3 id="ship-to-different-address".+<\/h3>/sU',
			'<input type="hidden" name="ship_to_different_address" value="1" />',
			ob_get_clean()
		);
	}

	public static function the_block_shipping_options() : void {
		self::the_block(
			array(
				'type'   => 'shipping-options-details',
				'title'  => esc_html__( 'Delivery Options', 'parent-theme' ),
				'action' => 'block_shipping_options',
				'static' => true,
			)
		);
	}

	public static function the_block_billing_details() : void {
		self::the_block(
			array(
				'type'      => 'billing-details',
				'title'     => esc_html__( 'Billing Address', 'parent-theme' ),
				'action'    => 'block_billing_details',
				'content'   => self::get_formatted_address( 'billing' ),
				'sub_block' => true,
				'save_text' => esc_html__( 'Save Billing Address', 'parent-theme' ),
			)
		);
	}

	public static function the_block_payment_type() : void {
		self::the_block(
			array(
				'type'      => 'payment-type-details',
				'title'     => esc_html__( 'Payment Type', 'parent-theme' ),
				'action'    => 'block_payment_type',
				'content'   => '',
				'sub_block' => true,
				'static'    => true,
				'save_text' => esc_html__( 'Use this Card', 'parent-theme' ),
			)
		);
	}

	public static function the_block_create_account() : void {
		if ( ! is_user_logged_in() && WC()->checkout()->is_registration_enabled() ) {
			self::the_block(
				array(
					'type'   => 'create-account',
					'title'  => esc_html__( 'Create an Account', 'parent-theme' ),
					'action' => 'block_create_account',
				)
			);
		}
	}

	public static function ajax_get_block_format_content() : void {
		check_ajax_referer( 'ajax-nonce', 'nonce' );

		$errors = new WP_Error();
		$type   = $_POST['type'] ?? '';
		$fields = $_POST['fields'] ?? array();

		if ( empty( $type ) || ! is_callable( array( self::class, 'the_block_' . $type ) ) ) {
			$errors->add(
				'invalid_block_type',
				esc_html__( 'Invalid Block Type', 'parent-theme' )
			);
		}

		if ( 'customer_details' === $type ) {
			self::validate_customer_details( $fields, $errors );
		} elseif ( 'billing_details' === $type || 'shipping_details' === $type ) {
			$fieldset_key = 'billing_details' === $type ? 'billing' : 'shipping';
			self::validate_customer_address( $fieldset_key, $fields, $errors );
		}

		if ( $errors->has_errors() ) {
			$errors_data = array();
			foreach ( $errors->errors as $code => $messages ) {
				$data = $errors->get_error_data( $code );
				foreach ( $messages as $message ) {
					$errors_data[] = array(
						'id'      => $data['id'] ?? null,
						'message' => $message,
					);
				}
			}

			wp_send_json_error( $errors_data );
		} else {
			ob_start();

			$_POST                                   = (array) $fields;
			$_POST['_ajax_get_block_format_content'] = true;

			call_user_func( array( self::class, 'the_block_' . $type ) );

			$content = ob_get_clean();
			wp_send_json_success( array( 'content' => $content ) );
		}
	}

	public static function add_fragments( array $fragments ) : array {
		self::$ajax_fragments = true;

		ob_start();
		self::the_shipping_options();

		$fragments['.block-shipping-options-details .block-content'] = sprintf(
			'<div class="block-content">%s</div>',
			ob_get_clean()
		);

		$fragments['.place-order'] = wc_get_template_html( 'checkout/submit.php' );

		self::$ajax_fragments = false;

		return $fragments;
	}

	public static function order_review_wrapper_start() : void {
		echo '<div class="order-review">';
	}

	public static function order_review_wrapper_end() : void {
		echo '</div>';
	}

	public static function disable_registration_in_billing( bool $enabled ) : bool {
		if ( doing_action( 'block_billing_details' ) ) {
			return false;
		}

		return $enabled;
	}

	public static function set_email_in_checkout_posted_data( array $data ) : array {
		if ( empty( $data['billing_email'] ) ) {
			$data['billing_email'] = wc_clean( wp_unslash( $_POST['billing_email'] ?? '' ) );
		}

		return $data;
	}

	public static function replace_products_in_review_order() : void {
		ob_end_clean();
		echo '<tr><td colspan="2">';
		woocommerce_mini_cart();
		echo '</td></tr>';
	}

	public static function get_formatted_address( string $address_type ) : string {
		if ( empty( $_POST['_ajax_get_block_format_content'] ) ) { // phpcs:ignore
			return wc_get_account_formatted_address( $address_type );
		}

		/** @noinspection PhpUnhandledExceptionInspection */
		$customer = new WC_Customer( get_current_user_id() );
		$address  = array();
		$fieldset = WC()->checkout()->get_checkout_fields( $address_type );
		$data     = wc_clean( $_POST ); // phpcs:ignore

		foreach ( $fieldset as $key => $field ) {
			$_key             = (string) str_replace( $address_type . '_', '', $key );
			$address[ $_key ] = $data[ $key ] ?? '';
		}

		return WC()->countries->get_formatted_address(
			apply_filters(
				'woocommerce_my_account_my_address_formatted_address',
				$address,
				$customer->get_id(),
				$address_type
			)
		);
	}

	public static function validate_customer_details( array $data, WP_Error $errors ) : void {
		$key         = 'billing_email';
		$field_label = esc_html__( 'Email address', 'woocommerce' );
		$value       = sanitize_email( $data[ $key ] ?? '' );
		if ( empty( $value ) ) {
			$errors->add(
				$key . '_required',
				apply_filters(
					'woocommerce_checkout_required_field_notice',
					sprintf( /* translators: %s: field label*/
						__( '%s is a required field.', 'woocommerce' ),
						'<strong>' . esc_html( $field_label ) . '</strong>'
					),
					$field_label
				),
				array( 'id' => $key )
			);
		} elseif ( ! is_email( $value ) ) {
			$errors->add(
				$key . '_validation',
				sprintf( /* translators: %s: email address */
					__( '%s is not a valid email address.', 'woocommerce' ),
					'<strong>' . esc_html( $field_label ) . '</strong>'
				),
				array( 'id' => $key )
			);
		} elseif ( ! is_user_logged_in() && email_exists( $value ) ) {
			$errors->add(
				'registration-error-email-exists',
				apply_filters(
					'woocommerce_registration_error_email_exists',
					__(
						'An account is already registered with your email address. Please log in.',
						'woocommerce'
					),
					$value
				),
				array( 'id' => $key )
			);
		}
	}

	public static function validate_customer_address( string $fieldset_key, array $data, WP_Error $errors ) : void {
		$fieldset = WC()->checkout()->get_checkout_fields( $fieldset_key );
		foreach ( $fieldset as $key => $field ) {
			if ( ! isset( $data[ $key ] ) ) {
				continue;
			}

			$required    = ! empty( $field['required'] );
			$format      = array_filter( isset( $field['validate'] ) ? (array) $field['validate'] : array() );
			$field_label = $field['label'] ?? '';

			switch ( $fieldset_key ) {
				case 'shipping':
					$field_label = sprintf( /* translators: %s: field name */
						_x( 'Shipping %s', 'checkout-validation', 'woocommerce' ),
						$field_label
					);
					break;
				case 'billing':
					$field_label = sprintf( /* translators: %s: field name */
						_x( 'Billing %s', 'checkout-validation', 'woocommerce' ),
						$field_label
					);
					break;
			}

			if ( in_array( 'postcode', $format, true ) ) {
				$country = $data[ $fieldset_key . '_country' ] ?? '';
				if ( empty( $country ) ) {
					$country = WC()->customer->{"get_{$fieldset_key}_country"}();
				}

				$data[ $key ] = wc_format_postcode( $data[ $key ], $country );

				if (
					'' !== $data[ $key ] &&
					! WC_Validation::is_postcode( $data[ $key ], $country )
				) {
					if ( 'IE' === $country ) {
						/** @noinspection HtmlUnknownTarget */
						$postcode_validation_notice = sprintf(
							__( // translators: %1$s: field name %2$s: finder.eircode.ie URL
								'%1$s is not valid. You can look up the correct Eircode
								 <a target="_blank" href="%2$s">here</a>.',
								'woocommerce'
							),
							'<strong>' . esc_html( $field_label ) . '</strong>',
							'https://finder.eircode.ie'
						);
					} else {
						$postcode_validation_notice = sprintf( /* translators: %s: field name */
							__( '%s is not a valid postcode / ZIP.', 'woocommerce' ),
							'<strong>' . esc_html( $field_label ) . '</strong>'
						);
					}

					$errors->add(
						$key . '_validation',
						apply_filters(
							'woocommerce_checkout_postcode_validation_notice',
							$postcode_validation_notice,
							$country,
							$data[ $key ]
						),
						array( 'id' => $key )
					);
				}
			}

			if (
				'' !== $data[ $key ] &&
				in_array( 'phone', $format, true ) &&
				! WC_Validation::is_phone( $data[ $key ] )
			) {
				$errors->add(
					$key . '_validation',
					sprintf( /* translators: %s: phone number */
						__( '%s is not a valid phone number.', 'woocommerce' ),
						'<strong>' . esc_html( $field_label ) . '</strong>'
					),
					array( 'id' => $key )
				);
			}

			if ( '' !== $data[ $key ] && in_array( 'email', $format, true ) ) {
				$email_is_valid = is_email( $data[ $key ] );
				$data[ $key ]   = sanitize_email( $data[ $key ] );

				if ( ! $email_is_valid ) {
					$errors->add(
						$key . '_validation',
						sprintf( /* translators: %s: email address */
							__( '%s is not a valid email address.', 'woocommerce' ),
							'<strong>' . esc_html( $field_label ) . '</strong>'
						),
						array( 'id' => $key )
					);
					continue;
				}
			}

			if ( '' !== $data[ $key ] && in_array( 'state', $format, true ) ) {
				$country = $data[ $fieldset_key . '_country' ] ?? '';
				if ( empty( $country ) ) {
					$country = WC()->customer->{"get_{$fieldset_key}_country"}();
				}

				$valid_states = WC()->countries->get_states( $country );

				if ( ! empty( $valid_states ) && is_array( $valid_states ) && count( $valid_states ) > 0 ) {
					$valid_state_values = array_map(
						'wc_strtoupper',
						array_flip( array_map( 'wc_strtoupper', $valid_states ) )
					);
					$data[ $key ]       = wc_strtoupper( $data[ $key ] );

					if ( isset( $valid_state_values[ $data[ $key ] ] ) ) {
						// With this part we consider state value to be valid as well,
						// convert it to the state key for the valid_states check below.
						$data[ $key ] = $valid_state_values[ $data[ $key ] ];
					}

					if ( ! in_array( $data[ $key ], $valid_state_values, true ) ) {
						$errors->add(
							$key . '_validation',
							sprintf(
								__( /* translators: %1$s state field, %2$s valid states */
									'%1$s is not valid. Please enter one of the following: %2$s',
									'woocommerce'
								),
								'<strong>' . esc_html( $field_label ) . '</strong>',
								implode( ', ', $valid_states )
							),
							array( 'id' => $key )
						);
					}
				}
			}

			if ( $required && '' === $data[ $key ] ) {
				/* translators: %s: field name */
				$errors->add(
					$key . '_required',
					apply_filters(
						'woocommerce_checkout_required_field_notice',
						sprintf( /* translators: %s: field label*/
							__( '%s is a required field.', 'woocommerce' ),
							'<strong>' . esc_html( $field_label ) . '</strong>'
						),
						$field_label
					),
					array( 'id' => $key )
				);
			}
		}
	}
}
