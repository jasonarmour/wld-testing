<?php

class WLD_Images {
	public static $sizes            = array();
	public static $bg_fit_endpoints = array( 320, 375, 768, 1024, 1440, 1600, 1920 );

	protected static $svg_sprite = '';

	public static function init() : void {
		// todo: it seems to me that these sizes are not actually used in the blog and you can probably switch to WP sizes
		$sizes = array(
			// Size for first post thumbnail in archive
			'390x400' => array(
				'width'  => 390,
				'height' => 400,
				'crop'   => 'cover',
			),
			// Size for post thumbnail in archive
			'262x258' => array(
				'width'  => 262,
				'height' => 258,
				'crop'   => 'cover',
			),
			// Size for post thumbnail in search
			'200x200' => array(
				'width'  => 200,
				'height' => 200,
				'crop'   => 'cover',
			),
			// Size for single post thumbnail
			'571x360' => array(
				'width'  => 571,
				'height' => 360,
				'crop'   => 'cover',
			),
			// Size for 404 page
			'500x0'   => array(
				'width'  => 500,
				'height' => 0,
				'crop'   => false,
			),
			// Size for Blog Banner
			'975x0'   => array(
				'width'  => 975,
				'height' => 0,
				'crop'   => false,
			),
		);

		self::$sizes            = apply_filters( 'wld_image_init_get_sizes', $sizes );
		self::$bg_fit_endpoints = apply_filters(
			'wld_image_init_get_bg_fit_endpoints',
			self::$bg_fit_endpoints
		);

		foreach ( self::$bg_fit_endpoints as $endpoint ) {
			// Sizes for cover background endpoints
			self::$sizes[ $endpoint . 'x0' ] = array(
				'width'  => $endpoint,
				'height' => 0,
				'crop'   => false,
			);
		}

		add_action(
			'after_setup_theme',
			array( self::class, 'register' )
		);
		add_filter(
			'image_size_names_choose',
			array( self::class, 'add_sizes_in_choose' )
		);
		add_filter(
			'image_resize_dimensions',
			array( self::class, 'add_crop_dimensions' ),
			10,
			6
		);
		add_filter(
			'the_content',
			array( self::class, 'remove_image_wrap_p' ),
			99
		);
		add_filter(
			'acf_the_content',
			array( self::class, 'remove_image_wrap_p' ),
			99
		);
		add_filter(
			'intermediate_image_sizes_advanced',
			array( self::class, 'recovery_crop' )
		);
		add_action(
			'wp_footer',
			array( self::class, 'the_sprite' ),
			2
		);
		add_filter(
			'jpeg_quality',
			static function () {
				return 100;
			}
		);
	}

	public static function add_size( string $name, string $crop = '', int $width = 0, int $height = 0 ) : void {
		if ( 0 === $width && 0 === $height ) {
			$sizes  = self::get_sizes( $name );
			$width  = $sizes['width'];
			$height = $sizes['height'];
			if ( 0 === $width && 0 === $height ) {
				return;
			}
		}
		self::$sizes[ $name ] = array(
			'width'  => $width,
			'height' => $height,
			'crop'   => 'cover' === $crop ? 'cover' : (bool) $crop,
		);
	}

	/** @noinspection OffsetOperationsInspection */
	public static function get_sizes( string $name ) : array {
		$sizes = explode( 'x', $name );

		return array(
			'width'  => absint( $sizes[0] ?? 0 ),
			'height' => absint( $sizes[1] ?? 0 ),
		);
	}

	public static function register() : void {
		ksort( self::$sizes, SORT_NUMERIC );
		foreach ( self::$sizes as $name => $args ) {
			add_image_size(
				$name,
				$args['width'],
				$args['height'],
				$args['crop']
			);
		}
	}

	public static function add_sizes_in_choose( array $size_names ) : array {
		foreach ( self::$sizes as $name => $args ) {
			$size_names[ $name ] = str_replace(
				array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '~' ),
				array( '𝟶', '𝟷', '𝟸', '𝟹', '𝟺', '𝟻', '𝟼', '𝟽', '𝟾', '𝟿', '  ' ),
				str_pad( $name, 8, '~' )
			);
		}

		return $size_names;
	}

	/** @noinspection PhpTooManyParametersInspection */
	public static function add_crop_dimensions( $default, $orig_w, $orig_h, $new_w, $new_h, $crop ) {
		$name = $new_w . 'x' . $new_h; // unfortunately the wrong size may get here
		if ( isset( self::$sizes[ $name ] ) && 'cover' === self::$sizes[ $name ]['crop'] ) {
			$crop = 'cover';
		}

		if ( 'cover' !== $crop ) {
			return $default;
		}

		$size_ratio = max( $new_w / $orig_w, $new_h / $orig_h );
		$crop_w     = round( $new_w / $size_ratio );
		$crop_h     = round( $new_h / $size_ratio );
		$s_x        = floor( ( $orig_w - $crop_w ) / 2 );
		$s_y        = floor( ( $orig_h - $crop_h ) / 2 );

		return array(
			0,
			0,
			(int) $s_x,
			(int) $s_y,
			(int) $new_w,
			(int) $new_h,
			(int) $crop_w,
			(int) $crop_h,
		);
	}

	public static function remove_image_wrap_p( ?string $content ) {
		return preg_replace( '/<p>(<img.*\/>)<\/p>/iU', '\1', $content );
	}

	public static function get_img( ?int $attachment_id, string $size = 'full', array $attr = array() ) : string {
		self::check_size( $size );
		if ( is_numeric( $attachment_id ) ) {
			$attachment_id = (int) $attachment_id;
			$attr          = array_merge( array( 'class' => '' ), $attr );

			return wp_get_attachment_image( $attachment_id, $size, false, $attr );
		}

		return '';
	}

	public static function the_sprite() : void {
		if ( self::$svg_sprite ) {
			echo '<svg width="0" height="0" style="display:none!important;">' . self::$svg_sprite . '</svg>';
		}
	}

	/** @noinspection ParameterDefaultValueIsNotNullInspection */
	public static function get_fit_bg_image(
		?int $attachment_id, ?string $size = '1920x0', $fit = 'cover', array $attr = array()
	) : string {
		// todo: add cover sizes creator method and function
		self::check_size( $size );
		if ( is_numeric( $attachment_id ) ) {
			$attachment_id = (int) $attachment_id;
			$attr          = array_merge( array( 'class' => '' ), $attr );
			$image         = wp_get_attachment_image_src( $attachment_id, $size );

			if ( $image ) {
				$image_meta   = wp_get_attachment_metadata( $attachment_id ) ?: array();
				$current_size = wp_get_registered_image_subsizes()[ $size ];

				$attr['class'] .= ' object-fit object-fit-' . $fit;

				if ( $image_meta ) {
					$sizes = array();

					$sizes[ $image_meta['width'] ][ $image_meta['height'] ] = basename( $image_meta['file'] );
					if ( ! in_array( $current_size['width'], self::$bg_fit_endpoints, true ) ) {
						self::$bg_fit_endpoints[] = $current_size['width'];
					}

					foreach ( $image_meta['sizes'] as $meta_size ) {
						if ( in_array( $meta_size['width'], self::$bg_fit_endpoints, true ) ) {
							$sizes[ $meta_size['width'] ][ $meta_size['height'] ] = $meta_size['file'];
						}
					}

					$dirname = _wp_get_attachment_relative_path( $image_meta['file'] ); // todo replace private

					if ( $dirname ) {
						$dirname = trailingslashit( $dirname );
					}

					$upload_dir    = wp_get_upload_dir();
					$image_baseurl = trailingslashit( $upload_dir['baseurl'] ) . $dirname;

					if (
						is_ssl() &&
						0 !== strncmp( $image_baseurl, 'https', 5 ) &&
						wp_parse_url( $image_baseurl, PHP_URL_HOST ) === $_SERVER['HTTP_HOST']
					) {
						$image_baseurl = set_url_scheme( $image_baseurl, 'https' );
					}

					$sources    = array();
					$size_array = array(
						absint( $image[1] ),
						absint( $image[2] ),
					);

					foreach ( self::$bg_fit_endpoints as $endpoint ) {
						if ( $endpoint > $current_size['width'] || empty( $sizes[ $endpoint ] ) ) {
							continue;
						}

						if ( 'cover' === $fit ) {
							if ( 0 === $current_size['height'] ) {
								$file = $sizes[ $endpoint ][ max( array_keys( $sizes[ $endpoint ] ) ) ];
							} else {
								$file = $sizes[ $endpoint ][ $current_size['height'] ] ?? '';
							}
						} else {
							$ratio_height = (int) round(
								$endpoint / $current_size['width'] * $current_size['height']
							);

							$file = $sizes[ $endpoint ][ $ratio_height ] ?? '';
						}

						if ( $file ) {
							$sources[ $endpoint ] = array(
								'url'        => $image_baseurl . $file,
								'descriptor' => 'w',
								'value'      => $endpoint,
							);
						}
					}

					ksort( $sources, SORT_NUMERIC );

					$sources = apply_filters(
						'wp_calculate_image_srcset',
						$sources,
						$size_array,
						$image[0],
						$image_meta,
						$attachment_id
					);

					if ( count( $sources ) > 1 ) {
						$srcset = '';
						foreach ( $sources as $source ) {
							$srcset .= str_replace( ' ', '%20', $source['url'] );
							$srcset .= ' ' . $source['value'] . $source['descriptor'] . ', ';
						}

						$attr['srcset'] = rtrim( $srcset, ', ' );
						if ( empty( $attr['sizes'] ) ) {
							$attr['sizes'] = wp_calculate_image_sizes(
								$size_array,
								$image[0],
								$image_meta,
								$attachment_id
							);

						}
					}
				}

				if ( empty( $attr['srcset'] ) && ! empty( $attr['sizes'] ) ) {
					unset( $attr['sizes'] );
				}

				return wp_get_attachment_image(
					$attachment_id,
					$size,
					false,
					$attr
				);
			}
		}

		return '';
	}

	protected static function check_size( string $size ) : void {
		$sizes   = get_intermediate_image_sizes();
		$sizes[] = 'full';
		if ( ! in_array( $size, $sizes, true ) ) {
			do_action(
				'qm/error', // phpcs:ignore WordPress.NamingConventions.ValidHookName
				'Size "{size}" not registered',
				array( 'size' => $size )
			);
		}
	}

	public static function get_bg_atts( ?int $attachment_id, string $size = 'full' ) : string {
		self::check_size( $size );
		$x2 = '';
		$x1 = (string) wp_get_attachment_image_url( $attachment_id, $size );
		if ( $x1 ) {
			$x2 = self::get_attachment_image_x2_url( $attachment_id, $size );
		}

		$atts = array(
			'data-1x' => $x1,
			'data-2x' => $x2,
		);

		return acf_esc_atts( acf_clean_atts( array_filter( $atts ) ) );
	}

	public static function get_attachment_image_x2_url( ?int $attachment_id, string $size = 'full' ) : string {
		$file = (array) image_get_intermediate_size( $attachment_id, $size );
		if ( isset( $file['path'] ) ) {
			$path_info = pathinfo( $file['path'] );
			if ( isset( $path_info['dirname'] ) ) {
				$upload_dir = wp_upload_dir();
				$extension  = $path_info['extension'] ?? '';
				$x2_file    = trailingslashit( $path_info['dirname'] ) . $path_info['filename'] . '@2x.' . $extension;
				if ( file_exists( $upload_dir['basedir'] . '/' . $x2_file ) ) {
					return $upload_dir['baseurl'] . '/' . $x2_file;
				}
			}
		}

		return '';
	}

	public static function recovery_crop( array $new_sizes ) : array {
		foreach ( $new_sizes as $name => $size ) {
			if ( isset( self::$sizes[ $name ] ) && 'cover' === self::$sizes[ $name ]['crop'] ) {
				$new_sizes[ $name ]['crop'] = 'cover';
			}
		}

		return $new_sizes;
	}
}
