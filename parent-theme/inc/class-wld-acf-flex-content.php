<?php

class WLD_ACF_Flex_Content {

	/**
	 * @var string|false
	 */
	public static $id;
	public static $change_title = true;
	public static $first_title  = true;
	public static $blocks       = array();

	private static $index = 0;

	public static function init() : void {
		add_filter( 'acf/load_field/type=flexible_content', array( self::class, 'set_layouts' ) );
		add_filter( 'body_class', array( self::class, 'body_class' ) );
		add_filter( 'acf/get_field_group', array( self::class, 'change_title' ) );
	}

	public static function set_layouts( array $field ) : array {
		$acf_tools_page = 'acf-tools' === sanitize_text_field( $_GET['page'] ?? '' ); // phpcs:ignore WordPress.Security.NonceVerification
		$acf_group_page = 'acf-field-group' === get_post_type();
		$display        = apply_filters( 'wld_flex_display', 'row' );

		if ( $acf_tools_page || $acf_group_page ) {
			return $field;
		}
		if ( isset( $field['wrapper']['id'] ) && 'tpl_flexible_content_content' === $field['wrapper']['id'] ) {
			$groups  = acf_get_field_groups();
			$layouts = array();
			if ( $groups ) {
				remove_filter( 'acf/load_field/type=flexible_content', array( self::class, __METHOD__ ) );
				foreach ( $groups as $group ) {
					if ( 0 === strncmp( $group['title'], 'FC:', 3 ) ) {
						$fields = acf_get_fields( $group );
						if ( $fields ) {

							$key   = (string) str_replace( 'group_', '', $group['key'] );
							$title = str_replace( 'FC: ', '', $group['title'] );

							$layouts[ $key ] = array(
								'key'                 => $key,
								'name'                => sanitize_title( $title ),
								'label'               => $title,
								'display'             => $display,
								'sub_fields'          => $fields,
								'min'                 => '',
								'max'                 => '',
								'wpml_cf_preferences' => 2,
							);
						}
					}
				}
				add_filter( 'acf/load_field/type=flexible_content', array( self::class, __METHOD__ ) );
			}
			$field['layouts'] = self::set_inner_layouts( $layouts );
		}

		return $field;
	}

	public static function set_inner_layouts( array $layouts ) : array {
		if ( empty( $layouts ) ) {
			return $layouts;
		}
		$inner_layouts = $layouts;
		foreach ( $layouts as $l => $layout ) {
			foreach ( (array) $layout['sub_fields'] as $k => $_field ) {
				$type = $_field['type'];
				$name = $_field['name'];
				if ( 'flexible_content' === $type && 'inner_content' === $name ) {
					unset( $inner_layouts[ $l ] );
				} elseif ( 'repeater' === $type ) {
					foreach ( (array) $_field['sub_fields'] as $k2 => $_field2 ) {
						$type = $_field2['type'];
						$name = $_field2['name'];
						if ( 'flexible_content' === $type && 'inner_content' === $name ) {
							unset( $inner_layouts[ $l ]['sub_fields'][ $k ]['sub_fields'][ $k2 ] );
						}
					}
				}
			}
		}
		foreach ( $layouts as $l => $layout ) {
			foreach ( (array) $layout['sub_fields'] as $k => $_field ) {
				$type = $_field['type'];
				$name = $_field['name'];
				if ( 'flexible_content' === $type && 'inner_content' === $name ) {
					$layouts[ $l ]['sub_fields'][ $k ]['layouts'] = $inner_layouts;
				} elseif ( 'repeater' === $type ) {
					foreach ( (array) $_field['sub_fields'] as $k2 => $_field2 ) {
						$type = $_field2['type'];
						$name = $_field2['name'];
						if ( 'flexible_content' === $type && 'inner_content' === $name ) {
							$layouts[ $l ]['sub_fields'][ $k ]['sub_fields'][ $k2 ]['layouts'] = $inner_layouts;
						}
					}
				}
			}
		}

		return $layouts;
	}

	public static function body_class( array $classes ) : array {
		$special_classes = get_field( 'css_special_classes' );
		if ( $special_classes ) {
			foreach ( (array) $special_classes as $class ) {
				$classes[] = sanitize_html_class( $class );
			}
		}

		return apply_filters( 'wld_flex_body_class', $classes );
	}

	public static function get_the_title( string $title, int $level, string $class = '' ) : string {
		_deprecated_function( __METHOD__, '5.1.3', 'ACF functions or WLD_Fields' );

		if ( empty( $title ) ) {
			return '';
		}

		$title = do_shortcode( preg_replace( '/<\/?h\d>/', '', $title ) );

		return sprintf( '<h%2$s class="%3$s">%1$s</h%2$s>', $title, $level, $class );
	}

	public static function the_content( string $slug = 'templates/flexible-content/', string $layout = '' ) : void {
		if ( empty( $layout ) ) {
			$layout = (string) get_row_layout();
		}

		if ( empty( self::$blocks ) ) {
			self::$blocks = get_field( 'content', false, false );
		}

		self::$index ++;

		ob_start();
		get_template_part( $slug . $layout );
		$content = ob_get_clean();

		// Set id attr
		if ( self::$id ) {
			$id = ' id="' . self::$id . '"';
		} elseif ( false !== self::$id ) {
			$id = ' id="section-' . self::get_index() . '"';
		} else {
			$id = false;
		}
		if ( $id && 1 !== preg_match( '/^<.+ id=".+".?>/', $content ) ) {
			$content = implode( $id . '>', explode( '>', $content, 2 ) );
		}

		$next    = self::$blocks[ self::get_index() ] ?? array();
		$classes = apply_filters( 'wld_flex_block_class', array(), $next['acf_fc_layout'] ?? '' );
		if ( $classes ) {
			$content = preg_replace(
				'|^(<[^>]+class=")|',
				'$1' . implode( ' ', $classes ) . ' ',
				$content
			);
		}

		// Remove empty elements
		$items    = apply_filters( 'wld_flex_replace_items', 'left|right|item' );
		$wrappers = apply_filters(
			'wld_flex_replace_wrappers',
			'wrap|btns|buttons|text-content|text|image|images|title|pretitle|pre-title|subtitle|sub-title'
		);
		$content  = preg_replace(
			array(
				'/\t/',
				'/<h\d[^>]*>\s*<\/h\d>/mi',
				'/(style|class|id)=""/',
			),
			'',
			$content
		);
		$content  = preg_replace(
			'/<div class="[^"]*(' . $items . ')[^"]*">\s*<\/div>/mi',
			'',
			$content
		);
		$content  = preg_replace(
			'/<div class="[^"]*(' . $wrappers . ')[^"]*">\s*<\/div>/mi',
			'',
			$content
		);

		echo $content;
		self::$id           = '';
		self::$change_title = true;
	}

	public static function get_index() : int {
		return self::$index;
	}

	public static function change_title( array $field_group ) : array {
		$post_type       = sanitize_text_field( $_GET['post_type'] ?? '' ); // phpcs:ignore WordPress.Security.NonceVerification
		$page            = sanitize_text_field( $_GET['page'] ?? '' ); // phpcs:ignore WordPress.Security.NonceVerification
		$action          = sanitize_text_field( $_POST['action'] ?? '' ); // phpcs:ignore WordPress.Security.NonceVerification
		$posts_files     = array( '/wp-admin/post-new.php', '/wp-admin/edit.php' );
		$if_post         = in_array( $_SERVER['PHP_SELF'], $posts_files, true );
		$if_get_tpl      = 'acf/ajax/check_screen' === $action;
		$if_settings     = 'theme-settings' === $page;
		$if_post_not_acf = 'acf-field-group' !== $post_type && $if_post;

		if ( $if_get_tpl || $if_settings || $if_post_not_acf ) {
			$field_group['title'] = str_replace(
				array(
					'TPL: ',
					'CPT: ',
					'OPT: ',
				),
				'',
				$field_group['title']
			);
		}

		return $field_group;
	}
}
