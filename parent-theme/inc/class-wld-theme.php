<?php

class WLD_Theme {
	protected static $cache_post_ids_taking_into_preview = array();

	/**
	 * @return WP_Filesystem_Direct In fact, it will not always be this particular class, but in most cases.
	 * @noinspection PhpMissingReturnTypeInspection, ReturnTypeCanBeDeclaredInspection
	 */
	public static function filesystem() {
		global $wp_filesystem;

		if ( null === $wp_filesystem ) {
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				/** @noinspection PhpIncludeInspection, RedundantSuppression */
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}

			WP_Filesystem();
		}

		return $wp_filesystem;
	}

	public static function get_file_contents( string $file ) : string {
		$filesystem = static::filesystem();
		if ( $filesystem->exists( $file ) ) {
			return $filesystem->get_contents( $file );
		}

		return '';
	}

	public static function put_file_contents( string $file, $contents ) : bool {
		$filesystem = static::filesystem();
		$dir        = pathinfo( $file, PATHINFO_DIRNAME );
		if ( ! $filesystem->exists( $dir ) ) {
			wp_mkdir_p( $dir );
		}

		return $filesystem->put_contents( $file, $contents );
	}

	public static function delete_file_or_folder( string $file ) : bool {
		return static::filesystem()->delete( $file, true );
	}

	public static function get_post_id_taking_into_preview() {
		$post_id = get_the_ID();
		if ( isset( static::$cache_post_ids_taking_into_preview[ $post_id ] ) ) {
			return static::$cache_post_ids_taking_into_preview[ $post_id ];
		}

		// phpcs:disabled WordPress.Security.NonceVerification
		if (
			isset( $_GET['preview'] ) &&
			(
				( isset( $_GET['preview_id'] ) && $post_id === (int) $_GET['preview_id'] ) ||
				( isset( $_GET['page_id'] ) && $post_id === (int) $_GET['page_id'] )
			)
		) {
			$revisions = wp_get_post_revisions( $post_id, array( 'numberposts' => 1 ) );
			$revision  = array_shift( $revisions );
			if ( $revision && $revision->post_parent === $post_id ) {
				$post_id = (int) $revision->ID;
			}
		}
		// phpcs:enabled WordPress.Security.NonceVerification

		static::$cache_post_ids_taking_into_preview[ $post_id ] = $post_id;

		return $post_id;
	}
}
