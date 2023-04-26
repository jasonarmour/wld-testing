<?php
/**
 * @var array $args array with data about one event
 */

$price_html    = ! empty( $args['product']->get_price() ) ? $args['product']->get_price_html() : esc_html_x( '', 'parent-theme' );
?>
<div id="member_free__non_member_buy" class="mfp-hide">
	<div class="wrapper">
		<?php while ( wld_loop( 'wld_product_popup_left_side', '<div class="left">' ) ) : ?>
			<?php wld_the( 'title', 'title' ); ?>
			<?php wld_the( 'text' ); ?>
			<?php
			$btn  = wld_get( 'button', 'btn' );
			$btn  = str_replace(
				array( 'href="/', '" class' ),
				array( 'href="' . BERYL_CRM_URL, '.communityhub/nc__joinrenew?forcompany=false&id=' . get_post_meta( get_the_ID(), 'crm_product_id', true ) . '" class' ),
				$btn
			);
			echo $btn;
			?>
			<?php
			$btn  = wld_get( 'link');
			$btn  = str_replace(
				array( 'href="#', '">' ),
				array( 'href="' . BERYL_CRM_URL, '?option=oauthredirect&app_name=NimbleAMS&redirect_url=' . home_url() . $_SERVER['REQUEST_URI'] . '">' ),
				$btn
			);
			echo $btn;
			?>
		<?php endwhile; ?>

		<?php while ( wld_loop( 'wld_product_popup_right_side', '<div class="right">' ) ) : ?>
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
		<?php endwhile;?>
	</div>
</div>
