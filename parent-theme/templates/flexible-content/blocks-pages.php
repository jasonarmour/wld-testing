<?php
$bg    = wld_get_sub_bg( 'background' );
$title = get_sub_field( 'title' );
?>
<section class="pages-section" <?php echo $bg; ?>>
	<div class="inner">
		<?php wld_the_title( $title, 'section-title' ); ?>
		<?php if ( have_rows( 'items' ) ) : ?>
			<div class="wrap">
				<?php while ( have_rows( 'items' ) ) : ?>
					<?php
					the_row();
					$image = wld_get_sub_image( 'image', '300x200' );
					$text  = get_sub_field( 'text' );
					$link  = wld_get_sub_link( 'link', 'link', true );
					?>
					<div class="item">
						<div class="image"><?php echo $image; ?></div>
						<div class="text-content"><?php echo $text; ?></div>
						<?php echo $link; ?>
					</div>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
