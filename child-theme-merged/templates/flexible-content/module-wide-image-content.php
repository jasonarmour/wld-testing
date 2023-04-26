<section class="module-wide-image-content background-<?php echo wld_get( 'background_color' ) ?> padding-<?php echo wld_get( 'padding' ) ?> <?php echo wld_get( 'custom_class' ) ?>">
	<?php /*if ( wld_get( 'set_the_background' ) ) : ?>
		<?php wld_the( 'background', '1903x0', array( 'class' => 'object-fit object-fit-cover' ) ); ?>
	<?php endif; */?>
	<div class="inner">
		<?php wld_the( 'title', 'title' ); ?>
		<?php if ( wld_get( 'video' ) ): ?>
			<div class="image">
				<?php while ( wld_wrap( 'video', 'popup-video' ) ) : ?>
					<span class="video-image"><?php wld_the( 'image', '1170x0' ); ?></span>
				<?php endwhile; ?>
			</div>
		<?php else: ?>
			<?php wld_the( 'image', '1170x0', '<div class="image">' ); ?>
		<?php endif; ?>
		<div class="content">
			<?php wld_the( 'category', '<div class="category">' ); ?>
			<?php wld_the( 'subtitle', 'sub-title' ); ?>
			<?php wld_the( 'text' ); ?>
			<?php wld_the( 'link', 'link' ); ?>
			<?php wld_the( 'button_one', 'btn' ); ?>
			<?php wld_the( 'button_two', 'btn' ); ?>
		</div>
	</div>
</section>



