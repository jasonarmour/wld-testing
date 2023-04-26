<?php
// Specify styles for .btn as in file styles.css
WLD_TinyMCE::add_editor_styles( '.btn', 'background-color:;color:#fff;' );

// Specify styles for login page
WLD_Login_Style::set( 'btn_bg', '' );
WLD_Login_Style::set( 'btn_color', '' );

// Add custom post types
WLD_CPT::add( 'testimonial' );
WLD_CPT::add( 'member', array( 'menu_icon' => 'dashicons-businessperson' ) );

// Add custom product category

WLD_Tax::add(
	'topic',
	array(
		'public'       => false,
		'object_type'  => 'product',
		'rewrite_tag'  => true,
		'default_slug' => 'topic',
	)
);

WLD_Tax::add(
	'focus',
	array(
		'public'       => false,
		'object_type'  => 'product',
		'rewrite_tag'  => true,
		'default_slug' => 'focus',
	)
);


// Add menus
WLD_Nav::add( 'Header Main' );
WLD_Nav::add( 'Header Second' );
WLD_Nav::add( 'Footer Main' );
WLD_Nav::add( 'Footer Links' );

// Add image sizes
//WLD_Images::add_size( '500x0' );
WLD_Images::add_size( '0x90' );
WLD_Images::add_size( '100x100' );
WLD_Images::add_size( '0x200' );
WLD_Images::add_size( '106x106' );
WLD_Images::add_size( '240x300' );
WLD_Images::add_size( '300x0' );
WLD_Images::add_size( '370x0' );
WLD_Images::add_size( '500x0' );
WLD_Images::add_size( '670x0' );
WLD_Images::add_size( '767x0' );
WLD_Images::add_size( '770x0' );
WLD_Images::add_size( '992x0' );
WLD_Images::add_size( '1170x0' );
WLD_Images::add_size( '1903x0' );
