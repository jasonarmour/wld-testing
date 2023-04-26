<section class="module-content-sidebar background-<?php echo wld_get( 'background_color' ) ?> padding-<?php echo wld_get( 'padding' ) ?> <?php echo wld_get( 'custom_class' ) ?>">
	<?php /*if ( wld_get( 'set_the_background' ) ) : 
		wld_the( 'background', '1903x0', array( 'class' => 'object-fit object-fit-cover' ) ); 
	endif; */
	$emphasis =  wld_get('emphasis');
	
	?>
	
	<div class="inner">
		<div class="wrapper">
			<?php while ( wld_loop( 'right-content', '<div class="left emphasis-' . $emphasis . '">' ) ) : ?>
				<?php wld_the( 'title', 'title' ); ?>
				<?php wld_the( 'text' ); ?>
				<?php wld_the( 'button', 'btn' ); ?>
			<?php endwhile; ?>
			<?php while ( wld_loop( 'left-sidebar', '<div class="right emphasis-' . $emphasis . '">' ) ) : ?>
				<div class="sidebar"><?php wld_the( 'title', 'title' ); ?>
				<?php while ( wld_loop( 'sidebar-blocks' ) ) : ?>
					<div class="item">
						<?php wld_the( 'subtitle', 'sub-title' ); ?>
						<?php wld_the( 'text' ); ?>
						<?php wld_the( 'link' ); ?>
					</div>
				<?php endwhile; ?>
			</div>
			<?php endwhile; ?>
		</div>
	</div>
</section>


