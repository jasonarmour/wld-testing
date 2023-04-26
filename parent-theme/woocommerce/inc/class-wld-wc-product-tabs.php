<?php /** @noinspection UnknownInspectionInspection, PhpUnused */


class WLD_WC_Product_Tabs {
	public static $tabs = array();

	public static function init(): void {
		add_filter(
			'woocommerce_product_tabs',
			array( static::class, 'tabs' )
		);
		add_action(
			'admin_head',
			array( static::class, 'styles' )
		);
		add_action(
			'admin_footer',
			array( static::class, 'script' )
		);

		// phpcs:disable Squiz.PHP.CommentedOutCode.Found
		//new WLD_WC_Product_Tab();
		//new WLD_WC_Product_Tab( esc_html__( 'Other', 'parent-theme' ), 40 );
	}

	public static function tabs( array $tabs ): array {
		/* Delete */
		if ( isset( $tabs['description'] ) ) {
			unset( $tabs['description'] );
		}

		/* Rename */
		if ( isset( $tabs['additional_information'] ) ) {
			$tabs['additional_information']['title'] = esc_html__( 'Product Information', 'parent-theme' );
		}

		return $tabs;
	}

	public static function styles(): void {
		?>
		<!--suppress CssUnusedSymbol -->
		<style>
			div.form-field {
				padding: 5px 20px 5px 162px !important;
				margin: 9px 0;
			}

			.form-field-upload .thumbnail {
				position: relative;
				display: inline-flex;
				width: 150px;
				height: 150px;
				box-shadow: inset 0 0 15px rgba(0, 0, 0, 0.1), inset 0 0 0 1px rgba(0, 0, 0, 0.05);
				background: #eee;
				margin: 0 .3em .3em 0;
				vertical-align: top;
			}

			.form-field-upload .thumbnail:after {
				content: "";
				display: block;
				position: absolute;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.1);
				overflow: hidden;
			}

			.form-field-upload .thumbnail img {
				display: block;
				margin: auto;
				max-width: 100%;
				height: auto;
			}

			.custom-tab .wp-editor-area {
				float: none !important;
				height: auto;
			}
		</style>
		<?php
	}

	public static function script(): void {
		?>
		<!--suppress HtmlRequiredAltAttribute, RequiredAttributes, JSCheckFunctionSignatures -->
		<script>
			( function( $ ) {
				$.fn.wldUpload = function() {
					if ( this && this.length ) {
						this.each( function() {
							const
								$selectUploads = $( '.select-uploads', this ),
								$clearUploads = $( '.clear-uploads', this );

							$selectUploads.on( 'click', function() {
								const
									$wrap = $( this ).parent(),
									multiple = $wrap.attr( 'data-multiple' ) === 'true',
									uploader = wp.media( { multiple: multiple } );

								uploader.on( 'select', function() {
									const values = [],
										$thumbnails = [];

									if ( multiple ) {
										uploader.state().get( 'selection' ).each( function( attachment ) {
											values.push( attachment.get( 'id' ) );
											$thumbnails.push( getThumbnail( attachment ) );
										} );
									} else {
										const attachment = uploader.state().get( 'selection' ).first();

										values.push( attachment.get( 'id' ) );
										$thumbnails.push( getThumbnail( attachment ) );
									}

									$wrap.find( '.thumbnail' ).remove().end().prepend( $thumbnails );
									$wrap.find( 'input' ).val( values.join( ',' ) ).trigger( 'change' );
									$wrap.find( '.clear-uploads' ).toggle( $thumbnails.length > 0 );
								} ).open();
							} );

							$clearUploads.on( 'click', function() {
								const $parent = $( this ).parent();

								$( this ).hide();
								$parent.find( '.thumbnail' ).remove();
								$parent.parent().find( 'input' ).val( '' );
							} );

							function getThumbnail( attachment ) {
								const type = attachment.get( 'type' );

								let src;

								if ( 'image' === type ) {
									const sizes = attachment.get( 'sizes' );
									if ( sizes && sizes['thumbnail'] ) {
										src = sizes['thumbnail']['url'];
									} else {
										src = attachment.get( 'url' );
									}
								} else {
									src = attachment.get( 'icon' );
								}

								const $img = $( '<img>', {
									src: src,
									alt: attachment.get( 'alt' )
								} );

								return $( '<span>', { class: 'thumbnail', append: $img } );
							}
						} );

						return this;
					}
				};

				$( '.form-field-upload' ).wldUpload();
			} )( jQuery );
		</script>
		<?php
	}
}
