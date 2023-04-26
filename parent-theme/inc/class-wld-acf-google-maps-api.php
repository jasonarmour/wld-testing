<?php /** @noinspection PhpUndefinedClassInspection */

class WLD_ACF_Google_Maps_API {
	public static $api_key = '';

	public static function init() : void {
		add_filter(
			'acf/fields/google_map/api',
			array( self::class, 'set_api_key_in_acf' )
		);
		add_filter(
			'wld_enqueue_get_theme_object',
			array( self::class, 'set_key_in_theme' )
		);
	}

	public static function set_api_key_in_acf( array $api ) : array {
		$api['key'] = self::get_api_key();

		return $api;
	}

	public static function set_key_in_theme( array $theme ) : array {
		$theme['googleMapsApiKey'] = self::get_api_key();

		return $theme;
	}

	public static function get_api_key() : string {
		return empty( self::$api_key ) ? (string) get_field( 'wld_api_google_maps_key', 'option' ) : self::$api_key;
	}
}
