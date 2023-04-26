<?php echo '<div class="container">'; ?>

<aside class="menu-wrap" style="display: none">
	<?php wld_the_nav( 'Header Main', true ); ?>
	<?php wld_the_nav( 'Header Second', true ); ?>
	<form role="search" method="get" action="/">
		<label>
			<span class="screen-reader-text">Search for:</span>
			<input type="search" placeholder="Search" value="" name="s">
		</label>
		<input type="submit">
	</form>
	<a class='view-all' href="/search">View All Content</a>
	<button class="close-button" id="close-button">
		<span class="screen-reader-text"><?php esc_html_e( 'Close Menu', 'parent-theme' ); ?></span>
	</button>
</aside>

<?php echo '<div class="content-wrap">'; ?>

<header class="header">
	<div id="sticky-header" class="unfixed">
		<div class="inner">
			<?php wld_the_logo(); ?>
			<div class="nav-menu">
				<?php wld_the_nav( 'Header Second' ); ?>
				<?php wld_the_nav( 'Header Main' ); ?>
				<div class="search-popup">
					<div class="inner">
						<form role="search" method="get" action="/">
							<label>
								<span class="screen-reader-text">Search for:</span>
								<input type="search" placeholder="Search" value="" name="s">
							</label>
							<input type="submit">
						</form>
						<a href="/search">View All Content</a>
						<button type="button" title="Close" class="close"></button>
					</div>
				</div>
			</div>
		</div>
		<button class="menu-button" id="open-button">
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'parent-theme' ); ?></span>
			<?php do_action( 'wld_the_cart_count' ); ?>
		</button>
	</div>
</header>

<?php echo '<main class="main">'; ?>

<?php if ( function_exists( 'yoast_breadcrumb' ) && ! is_front_page() && ! is_404() ) : ?>
	<?php //yoast_breadcrumb( '<div class="breadcrumbs">', '</div>' ); ?>
<?php endif; ?>
