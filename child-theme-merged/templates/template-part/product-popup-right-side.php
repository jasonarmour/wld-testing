<?php
/**
 * @var array $args array with data about one event
 */
?>
<?php
$product_image = wp_get_attachment_url( $args['product']->get_image_id() );
$price_html    = ! empty( $args['product']->get_price() ) ? $args['product']->get_price_html() : esc_html_x( '', 'parent-theme' );
$attr_type     = get_post_meta( $post->ID, '_type', true );
$is_member     = get_user_meta( get_current_user_id(), 'is_member', true );

?>
<?php if ( ! is_user_logged_in() || ( is_user_logged_in() && empty( $is_member ) ) ) : ?>
	<div class="wrap-image-product">
		<img src="<?php echo $product_image; ?>" alt="">
	</div>
<?php else : ?>
	<?php if ( empty( $is_member ) ) : ?>
		<div class="category"><?php wld_the( 'title' ); ?></div>
		<h2 class="title"><?php echo $args['product']->get_name(); ?></h2>

		<p class="price">
			<?php echo $price_html; ?>
		</p>
		<?php if ( ! empty( $args['product']->get_price() ) ) : ?>
			<?php echo '<a href="' . BERYL_CRM_URL . 'communityhub/s/product-details?id=' . get_post_meta( get_the_ID(), 'crm_product_id', true ) . '" class="btn">PURCHASE NOW</a>'; ?>
		<?php else : ?>
			<?php wld_the( 'button_no_price', 'btn' ); ?>
		<?php endif; ?>
	<?php else : ?>
		<div class="category"><?php wld_the( 'title' ); ?></div>
		<h2 class="title"><?php echo $args['product']->get_name(); ?></h2>
		<?php $price = get_user_meta( get_current_user_id(), 'member_price', true ); ?>
		<p class="price">
			<span class="woocommerce-Price-amount amount">
				<bdi>
					<span class="woocommerce-Price-currencySymbol">$</span>
					<?php echo $price; ?>
				</bdi>
			</span>
		</p>
		<?php if ( ! empty( $price ) ) : ?>
			<button class="btn download-content-product" data-post-id="<?php echo get_the_ID(); ?>">Purchase Now
			</button>
		<?php else : ?>
			<?php wld_the( 'button_no_price', 'btn' ); ?>
		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>
