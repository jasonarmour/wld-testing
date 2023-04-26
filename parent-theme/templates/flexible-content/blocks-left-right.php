<div class="left-right">
	<div class="inner">
		<?php if ( have_rows( 'left' ) || have_rows( 'right' ) ) : ?>
			<div class="wrap">
				<?php foreach ( array( 'left', 'right' ) as $col_name ) : ?>
					<?php if ( have_rows( $col_name ) ) : ?>
						<?php while ( have_rows( $col_name ) ) : ?>
							<?php the_row(); ?>
							<div class="<?php echo $col_name; ?>">
								<?php
								$title = get_sub_field( 'title' );
								$image = wld_get_sub_image( 'image', '500x0' );
								$text  = get_sub_field( 'text' );
								?>
								<?php wld_the_title( $title, 'section-title' ); ?>
								<div class="image"><?php echo $image; ?></div>
								<div class="text-content"><?php echo $text; ?></div>
							</div>
						<?php endwhile; ?>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
