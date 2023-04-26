<?php /** @noinspection SqlDialectInspection */

class WLD_YouTube {
	public static function get_image(
		?string $youtube_url, string $wp_size, int $attachment_id = 0, array $attr = array()
	): string {
		$youtube_id = self::get_id( $youtube_url );
		if ( 0 === $attachment_id && $youtube_id ) {
			$attachment_id = self::get_attachment_id( $youtube_id );
		}

		return WLD_Images::get_img( $attachment_id, $wp_size, $attr );
	}

	public static function get_id( ?string $youtube_url ): string {
		$pattern = '/^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))([^#\&\?]*).*/';
		if (
			$youtube_url &&
			filter_var( $youtube_url, FILTER_VALIDATE_URL ) &&
			preg_match( $pattern, $youtube_url, $matches )
		) {
			return $matches[1];
		}

		return '';
	}

	protected static function get_attachment_id( string $youtube_id ): int {
		$url           = 'http://img.youtube.com/vi/' . $youtube_id . '/maxresdefault.jpg';
		$attachment_id = self::get_attachment_id_by_url( $url );

		if ( empty( $attachment_id ) ) {
			if ( ! function_exists( 'download_url' ) ) {
				/** @noinspection PhpIncludeInspection */
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}

			$tmp = download_url( $url );
			if ( ! is_wp_error( $tmp ) ) {
				$file_array = array(
					'name'     => 'youtube-' . $youtube_id . '.jpg',
					'tmp_name' => $tmp,
				);

				$post_data = array(
					'post_title'   => 'YouTube ' . $youtube_id,
					'post_content' => $url,
				);

				if ( ! function_exists( 'media_handle_sideload' ) ) {
					/** @noinspection PhpIncludeInspection */
					require_once ABSPATH . 'wp-admin/includes/media.php';
					/** @noinspection PhpIncludeInspection */
					require_once ABSPATH . 'wp-admin/includes/image.php';
				}

				$attachment_id = media_handle_sideload( $file_array, 0, '', $post_data );
				if ( is_wp_error( $attachment_id ) ) {
					return 0;
				}
			}
		}

		return (int) $attachment_id;
	}

	/** @noinspection SqlNoDataSourceInspection, SqlResolve */
	protected static function get_attachment_id_by_url( string $url ): int {
		global $wpdb;

		return (int) $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_content=%s", $url ) );
	}

	public static function get_image_url( ?string $youtube_url, string $wp_size, int $default_attachment_id = 0
	): string {
		$url        = '';
		$youtube_id = self::get_id( $youtube_url );

		if ( $youtube_id ) {
			$attachment_id = self::get_attachment_id( $youtube_id );
			if ( $attachment_id ) {
				$url = wp_get_attachment_image_url( $attachment_id, $wp_size );
			}
		}

		if ( empty( $url ) ) {
			$url = wp_get_attachment_image_url( $default_attachment_id, $wp_size );
		}

		return (string) $url;
	}
}
