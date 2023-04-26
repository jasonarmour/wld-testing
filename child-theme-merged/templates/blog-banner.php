<section class="section-blog">

	<div class="wrapper">
		<div class="text">
			<?php if (is_category('blog')) : ?>
				<?php wld_the( 'title', 'title' ); ?>
				<?php wld_the( 'text' ); ?>
				<?php wld_the( 'form' ); ?>
			<?php elseif (is_category('press-release')) : ?>
				<h1 class="title">Press Releases</h1>
			<?php elseif (is_category('recent-news')) : ?>
				<h1 class="title">Recent News</h1>
			<?php elseif (is_category('upcoming-events')) : ?>
				<h1 class="title">Upcoming Events</h1>
			<?php else: ?>
				 <h1 class="title">Posts</h1>
			<?php endif; ?>
			
		</div>
		<div class="image">
			<?php wld_the( 'image', '975x0', '<div class="img">' ); ?>
		</div>
	</div>
</section>
