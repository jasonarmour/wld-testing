<section class="gallery-section">
	<div class="inner">
		<?php if ( have_rows( 'gallery' ) ) : ?>
			<div class="wrap">
				<?php while ( have_rows( 'gallery' ) ) : ?>
					<?php
					the_row();
					$id = get_sub_field( 'image', false );
					if ( ! $id ) {
						continue;
					}
					$image      = wld_get_sub_image( 'image', '300x300' );
					$url        = wp_get_attachment_image_url( $id, 'full' );
					$caption    = get_sub_field( 'caption' );
					$categories = array_map( 'sanitize_title', (array) get_sub_field( 'categories' ) );
					?>
					<div class="item all <?php echo esc_attr( implode( ' ', $categories ) ); ?>">
						<?php echo $image; ?>
						<a href="<?php echo esc_url( $url ); ?>"
						   title="<?php echo esc_attr( $caption ); ?>"><?php echo $caption; ?></a>
					</div>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
hey