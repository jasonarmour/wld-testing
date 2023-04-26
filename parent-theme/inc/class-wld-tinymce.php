<?php

class WLD_TinyMCE {

	public static $styles = '';

	public static function init() : void {
		add_action( 'wp_ajax_wld_show_editor_styles', array( self::class, 'show_editor_styles' ) );
		add_filter( 'tiny_mce_before_init', array( self::class, 'set_editor_styles' ) );
		self::add_editor_styles(
			'.btn',
			'
				color: #FFF;
				background-color: #999;
				border-radius: 5px;
				display: inline-block;
				padding: 5px 10px;
				margin: 0;
				text-align: center;
				text-decoration: none;
				vertical-align: middle;
			'
		);
	}

	public static function add_editor_styles( string $selector, string $rules ) : void {
		if ( '.btn' === $selector ) {
			$selector .= ', .mce-content-body a.btn[data-mce-selected]';
		}
		self::$styles .= $selector . '{' . $rules . '}';
	}

	public static function set_editor_styles( array $mce_init ) : array {
		if ( isset( $mce_init['content_css'] ) ) {
			$mce_init['content_css'] .= ',' . admin_url( 'admin-ajax.php' ) . '?action=wld_show_editor_styles';
		}

		return $mce_init;
	}

	public static function show_editor_styles() : void {
		header( 'Content-type: text/css' );
		echo self::$styles;
		exit();
	}
}
