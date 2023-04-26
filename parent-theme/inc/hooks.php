<?php
if ( ! function_exists( '_hook_enqueue' ) ) {
	function _hook_enqueue() {
		wp_enqueue_style(
			'theme-styles',
			get_stylesheet_directory_uri() . '/css/styles.css',
			array(),
			WLD_VER
		);
		wp_enqueue_script(
			'theme-init',
			get_stylesheet_directory_uri() . '/js/init.js',
			array( 'jquery' ),
			WLD_VER,
			true
		);
		wp_localize_script(
			'theme-init',
			'theme',
			apply_filters(
				'wld_enqueue_get_theme_object',
				array(
					'url'       => get_stylesheet_directory_uri() . '/',
					'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
					'ajaxNonce' => wp_create_nonce( 'ajax-nonce' ),
				)
			)
		);
		wp_localize_script(
			'theme-init',
			'theme_i18n',
			apply_filters(
				'wld_enqueue_get_theme_i18n_object',
				array(
					'more' => __( 'View More', 'parent-theme' ),
					'less' => __( 'View Less', 'parent-theme' ),
				)
			)
		);
		if ( WLD_NEVER ) { // The condition is never fulfilled, only for IDE
			?>
			<script>
				window.theme = { ajaxUrl: '', ajaxNonce: '' };
				window.theme_i18n = { more: '', less: '' };
			</script>
			<?php
		}
	}

	add_action( 'wp_enqueue_scripts', '_hook_enqueue', PHP_INT_MAX - 1 );
}
if ( ! function_exists( '_hook_load_theme_textdomain' ) ) {
	function _hook_load_theme_textdomain() {
		if ( is_dir( get_template_directory() . '/languages' ) ) {
			load_theme_textdomain( 'parent-theme', get_template_directory() . '/languages' );
		}
	}

	add_action( 'after_setup_theme', '_hook_load_theme_textdomain', 5, 0 );
}
if ( ! function_exists( '_hook_add_acf_types' ) ) {
	function _hook_add_acf_types() {
		if ( wld_is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) ) {
			acf_include( 'includes/fields/class-acf-field-group.php' );
			acf_include( 'includes/fields/class-acf-field-image.php' );

			acf_register_field_type( 'WLD_ACF_Replace_Text_Field' );
			acf_register_field_type( 'WLD_ACF_Contact_Link_Field' );
			acf_register_field_type( 'WLD_ACF_Copyright_Field' );
			acf_register_field_type( 'WLD_ACF_Menu_Field' );
			acf_register_field_type( 'WLD_ACF_Social_Links_Field' );
			acf_register_field_type( 'WLD_ACF_Title_Field' );
			acf_register_field_type( 'WLD_ACF_Background_Field' );

			if ( wld_is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
				acf_register_field_type( 'WLD_ACF_Forms_Field' );
			}
		}
	}

	add_action( 'after_setup_theme', '_hook_add_acf_types' );
}
if ( ! function_exists( '_hook_add_theme_support' ) ) {
	function _hook_add_theme_support() {
		add_theme_support( 'title-tag' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'woocommerce' );
		add_theme_support(
			'html5',
			array(
				'comment-list',
				'comment-form',
				'search-form',
				'gallery',
				'caption',
				'script',
				'style',
			)
		);
		add_theme_support( 'post-thumbnails', apply_filters( 'wld_thumbnails_support', array( 'post' ) ) );
	}

	add_action( 'after_setup_theme', '_hook_add_theme_support' );
}
if ( ! function_exists( '_hook_remove_type_javascript_from_gf' ) ) {
	function _hook_remove_type_javascript_from_gf( $form_string ) {
		return str_replace( ' type=\'text/javascript\'', '', $form_string );
	}

	add_filter( 'gform_get_form_filter', '_hook_remove_type_javascript_from_gf' );
}
if ( ! function_exists( '_hook_jquery_scripts_in_footer' ) ) {
	function _hook_jquery_scripts_in_footer() {
		$ver = '3.6.0';
		wp_deregister_script( 'jquery-core' );
		wp_deregister_script( 'jquery' );
		wp_register_script(
			'jquery-core',
			get_stylesheet_directory_uri() . '/js/jquery.js',
			array(),
			$ver,
			true
		);
		wp_register_script(
			'jquery',
			false,
			array( 'jquery-core' ),
			$ver,
			true
		);
	}

	add_action( 'wp_enqueue_scripts', '_hook_jquery_scripts_in_footer' );
}

if (
	! function_exists( '_hook_remove_type_javascript_from_woo' ) &&
	wld_is_plugin_active( 'woocommerce/woocommerce.php' )
) {
	add_action(
		'body_class',
		static function () {
			remove_action( 'wp_footer', 'wc_no_js' );
		}
	);

	function _hook_remove_type_javascript_from_woo() {
		?>
		<script>
			( function() {
				let c = document.body.className;
				c = c.replace( /woocommerce-no-js/, 'woocommerce-js' );
				document.body.className = c;
			} )();
		</script>
		<?php
	}

	add_filter( 'wp_footer', '_hook_remove_type_javascript_from_woo' );
}
if ( ! function_exists( '_hook_add_theme_settings' ) ) {
	function _hook_add_theme_settings() {
		acf_add_options_page(
			array(
				'page_title' => __( 'Theme Settings', 'parent-theme' ),
				'menu_title' => __( 'Theme Settings', 'parent-theme' ),
				'menu_slug'  => 'theme-settings',
				'capability' => 'edit_posts',
				'redirect'   => false,
				'autoload'   => true,
			)
		);
	}

	add_action( 'after_setup_theme', '_hook_add_theme_settings' );
}
if ( ! function_exists( '_hook_excerpt_more' ) ) {
	function _hook_excerpt_more() : string {
		return '';
	}

	add_filter( 'excerpt_more', '_hook_excerpt_more' );
}
if ( ! function_exists( '_hook_add_body_classes' ) ) {
	function _hook_add_body_classes( $classes ) {
		if ( is_front_page() ) {
			$classes[] = 'home-page';
		} else {
			$classes[] = 'inner-page';
		}

		return $classes;
	}

	add_filter( 'body_class', '_hook_add_body_classes' );
}
if ( ! function_exists( '_hook_gf_label_settings' ) ) {
	function _hook_gf_label_settings() : bool {
		return true;
	}

	add_filter( 'gform_enable_field_label_visibility_settings', '_hook_gf_label_settings' );
}
if ( ! function_exists( '_hook_set_favicon' ) ) {
	function _hook_set_favicon() {
		echo get_option( 'wld_other_favicon_html' );
	}

	add_action( 'wp_head', '_hook_set_favicon' );
	add_action( 'admin_head', '_hook_set_favicon', 2 );
	add_filter(
		'acf/update_value/name=wld_other_favicon',
		static function ( $value ) {
			$favicon = $value ? wp_get_attachment_image_url( (int) $value, 'full' ) : '';
			if ( $favicon ) {
				/** @noinspection HtmlUnknownTarget */
				update_option(
					'wld_other_favicon_html',
					sprintf( '<link rel="shortcut icon" href="%s">', esc_url( $favicon ) ),
					true
				);
			} else {
				delete_option( 'wld_other_favicon_html' );
			}

			return $value;
		}
	);
	remove_action( 'wp_head', 'wp_site_icon', 99 );
}
if ( ! function_exists( '_hook_header_meta' ) ) {
	function _hook_header_meta() {
		?>
		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php
	}

	add_action( 'wp_head', '_hook_header_meta', 1 );
}
if ( ! function_exists( '_hook_widgets_init' ) ) {
	function _hook_widgets_init() {
		register_sidebar(
			array(
				'id'            => 'blog_sidebar',
				'name'          => __( 'Blog Sidebar', 'parent-theme' ),
				'description'   => __( 'This is a sidebar for blog widgets', 'parent-theme' ),
				'before_widget' => '<div class="archive-list-block">',
				'after_widget'  => '</div>',
				'before_title'  => '<h2 class="archive-title">',
				'after_title'   => '</h2>',
			)
		);
	}

	add_action( 'widgets_init', '_hook_widgets_init' );
}
if ( ! function_exists( '_hook_hide_admin_pages' ) ) {
	function _hook_hide_admin_pages() {
		if ( WLD_DISABLE_COMMENTS ) {
			remove_menu_page( 'edit-comments.php' );
			remove_submenu_page( 'options-general.php', 'options-discussion.php' );
		}
		remove_submenu_page( 'themes.php', 'theme-editor.php' );
		remove_submenu_page( 'plugins.php', 'plugin-editor.php' );
		remove_submenu_page( 'tools.php', 'tools.php' );
		remove_submenu_page( 'options-general.php', 'options-media.php' );
	}

	add_action( 'admin_menu', '_hook_hide_admin_pages', 999, 0 );
}
if ( ! function_exists( '_hook_admin_remove_customize' ) ) {
	function _hook_admin_remove_customize() {
		echo '<style>.hide-if-no-customize { display: none !important; }</style>';
	}

	add_action( 'admin_head', '_hook_admin_remove_customize' );
}
if ( ! function_exists( '_hook_clear_head' ) ) {
	function _hook_clear_head() {
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'rsd_link' );
	}

	add_action( 'init', '_hook_clear_head' );
}
if ( ! function_exists( '_hook_remove_wp_embed' ) ) {
	function _hook_remove_wp_embed() {
		wp_deregister_script( 'wp-embed' );
	}

	add_action( 'wp_enqueue_scripts', '_hook_remove_wp_embed', 999, 0 );
}
if ( ! function_exists( '_hook_remove_gutenberg_style' ) ) {
	function _hook_remove_gutenberg_style() {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wc-blocks-style' );
	}

	add_action( 'wp_enqueue_scripts', '_hook_remove_gutenberg_style', 999, 0 );
	remove_action( 'wp_enqueue_scripts', 'wp_common_block_scripts_and_styles' );
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
	remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
	remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
	remove_action( 'wp_body_open', 'gutenberg_experimental_global_styles_render_svg_filters' );
}
if ( ! function_exists( '_hook_remove_jquery_migrate' ) ) {
	function _hook_remove_jquery_migrate( $scripts ) {
		if ( isset( $scripts->registered['jquery'] ) && ! is_admin() ) {
			$script = $scripts->registered['jquery'];
			if ( $script->deps ) {
				$script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
			}
		}
	}

	add_action( 'wp_default_scripts', '_hook_remove_jquery_migrate' );
}
if ( ! function_exists( '_hook_remove_emoji' ) ) {
	function _hook_remove_emoji() {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		add_filter(
			'tiny_mce_plugins',
			static function ( $plugins ) {
				if ( is_array( $plugins ) ) {
					return array_diff( $plugins, array( 'wpemoji' ) );
				}

				return array();
			}
		);
		add_filter( 'emoji_svg_url', '__return_empty_string' );
	}

	add_action( 'init', '_hook_remove_emoji' );
}
if ( ! function_exists( '_hook_remove_wp_version' ) ) {
	function _hook_remove_wp_version( $src ) {
		return $src ? remove_query_arg( 'ver', $src ) : $src;
	}

	add_filter( 'the_generator', '__return_empty_string' );
	add_filter( 'script_loader_src', '_hook_remove_wp_version' );
	add_filter( 'style_loader_src', '_hook_remove_wp_version' );
}
if ( ! function_exists( '_hook_save_sample_json' ) ) {
	function _hook_save_sample_json() : string {
		return get_stylesheet_directory() . '/acf-json';
	}

	add_filter( 'acf/settings/save_json', '_hook_save_sample_json' );
}
/*if ( ! function_exists( '_hook_acf_show_admin' ) ) {
	function _hook_acf_show_admin() : bool {
		$post_type = sanitize_text_field( $_GET['post_type'] ?? '' ); // phpcs:ignore WordPress.Security.NonceVerification

		return 'local' === wp_get_environment_type() || 'acf-field-group' === $post_type;
	}

	add_filter( 'acf/settings/show_admin', '_hook_acf_show_admin' );
}*/
if ( ! function_exists( '_hook_file_link_format' ) ) {
	function _hook_file_link_format() : string {
		// https://tracy.nette.org/en/open-files-in-ide
		return 'editor://open/?file=%f&line=%l';
	}

	add_filter( 'qm/output/file_link_format', '_hook_file_link_format' );
}
if ( ! function_exists( '_hook_page_exist_pre_handle_404' ) ) {
	function _hook_page_exist_pre_handle_404( $preempt ) {
		global $wp_query, $wp, $wp_the_query;

		if ( null === $wp_query->post && $wp->request ) {
			$page = get_page_by_path( $wp->request );
			if ( $page ) {
				// phpcs:ignore WordPress.WP.DiscouragedFunctions
				query_posts( 'pagename=' . $wp->request );
				$wp_the_query = $wp_query;
			}
		}

		return $preempt;
	}

	add_action( 'pre_handle_404', '_hook_page_exist_pre_handle_404', 1 );
}
if ( ! function_exists( '_hook_tiny_mce_before_init' ) ) {
	function _hook_tiny_mce_before_init( $mce_init ) {
		$mce_init['wpautop']      = false;
		$mce_init['indent']       = true;
		$mce_init['tadv_noautop'] = true;

		return $mce_init;
	}

	add_action( 'tiny_mce_before_init', '_hook_tiny_mce_before_init' );
}
if ( ! function_exists( '_hook_extra_theme_headers' ) ) {
	function _hook_extra_theme_headers( $headers ) {
		$headers[] = 'Author Title';
		$headers[] = 'SEO';
		$headers[] = 'SEO URI';
		$headers[] = 'SEO Title';

		return $headers;
	}

	add_filter( 'extra_theme_headers', '_hook_extra_theme_headers' );
}
if ( ! function_exists( '_hook_yst_add_page_number' ) ) {
	function _hook_yst_add_page_number( $title ) : string {
		global $page;

		$paged = 1 < (int) $page ? $page : get_query_var( 'paged', 1 );
		if ( $paged > 1 ) {
			// translators: %s: number page
			$title .= ' | ' . sprintf( __( 'Page %s', 'parent-theme' ), $paged );
		}

		return $title;
	}

	add_filter( 'wpseo_metadesc', '_hook_yst_add_page_number' );
	add_filter( 'wpseo_title', '_hook_yst_add_page_number' );
}
if ( ! function_exists( '_hook_fatal_error_handler_enabled' ) ) {
	function _hook_fatal_error_handler_enabled() : bool {
		return false;
	}

	add_filter( 'wp_fatal_error_handler_enabled', '_hook_fatal_error_handler_enabled' );
}
if ( ! function_exists( '_hook_fix_custom_choices_get' ) ) {
	function _hook_fix_custom_choices_get( $field ) {
		if ( isset( $field['allow_custom'] ) && 1 === $field['allow_custom'] && ! acf_is_screen( 'acf-field-group' ) ) {
			$choices          = get_option( 'wld_custom_choices_' . $field['key'] );
			$field['choices'] = $choices && is_array( $choices ) ? $choices : array();
		}

		return $field;
	}

	add_filter( 'acf/load_field/type=checkbox', '_hook_fix_custom_choices_get' );
	add_filter( 'acf/load_field/type=radio', '_hook_fix_custom_choices_get' );
}
if ( ! function_exists( '_hook_fix_custom_choices_save' ) ) {
	/** @noinspection PhpUnusedParameterInspection */
	function _hook_fix_custom_choices_save( $values, $post_id, $field ) {
		if ( isset( $field['allow_custom'] ) && 1 === $field['allow_custom'] && is_array( $values ) ) {
			$update  = false;
			$choices = get_option( 'wld_custom_choices_' . $field['key'] );
			$choices = $choices && is_array( $choices ) ? $choices : array();
			foreach ( $values as $value ) {
				if ( ! in_array( $value, $choices, true ) ) {
					$update            = true;
					$choices[ $value ] = $value;
				}
			}
			if ( $update ) {
				update_option( 'wld_custom_choices_' . $field['key'], $choices, false );
			}
		}

		return $values;
	}

	add_filter( 'acf/update_value/type=checkbox', '_hook_fix_custom_choices_save', 10, 3 );
	add_filter( 'acf/update_value/type=radio', '_hook_fix_custom_choices_save', 10, 3 );
}
if ( ! function_exists( '_hook_fonts_load_optimization' ) ) {
	function _hook_fonts_load_optimization() {
		?>
		<link rel="dns-prefetch" href="//fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<script>
			( function( a, b, c, d ) {
				c = '';
				for ( let i = 0; i < b; i++ ) {
					if ( 'fontLoaderCss' === localStorage.key( i ).substring( 0, 13 ) ) {
						c += "\r\n" + localStorage.getItem( localStorage.key( i ) );
					}
				}

				if ( c ) {
					d = a.createElement( 'style' );
					d.rel = 'stylesheet';
					a.head.appendChild( d );
					d.textContent = c;
				}
			} )( document, localStorage.length );
		</script>
		<?php
	}

	add_action( 'wp_head', '_hook_fonts_load_optimization', 1 );
}
if ( ! function_exists( '_hook_disabled_monsterinsights_assets' ) ) {
	function _hook_disabled_monsterinsights_assets() {
		wp_dequeue_script( 'monsterinsights-gutenberg-editor-js' );
		wp_dequeue_script( 'monsterinsights-frontend-script' );
		wp_dequeue_style( 'monsterinsights-gutenberg-editor-css' );
		wp_dequeue_style( 'monsterinsights-popular-posts-style' );
	}

	add_action( 'wp_enqueue_scripts', '_hook_disabled_monsterinsights_assets' );
}
if ( ! function_exists( '_hook_disabled_unused_widgets' ) ) {
	global $wp_widget_factory;

	function _hook_disabled_unused_widgets() {
		global $wp_registered_widgets, $wp_widget_factory;

		$disabled   = array(
			'WP_Widget_Pages',
			'WP_Widget_Calendar',
			'WP_Widget_Media_Audio',
			'WP_Widget_Media_Image',
			'WP_Widget_Media_Gallery',
			'WP_Widget_Media_Video',
			'WP_Widget_Meta',
			'WP_Widget_Text',
			'WP_Widget_Recent_Posts',
			'WP_Widget_Recent_Comments',
			'WP_Widget_RSS',
			'WP_Widget_Tag_Cloud',
			'WP_Nav_Menu_Widget',
			'WP_Widget_Custom_HTML',
		);
		$keys       = array_diff( array_keys( $wp_widget_factory->widgets ), $disabled );
		$registered = array_keys( $wp_registered_widgets );
		$registered = array_map( '_get_widget_id_base', $registered );

		foreach ( $keys as $key ) {
			// Don't register new widget if old widget with the same id is already registered.
			if ( in_array( $wp_widget_factory->widgets[ $key ]->id_base, $registered, true ) ) {
				unset( $wp_widget_factory->widgets[ $key ] );
				continue;
			}

			$wp_widget_factory->widgets[ $key ]->_register();
		}
	}

	add_action( 'widgets_init', '_hook_disabled_unused_widgets' );
	remove_action( 'widgets_init', array( $wp_widget_factory, '_register_widgets' ), 100 );
}
if ( ! function_exists( '_hook_allowed_iframe_kses' ) ) {
	function _hook_allowed_iframe_kses( array $tags, string $context ) : array {
		if ( 'post' === $context ) {
			$tags['iframe'] = array(
				'src'             => true,
				'height'          => true,
				'width'           => true,
				'allowfullscreen' => true,
				'loading'         => true,
				'title'           => true,
			);
		}

		return $tags;
	}

	add_filter( 'wp_kses_allowed_html', '_hook_allowed_iframe_kses', 10, 2 );
}

if ( ! function_exists( '_hook_removed_wptexturize' ) ) {
	function _hook_removed_wptexturize() {
		remove_filter( 'acf_the_content', 'wptexturize' );
	}

	add_action( 'acf/init', '_hook_removed_wptexturize' );
}
