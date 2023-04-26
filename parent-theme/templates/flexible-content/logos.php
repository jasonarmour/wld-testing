<?php
$title = get_sub_field( 'title' );
?>
<section class="logos-section">
	<div class="inner">
		<?php wld_the_title( $title, 'section-title' ); ?>
		<div class="wrap">
			<?php if ( have_rows( 'items' ) ) : ?>
				<?php while ( have_rows( 'items' ) ) : ?>
					<?php
					the_row();
					$image = wld_get_sub_image( 'image', '150x100' );
					$link  = get_sub_field( 'link' );
					wld_the_maybe_link( $image, $link );
					?>
				<?php endwhile; ?>
			<?php endif; ?>
		</div>
	</div>
</section>
