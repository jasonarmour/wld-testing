<?php
get_header();

if ( is_singular() ) {
	if ( is_single() ) {
		while ( wld_loop( 'wld_blog_menu' ) ) {
			get_template_part( 'templates/blog-menu' );
		}
		get_template_part( 'templates/blog-title' );
		get_template_part( 'templates/blog-post' );
		while ( wld_loop( 'wld_blog_form' ) ) {
			get_template_part( 'templates/blog-form' );
		}
	} else {
		get_template_part( 'templates/default-page' );
	}
} elseif ( is_home() || is_archive() ) {
	while ( wld_loop( 'wld_blog_banner' ) ) {
		get_template_part( 'templates/blog-banner' );
	}
	while ( wld_loop( 'wld_blog_menu' ) ) {
		get_template_part( 'templates/blog-menu' );
	}
	get_template_part( 'templates/blog-posts' );
	while ( wld_loop( 'wld_blog_form' ) ) {
		get_template_part( 'templates/blog-form' );
	}
} elseif ( is_search() ) {
	$search_title = sprintf( '<h1 class="title">%s</h1>', esc_html__( 'Search', 'parent-theme' ) );
	get_template_part( 'templates/search-form', null, array( 'title' => $search_title ) );
	get_template_part( 'templates/search-results' );
} else {
	get_template_part( 'templates/error-404' );
}

get_footer();
