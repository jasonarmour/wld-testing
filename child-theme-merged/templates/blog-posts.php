<section class="section-blog-content">
	<div class="inner">
		<div class="wrapper">
			<div class="blog-content">
				<?php if ( have_posts() ) : ?>
					<?php while ( have_posts() ) : ?>
						<?php the_post(); ?>
						<?php get_template_part( 'templates/blog-item' ); ?>
					<?php endwhile; ?>
					<?php wld_the_pagination(); ?>
				<?php else : ?>
					<p><?php esc_html_e( 'Nothing found', 'parent-theme' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
