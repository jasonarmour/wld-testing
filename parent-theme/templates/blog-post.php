<section class="section-blog-post-content-bottom">
	<div class="inner">
		<div class="wrapper">
			<div class="blog-post-content">
				<?php the_content(); ?>
				<hr>
				<?php get_template_part( 'templates/blog-share-links' ); ?>
			</div>
			<div class="form">
				<?php dynamic_sidebar( 'blog_sidebar' ); ?>
				<?php wld_the( 'wld_social_links' ); ?>
			</div>
		</div>
	</div>
</section>
