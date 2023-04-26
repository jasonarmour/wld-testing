<?php

class WLD_Login_Style {

	public static $login_styles = '';

	public static $default = array(
		'color'        => '#222',
		'bg_color'     => '#fff',
		'btn_color'    => '#fff',
		'btn_bg'       => '#0073aa',
		'links_color'  => '#222',
		'active_color' => '#0073aa',
		'show_logo'    => true,
	);

	public static function init(): void {
		WLD_Images::add_size( '320x100' );
		add_action( 'login_head', array( self::class, 'login_style' ) );
		add_filter( 'login_headerurl', array( self::class, 'login_logo_url' ) );
		add_filter( 'login_headertext', array( self::class, 'login_logo_text' ) );
	}

	public static function add( string $styles ): void {
		self::$login_styles .= $styles;
	}

	public static function set( string $key, $value ): void {
		self::$default[ $key ] = $value;
	}

	public static function login_style(): void {
		?>
		<style>
			<?php echo self::default_styles(); ?>
			<?php echo self::$login_styles; ?>
		</style>
		<?php
	}

	public static function default_styles(): string {
		$styles = '';
		if ( self::$default['show_logo'] ) {
			$logo_id = get_field( 'wld_header_logo', 'options', false );
			$url     = wp_get_attachment_image_url( $logo_id, '320x100' );

			$styles .= sprintf(
				'
				.login h1 a {
					background-image: url("%s");
					background-size: contain;
					height: 100px;
					width: auto;
				}
				',
				$url
			);
		} else {
			$styles .= '.login h1 a { display: none; }';
		}
		$styles .= sprintf(
			'
				.login, .login label, .login #nav a, .login #backtoblog a {
					color: %1$s;
				}
				.login {
					background-color: %2$s;
				}
				.login .button.button-primary {
					color: %3$s;
					background-color: %4$s;
					text-align: center;
					border-color: %4$s;
					box-shadow: 0 1px 0 %4$s;
					text-decoration: none;
					text-shadow: 0 -1px 1px %4$s, 1px 0 1px %4$s, 0 1px 1px %4$s, -1px 0 1px %4$s;
				}
				.login a {
					color: %6$s !important;
				}
				.login a:hover, .login a:active, .login a:focus {
					color: %5$s !important;
					text-decoration: underline !important;
				}
				.login a:focus {
					box-shadow: none !important;
					text-decoration: underline;
				}
				.login input:focus {
					box-shadow: none !important;
				}
			',
			self::$default['color'],
			self::$default['bg_color'],
			self::$default['btn_color'],
			self::$default['btn_bg'],
			self::$default['active_color'],
			self::$default['links_color']
		);

		return $styles;
	}

	public static function login_logo_url(): string {
		return home_url();
	}

	public static function login_logo_text(): string {
		return get_option( 'blogname' );
	}
}
