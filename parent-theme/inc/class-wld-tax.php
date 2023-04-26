<?php

class WLD_Tax {

	public const KEY = 'wld_tax_slug_';

	public static $permalinks = array();

	public static $tags = array();

	public static $taxes = array();

	public static function init(): void {
		add_action( 'init', array( self::class, 'register' ), 20 );
		add_action( 'load-options-permalink.php', array( self::class, 'settings' ) );
		add_filter( 'post_type_link', array( self::class, 'replace_tags' ), 10, 2 );

		if ( defined( 'WLD_TAX_SET_DEFAULT_TERM' ) && WLD_TAX_SET_DEFAULT_TERM ) {
			add_action( 'save_post', array( self::class, 'set_default_term' ), 10, 2 );
		}
	}

	public static function add( string $taxonomy, ?array $args ): void {
		$args = array_merge(
			array(
				// Default parameters for register_taxonomy
				'labels'            => '',
				'public'            => false,
				'show_ui'           => true,
				'hierarchical'      => false,
				'show_admin_column' => true,
				'rewrite'           => array(
					'slug'       => '',
					'with_front' => false,
				),
				// Special parameters
				'object_type'       => array(), // Object_type for register_taxonomy
				'rewrite_tag'       => false, // Add rewrite tag,
				'default_slug'      => '', // If public and empty rewrite, add default slug and create option
				'single_label'      => '',
				'plural_label'      => '',
			),
			$args
		);
		if ( $args['public'] && is_array( $args['rewrite'] ) && empty( $args['rewrite']['slug'] ) ) {
			self::$permalinks[ $taxonomy ] = $args['default_slug'];
			$args['rewrite']['slug']       = self::get_slug( $taxonomy );
		}
		if ( $args['rewrite_tag'] ) {
			self::$tags[ $taxonomy ] = is_string( $args['rewrite_tag'] ) ? $args['rewrite_tag'] : "%$taxonomy%";
		}
		self::$taxes[ $taxonomy ] = $args;
	}

	public static function get_slug( string $post_type ): string {
		$slug = false;
		if ( isset( self::$permalinks[ $post_type ] ) ) {
			$value = get_option( self::KEY . $post_type );
			if ( trim( $value ) ) {
				$slug = $value;
			} else {
				$slug = self::$permalinks[ $post_type ];
			}
		}

		return $slug;
	}

	public static function register(): void {
		foreach ( self::$taxes as $taxonomy => $args ) {
			if ( ! taxonomy_exists( $taxonomy ) ) {
				if ( empty( $args['labels'] ) ) {
					$args['labels'] = self::get_labels( $taxonomy );
				}
				register_taxonomy(
					$taxonomy,
					$args['object_type'],
					$args
				);
			}
		}
		foreach ( self::$tags as $tag ) {
			add_rewrite_tag( $tag, '([^/]+)' );
		}
	}

	public static function get_labels( string $taxonomy ): array {
		$single = self::get_single_label( $taxonomy );
		$plural = self::get_plural_label( $single, $taxonomy );

		// phpcs:disable WordPress.WP.I18n
		return array(
			'name'                       => $plural,
			'singular_name'              => $single,
			'search_items'               => sprintf( __( 'Search %s', 'parent-theme' ), $plural ),
			'popular_items'              => sprintf( __( 'Popular %s', 'parent-theme' ), $plural ),
			'all_items'                  => sprintf( __( 'All %s', 'parent-theme' ), $plural ),
			'parent_item'                => sprintf( __( 'Parent %s', 'parent-theme' ), $single ),
			'parent_item_colon'          => sprintf( __( 'Parent %s:', 'parent-theme' ), $single ),
			'edit_item'                  => sprintf( __( 'Edit %s', 'parent-theme' ), $single ),
			'view_item'                  => sprintf( __( 'View %s', 'parent-theme' ), $single ),
			'update_item'                => sprintf( __( 'Update %s', 'parent-theme' ), $single ),
			'add_new_item'               => sprintf( __( 'Add New %s', 'parent-theme' ), $single ),
			'new_item_name'              => sprintf( __( 'New %s Name', 'parent-theme' ), $single ),
			'add_or_remove_items'        => sprintf( __( 'Add or remove %s', 'parent-theme' ), $plural ),
			'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', 'parent-theme' ), $plural ),
			'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s', 'parent-theme' ), $plural ),
			'not_found'                  => sprintf( __( 'No %s found.', 'parent-theme' ), $plural ),
			'no_terms'                   => sprintf( __( 'No %s', 'parent-theme' ), $plural ),
			'items_list_navigation'      => sprintf( __( '%s list navigation', 'parent-theme' ), $plural ),
			'items_list'                 => sprintf( __( '%s list', 'parent-theme' ), $plural ),
		);
		// phpcs:enable WordPress.WP.I18n
	}

	public static function get_single_label( string $taxonomy ): string {
		if ( empty( self::$taxes[ $taxonomy ]['single_label'] ) ) {
			$label = ucwords( str_replace( array( '_', '-' ), ' ', $taxonomy ) );
		} else {
			$label = self::$taxes[ $taxonomy ]['single_label'];
		}

		return apply_filters( 'wld_get_taxonomy_label_plural', $label, $taxonomy );
	}

	/** @noinspection DuplicatedCode */
	public static function get_plural_label( string $singular, string $taxonomy ): string {
		if ( empty( self::$taxes[ $taxonomy ]['plural_label'] ) ) {
			switch ( strtolower( $singular[ strlen( $singular ) - 1 ] ) ) {
				case 'y':
					$label = substr( $singular, 0, - 1 ) . 'ies';
					break;
				case 's':
					$label = $singular . 'es';
					break;
				default:
					$label = $singular . 's';
			}
		} else {
			$label = self::$taxes[ $taxonomy ]['plural_label'];
		}

		return apply_filters( 'wld_get_cpt_label_plural', $label, $taxonomy );
	}

	public static function replace_tags( string $post_link, ?WP_Post $post ) : string {
		foreach ( self::$tags as $taxonomy => $tag ) {
			if ( false !== strpos( $post_link, $tag ) ) {
				/** @var WP_Term[] $terms */
				$terms = get_the_terms( $post, $taxonomy );
				if ( $terms && ! is_wp_error( $terms ) && isset( $terms[0] ) ) {
					$taxonomy_slug = $terms[0]->slug;
				} elseif ( defined( 'WLD_TAX_SET_DEFAULT_TERM' ) && WLD_TAX_SET_DEFAULT_TERM ) {
					$taxonomy_slug = self::get_default_term( $taxonomy )->slug;
				} else {
					$taxonomy_slug = '';
				}
				$post_link = str_replace( $tag, $taxonomy_slug, (string) $post_link );
			}
		}

		return $post_link;
	}

	public static function get_default_term( string $taxonomy ) {
		$term = get_term_by( 'slug', 'default', $taxonomy );
		if ( ! $term ) {
			self::add_default_term( $taxonomy );
			$term = get_term_by( 'slug', 'default', $taxonomy );
		}

		return $term;
	}

	public static function add_default_term( string $taxonomy ): void {
		wp_insert_term(
			'Default',
			$taxonomy,
			array(
				'slug' => 'default',
			)
		);
	}

	public static function get_tags_by_post_type( string $post_type ): array {
		$tags = array();
		foreach ( self::$taxes as $taxonomy => $args ) {
			if ( isset( self::$tags[ $taxonomy ] ) && in_array( $post_type, (array) $args['object_type'], true ) ) {
				$tags[] = self::$tags[ $taxonomy ];
			}
		}

		return $tags;
	}

	public static function set_default_term( $post_id, WP_Post $post ): void {
		foreach ( self::$taxes as $taxonomy => $args ) {
			if ( in_array( $post->post_type, (array) $args['object_type'], true ) ) {
				$terms = wp_get_object_terms(
					$post_id,
					$taxonomy,
					array(
						'fields'                 => 'tt_ids',
						'orderby'                => 'none',
						'update_term_meta_cache' => false,
					)
				);
				if ( empty( $terms ) ) {
					$term_id = self::get_default_term( $taxonomy )->term_id;
					wp_set_object_terms( $post_id, $term_id, $taxonomy );
				}
			}
		}
	}

	/** @noinspection DuplicatedCode */
	public static function settings(): void {
		if ( empty( self::$permalinks ) ) {
			return;
		}
		add_settings_section(
			'wld_tax_permalinks',
			__( 'Theme Custom Taxonomies', 'parent-theme' ),
			'__return_empty_string',
			'permalink'
		);
		foreach ( self::$permalinks as $taxonomy => $permalink ) {
			$id    = self::KEY . $taxonomy;
			$value = sanitize_text_field( $_POST[ $id ] ?? '' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

			if ( $value ) {
				$value = trim( $value, '/' );
				$value = esc_url_raw( $value );
				$value = preg_replace( '|http[s]?://|', '', $value );
				update_option( $id, $value, false );
			}

			register_setting( 'permalink', $id );
			add_settings_field(
				$id,
				self::get_single_label( $taxonomy ),
				static function () use ( $id, $taxonomy ) {
					$slug = self::get_slug( $taxonomy );
					$home = home_url( '/' );
					?>
					<code><?php echo $home; ?></code>
					<!--suppress HtmlFormInputWithoutLabel -->
					<input name="<?php echo $id; ?>" id="<?php echo $id; ?>"
						   type="text" value="<?php echo esc_attr( $slug ); ?>" class="regular-text code"/>
					<div class="available-structure-tags hide-if-no-js">
						<div id="custom_selection_updated" aria-live="assertive"
							 class="screen-reader-text"></div>
					</div>
					<?php
				},
				'permalink',
				'wld_tax_permalinks'
			);
		}
	}
}
