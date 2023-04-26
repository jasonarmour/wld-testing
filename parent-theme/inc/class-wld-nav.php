<?php

class WLD_Nav {

	public static $navs = array();

	public static function init(): void {
		add_action( 'after_setup_theme', array( self::class, 'register' ) );
	}

	public static function get_location( string $label ): string {
		return mb_strtolower( str_replace( ' ', '_', $label ) . '_location' );
	}

	public static function register(): void {
		register_nav_menus( self::$navs );
	}

	public static function add( string $label ): void {
		self::$navs[ self::get_location( $label ) ] = $label;
	}
}
