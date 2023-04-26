<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @var $order WC_Order
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-order">
	<div class="content">
		<?php if ( $order ) : ?>
			<?php do_action( 'woocommerce_before_thankyou', $order->get_id() ); ?>
			<?php if ( $order->has_status( 'failed' ) ) : ?>
				<h2 class="title"><?php esc_html_e( 'Unfortunately.', 'parent-theme' ); ?></h2>
				<p class="woocommerce-thankyou-order-failed">
					<?php
					esc_html_e(
						'Your order cannot be processed as the originating bank/merchant has declined your transaction.
						Please attempt your purchase again.',
						'woocommerce'
					);
					?>
				</p>
				<p class="woocommerce-thankyou-order-failed-actions">
					<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>"
					   class="button pay"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a>
					<?php if ( is_user_logged_in() ) : ?>
						<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"
						   class="button pay"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a>
					<?php endif; ?>
				</p>
			<?php else : ?>
				<span class="icon"></span>
				<h2 class="title"><?php esc_html_e( 'Thank you!', 'parent-theme' ); ?></h2>
				<p class="woocommerce-thankyou-order-received">
					<?php
					echo apply_filters(
						'woocommerce_thankyou_order_received_text',
						esc_html__( 'Your order was completed successfully!', 'parent-theme' ),
						$order
					);
					?>
				</p>
				<?php if ( $order->get_order_number() ) : ?>
					<p class="woocommerce-order-overview__order order">
						<?php esc_html_e( 'Order #', 'parent-theme' ); ?>
						<?php echo $order->get_order_number(); ?>
					</p>
				<?php endif; ?>
				<p>
					<?php
					esc_html_e(
						'An email receipt, including the details about your order,
						has been sent to the email address provided. Please keep it for your records.',
						'parent-theme'
					);
					?>
				</p>
				<?php
				$downloads      = $order->get_downloadable_items();
				$show_downloads = $order->has_downloadable_item() && $order->is_download_permitted();
				if ( $show_downloads ) {
					wc_get_template(
						'order/order-downloads.php',
						array(
							'downloads'  => $downloads,
							'show_title' => true,
						)
					);
				}

				$social_links = wld_get( 'wld_other_thank_social_links' );
				if ( empty( $social_links ) ) {
					$social_links = wld_get( 'wld_header_social_links' );
					if ( empty( $social_links ) ) {
						$social_links = wld_get( 'wld_footer_social_links' );
					}
				}
				echo $social_links;
				?>
			<?php endif; ?>
			<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
			<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>
		<?php else : ?>
			<span class="icon"></span>
			<h2 class="title"><?php esc_html_e( 'Thank you!', 'parent-theme' ); ?></h2>
			<p class="woocommerce-thankyou-order-received">
				<?php
				echo apply_filters(
					'woocommerce_thankyou_order_received_text',
					esc_html__( 'Your order was completed successfully!', 'parent-theme' ),
					null
				);
				?>
			</p>
		<?php endif; ?>
	</div>
	<?php if ( wld_has( 'wld_other_thank_you_background' ) ) : ?>
		<div class="background">
			<?php wld_the( 'wld_other_thank_you_background', 'cover', 'thank_you_background' ); ?>
		</div>
	<?php endif; ?>
</div>
