<?php /** @noinspection PhpUndefinedMethodInspection, PhpUndefinedClassInspection, PhpUndefinedFunctionInspection */

defined( 'ABSPATH' ) || exit;

$count = WC()->cart->get_cart_contents_count();
$items = '';

if ( $count ) {
	$items = sprintf( // translators: %d count in cart
		_n( '%d item', '%d items', $count, 'parent-theme' ),
		$count
	);
}
?>
<div class="menu-cart">
	<div class="title">
		<h2>
			<?php esc_html_e( 'Cart', 'parent-theme' ); ?>
			<?php if ( $items ): ?>
				<?php echo '<span>' . esc_html( $items ) . '</span>'; ?>
			<?php endif; ?>
		</h2>
		<a href="#" class="close" data-close>
			<span class="screen-reader-text">
				<?php esc_html_e( 'Close cart', 'parent-theme' ); ?>
			</span>
		</a>
	</div>
	<?php if ( ! WC()->cart->is_empty() ) : ?>
		<?php woocommerce_mini_cart(); ?>
	<?php else : ?>
		<div class="empty">
			<?php esc_html_e( 'Empty Cart', 'parent-theme' ); ?>
		</div>
	<?php endif; ?>
</div>
