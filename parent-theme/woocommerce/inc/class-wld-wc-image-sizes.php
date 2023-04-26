<?php /** @noinspection PhpUnused, UnknownInspectionInspection */


class WLD_WC_Image_Sizes {
	public const WOO_SIZES = array(
		'single'               => array(
			'width'  => 600,
			'height' => 600,
			'crop'   => 0,
		),
		'gallery_thumbnail'    => array(
			'width'  => 100,
			'height' => 100,
			'crop'   => 0,
		),
		'loop_thumbnail'       => array(
			'width'  => 300,
			'height' => 300,
			'crop'   => 0,
		),
		'cart_thumbnail'       => array(
			'width'  => 100,
			'height' => 100,
			'crop'   => 0,
		),
		'thank_you_background' => array(
			'width'  => 600,
			'height' => 0,
			'crop'   => 0,
		),
	);

	public static function init( array $sizes = array() ) : void {
		$sizes = array_merge( static::WOO_SIZES, $sizes );

		/** @see wc_get_image_size */
		add_filter(
			'woocommerce_get_image_size_thumbnail',
			static function () use ( $sizes ) {
				return $sizes['cart_thumbnail'];
			}
		);
		add_filter(
			'woocommerce_get_image_size_single',
			static function () use ( $sizes ) {
				return $sizes['single'];
			}
		);
		add_filter(
			'woocommerce_get_image_size_gallery_thumbnail',
			static function () use ( $sizes ) {
				return $sizes['gallery_thumbnail'];
			}
		);
		add_filter(
			'woocommerce_gallery_thumbnail_size',
			static function () {
				return 'woocommerce_gallery_thumbnail';
			}
		);
		add_filter(
			'single_product_archive_thumbnail_size',
			static function () {
				return 'woocommerce_loop_thumbnail';
			}
		);
		add_filter(
			'subcategory_archive_thumbnail_size',
			static function () {
				return 'woocommerce_loop_thumbnail';
			}
		);

		WLD_Images::add_size(
			'woocommerce_loop_thumbnail',
			$sizes['loop_thumbnail']['crop'],
			$sizes['loop_thumbnail']['width'],
			$sizes['loop_thumbnail']['height']
		);
		WLD_Images::add_size(
			'thank_you_background',
			$sizes['thank_you_background']['crop'],
			$sizes['thank_you_background']['width'],
			$sizes['thank_you_background']['height']
		);
	}
}
