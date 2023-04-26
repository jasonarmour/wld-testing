<section class="section-our-team">
	<?php wld_the( 'background', '1903x0', array( 'class' => 'object-fit object-fit-cover' ) ); ?>
	<div class="inner">
		<?php while ( wld_loop( 'members', '<div class="slider-our-team">' ) ) : ?>
			<div class="item">
				<?php wld_the( 'title', 'title' ); ?>
				<h3><?php wld_the( 'display_name' ); ?> </h3>
				<p><?php wld_the( 'position' ); ?></p>
			</div>
		<?php endwhile; ?>
		<?php wld_the( 'btn', 'btn' ); ?>
	</div>
</section>



