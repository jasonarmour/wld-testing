<?php


class WLD_WC_My_Account {
	public static function init() : void {
		add_filter(
			'woocommerce_login_redirect',
			array( self::class, 'login_redirect' ),
			10,
			2
		);
		add_filter(
			'woocommerce_process_registration_errors',
			array( self::class, 'validate_register_fields' )
		);
		add_action(
			'woocommerce_created_customer',
			array( self::class, 'save_register_fields' )
		);
		remove_action(
			'woocommerce_register_form',
			'wc_registration_privacy_policy_text',
			20
		);
	}

	public static function login_redirect( string $redirect, WP_User $user ) : string {
		if (
			wc_get_checkout_url() !== $redirect &&
			( $user->has_cap( 'edit_posts' ) || $user->has_cap( 'manage_woocommerce' ) )
		) {
			$redirect = admin_url();
		}

		return $redirect;
	}

	public static function validate_register_fields( WP_Error $errors ) : WP_Error {
		$first_name = $_POST['first_name'] ?? ''; // phpcs:ignore
		if ( empty( $first_name ) ) {
			$errors->add(
				'required',
				__( 'Please enter first name.', 'parent-theme' )
			);
		}

		$last_name = $_POST['last_name'] ?? ''; // phpcs:ignore
		if ( empty( $last_name ) ) {
			$errors->add(
				'required',
				__( 'Please enter last name.', 'parent-theme' )
			);
		}

		$email_1 = $_POST['email'] ?? ''; // phpcs:ignore
		$email_2 = $_POST['email_confirm'] ?? ''; // phpcs:ignore
		if ( empty( $email_2 ) ) {
			$errors->add(
				'required',
				__( 'Please enter confirm email.', 'parent-theme' )
			);
		} elseif ( $email_1 !== $email_2 ) {
			$errors->add(
				'email',
				__( 'Confirm email do not match email.', 'parent-theme' )
			);
		}

		if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) {
			$password_1 = $_POST['password'] ?? ''; // phpcs:ignore
			$password_2 = $_POST['password_confirm'] ?? ''; // phpcs:ignore

			if ( empty( $password_1 ) || empty( $password_2 ) ) {
				$errors->add(
					'password',
					__( 'Please enter password.', 'parent-theme' )
				);
			} elseif ( $password_1 !== $password_2 ) {
				$errors->add(
					'password',
					__( 'Confirm password do not match password.', 'parent-theme' )
				);
			}
		}

		return $errors;
	}

	public static function save_register_fields( $customer_id ) : void {
		try {
			$customer = new WC_Customer( $customer_id );
		} catch ( Exception $e ) {
			return;
		}

		$email      = wc_clean( $_POST['email'] ?? '' ); // phpcs:ignore
		$first_name = wc_clean( $_POST['first_name'] ?? '' ); // phpcs:ignore
		$last_name  = wc_clean( $_POST['last_name'] ?? '' ); // phpcs:ignore

		$customer->set_first_name( $first_name );
		$customer->set_last_name( $last_name );
		$customer->set_billing_first_name( $first_name );
		$customer->set_billing_last_name( $last_name );
		$customer->set_billing_email( $email );
		$customer->set_display_name( $customer->get_first_name() . ' ' . $customer->get_last_name() );
		$customer->save();
	}
}
