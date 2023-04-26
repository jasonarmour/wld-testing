<?php

class WLD_Yoast_SEO_Score_Fix {
	public static function init() : void {
		if ( wld_is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {
			add_filter(
				'wpseo_pre_analysis_post_content',
				array( self::class, 'pre_analysis_post_content' ),
				10,
				2
			);

			if ( is_admin() ) {
				add_action(
					'admin_footer-post.php',
					array( self::class, 'js' )
				);
			}

			if ( wp_doing_ajax() ) {
				add_action(
					'wp_ajax_wld_get_content_by_yoast_seo_score',
					array( self::class, 'ajax_get_content_by_yoast_seo_score' )
				);
			}

			add_action(
				'after_delete_post',
				array( self::class, 'delete_post' )
			);
		}
	}

	public static function pre_analysis_post_content( string $content, ?WP_Post $post ) : string {
		if ( $post && 'templates/tpl-flexible-content.php' === get_page_template_slug() ) {
			$content .= get_option( '_content_by_yoast_seo_score_' . $post->ID );
		}

		return $content;
	}

	public static function js() : void {
		if ( 'templates/tpl-flexible-content.php' === get_page_template_slug() ) {
			$post_id = get_the_ID();
			?>
			<!--suppress JSUnresolvedVariable, JSUnresolvedFunction -->
			<script>
				jQuery( window ).on( 'YoastSEO:ready', function() {
					const pluginName = 'wldFlexContentPlugin';

					YoastSEO.app.registerPlugin( pluginName, { status: 'loading' } );

					jQuery.post( {
						url: ajaxurl,
						data: {
							action: 'wld_get_content_by_yoast_seo_score',
							post_id: <?php echo $post_id; ?>,
							_ajax_nonce: '<?php echo wp_create_nonce( 'wld_get_content_by_yoast_seo_score' ); ?>'
						}
					} ).done( function( response ) {
						if ( response.success ) {
							YoastSEO.app.pluginReady( pluginName );
							YoastSEO.app.registerModification( 'content', () => response.data.content, pluginName, 1 );
						}
					} );
				} );
			</script>
			<?php
		}
	}

	public static function ajax_get_content_by_yoast_seo_score() : void {
		check_ajax_referer( 'wld_get_content_by_yoast_seo_score' );

		$post_id = $_POST['post_id'] ?? 0;
		if ( $post_id ) {
			$post = get_post( $post_id );
			if ( $post ) {
				$content = self::get_content( $post );
				update_option( '_content_by_yoast_seo_score_' . $post->ID, $content );
				wp_send_json_success( array( 'content' => $content ) );
			}
		}

		wp_send_json_error();
	}

	public static function delete_post( $post_id ) : void {
		delete_option( '_content_by_yoast_seo_score_' . $post_id );
	}

	public static function get_content( ?WP_Post $current_post ) : string {
		global $post;
		static $content = array();

		$post_id = $current_post->ID ?? 0;
		$post    = $current_post;

		if ( $post_id && empty( $content[ $post_id ] ) ) {
			ob_start();
			if ( have_rows( 'content', $current_post ) ) {
				WLD_ACF_Title_Field::$has_main_auto_title = true;
				while ( have_rows( 'content', $current_post ) ) {
					the_row();
					WLD_ACF_Flex_Content::the_content();
				}
			}

			$allowed_html        = array(
				'a'          => array(
					'href' => true,
				),
				'blockquote' => array(
					'cite' => true,
				),
				'br'         => array(),
				'caption'    => array(),
				'dd'         => array(),
				'dl'         => array(),
				'dt'         => array(),
				'em'         => array(),
				'h1'         => array(),
				'h2'         => array(),
				'h3'         => array(),
				'h4'         => array(),
				'h5'         => array(),
				'h6'         => array(),
				'img'        => array(
					'alt'    => true,
					'height' => true,
					'src'    => true,
					'width'  => true,
				),
				'li'         => array(),
				'p'          => array(),
				'strong'     => array(),
				'table'      => array(),
				'tbody'      => array(),
				'td'         => array(),
				'tfoot'      => array(),
				'th'         => array(),
				'thead'      => array(),
				'tr'         => array(),
				'ul'         => array(),
				'ol'         => array(),
			);
			$content[ $post_id ] = wp_kses( ob_get_clean(), $allowed_html );

			while ( ob_get_level() ) {
				ob_end_clean();
			}
		}

		wp_reset_postdata();

		return $content[ $post_id ] ?? '';
	}
}
