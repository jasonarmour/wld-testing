<?php
/**
 * ACF helpers
 */
if ( ! function_exists( 'wld_get' ) ) {
	function wld_get( string $selector, ...$args ) : string {
		return WLD_Fields::get( $selector, $args );
	}
}
if ( ! function_exists( 'wld_the' ) ) {
	function wld_the( string $selector, ...$args ) : void {
		echo WLD_Fields::get( $selector, $args );
	}
}
if ( ! function_exists( 'wld_loop' ) ) {
	function wld_loop( $selector_or_posts_array, string $wrapper = '' ) : bool {
		return WLD_Fields::loop( $selector_or_posts_array, $wrapper );
	}
}
if ( ! function_exists( 'wld_wrap' ) ) {
	function wld_wrap( string $selector, string $class = '', bool $force = true, array $attrs = array() ) : bool {
		return WLD_Fields::wrap( $selector, $class, $force, $attrs );
	}
}
if ( ! function_exists( 'wld_has' ) ) {
	function wld_has( ...$selectors_and_maybe_conditional ) : bool {
		return WLD_Fields::has( $selectors_and_maybe_conditional );
	}
}
if ( ! function_exists( 'wld_the_as' ) ) {
	function wld_the_as( string $as_type, string $selector, ...$args ) : void {
		WLD_Fields::next_field_process_as_type( $as_type );

		echo WLD_Fields::get( $selector, $args );
	}
}
if ( ! function_exists( 'wld_get_as' ) ) {
	function wld_get_as( string $as_type, string $selector, ...$args ) : string {
		WLD_Fields::next_field_process_as_type( $as_type );

		return WLD_Fields::get( $selector, $args );
	}
}
if ( ! function_exists( 'wld_get_menu' ) ) {
	function wld_get_menu( string $selector, array $args = array(), $post_id = null ) : string {
		$menu_id = get_field( $selector, $post_id, false );
		if ( $menu_id ) {
			$args = (array) wp_parse_args(
				$args,
				array(
					'fallback_cb' => '__return_empty_string',
				)
			);

			$args['menu'] = $menu_id;
			$args['echo'] = false;

			return wp_nav_menu( $args );
		}

		return '';
	}
}
if ( ! function_exists( 'wld_the_menu' ) ) {
	function wld_the_menu( string $selector, array $args = array(), $post_id = null ) : void {
		echo wld_get_menu( $selector, $args, $post_id );
	}
}
if ( ! function_exists( 'wld_get_sub_menu' ) ) {
	function wld_get_sub_menu( string $selector, array $args = array() ) : string {
		$menu_id = get_sub_field( $selector, false );
		if ( $menu_id ) {
			$args = (array) wp_parse_args(
				$args,
				array(
					'fallback_cb' => '__return_empty_string',
				)
			);

			$args['menu'] = $menu_id;
			$args['echo'] = false;

			return wp_nav_menu( $args );
		}

		return '';
	}
}
if ( ! function_exists( 'wld_the_sub_menu' ) ) {
	function wld_the_sub_menu( string $selector, array $args = array() ) : void {
		echo wld_get_sub_menu( $selector, $args );
	}
}
if ( ! function_exists( 'wld_get_form' ) ) {
	function wld_get_form( string $selector, bool $title = false, bool $description = false, $post_id = null
	) : string {
		if ( function_exists( 'gravity_form' ) ) {
			$form_id = get_field( $selector, $post_id, false );
			if ( $form_id ) {
				return gravity_form(
					$form_id,
					$title,
					$description,
					false,
					null,
					true,
					false,
					false
				);
			}
		}

		return '';
	}
}
if ( ! function_exists( 'wld_the_form' ) ) {
	function wld_the_form( string $selector, bool $title = false, bool $description = false, $post = null ) : void {
		echo wld_get_form( $selector, $title, $description, $post );
	}
}
if ( ! function_exists( 'wld_get_sub_form' ) ) {
	function wld_get_sub_form( string $selector, bool $title = false, bool $description = false ) : string {
		if ( function_exists( 'gravity_form' ) ) {
			$form_id = get_sub_field( $selector, false );
			if ( $form_id ) {
				return gravity_form(
					$form_id,
					$title,
					$description,
					false,
					null,
					true,
					false,
					false
				);
			}
		}

		return '';
	}
}
if ( ! function_exists( 'wld_the_sub_form' ) ) {
	function wld_the_sub_form( string $selector, bool $title = false, bool $description = false ) : void {
		echo wld_get_sub_form( $selector, $title, $description );
	}
}
if ( ! function_exists( 'wld_get_link' ) ) {
	/**
	 * @param string $selector array deprecated use wld_get_link_html_from_array()
	 * @param string $class
	 * @param bool   $empty
	 * @param null   $post_id
	 *
	 * @return string
	 */
	function wld_get_link( string $selector, string $class = '', bool $empty = false, $post_id = null ) : string {
		return wld_get_link_html_from_array( (array) get_field( $selector, $post_id, false ), $class, $empty );
	}
}
if ( ! function_exists( 'wld_the_link' ) ) {
	/**
	 * @param string $selector array deprecated use echo wld_get_link_html_from_array()
	 * @param string $class
	 * @param bool   $empty
	 * @param null   $post_id
	 *
	 * @return void
	 */
	function wld_the_link( string $selector, string $class = '', bool $empty = false, $post_id = null ) : void {
		echo wld_get_link( $selector, $class, $empty, $post_id );
	}
}
if ( ! function_exists( 'wld_get_sub_link' ) ) {
	function wld_get_sub_link( string $selector, string $class = '', bool $empty = false ) : string {
		return wld_get_link_html_from_array( (array) get_sub_field( $selector, false ), $class, $empty );
	}
}
if ( ! function_exists( 'wld_the_sub_link' ) ) {
	function wld_the_sub_link( string $selector, string $class = '', bool $empty = false ) : void {
		echo wld_get_sub_link( $selector, $class, $empty );
	}
}
if ( ! function_exists( 'wld_get_image' ) ) {
	function wld_get_image( string $selector, string $size = 'full', $post_id = null, array $attr = array()
	) : string {
		return WLD_Images::get_img( (int) get_field( $selector, $post_id, false ), $size, $attr );
	}
}
if ( ! function_exists( 'wld_the_image' ) ) {
	function wld_the_image( string $selector, string $size = 'full', $post_id = null, array $attr = array() ) : void {
		echo wld_get_image( $selector, $size, $post_id, $attr );
	}
}
if ( ! function_exists( 'wld_get_sub_image' ) ) {
	function wld_get_sub_image( string $selector, string $size = 'full', array $attr = array() ) : string {
		return WLD_Images::get_img( (int) get_sub_field( $selector, false ), $size, $attr );
	}
}
if ( ! function_exists( 'wld_the_sub_image' ) ) {
	function wld_the_sub_image( string $selector, string $size = 'full', array $attr = array() ) : void {
		echo wld_get_sub_image( $selector, $size, $attr );
	}
}
if ( ! function_exists( 'wld_get_sub_bg' ) ) {
	function wld_get_sub_bg( string $selector, string $size = '1920x0' ) : string {
		return WLD_Images::get_bg_atts( (int) get_sub_field( $selector, false ), $size );
	}
}
if ( ! function_exists( 'wld_the_sub_bg' ) ) {
	function wld_the_sub_bg( string $selector, string $size = '1920x0' ) : void {
		echo wld_get_sub_bg( $selector, $size );
	}
}
if ( ! function_exists( 'wld_get_bg' ) ) {
	function wld_get_bg( string $selector, string $size = '1920x0', $post_id = null ) : string {
		return WLD_Images::get_bg_atts( (int) get_field( $selector, $post_id, false ), $size );
	}
}
if ( ! function_exists( 'wld_the_bg' ) ) {
	function wld_the_bg( string $selector, string $size = '1920x0', $post_id = null ) : void {
		echo wld_get_bg( $selector, $size, $post_id );
	}
}
if ( ! function_exists( 'wld_get_sub_video_url' ) ) {
	function wld_get_sub_video_url( string $selector ) : string {
		return wld_get_supported_video_url( get_sub_field( $selector, false ) );
	}
}
if ( ! function_exists( 'wld_the_sub_video_url' ) ) {
	function wld_the_sub_video_url( string $selector ) : void {
		echo wld_get_sub_video_url( $selector );
	}
}
if ( ! function_exists( 'wld_get_video_url' ) ) {
	function wld_get_video_url( string $selector, $post_id = null ) : string {
		return wld_get_supported_video_url( get_field( $selector, $post_id, false ) );
	}
}
if ( ! function_exists( 'wld_the_video_url' ) ) {
	function wld_the_video_url( string $selector, $post_id = null ) : void {
		echo wld_get_video_url( $selector, $post_id );
	}
}

/**
 * Templates helpers
 */
if ( ! function_exists( 'wld_get_the_excerpt' ) ) {
	function wld_get_the_excerpt( int $length, bool $filter = false, bool $trim = false, string $after = '' ) : string {
		global $post;
		if ( has_excerpt() ) {
			$output = wp_strip_all_tags( $post->post_excerpt );
		} else {
			$output = get_the_content();
			if ( empty( $output ) && wld_is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) ) {
				$fields = get_fields( $post->ID );
				wld_array_to_excerpt( $fields, $output );
			}
			$output = wp_strip_all_tags( strip_shortcodes( $output ) );
			if ( false === $filter ) {
				$output = str_replace( array( "\r\n", "\r", "\n" ), '', $output );
				$output = trim( str_replace( '&nbsp;', ' ', $output ) );
				$output = preg_replace( '/[\s]+/', ' ', $output );
			}
			if ( strlen( $output ) > $length ) {
				$output = substr( $output, 0, $length );
				for ( $i = $length - 1; $i >= 0; $i -- ) {
					if ( preg_match( '/(\.|,|!|\?|:|;|\s)/', $output[ $i ] ) ) {
						$output = substr( $output, 0, $i + 1 );
						break;
					}
				}
			}
			if ( $trim ) {
				$output = rtrim( $output, '.,!?:; ' );
			}
		}
		$output = rtrim( $output, "\r\n" ) . $after;
		if ( $filter ) {
			return apply_filters( 'get_the_excerpt', $output );
		}

		return $output;
	}
}
if ( ! function_exists( 'wld_the_excerpt' ) ) {
	function wld_the_excerpt( int $length, bool $filter = false, bool $trim = false, string $after = '' ) : void {
		$output = wld_get_the_excerpt( $length, $filter, $trim, $after );
		if ( $filter ) {
			$output = apply_filters( 'the_excerpt', $output );
		}

		echo $output;
	}
}
if ( ! function_exists( 'wld_get_the_replace' ) ) {
	function wld_get_the_replace( ?string $text, string $replace = 'strong' ) : string {
		return str_replace(
			array( '[', ']' ),
			array( '<' . $replace . '>', '</' . $replace . '>' ),
			$text
		);
	}
}
if ( ! function_exists( 'wld_the_replace' ) ) {
	function wld_the_replace( ?string $text, string $replace = 'strong' ) : void {
		echo wld_get_the_replace( $text, $replace );
	}
}
if ( ! function_exists( 'wld_get_the_archive_link' ) ) {
	function wld_get_the_archive_link( $post = null, string $class = 'back-link' ) : string {
		$post = get_post( $post );
		if ( empty( $post ) ) {
			return '';
		}
		$url   = wld_get_the_archive_url( $post );
		$title = wld_get_the_archive_title( $post );
		if ( empty( $url ) || empty( $title ) ) {
			return '';
		}
		$title = __( 'Back to ', 'parent-theme' ) . '"' . $title . '"';

		return '<a href="' . $url . '" class="' . $class . '">' . $title . '</a>';
	}
}
if ( ! function_exists( 'wld_the_archive_link' ) ) {
	function wld_the_archive_link( $post = null, string $class = 'back-link' ) : void {
		echo wld_get_the_archive_link( $post, $class );
	}
}
if ( ! function_exists( 'wld_get_the_archive_url' ) ) {
	function wld_get_the_archive_url( $post = null ) : string {
		$post = get_post( $post );
		if ( empty( $post ) ) {
			return '';
		}
		$post_type = get_post_type( $post );
		if ( 'product' === $post_type && function_exists( 'wc_get_page_id' ) ) {
			$url = get_permalink( wc_get_page_id( 'shop' ) );
		} else {
			$url = get_post_type_archive_link( $post_type );
		}

		return (string) $url;
	}
}
if ( ! function_exists( 'wld_the_archive_url' ) ) {
	function wld_the_archive_url( $post = null ) : void {
		echo wld_get_the_archive_url( $post );
	}
}
if ( ! function_exists( 'wld_get_the_archive_title' ) ) {
	function wld_get_the_archive_title( $post = null ) : string {
		$post = get_post( $post );
		if ( ! $post ) {
			return '';
		}
		$post_type = get_post_type( $post );
		if ( 'post' === $post_type ) {
			return get_the_title( get_option( 'page_for_posts' ) );
		}
		if ( 'product' === $post_type && function_exists( 'wc_get_page_id' ) ) {
			return get_the_title( wc_get_page_id( 'shop' ) );
		}
		$post_type = get_post_type_object( get_post_type( $post ) );
		if ( $post_type ) {
			return $post_type->labels->all_items;
		}

		return '';
	}
}
if ( ! function_exists( 'wld_the_archive_title' ) ) {
	function wld_the_archive_title( $post = null ) : void {
		echo wld_get_the_archive_title( $post );
	}
}
if ( ! function_exists( 'wld_get_the_maybe_link' ) ) {
	function wld_get_the_maybe_link( ?string $text, ?array $link_array, string $class = '', bool $force_wrap = false
	) : string {
		if ( ! empty( $link_array['url'] ) ) {
			$link_array['title'] = $text;

			return wld_get_link_html_from_array( $link_array, $class );
		}

		if ( $force_wrap ) {
			$text = '<div class="' . $class . '">' . $text . '</div>';
		}

		return $text;
	}
}
if ( ! function_exists( 'wld_the_maybe_link' ) ) {
	function wld_the_maybe_link( ?string $text, ?array $link_array, string $class = '', bool $force_wrap = false
	) : void {
		echo wld_get_the_maybe_link( $text, $link_array, $class, $force_wrap );
	}
}
if ( ! function_exists( 'wld_get_the_by_seo_link' ) ) {
	function wld_get_the_by_seo_link( array $args = array( 'title' => 'Search engine optimization by:' ) ) : string {
		$args = (array) wp_parse_args(
			$args,
			array(
				'name' => '',
				'href' => '',
			)
		);

		return wld_get_the_by_link( $args );
	}
}
if ( ! function_exists( 'wld_the_by_seo_link' ) ) {
	function wld_the_by_seo_link( array $args = array() ) : void {
		echo wld_get_the_by_seo_link( $args );
	}
}
if ( ! function_exists( 'wld_get_the_by' ) ) {
	function wld_get_the_by() : string {
		return wld_get_the_by_link() . wld_get_the_by_seo_link();
	}
}
if ( ! function_exists( 'wld_the_by' ) ) {
	function wld_the_by() : void {
		echo wld_get_the_by();
	}
}
if ( ! function_exists( 'wld_get_the_logo' ) ) {
	function wld_get_the_logo(
		string $selector = 'wld_header_logo', string $size = 'full', string $id = 'options', $sizes = ''
	) : string {
		$logo  = '';
		$image = '';
		$attr  = array(
			'loading' => 'wld_header_logo' === $selector ? 'eager' : 'lazy',
			'alt'     => get_bloginfo( 'name' ),
		);
		if ( $sizes ) {
			$attr['sizes'] = $sizes;
		}

		foreach ( array( '', '_2' ) as $i ) {
			$image .= WLD_Images::get_img(
				(int) get_field( $selector . $i, $id, false ),
				$size,
				$attr
			);
		}
		if ( $image ) {
			$logo .= is_front_page() ? $image : '<a href="' . home_url() . '">' . $image . '</a>';
		}

		WLD_Images::the_sprite();

		return '<div class="logo">' . $logo . '</div>';
	}
}
if ( ! function_exists( 'wld_the_logo' ) ) {
	function wld_the_logo(
		string $selector = 'wld_header_logo', string $size = 'full', string $id = 'options', string $sizes = ''
	) : void {
		echo wld_get_the_logo( $selector, $size, $id, $sizes );
	}
}
if ( ! function_exists( 'wld_get_the_title' ) ) {
	function wld_get_the_title( ?string $title, string $class = '', int $level = 1 ) : string {
		return WLD_ACF_Flex_Content::get_the_title( (string) $title, $level, $class );
	}
}
if ( ! function_exists( 'wld_the_title' ) ) {
	function wld_the_title( ?string $title, string $class = '', int $level = 1 ) : void {
		echo wld_get_the_title( $title, $class, $level );
	}
}
if ( ! function_exists( 'wld_get_the_nav' ) ) {
	function wld_get_the_nav( string $location_label, bool $is_mobile = false ) : string {
		$args = array();

		if ( 'Header Main' === $location_label ) {
			if ( $is_mobile ) {
				$args['container']  = false;
				$args['menu_class'] = 'main-nav mobile-nav';
			} else {
				$args['container']       = 'nav';
				$args['container_class'] = 'main-nav-container';
				$args['menu_class']      = 'main-nav header-nav';
			}
		} elseif ( 'Header Second' === $location_label ) {
			if ( $is_mobile ) {
				$args['container']  = false;
				$args['menu_class'] = 'second-nav mobile-nav';
			} else {
				$args['container']       = 'nav';
				$args['container_class'] = 'second-nav-container';
				$args['menu_class']      = 'second-nav';
			}
		} elseif ( 'Footer Links' === $location_label ) {
			$args['container']  = false;
			$args['menu_class'] = 'links-nav';
		} elseif ( 'Footer Main' === $location_label ) {
			$args['container']       = 'nav';
			$args['container_class'] = 'footer-nav-container';
			$args['menu_class']      = 'footer-nav';
		}

		$args['theme_location']       = WLD_Nav::get_location( $location_label );
		$args['fallback_cb']          = '__return_empty_string';
		$args['is_mobile']            = $is_mobile;
		$args['echo']                 = false;
		$args['container_aria_label'] = $location_label;

		return (string) wp_nav_menu( $args );
	}
}
if ( ! function_exists( 'wld_the_nav' ) ) {
	function wld_the_nav( string $location_label, bool $is_mobile = false ) : void {
		echo wld_get_the_nav( $location_label, $is_mobile );
	}
}
if ( ! function_exists( 'wld_get_supported_video_url' ) ) {
	function wld_get_supported_video_url( ?string $url ) : string {
		$video_url = '';

		if ( $url ) {
			$youtube_id = WLD_YouTube::get_id( $url );
			if ( $youtube_id ) {
				$video_url = 'https://www.youtube.com/watch?v=' . $youtube_id;
			} elseif ( false !== strpos( $url, 'vimeo' ) ) {
				preg_match( '~vimeo\.com/(?>video/)?(\d+)~', $url, $matches );
				if ( isset( $matches[1] ) ) {
					$video_url = 'https://vimeo.com/' . $matches[1];
				}
			}
		}

		return $video_url;
	}
}
if ( ! function_exists( 'wld_get_supported_video_embed_url' ) ) {
	function wld_get_supported_video_embed_url( ?string $url ) : string {
		$video_url = '';

		if ( $url ) {
			$youtube_id = WLD_YouTube::get_id( $url );
			if ( $youtube_id ) {
				$video_url = 'https://www.youtube.com/embed/' . $youtube_id;
			} elseif ( false !== strpos( $url, 'vimeo' ) ) {
				preg_match( '~vimeo\.com/(?>video/)?(\d+)(\?.+)?~', $url, $matches );
				if ( isset( $matches[1] ) ) {
					$params = '?title=0&byline=0&portrait=0';
					if ( $matches[2] ) {
						$params = $matches[2];
					}

					$video_url = 'https://player.vimeo.com/video/' . $matches[1] . $params;
				}
			}
		}

		return $video_url;
	}
}

if ( ! function_exists( 'wld_get_the_pagination' ) ) {
	/** @noinspection DuplicatedCode, HtmlUnknownTarget */
	function wld_get_the_pagination( array $args = array() ) : string {
		global $wp_query;

		$args      = apply_filters( 'wld_pagination_args', $args );
		$old_query = $wp_query;
		if ( isset( $args['query'] ) ) {
			$wp_query = $args['query'];
		}

		$format = '';
		if ( isset( $args['format'] ) ) {
			$format = trim( str_replace( '=%#%', '', $args['format'] ), '?' );
		}

		$defaults = array(
			'type'      => 'list',
			'prev_text' => esc_html__( '&#8592; Previous', 'parent-theme' ),
			'next_text' => esc_html__( 'Next &#8594;', 'parent-theme' ),
		);

		$pagination = paginate_links( wp_parse_args( apply_filters( 'wld_pagination_args', $args ), $defaults ) );

		if ( ! empty( $args['first_last'] ) ) {

			$total   = $wp_query->max_num_pages;
			$current = get_query_var( 'paged' ) ? (int) get_query_var( 'paged' ) : 1;

			$first_url = get_pagenum_link();
			$last_url  = get_pagenum_link( $total );
			if ( $format ) {
				$last_url  = str_replace(
					array( '%_%', '%#%' ),
					array( $args['format'], $total ),
					trailingslashit( explode( '?', $first_url )[0] ) . '%_%'
				);
				$first_url = remove_query_arg( $format, $first_url );
			}

			$first_text  = esc_html__( 'First Page', 'parent-theme' );
			$first_link  = sprintf( '<a href="%s" class="first-page">%s</a>', $first_url, $first_text );
			$last_text   = esc_html__( 'Last Page', 'parent-theme' );
			$last_link   = sprintf( '<a href="%s" class="last-page">%s</a>', $last_url, $last_text );
			$pattern     = array();
			$replacement = array();

			if ( $current > 1 ) {
				$pattern[]     = '/(<ul[^>]*>)/';
				$replacement[] = "$1<li>$first_link</li>";
			}

			if ( $current < $total ) {
				$pattern[]     = '/(<\/ul>)/';
				$replacement[] = "<li>$last_link</li>$1";
			}

			$pagination = preg_replace( $pattern, $replacement, $pagination );
		}

		$pagination = str_replace(
			array( "<ul class='page-numbers" ),
			array( "<ul class='page-numbers pagination" ),
			$pagination
		);

		$wp_query = $old_query;

		return (string) $pagination;
	}
}

if ( ! function_exists( 'wld_the_pagination' ) ) {
	function wld_the_pagination( array $args = array() ) : void {
		echo wld_get_the_pagination( $args );
	}
}

if ( ! function_exists( 'wld_theme_required' ) ) {
	function wld_theme_required() : bool {
		$title    = '';
		$mess     = '';
		$in_admin = is_admin() || 'wp-login.php' === $GLOBALS['pagenow'];
		$required = true;
		if ( ! wld_is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) ) {
			$t = __( 'ACF PRO Disabled', 'parent-theme' );
			$m = __(
				'For the theme to work, you need to install and enable the "ACF PRO" plugin.',
				'parent-theme'
			);
			if ( $in_admin ) {
				add_action(
					'admin_notices',
					static function () use ( $t, $m ) {
						?>
						<div class="notice notice-error">
							<h3><?php echo $t; ?></h3>
							<?php echo wpautop( $m ); ?>
						</div>
						<?php
					},
					10,
					0
				);
			} else {
				$title .= ' & ' . $t;
				$mess  .= "\n\n" . $m;
			}
			$required = false;
		}

		if ( $in_admin || $required ) {
			return $required;
		}

		/** @noinspection ForgottenDebugOutputInspection */
		wp_die( nl2br( trim( $mess ) ), trim( $title, '& ' ), 500 );
	}
}
if ( ! function_exists( 'wld_init' ) ) {
	function wld_init() : void {
		if ( false === wld_theme_required() ) {
			return;
		}

		do_action( 'wld_before_init' );

		locate_template( 'inc/hooks.php', true );

		WLD_ACF_Add_Field_Helper::init();
		WLD_ACF_Flex_Content::init();
		WLD_ACF_Google_Maps_API::init();
		WLD_ACF_Relationship_All::init();
		WLD_ACF_WYSIWYG_Height::init();
		WLD_ACF_Search::init();
		WLD_Admin_Bar::init();
		WLD_Admin_Notices::init();
		WLD_CPT::init();
		WLD_Extend_WPLink::init();
		WLD_Fix_GF_Invisible_ReCaptcha::init();
		WLD_Fix_GF_Multiple_IDs::init();
		WLD_GA_GTM::init();
		WLD_GF_Custom_Merge_Tags::init();
		WLD_GF_Load_Optimization::init();
		WLD_Images::init();
		WLD_Importer::init();
		WLD_Login_Style::init();
		WLD_Nav::init();
		WLD_Not_A_Page::init();
		WLD_Site_Map::init();
		WLD_SVG::init();
		WLD_Tax::init();
		WLD_TinyMCE::init();
		WLD_Yoast_SEO_Score_Fix::init();

		do_action( 'wld_init' );

		if ( wld_is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			locate_template( 'woocommerce/wc-functions.php', true );
		}

		locate_template( 'theme-functions.php', true );
	}
}
if ( ! function_exists( 'wld_class_file_autoloader' ) ) {
	function wld_class_file_autoloader( string $class_name ) : void {
		if ( 0 !== strncmp( $class_name, 'WLD_', 4 ) ) {
			return;
		}
		$file_name      = strtolower( $class_name );
		$class_name     = 'class-' . str_replace( '_', '-', $file_name );
		$trait_name     = 'trait-' . str_replace( '_', '-', $file_name );
		$template_names = array(
			'/inc/' . $class_name . '.php',
			'/inc/' . $trait_name . '.php',
			'/woocommerce/inc/' . $class_name . '.php',
			'/woocommerce/inc/' . $trait_name . '.php',
		);

		locate_template( $template_names, true );
	}
}
if ( ! function_exists( 'wld_is_plugin_active' ) ) {
	function wld_is_plugin_active( string $plugin ) : bool {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true );
	}
}
if ( ! function_exists( 'wld_array_to_excerpt' ) ) {
	function wld_array_to_excerpt( $values, string &$excerpt ) : void {
		$keys = array( 'text', 'content', 'title', 'subtitle' );
		if ( is_array( $values ) ) {
			foreach ( $values as $key => $value ) {
				if ( ! empty( $value ) ) {
					if ( is_array( $value ) && ! isset( $value['filename'] ) ) {
						wld_array_to_excerpt( $value, $excerpt );
					} elseif ( is_string( $value ) && in_array( $key, $keys, true ) ) {
						$excerpt .= $value . "\n ";
					}
				}
			}
		}
	}
}
if ( ! function_exists( 'wld_the_by_link' ) ) {
	function wld_the_by_link( array $args = array() ) : void {
		echo wld_get_the_by_link( $args );
	}
}
if ( ! function_exists( 'wld_get_link_html_from_array' ) ) {
	function wld_get_link_html_from_array( array $link_array, string $class = '', bool $empty = false ) : string {
		$link_array = array_filter( $link_array );
		if ( empty( $link_array ) ) {
			return '';
		}
		if ( ! isset( $link_array['class'] ) ) {
			$link_array['class'] = '';
		}
		if ( ! isset( $link_array['title'] ) ) {
			$link_array['title'] = '';
		}
		if ( $class ) {
			$link_array['class'] = trim( $link_array['class'] . ' ' . $class );
		}
		$atts = '';
		foreach ( $link_array as $k => $v ) {
			if ( 'title' === $k && ! $empty ) {
				continue;
			}
			if ( 'url' === $k ) {
				$k = 'href';
			}
			if ( is_string( $v ) ) {
				$v = trim( $v );
			} elseif ( is_bool( $v ) ) {
				$v = $v ? 1 : 0;
			} else {
				$v = false;
			}
			if ( $v ) {
				$atts .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
			}
		}
		if ( $empty ) {
			$link_array['title'] = '';
		}

		if ( $link_array['title'] ) {
			$link_array['title'] = do_shortcode( $link_array['title'] );
		}

		return '<a' . $atts . '>' . $link_array['title'] . '</a> ';
	}
}
if ( ! function_exists( 'wld_get_the_by_link' ) ) {
	function wld_get_the_by_link( array $args = array( 'title' => 'Dallas WordPress Development: ' ) ) : string {
		if ( ! is_front_page() ) {
			return '';
		}

		$theme = wp_get_theme();
		$name  = $theme->get( 'Author' );
		$href  = $theme->get( 'AuthorURI' );
		$args  = (array) wp_parse_args(
			$args,
			array(
				'title' => '',
				'name'  => $name,
				'href'  => $href,
				'attr'  => array(),
			)
		);

		if ( empty( $args['name'] ) || empty( $args['href'] ) ) {
			return '';
		}

		$_attr = array_filter(
			wp_parse_args(
				$args['attr'],
				array(
					'href'   => $args['href'],
					'target' => '_blank',
					'rel'    => 'noopener',
				)
			)
		);

		$attr = '';
		foreach ( $_attr as $k => $v ) {
			$attr .= ' ' . $k . '="' . $v . '"';
		}

		return sprintf(
			'<p>%s%s<span>%s</span></a></p>',
			wp_kses_attr( 'a', $attr, 'post', wp_allowed_protocols() ),
			esc_html( $args['title'] ),
			esc_html( $args['name'] )
		);
	}
}
if ( ! function_exists( 'wld_remove_filter_for_class' ) ) {
	function wld_remove_filter_for_class( string $hook, string $class, string $method, int $priority = 10 ) {
		global $wp_filter;

		$callbacks = $wp_filter[ $hook ][ $priority ] ?? array();
		if ( $callbacks ) {
			foreach ( $callbacks as $id => $filter ) {
				if (
					is_array( $filter['function'] ) &&
					! empty( $filter['function'][0] ) &&
					! empty( $filter['function'][1] ) &&
					$filter['function'][1] === $method
				) {
					if ( is_object( $filter['function'][0] ) ) {
						$filter_class = get_class( $filter['function'][0] );
					} else {
						$filter_class = $filter['function'][0];
					}
					if ( $class === $filter_class ) {
						unset( $wp_filter[ $hook ]->callbacks[ $priority ][ $id ] );
					}
				}
			}
		}
	}
}
if ( ! function_exists( 'wld_get_file_content' ) ) {
	/** @noinspection PhpRedundantVariableDocTypeInspection */
	function wld_get_file_content( string $file ) : string {
		/** @var WP_Filesystem_Direct $wp_filesystem */
		global $wp_filesystem;

		if ( null === $wp_filesystem ) {
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				/** @noinspection PhpIncludeInspection, RedundantSuppression */
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}

			WP_Filesystem();
		}

		if ( $wp_filesystem->exists( $file ) ) {
			return $wp_filesystem->get_contents( $file );
		}

		return '';
	}
}
if ( ! function_exists( 'wld_put_file_content' ) ) {
	/** @noinspection PhpRedundantVariableDocTypeInspection */
	function wld_put_file_content( string $file, $contents ) : string {
		/** @var WP_Filesystem_Direct $wp_filesystem */
		global $wp_filesystem;

		if ( null === $wp_filesystem ) {
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				/** @noinspection PhpIncludeInspection, RedundantSuppression */
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}

			WP_Filesystem();
		}

		$dir = pathinfo( $file, PATHINFO_DIRNAME );
		if ( ! $wp_filesystem->exists( $dir ) ) {
			wp_mkdir_p( $dir );
		}

		return $wp_filesystem->put_contents( $file, $contents );
	}
}
