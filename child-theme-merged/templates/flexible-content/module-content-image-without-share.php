<section class="module-content-image background-<?php echo wld_get( 'background_color' ) ?> padding-<?php echo wld_get( 'padding' ) ?> <?php echo wld_get( 'custom_class' ) ?>">
	<?php /*if ( wld_get( 'set_the_background' ) ) : ?>
		<?php wld_the( 'background', '1903x0', array( 'class' => 'object-fit object-fit-cover' ) ); ?>
	<?php endif; */?>
	<div class="inner">
		<?php wld_the( 'title', 'title' ); ?>
		<?php if ( wld_get( 'full_width' ) == "1" ) {
		  echo  '<div class="wrapper" style="max-width: inherit;">';
		} else {
		  echo '<div class="wrapper">';
		}; 
	 ?>
		<!--reorder - add сlass wrapper-reverse-->
			<div class="left">
				<?php wld_the( 'category','<div class="category">' ); ?>
				<?php wld_the( 'subtitle', 'sub-title' ); ?>
				<?php wld_the( 'text' ); ?>
				<?php wld_the( 'link', 'link' ); ?>
				<div class="btn-wrap">
					<?php wld_the( 'button_one', 'btn' ); ?>
					<?php wld_the( 'button_two', 'btn' ); ?>
				</div>
			</div>
			<?php if ( wld_get( 'video' ) ): ?>
				<div class="right">
					<?php while ( wld_wrap( 'video', 'popup-video' ) ) : ?>
					<span class="video-image"><?php wld_the( 'image', '770x0' ); ?></span>
					<?php endwhile; ?>
				</div>
			<?php else: ?>
				<?php wld_the( 'image', '770x0', '<div class="right">' ); ?>
			<?php endif; ?>
		</div>
	</div>
</section>



