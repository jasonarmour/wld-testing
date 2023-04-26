<?php /** @noinspection UnknownInspectionInspection, PhpUnused, PhpUndefinedFunctionInspection */


class WLD_WC_Video_URL_For_Gallery {
	public const KEY = 'video_url_for_gallery';

	public static function init(): void {
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
		add_filter(
			'woocommerce_gallery_full_size',
			array( static::class, 'change_full_size_in_gallery' )
		);
		add_filter(
			'image_downsize',
			array( static::class, 'set_video_url_in_full_gallery_size' ),
			10,
			3
		);
		add_filter(
			'woocommerce_gallery_image_html_attachment_image_params',
			array( static::class, 'add_video_classname_in_image' ),
			10,
			2
		);
		add_action(
			'admin_head',
			array( static::class, 'add_admin_style' )
		);
	}

	public static function add_field( array $fields, WP_Post $post ): array {
		if ( wp_attachment_is_image( $post ) ) {
			$value = get_post_meta( $post->ID, static::KEY, true );

			$fields[ static::KEY ] = array(
				'value' => $value ?: '',
				'class' => 'widefat',
				'label' => esc_html__( 'Product Gallery Video URL', 'parent-theme' ),
			);
		}

		return $fields;
	}

	public static function save_field( $attachment_id ): void {
		$value = wc_clean( $_REQUEST['attachments'][ $attachment_id ][ static::KEY ] ?? '' ); // phpcs:ignore
		if ( $value ) {
			update_post_meta( $attachment_id, static::KEY, $value );
		} else {
			delete_post_meta( $attachment_id, static::KEY );
		}
	}

	public static function change_full_size_in_gallery(): string {
		return static::KEY;
	}

	/**
	 * @param bool|array $downsize Whether to short-circuit the image downsize. Default false.
	 * @param int|string $attachment_id Attachment ID for image.
	 * @param string|array $size Size of image.
	 *
	 * @return array|false Return array where full URL replaced with the video URL or false if not video URL.
	 */
	public static function set_video_url_in_full_gallery_size( $downsize, $attachment_id, $size ) {
		if ( static::KEY === $size && static::has_video( (int) $attachment_id ) ) {
			$meta = wp_get_attachment_metadata( $attachment_id );
			if ( $meta ) {
				$downsize = array(
					static::get_video_url( $attachment_id ),
					$meta['width'],
					$meta['height'],
					false,
				);
			}
		}

		return $downsize;
	}

	public static function add_video_classname_in_image( array $params, int $attachment_id ): array {
		if ( static::has_video( $attachment_id ) ) {
			$params['class'] = trim( $params['class'] . ' video' );
		}

		return $params;
	}

	public static function add_admin_style(): void {
		if ( wp_attachment_is_image() ) {
			?>
			<style>
				input[id*="-video_url_for_gallery"] {
					width: 100%;
				}
			</style>
			<?php
		}
	}

	public static function has_video( int $attachment_id ): bool {
		return ! empty( static::get_video_url( $attachment_id ) );
	}

	public static function get_video_url( int $attachment_id ): string {
		return wld_get_supported_video_url(
			(string) get_post_meta( $attachment_id, static::KEY, true )
		);
	}
}
