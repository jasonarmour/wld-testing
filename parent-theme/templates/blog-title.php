<section class="section-blog-post-content-top">
	<div class="inner">
		<div class="wrapper">
			<div class="text">
				<?php if ( has_category() ) : ?>
					<div class="categories">
						<?php the_category( ' ' ); ?>
					</div>
				<?php endif; ?>
				<div class="posted">
					<?php
					printf( // translators: %s - posted date
						esc_html__( 'Posted - %s', 'parent-theme' ),
						get_the_date( 'F j, Y' )
					);
					?>
				</div>
				<h1 class="title"><?php the_title(); ?></h1>
				<?php the_excerpt(); ?>
			</div>
			<div class="img">
				<?php the_post_thumbnail( '571x360' ); ?>
			</div>
		</div>
	</div>
</section>
