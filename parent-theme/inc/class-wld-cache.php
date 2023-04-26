<?php

class WLD_Cache {
	public static $enable_theme_cache;

	protected static $path;

	public static function init() : void {
		add_action(
			'init',
			array( static::class, 'hook_init' )
		);
	}

	public static function hook_init() : void {
		static::$enable_theme_cache = apply_filters( 'wld_need_cache', true );
		if ( static::$enable_theme_cache ) {
			add_action(
				'acf/options_page/submitbox_before_major_actions',
				array( static::class, 'the_clear_button' )
			);
			add_action(
				'admin_init',
				array( static::class, 'clear_action' )
			);
			add_action(
				'post_updated',
				array( static::class, 'post_updated' )
			);
			add_action(
				'acf/update_field_group',
				array( static::class, 'clear' )
			);
			add_action(
				'acf/save_post',
				array( static::class, 'clear_theme_settings' )
			);
			add_action(
				'wp_update_nav_menu',
				array( static::class, 'clear' )
			);
			add_action(
				'updated_option',
				array( static::class, 'clear_options' )
			);
			add_action(
				'gform_after_save_form',
				array( static::class, 'clear' )
			);
		}
	}

	public static function post_updated() : void {
		if ( apply_filters( 'wld_cache_need_full_clear_for_post_update', false ) ) {
			static::clear();
		} else {
			WLD_Theme::delete_file_or_folder( static::get_path() );
		}
	}

	public static function clear() : void {
		WLD_Theme::delete_file_or_folder( static::get_path() );
	}

	protected static function get_path() : string {
		if ( null === static::$path ) {
			static::$path = wp_get_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . 'wld-theme-cache' . DIRECTORY_SEPARATOR;
		}

		return static::$path;
	}

	public static function the_clear_button( $page ) : void {
		if ( 'theme-settings' === $page['menu_slug'] ) {
			?>
			<!--suppress CssUnusedSymbol -->
			<style>
				#clear-theme-cache {
					margin: 10px;
					float: right;
				}

				#clear-theme-cache .spinner {
					float: none;
				}
			</style>
			<div class="clear"></div>
			<div id="clear-theme-cache">
				<span class="spinner"></span>
				<input type="button" class="button" value="Clear Theme Cache">
				<script>
					( function() {
						const spinner = document.querySelector( '#clear-theme-cache .spinner' );
						const button = document.querySelector( '#clear-theme-cache input' );
						if ( button ) {
							button.addEventListener( 'click', function() {
								button.classList.add( 'disabled' );
								spinner.classList.add( 'is-active' );
								fetch( '<?php echo admin_url( '?wld_clear_cache=1' ); ?>' )
									.then( response => response.text() )
									.then( message => {
										alert( message );
										button.classList.remove( 'disabled' );
										spinner.classList.remove( 'is-active' );
									} );
							} )
						}
					} )();
				</script>
			</div>
			<?php
		}
	}

	public static function clear_action() : void {
		if ( isset( $_GET['wld_clear_cache'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			static::clear();
			exit( 'The theme cache is cleared.' );
		}
	}

	public static function clear_theme_settings( $post_id ) : void {
		if ( 'options' === $post_id ) {
			static::clear();
		}
	}

	public static function clear_options( $option ) : void {
		$need_clear = apply_filters(
			'wld_cache_need_full_clear_for_option_update',
			array(
				'blogname'      => true,
				'date_format'   => true,
				'time_format'   => true,
				'rewrite_rules' => true,
				'hicpo_options' => true,
			)
		);

		if ( isset( $need_clear[ $option ] ) ) {
			static::clear();
		}
	}

	public static function maybe_cache_template_part( string $part_slug ) : void {
		// phpcs:ignore WordPress.Security.NonceVerification
		if ( static::$enable_theme_cache && empty( $_POST ) && empty( $_GET ) && is_singular() ) {
			$path = static::get_path();

			$path .= get_the_ID() . DIRECTORY_SEPARATOR;
			$path .= ( is_user_logged_in() ? 'user' : 'guest' ) . DIRECTORY_SEPARATOR;
			$path .= $part_slug;

			$contents = WLD_Theme::get_file_contents( $path );
			if ( ! $contents ) {
				ob_start();
				get_template_part( $part_slug );
				$contents = ob_get_clean();
				WLD_Theme::put_file_contents( $path, $contents );
			} else {
				preg_match_all(
					'/<input type=\'hidden\' name=\'gform_original_id\' value=\'(\d+)\'>/',
					$contents,
					$matches
				);

				if ( $matches[1] ) {
					foreach ( $matches[1] as $form_id ) {
						gravity_form(
							(int) $form_id,
							false,
							false,
							false,
							null,
							true,
							false,
							false
						);
					}
				}
			}
			echo $contents;
		} else {
			get_template_part( $part_slug );
		}
	}
}
