<?php


class WLD_Admin_Notices {

	public static function init(): void {
		if ( is_admin() ) {
			add_action( 'all_admin_notices', array( self::class, 'show_notices' ) );
		}
	}

	public static function add_notice( string $notice, string $type = 'info' ): void {
		$notices = get_transient( self::get_key() );

		if ( false === $notices ) {
			$notices = array();
		}

		$notices[] = array( $notice, $type );

		set_transient( self::get_key(), $notices, 24 * HOUR_IN_SECONDS );
	}

	public static function get_key(): string {
		return 'wld_admin_notices_' . get_current_user_id();
	}

	public static function show_notices(): void {
		$notices = get_transient( self::get_key() );
		if ( $notices ) {
			foreach ( $notices as $notice ) {
				?>
				<div class="notice notice-<?php echo $notice[1]; ?> is-dismissible">
					<?php echo wpautop( $notice[0] ); ?>
					<button type="button" class="notice-dismiss">
						<span class="screen-reader-text">
							<?php esc_html_e( 'Dismiss this notice.', 'parent-theme' ); ?>
						</span>
					</button>
				</div>
				<?php
			}
		}

		delete_transient( self::get_key() );
	}
}
