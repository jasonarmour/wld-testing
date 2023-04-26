<?php

class WLD_WC_Video_Thumbnail {
	public const KEY = '_thumbnail_id';

	public static function init() : void {
		add_filter(
			'wp_get_attachment_image_src',
			array( self::class, 'set_src' ),
			10,
			4
		);
		add_filter(
			'wp_get_attachment_image_attributes',
			array( self::class, 'set_attr' ),
			10,
			2
		);
		add_filter(
			'wp_prepare_attachment_for_js',
			array( self::class, 'set_js_data' ),
			10,
			2
		);
		add_filter(
			'attachment_fields_to_edit',
			array( static::class, 'add_field' ),
			10,
			2
		);
		add_action(
			'edit_attachment',
			array( static::class, 'save_field' )
		);
		add_action(
			'admin_print_footer_scripts',
			array( static::class, 'add_js' ),
			PHP_INT_MAX
		);
	}

	public static function set_src( $image, $attachment_id, $size, bool $icon ) {
		if ( false === $icon && empty( $image[0] ) && wp_attachment_is( 'video', $attachment_id ) ) {
			$attachment_id = absint( get_post_meta( $attachment_id, static::KEY, true ) );

			remove_filter(
				'wp_get_attachment_image_src',
				array( self::class, 'set_src' )
			);

			$image = wp_get_attachment_image_src( $attachment_id, $size );

			if ( empty( $image ) ) {
				$dimensions = wc_get_image_size( $size );
				$src        = wc_placeholder_img_src( $size );
				if ( $src ) {
					$image = array(
						$src,
						$dimensions['width'],
						$dimensions['height'],
					);
				}
			}

			add_filter(
				'wp_get_attachment_image_src',
				array( self::class, 'set_src' ),
				10,
				4
			);
		}

		return $image;
	}

	public static function set_attr( array $attr, $attachment ) : array {
		if ( wp_attachment_is( 'video', $attachment ) ) {
			if ( empty( $attr['title'] ) ) {
				$attr['title'] = 'Video: ' . get_the_title( $attachment );
			}

			$attr['class'] .= ' video';
		}

		return $attr;
	}

	public static function set_js_data( array $response, $attachment ) : array {
		$size = 'thumbnail';
		if ( wp_attachment_is( 'video', $attachment ) && empty( $response['sizes'][ $size ] ) ) {
			if ( ! isset( $response['sizes'] ) ) {
				$response['sizes'] = array();
			}

			$attachment_id = absint( get_post_meta( $attachment->ID, static::KEY, true ) );
			if ( $attachment_id ) {
				$downsize                   = image_downsize( $attachment_id, $size );
				$response['sizes'][ $size ] = array(
					'height'      => $downsize[2],
					'width'       => $downsize[1],
					'url'         => $downsize[0],
					'orientation' => $downsize[2] > $downsize[1] ? 'portrait' : 'landscape',
				);
			} else {
				$url                        = wc_placeholder_img_src( $size );
				$dimensions                 = wc_get_image_size( $size );
				$response['sizes'][ $size ] = array(
					'height'      => $dimensions['height'],
					'width'       => $dimensions['width'],
					'url'         => $url,
					'orientation' => $dimensions['height'] > $dimensions['width'] ? 'portrait' : 'landscape',
				);
			}
		}

		return $response;
	}

	public static function add_field( array $fields, WP_Post $post ) : array {
		if ( wp_attachment_is( 'video', $post ) && is_ajax() ) {
			$attachment_id = absint( get_post_meta( $post->ID, static::KEY, true ) );
			$src           = wp_get_attachment_image_url( $attachment_id );
			if ( $attachment_id && $src ) {
				$html = '
					<button type="button" class="wld-video-thumbnail-set"><img src="' . $src . '" alt=""></button>
					<button type="button" class="wld-video-thumbnail-del">Del image</button>';
			} else {
				$attachment_id = '';
				$html          = '
					<button type="button" class="wld-video-thumbnail-set">Set Image</button>
					<button type="button" class="wld-video-thumbnail-del hidden">Del image</button>';
			}

			$html .= sprintf(
				'
				<input type="hidden" id="attachments-%1$d-%2$s" name="attachments[%1$d][%2$s]" value="%3$d">',
				$post->ID,
				self::KEY,
				$attachment_id
			);

			$fields[ trim( static::KEY, '_' ) ] = array(
				'label' => __( 'Thumbnail ID', 'parent-theme' ),
				'input' => 'html',
				'html'  => $html,
			);
		}

		return $fields;
	}

	public static function save_field( $attachment_id ) : void {
		if ( isset( $_REQUEST['attachments'][ $attachment_id ][ static::KEY ] ) ) { // phpcs:ignore
			$value = absint( $_REQUEST['attachments'][ $attachment_id ][ static::KEY ] ?? '' ); // phpcs:ignore
			if ( $value ) {
				update_post_meta( $attachment_id, static::KEY, $value );
			} else {
				delete_post_meta( $attachment_id, static::KEY );
			}
		}
	}

	public static function add_js() : void {
		if ( 'post' === get_current_screen()->base ) {
			?>
			<script>
				jQuery( function( $ ) {
					$( 'body' ).on( 'click', '.wld-video-thumbnail-set', function( e ) {
						e.preventDefault();

						const $field = $( this ).siblings( 'input' );
						const custom_uploader = wp.media( {
							title: 'Set Thumbnail for Video',
							library: {
								type: 'image'
							},
							button: {
								text: 'Set Thumbnail'
							},
							multiple: false
						} ).on( 'select', function() {
							const attachment = custom_uploader.state().get( 'selection' ).first().toJSON();
							if ( attachment.id ) {
								const $img = `<img
									src="${ attachment.sizes.thumbnail.url }"
									width="${ attachment.sizes.thumbnail.width }"
									height="${ attachment.sizes.thumbnail.height }"
									title="Set Image"
									alt="">`;

								$field
									.val( attachment.id )
									.trigger( 'change' )
									.siblings( '.wld-video-thumbnail-del' )
									.removeClass( 'hidden' )
									.siblings( '.wld-video-thumbnail-set' )
									.html( $img );
							}
						} ).open();
					} ).on( 'click', '.wld-video-thumbnail-del', function() {
						$( this )
							.addClass( 'hidden' )
							.siblings( '.wld-video-thumbnail-set' )
							.html( 'Set Image' )
							.siblings( 'input' )
							.val( '' )
							.trigger( 'change' );
					} );
				} );
			</script>
			<?php
		}
	}
}
