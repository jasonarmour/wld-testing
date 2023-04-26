<?php

class WLD_Site_Map {
	public static function init(): void {
		add_filter(
			'display_post_states',
			array( self::class, 'post_states' ),
			10,
			2
		);

		add_shortcode( 'site_map', array( self::class, 'site_map_shortcode' ) );

		WLD_Nav::add( 'Site Map' );
	}

	public static function post_states( array $post_states, WP_Post $post ): array {
		if ( false !== strpos( $post->post_content, '[site_map]' ) ) {
			$post_states['site-map'] = __( 'Site Map Page', 'parent-theme' );
		}

		return $post_states;
	}

	public static function site_map_shortcode(): string {
		return (string) wp_nav_menu(
			array(
				'theme_location' => WLD_Nav::get_location( 'Site Map' ),
				'container'      => false,
				'menu_class'     => 'menu-site_map',
				'echo'           => false,
			)
		);
	}
}
