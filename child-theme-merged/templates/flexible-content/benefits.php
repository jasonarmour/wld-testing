<section class="section-benefits">
	<div class="inner">
		<?php wld_the( 'title', 'title' ); ?>
		<?php wld_the( 'text', '' ); ?>
		<?php while ( wld_loop( 'items', '<ul>' ) ) : ?>
			<li>
				<?php wld_the( 'image', '106x106' ); ?>
				<?php wld_the( 'title' ); ?>
			</li>
		<?php endwhile; ?>
		<?php wld_the( 'btn', 'btn' ); ?>
	</div>
</section>



