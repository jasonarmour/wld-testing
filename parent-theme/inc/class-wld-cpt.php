<?php

class WLD_CPT {

	public const KEY = 'wld_cpt_slug_';

	public static $permalinks = array();

	public static $types = array();

	public static $thumbnails = array();

	public static function init(): void {
		add_action( 'init', array( self::class, 'register' ), 30 );
		add_action( 'load-options-permalink.php', array( self::class, 'settings' ) );
		add_filter( 'wld_thumbnails_support', array( self::class, 'set_thumbnail_support' ) );
	}

	public static function set_thumbnail_support( array $types ): array {
		return array_unique( array_merge( $types, self::$thumbnails ) );
	}

	public static function add( string $post_type, array $args = array() ): void {
		$args = array_merge(
			array(
				// Default parameters for register_post_type
				'labels'        => '',
				'public'        => false,
				'show_ui'       => true,
				'supports'      => array( 'title', 'revisions', 'editor', 'excerpt' ),
				'menu_icon'     => '',
				'menu_position' => 5,
				'rewrite'       => array(
					'slug'       => '',
					'with_front' => false,
				),
				// Special parameters
				'default_slug'  => '', // If public and empty rewrite, add default slug and create option
				'single_label'  => '',
				'plural_label'  => '',
			),
			$args
		);
		if ( $args['public'] && empty( $args['rewrite']['slug'] ) ) {
			self::$permalinks[ $post_type ] = $args['default_slug'];
			$args['rewrite']['slug']        = self::get_slug( $post_type );
		}
		if ( empty( $args['menu_icon'] ) && false !== $args['menu_icon'] ) {
			$args['menu_icon'] = 'dashicons-admin-post';
		}
		if ( in_array( 'thumbnail', $args['supports'], true ) ) {
			self::$thumbnails[] = $post_type;
		}
		self::$types[ $post_type ] = $args;
	}

	public static function get_slug( string $post_type ): string {
		$slug = false;
		if ( isset( self::$permalinks[ $post_type ] ) ) {
			$value = get_option( self::KEY . $post_type );
			if ( trim( $value ) ) {
				$slug = $value;
			} elseif ( empty( self::$permalinks[ $post_type ] ) ) {
				$slug = $post_type;
			} else {
				$slug = self::$permalinks[ $post_type ];
			}
		}

		return $slug;
	}

	public static function register(): void {
		foreach ( self::$types as $post_type => $args ) {
			if ( empty( $args['labels'] ) ) {
				$args['labels'] = self::get_labels( $post_type );
			}
			register_post_type( $post_type, $args );
		}
	}

	public static function get_labels( string $post_type ): array {
		$single = self::get_single_label( $post_type );
		$plural = self::get_plural_label( $single, $post_type );

		// phpcs:disable WordPress.WP.I18n
		return array(
			'name'                  => $plural,
			'singular_name'         => $single,
			'add_new'               => __( 'Add New', 'parent-theme' ),
			'add_new_item'          => sprintf( __( 'Add New %s', 'parent-theme' ), $single ),
			'edit_item'             => sprintf( __( 'Edit %s', 'parent-theme' ), $single ),
			'new_item'              => sprintf( __( 'New %s', 'parent-theme' ), $single ),
			'view_item'             => sprintf( __( 'View %s', 'parent-theme' ), $single ),
			'view_items'            => sprintf( __( 'View %s', 'parent-theme' ), $plural ),
			'search_items'          => sprintf( __( 'Search %s', 'parent-theme' ), $plural ),
			'not_found'             => sprintf( __( 'No %s found.', 'parent-theme' ), $plural ),
			'not_found_in_trash'    => sprintf( __( 'No %s found in Trash.', 'parent-theme' ), $plural ),
			'parent_item_colon'     => sprintf( __( 'Parent %s:', 'parent-theme' ), $plural ),
			'all_items'             => sprintf( __( 'All %s', 'parent-theme' ), $plural ),
			'uploaded_to_this_item' => sprintf( __( 'Uploaded to this %s', 'parent-theme' ), $single ),
			'filter_items_list'     => sprintf( __( 'Filter %s list', 'parent-theme' ), $plural ),
			'items_list_navigation' => sprintf( __( '%s list navigation', 'parent-theme' ), $plural ),
			'items_list'            => sprintf( __( '%s list', 'parent-theme' ), $plural ),
		);
		// phpcs:enable WordPress.WP.I18n
	}

	public static function get_single_label( string $post_type ) {
		if ( empty( self::$types[ $post_type ]['single_label'] ) ) {
			$label = ucwords( str_replace( array( '_', '-' ), ' ', $post_type ) );
		} else {
			$label = self::$types[ $post_type ]['single_label'];
		}

		return apply_filters( 'wld_get_cpt_label_single', $label, $post_type );
	}

	/** @noinspection DuplicatedCode */
	public static function get_plural_label( string $singular, string $post_type ) {
		if ( empty( self::$types[ $post_type ]['plural_label'] ) ) {
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
			$label = self::$types[ $post_type ]['plural_label'];
		}

		return apply_filters( 'wld_get_cpt_label_plural', $label, $post_type );
	}

	/** @noinspection DuplicatedCode */
	public static function settings(): void {
		if ( empty( self::$permalinks ) ) {
			return;
		}
		add_settings_section(
			'wld_cpt_permalinks',
			__( 'Theme Custom Post Types', 'parent-theme' ),
			'__return_empty_string',
			'permalink'
		);
		foreach ( self::$permalinks as $post_type => $permalink ) {
			$id    = self::KEY . $post_type;
			$value = sanitize_text_field( $_POST[ $id ] ?? '' ); // phpcs:ignore WordPress.Security.NonceVerification

			if ( $value ) {
				$value = trim( $value, '/' );
				$value = esc_url_raw( $value );
				$value = str_replace( 'http://', '', $value );
				update_option( $id, $value, true );
			}

			register_setting( 'permalink', $id );
			add_settings_field(
				$id,
				self::get_single_label( $post_type ),
				static function () use ( $id, $post_type ) {
					$slug = self::get_slug( $post_type );
					$home = home_url( '/' );
					$tags = array();
					if ( class_exists( 'WLD_Tax' ) ) {
						$tags = WLD_Tax::get_tags_by_post_type( $post_type );
						$tags = trim( implode( ', ', $tags ), ', ' );
					}
					?>
					<code><?php echo $home; ?></code>
					<!--suppress HtmlFormInputWithoutLabel -->
					<input name="<?php echo $id; ?>" id="<?php echo $id; ?>"
						   type="text" value="<?php echo esc_attr( $slug ); ?>" class="regular-text code"/>
					<div class="available-structure-tags hide-if-no-js">
						<div id="custom_selection_updated" aria-live="assertive"
							 class="screen-reader-text"></div>
						<?php if ( ! empty( $tags ) ) : ?>
							<p><?php _e( 'Available tags: ', 'parent-theme' ); ?><?php echo $tags; ?></p>
						<?php endif; ?>
					</div>
					<?php
				},
				'permalink',
				'wld_cpt_permalinks'
			);
		}
	}
}
