<?php
// Specify styles for .btn as in file styles.css
WLD_TinyMCE::add_editor_styles( '.btn', '' );

// Specify styles for login page
WLD_Login_Style::set( 'color', '#000' );
WLD_Login_Style::set( 'btn_bg', 'rgb(32, 113, 120)' );
WLD_Login_Style::add(
	'.login { 
		background: linear-gradient( 90deg, rgba(255, 225, 132, .7) 50%, rgba(255,255,255, 0) 50%),
		linear-gradient( 90deg, rgba(32, 113, 120, .3) 50%, rgba(255,255,255, 0) 50%),
		linear-gradient( 90deg, rgba(255, 150, 102, .3) 50%, rgba(255,255,255, 0) 50%),
		linear-gradient( 90deg, rgba(23, 76, 79, .1) 50%, rgba(255,255,255, 0) 50%);
		background-size: 7em 7em, 5em 5em, 3em 3em, 1em 1em;
		background-color: #F5E9BE;
		font-family: monospace;
	}'
);

// Add custom post types
WLD_CPT::add( 'testimonial' );
WLD_CPT::add(
	'portfolio',
	array(
		'public' => true,
	)
);
WLD_CPT::add(
	'team',
	array(
		'public'       => true,
		'default_slug' => 'our-team',
		'single_label' => 'Member',
		'plural_label' => 'Team',
		'supports'     => array( 'title', 'revisions', 'editor', 'thumbnail' ),
	)
);

// Add taxonomies
WLD_Tax::add(
	'portfolio_category',
	array(
		'public'       => true,
		'object_type'  => 'portfolio',
		'rewrite_tag'  => true,
		'default_slug' => 'portfolio',
	)
);

// Add menus
WLD_Nav::add( 'Header Main' );
WLD_Nav::add( 'Header Second' );
WLD_Nav::add( 'Footer Main' );
WLD_Nav::add( 'Footer Links' );

// Add image sizes
WLD_Images::add_size( '500x0' );
WLD_Images::add_size( '150x100' );
WLD_Images::add_size( '150x150', 'cover' );
WLD_Images::add_size( '300x200', 'cover' );
WLD_Images::add_size( '300x300', 'cover' );
