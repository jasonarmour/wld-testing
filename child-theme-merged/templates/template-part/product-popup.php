<?php
/**
 * @var array $args array with data about one event
 */
?>
<div id="become-member-popup" class="mfp-hide">
	<div class="wrapper">
		<?php while ( wld_loop( 'wld_product_popup_left_side', '<div class="left">' ) ) : ?>
			<?php wld_the( 'title', 'title' ); ?>
			<?php wld_the( 'text' ); ?>
			<?php
				$btn  = wld_get( 'button', 'btn' );
				$btn  = str_replace(
					array( 'href="', '" class' ),
					array( 'href="' . BERYL_CRM_URL, '?forcompany=false&id=' . get_post_meta( get_the_ID(), 'crm_product_id', true ) . '" class' ),
					$btn
				);
				echo $btn;
			?>
			<?php
				$btn  = wld_get( 'link');
				$btn  = str_replace(
					array( 'href="', '">' ),
					array( 'href="' . BERYL_CRM_URL, '?forcompany=false&id=' . get_post_meta( get_the_ID(), 'crm_product_id', true ) . '">' ),
					$btn
				);
				echo $btn;
			?>
		<?php endwhile; ?>

		<?php while ( wld_loop( 'wld_product_popup_right_side', '<div class="right">' ) ) : ?>
			<?php
				get_template_part(
					'templates/template-part/product-popup-right-side',
					'',
					array(
						'product' => $args['product'],
					),
				);
			?>
		<?php endwhile;?>
	</div>
</div>
