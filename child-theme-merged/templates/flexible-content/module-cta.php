<section class="module-CTA background-<?php echo wld_get( 'background_color' ) ?> padding-<?php echo wld_get( 'padding' ) ?> <?php echo wld_get( 'custom-class' ) ?>">
	<?php if ( wld_get( 'set_the_background' ) ) : ?>
		<?php wld_the( 'background', '1903x0', array( 'class' => 'object-fit object-fit-cover' ) ); ?>
	<?php endif; ?>
	<div class="inner">
		<?php wld_the( 'title', 'title' ); ?>
		<span><?php wld_the( 'text_under_the_headline' ); ?></span>
		<?php wld_the( 'text' ); ?>
		<?php while ( wld_loop( 'logos', '<ul>' ) ) : ?>

			<li>
				<?php while ( wld_wrap( 'link' ) ) : ?>
					<?php wld_the( 'logo', '100x100' ); ?>
				<?php endwhile; ?>
			</li>

		<?php endwhile; ?>
		<div class="btn-wrap">
			<?php wld_the( 'button_one', 'btn' ); ?>
			<?php wld_the( 'button_two', 'btn' ); ?>
		</div>
	</div>
</section>



