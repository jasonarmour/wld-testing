<section class="module-products-three-columns background-<?php echo wld_get( 'background_color' ) ?> padding-<?php echo wld_get( 'padding' ) ?> <?php echo wld_get( 'custom_class' ) ?>">
<?php /*if ( wld_get( 'set_the_background' ) ) : ?>
	<?php wld_the( 'background', '1903x0', array( 'class' => 'object-fit object-fit-cover' ) ); ?>
<?php endif; */?>
	<div class="inner">
		<?php wld_the( 'title', 'title' ); ?>
		<?php wld_the( 'text' ); ?>
		<?php while ( wld_loop( 'products', '<div class="wrapper">' ) ) : ?>
			<div class="item">
				<?php if ( wld_get( 'switch' ) ): ?>
					<div class="image">
						<?php while ( wld_wrap( 'image_link' ) ) : ?>
							<?php wld_the( 'image', '370x0' ); ?>
						<?php endwhile; ?>
					</div>
				<?php else: ?>
					<?php if ( wld_get( 'video' ) ): ?>
						<div class="video">
							<?php echo do_shortcode('[video src="'. wld_get ('video')  .'"]');?>
						</div>
					<?php else: ?>
						<?php wld_the( 'image', '370x0', '<div class="image">' ); ?>
					<?php endif; ?>
				<?php endif; ?>
				<div class="content">
					<?php wld_the( 'category', '<div class="category">' ); ?>
					<div class="date"><?php wld_the( 'date' ); ?></div>
					<?php wld_the( 'subtitle', 'sub-title' ); ?>
					<?php wld_the( 'text' ); ?>
					<?php wld_the( 'link', 'link' ); ?>
				</div>
				<?php wld_the( 'button', 'btn' ); ?>
			</div>
		<?php endwhile; ?>
		<?php wld_the( 'button', 'btn' ); ?>
	</div>
</section>



