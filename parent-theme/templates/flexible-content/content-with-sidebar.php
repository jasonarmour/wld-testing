<div class="content-with-sidebar">
	<div class="inner">
		<div class="wrap">
			<div class="content">
				<?php
				if ( have_rows( 'inner_content' ) ) {
					while ( have_rows( 'inner_content' ) ) {
						the_row();
						WLD_ACF_Flex_Content::the_content();
					}
				}
				?>
			</div>
			<div class="sidebar">
				<?php
				if ( have_rows( 'sidebar' ) ) {
					while ( have_rows( 'sidebar' ) ) {
						the_row();
						WLD_ACF_Flex_Content::the_content( 'templates/flexible-content/sidebar-blocks/' );
					}
				}
				?>
			</div>
		</div>
	</div>
</div>
