<?php

class WLD_Admin_Bar {
	public static function init() : void {
		add_action(
			'admin_bar_menu',
			array( self::class, 'hide_comments' ),
			999
		);
		add_action(
			'show_admin_bar',
			array( self::class, 'is_show' )
		);
	}

	public static function is_show( bool $show ) : bool {
		if ( $show ) {
			$user_can = current_user_can( 'edit_posts' ) || current_user_can( 'manage_woocommerce' );
			$show     = apply_filters( 'wld_show_admin_bar', $user_can );
			if ( $show ) {
				add_theme_support( 'admin-bar', array( 'callback' => '__return_false' ) );
				add_action( 'admin_bar_menu', array( self::class, 'add_button' ) );
				add_action( 'wp_print_footer_scripts', array( self::class, 'script' ), PHP_INT_MAX );
				add_action( 'wp_head', array( self::class, 'style' ) );
			}
		}

		return $show;
	}

	public static function add_button() : void {
		/**
		 * @var WP_Admin_Bar $wp_admin_bar
		 * @noinspection PhpRedundantVariableDocTypeInspection
		 */
		global $wp_admin_bar;
		if ( ! is_admin_bar_showing() || is_admin() ) {
			return;
		}
		$title = '<button type="button" role="button" class="ab-item"><span class="ab-icon"></span></button>';
		$wp_admin_bar->add_node(
			array(
				'id'     => 'collapse',
				'title'  => $title,
				'parent' => false,
				'meta'   => array(
					'title' => 'collapse',
				),
			)
		);
	}

	public static function script() : void {
		?>
		<script>
			jQuery( function( $ ) {
				const
					$bar = $( '#wpadminbar' ),
					$items = $bar.find( '#wp-toolbar>ul>li:not(#wp-admin-bar-collapse)' );

				let open = $items.hasClass( 'qm-all-clear' );

				$( '#wp-admin-bar-collapse' ).on( 'click', toggle ).trigger( 'click' );

				function toggle() {
					open = ! open;
					if ( open ) {
						$bar.css( { opacity: 1, width: '100%', minWidth: '600px' } );
					} else {
						$bar.css( { opacity: 0.1, width: '66px', minWidth: 0 } );
					}

					$items.toggle( open );
				}
			} );
		</script>
		<?php
	}

	public static function style() : void {
		echo '
		<style>
			html {
				scroll-padding-top: 0;
			}
			#wpadminbar {
				opacity:0;
			}
			#wpadminbar:hover {
				opacity: 1 !important;
			}
			#wpadminbar #wp-admin-bar-collapse button {
				background: transparent;
				border: 0;
				outline: 0;
			}
			#wpadminbar #wp-admin-bar-collapse > .ab-item .ab-icon:before {
				content: "\\f333";
				top: 2px;
			}
			@media all and (max-width: 782px) {
				#wpadminbar {
					display: none !important;
				}
			}
		</style>
		';
	}

	public static function hide_comments( WP_Admin_Bar $wp_admin_bar ) : void {
		$wp_admin_bar->remove_menu( 'comments' );
	}
}
