<?php

class WLD_Fields {
	protected static $loop    = array();
	protected static $wrap    = array();
	protected static $as_type = '';

	public static function get( string $selector, $args ) : string {
		$values = static::get_value( $selector );
		$field  = static::get_field( $selector );
		$value  = '';
		$wrap   = '';
		$attrs  = array();

		if ( $field && $values['format'] ) {
			if ( self::$as_type ) {
				$field['type'] = self::$as_type;
				self::$as_type = '';
			}

			switch ( $field['type'] ) {
				case 'title':
					$class = '';
					$level = null;

					if ( isset( $args[1] ) ) {
						$class = $args[0];
						if ( is_numeric( $args[1] ) ) {
							$level = (int) $args[1];
							$wrap  = $args[2] ?? '';
						} else {
							$wrap = $args[1];
						}
					} elseif ( isset( $args[0] ) ) {
						if ( is_numeric( $args[0] ) ) {
							$level = (int) $args[0];
						} else {
							$class = $args[0];
						}
					}

					if ( $level ) {
						_deprecated_argument( __FUNCTION__, '5.1.3', 'Set level in wp admin' );
					}

					$value = apply_shortcodes( $values['format'] );
					$class = esc_attr( $class );
					if ( $class ) {
						$value = preg_replace(
							'/(<h\d)/',
							'$1 class="' . $class . '"',
							$value
						);
					}
					break;
				case 'text':
				case 'textarea':
				case 'wysiwyg':
					$wrap  = $args[0] ?? '';
					$value = $values['text'];
					break;
				case 'image':
					$size = $args[0] ?? '';
					self::set_attrs_wrap( $args, $attrs, $wrap );
					$value = WLD_Images::get_img( (int) $values['raw'], $size, $attrs );
					break;
				case 'background':
					$first = $args[0] ?? '';
					if ( 'cover' === $first || 'contain' === $first ) {
						$type = $first;
						if ( empty( $args[1] ) ) {
							$size = '1920x0';
							$attr = array();
						} elseif ( is_array( $args[1] ) ) {
							$size = $args[2] ?? '1920x0';
							$attr = $args[1];
						} else {
							$size = $args[1];
							$attr = $args[2] ?? array();
						}
					} else {
						$size = $first ?: '1920x0';
						$attr = array();
					}

					if ( empty( $type ) ) {
						$type = 'cover';
						$attr = $args[1] ?? array();
					}

					$value = WLD_Images::get_fit_bg_image(
						(int) $values['raw'],
						$size,
						$type,
						$attr
					);
					break;
				case 'link':
					$class = $args[0] ?? '';
					if ( isset( $args[1] ) ) {
						if ( is_bool( $args[1] ) ) {
							$empty = $args[1];
							$wrap  = $args[2] ?? '';
						} else {
							$empty = false;
							$wrap  = $args[1];
						}
					} else {
						$empty = false;
						$wrap  = '';
					}

					if ( is_array( $values['raw'] ) ) {
						$value = wld_get_link_html_from_array( $values['raw'], $class, $empty );
					}
					break;
				case 'wld_contact_link':
					$class = $args[0] ?? '';
					$title = $values['raw']['title'];
					self::set_attrs_wrap( $args, $attrs, $wrap );

					switch ( $values['raw']['link_type'] ) {
						case 'phone':
							$attrs['href'] = 'tel:' . $values['raw']['number'];
							break;
						case 'fax':
							$attrs['href'] = 'fax:' . $values['raw']['number'];
							break;
						case 'email':
							$attrs['href'] = 'mailto:' . antispambot( $values['raw']['number'] );
							$title         = antispambot( $title );
							break;
					}

					$attrs['class'] = trim( $values['raw']['class'] . ' ' . $class );

					$value = '<a ' . acf_esc_attrs( acf_filter_attrs( $attrs ) ) . '>' . $title . '</a>';
					break;
				case 'forms':
					if ( isset( $args[0] ) && is_string( $args[0] ) ) {
						$title = false;
						$desc  = false;
						$wrap  = $args[0];
					} elseif ( isset( $args[1] ) && is_string( $args[1] ) ) {
						$title = $args[0];
						$desc  = false;
						$wrap  = $args[1];
					} else {
						$title = $args[0] ?? false;
						$desc  = $args[1] ?? false;
						$wrap  = $args[2] ?? '';
					}

					if ( $values['raw'] ) {
						$value = sprintf(
							'[gravityform id="%d" title="%s" description="%s" ajax="true"]',
							$values['raw'],
							$title ? 'true' : 'false',
							$desc ? 'true' : 'false'
						);
					}
					break;
				case 'menu':
					$attrs         = $args[0] ?? array();
					$attrs         = array_merge(
						array( 'fallback_cb' => '__return_empty_string' ),
						$attrs
					);
					$attrs['menu'] = (int) $values['raw'];
					$attrs['echo'] = false;

					$value = wp_nav_menu( $attrs );
					break;
				case 'google_map':
					$icon = $args[0] ?? '';
					if ( isset( $args[1] ) ) {
						if ( is_array( $args[1] ) ) {
							$attrs = $args[1];
							$wrap  = $args[2] ?? '';
						} else {
							$wrap = $args[1];
						}
					}

					$attrs                   = array_merge( array( 'class' => 'map-canvas' ), $attrs );
					$attrs['data-latitude']  = $values['format']['lat'];
					$attrs['data-longitude'] = $values['format']['lng'];
					$attrs['data-zoom']      = $values['format']['zoom'];
					$attrs['data-address']   = $values['format'];
					$attrs['data-icon']      = $icon ? get_stylesheet_directory_uri() . '/images/' . $icon : '';

					unset(
						$attrs['data-address']['lat'],
						$attrs['data-address']['lng'],
						$attrs['data-address']['zoom']
					);

					$value = '<div ' . acf_esc_attrs( acf_filter_attrs( $attrs ) ) . '"></div>';
					break;
				case 'radio':
				case 'checkbox':
					$sanitize_class = $args[0] ?? true;
					$value          = $values['raw'];
					if ( is_array( $value ) ) {
						if ( $sanitize_class ) {
							$value = array_map( 'sanitize_html_class', $value );
						}
						$value = implode( ' ', $value );
					} elseif ( $sanitize_class ) {
						$value = sanitize_html_class( $value );
					}
					break;
				default:
					$wrap  = $args[0] ?? '';
					$value = $values['format'];
			}
		}

		$value = (string) $value;
		if ( $value ) {
			$value = static::get_start_wrapper( $wrap ) . apply_shortcodes( $value ) . static::get_end_wrapper( $wrap );
		}

		return $value;
	}

	public static function loop( &$selector_or_posts_array, string $wrapper = '' ) : bool {
		$selector = 'array';
		if ( is_string( $selector_or_posts_array ) ) {
			$selector = $selector_or_posts_array;
		}

		$start     = ! isset( static::$loop[ $selector ] );
		$end       = true;
		$has_value = true;

		if ( $start ) {
			if ( is_string( $selector_or_posts_array ) ) {
				static::$loop[ $selector ] = static::get_field( $selector );
			} else {
				static::$loop[ $selector ] = array(
					'type'  => 'relationship',
					'value' => &$selector_or_posts_array,
				);
			}

			$value     = static::$loop[ $selector ]['value'] ?? '';
			$is_array  = is_array( $value );
			$has_value = ( ! $is_array && ! empty( $value ) ) || ( $is_array && ! empty( array_filter( $value ) ) );

			if ( $has_value ) {
				static::$loop[ $selector ]['need_end_wrapper'] = (bool) $wrapper;
				echo static::get_start_wrapper( $wrapper );
			}
		}

		if ( $has_value ) {
			$post_id = self::get_post_id( $selector );
			if ( 'relationship' === static::$loop[ $selector ]['type'] ) {
				if ( ! empty( static::$loop[ $selector ]['value'] ) ) {
					$GLOBALS['post'] = get_post( array_shift( static::$loop[ $selector ]['value'] ) );
					setup_postdata( $GLOBALS['post'] );
					$end = false;
				} elseif ( isset( static::$loop[ $selector ] ) ) {
					wp_reset_postdata();
				}
			} elseif ( have_rows( $selector, $post_id ) ) {
				the_row();
				$end = false;
			}
		}

		if ( $end ) {
			if ( ! empty( static::$loop[ $selector ]['need_end_wrapper'] ) ) {
				echo static::get_end_wrapper( $wrapper );
			}
			unset( static::$loop[ $selector ] );
		}

		return ! $end;
	}

	public static function wrap(
		string $selector_or_url, string $class = '', bool $force = true, array $attrs = array()
	) : bool {
		if ( ! isset( static::$wrap[ $selector_or_url ] ) ) {
			if ( self::is_url( $selector_or_url ) ) {
				$values = array();
				$field  = array();
				$value  = $selector_or_url;
			} else {
				$values = static::get_value( $selector_or_url );
				$field  = static::get_field( $selector_or_url );
				$value  = $values['format'];
			}

			static::$wrap[ $selector_or_url ] = '';

			if ( is_string( $value ) && self::is_url( $value ) ) {
				static::$wrap[ $selector_or_url ] = $value;

				$attrs['href']  = esc_url( $value );
				$attrs['class'] = $class;

				echo '<a ' . acf_esc_attrs( acf_filter_attrs( $attrs ) ) . '>';
			} elseif ( $value && ( 'link' === $field['type'] || 'wld_contact_link' === $field['type'] ) ) {
				static::$wrap[ $selector_or_url ] = $value;

				$attrs['href']   = esc_url( $values['raw']['url'] );
				$attrs['target'] = $values['raw']['target'];
				$attrs['title']  = $values['raw']['title'];
				$attrs['class']  = trim( $values['raw']['class'] . ' ' . $class );

				if ( '_blank' === $values['raw']['target'] ) {
					$attrs['rel'] = 'noopener';
				}

				echo '<a ' . acf_esc_attrs( acf_filter_attrs( $attrs ) ) . '>';
			} elseif ( $value || $force ) {
				$attrs['class'] = $class;

				echo '<div ' . acf_esc_attrs( acf_filter_attrs( $attrs ) ) . '>';
			}

			return true;
		}

		if ( ! empty( static::$wrap[ $selector_or_url ] ) ) {
			echo '</a>';
		} elseif ( $force ) {
			echo '</div>';
		}

		unset( static::$wrap[ $selector_or_url ] );

		return false;
	}

	/** @noinspection MultipleReturnStatementsInspection */
	public static function has( array $selectors_and_maybe_conditional ) : bool {
		$conditional = 'OR';
		if ( 'AND' === $selectors_and_maybe_conditional[0] ) {
			$conditional = 'AND';
			array_shift( $selectors_and_maybe_conditional );
		}

		if ( 'OR' === $conditional ) {
			foreach ( $selectors_and_maybe_conditional as $selector ) {
				$values = self::get_value( $selector );

				if (
					( ! is_array( $values['format'] ) && ! empty( $values['format'] ) ) ||
					( is_array( $values['format'] ) && ! empty( array_filter( $values['format'] ) ) )
				) {
					return true;
				}
			}

			return false;
		}

		foreach ( $selectors_and_maybe_conditional as $selector ) {
			$values = self::get_value( $selector );
			if ( empty( $values['format'] ) ) {
				return false;
			}
		}

		return true;
	}

	public static function get_field( string $selector ) : array {
		$post_id = self::get_post_id( $selector );
		if ( false === $post_id && static::is_sub_loop() ) {
			$field = get_sub_field_object( $selector, false );
		} else {
			$field = get_field_object( $selector, $post_id, false );
		}

		return $field ?: array();
	}

	public static function next_field_process_as_type( string $as_type ) : void {
		self::$as_type = $as_type;
	}

	protected static function is_sub_loop() : bool {
		$loop    = acf_get_loop();
		$is_loop = false;
		if ( ! empty( $loop ) ) {
			$type    = $loop['field']['type'] ?? '';
			$post_id = $loop['post_id'] ?? '';
			$types   = array( 'group', 'repeater', 'clone' );

			if ( 0 === strpos( $post_id, 'options' ) && in_array( $type, $types, true ) ) {
				$is_loop = true;
			} else {
				$is_loop = WLD_Theme::get_post_id_taking_into_preview() === $post_id;
			}
		}

		return $is_loop;
	}

	protected static function get_value( string $selector ) : array {
		$post_id = self::get_post_id( $selector );
		if ( false === $post_id && static::is_sub_loop() ) {
			return static::get_values(
				get_sub_field( $selector, false ),
				get_sub_field( $selector )
			);
		}

		return static::get_values(
			get_field( $selector, $post_id, false ),
			get_field( $selector, $post_id )
		);
	}

	protected static function get_values( $row_value, $format_value ) : array {
		return array(
			'raw'    => $row_value,
			'format' => $format_value,
			'text'   => is_string( $format_value ) ? wp_kses_post( $format_value ) : '',
		);
	}

	protected static function get_start_wrapper( string $wrapper ) : string {
		$start_wrap = '';
		if ( $wrapper ) {
			if ( false !== strpos( $wrapper, '%s' ) ) {
				$start_wrap = explode( '%s', $wrapper )[0];
			} elseif ( preg_match( '/^<([^>\s]+)[^>]*>$/', $wrapper, $matches ) ) {
				$start_wrap = $wrapper;
			} else {
				$start_wrap = '';
			}
		}

		return $start_wrap;
	}

	protected static function get_end_wrapper( string $wrapper ) : string {
		$end_wrapper = '';
		if ( $wrapper ) {
			if ( false !== strpos( $wrapper, '%s' ) ) {
				$end_wrapper = explode( '%s', $wrapper )[1];
			} elseif ( preg_match( '/^<([^>\s]+)[^>]*>$/', $wrapper, $matches ) ) {
				$end_wrapper = '</' . $matches[1] . '>';
			} else {
				$end_wrapper = '';
			}
		}

		return $end_wrapper;
	}

	protected static function set_attrs_wrap( array $args, array &$attrs, string &$wrap ) : void {
		if ( isset( $args[1] ) ) {
			if ( is_array( $args[1] ) ) {
				$attrs = $args[1];
				$wrap  = $args[2] ?? '';
			} else {
				$wrap = $args[1];
				if ( isset( $args[2] ) && is_array( $args[2] ) ) {
					$attrs = $args[2];
				}
			}
		}
	}

	protected static function is_url( ?string $url ) : bool {
		if ( $url ) {
			return false !== filter_var( $url, FILTER_VALIDATE_URL ) || 0 === strncmp( $url, '#', 1 );
		}

		return false;
	}

	protected static function get_post_id( string $selector ) {
		return 0 === strncmp( $selector, 'wld_', 4 ) ? 'options' : false;
	}
}
