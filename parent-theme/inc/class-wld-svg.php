<?php

class WLD_SVG {
	public static function init(): void {
		add_filter(
			'upload_mimes',
			array( self::class, 'add' )
		);
		add_filter(
			'wp_check_filetype_and_ext',
			array( self::class, 'check' ),
			10,
			5
		);
		add_filter(
			'wp_prepare_attachment_for_js',
			array( self::class, 'show' )
		);
		add_filter(
			'wp_get_attachment_image_src',
			array( self::class, 'set_sizes' ),
			10,
			2
		);
		WLD_TinyMCE::add_editor_styles(
			'img[src$=".svg"]',
			'
			background: #eee;
			box-shadow: inset 0 0 15px rgba(0,0,0,.1), inset 0 0 0 1px rgba(0,0,0,.05);
			min-width: 24px;
			'
		);
	}

	public static function add( array $mimes ): array {
		$mimes['svg'] = 'image/svg+xml';

		return $mimes;
	}

	/** @noinspection PhpUnusedParameterInspection */
	public static function check( array $data, $file, $filename, $mimes, string $real_mime ): array {
		if ( in_array( $real_mime, array( 'image/svg', 'image/svg+xml' ), true ) ) {
			if ( current_user_can( 'manage_options' ) ) {
				$data['ext']  = 'svg';
				$data['type'] = 'image/svg+xml';
			} else {
				$type_and_ext['type'] = false;
				$data['ext']          = false;
			}
		}

		return $data;
	}

	public static function show( array $response ): array {
		if ( 'image/svg+xml' === $response['mime'] ) {
			$response['sizes'] = [
				'full' => [
					'url' => $response['url'],
				],
			];
		}

		return $response;
	}

	public static function set_sizes( $image, $attachment_id ) {
		if ( 'image/svg+xml' === get_post_mime_type( $attachment_id ) ) {
			$path = get_attached_file( $attachment_id );
			if ( file_exists( $path ) ) {
				$svg = new SimpleXMLElement( wld_get_file_content( $path ) );
				if ( $svg ) {
					$width      = 0;
					$height     = 0;
					$attributes = $svg->attributes();
					$view_box   = $attributes->viewBox ?? ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName

					if ( isset( $attributes->width, $attributes->height ) ) {
						$width  = $attributes->width;
						$height = $attributes->height;
					} elseif ( $view_box ) {
						$sizes = explode( ' ', $view_box );
						/** @noinspection OffsetOperationsInspection */
						if ( isset( $sizes[2], $sizes[3] ) ) {
							[ , , $width, $height ] = $sizes;
						}
					}

					$image[1] = (int) $width;
					$image[2] = (int) $height;
				}
			}
		}

		return $image;
	}
}
