<?php

class WLD_ACF_Relationship_All {
	public const KEY = 'relationship_all';

	public static function init() : void {
		add_action(
			'acf/render_field_settings/type=relationship',
			array( static::class, 'settings' )
		);
		add_action(
			'acf/render_field/type=relationship',
			array( static::class, 'the_checkbox' )
		);
		add_filter(
			'acf/update_value/type=relationship',
			array( static::class, 'save_checkbox' ),
			10,
			3
		);
		add_action(
			'acf/input/admin_footer',
			array( static::class, 'script' )
		);
		add_filter(
			'acf/load_value/type=relationship',
			array( static::class, 'load_value' ),
			10,
			3
		);
	}

	public static function settings( $field ) : void {
		acf_render_field_setting(
			$field,
			array(
				'label' => __( 'Enable All Checkbox', 'parent-theme' ),
				'name'  => static::KEY,
				'type'  => 'true_false',
				'ui'    => 1,
			)
		);
	}

	public static function the_checkbox( $field ) : void {
		if ( static::enabled( $field ) ) {
			$name = static::get_name( $field );
			if ( is_numeric( $field['post_id'] ?? '' ) ) {
				$all = get_post_meta( $field['post_id'], $name, true );
			} elseif ( isset( $field['_name'] ) ) {
				$all = get_option( $field['_name'] . '_all', 'no' );
			} else {
				$all = 'no';
			}

			$label = esc_html__( 'All', 'parent-theme' );

			/** @noinspection HtmlUnknownAttribute */
			printf(
				'<p%s><label><input type="checkbox" class="wld-r-all" value="1" name="%s" %s>%s</label></p>',
				' style="margin-bottom:0;"',
				$name,
				checked( $all, 'yes', false ),
				$label
			);
		}
	}

	protected static function enabled( array $field ) : bool {
		return (bool) ( $field[ static::KEY ] ?? '0' );
	}

	protected static function get_name( array $field ) : string {
		return static::KEY . '_' . $field['key'];
	}

	public static function save_checkbox( $value, $post_id, array $field ) {
		if ( static::enabled( $field ) ) {
			$name = static::get_name( $field );
			$all  = isset( $_POST[ $name ] ) ? 'yes' : 'no'; // phpcs:ignore
			if ( is_numeric( $post_id ) ) {
				update_post_meta( $post_id, $name, $all );
			} elseif ( isset( $field['_name'] ) ) {
				update_option( $field['_name'] . '_all', $all, false );
			}
		}

		return $value;
	}

	public static function load_value( $value, $post_id, array $field ) {
		if ( static::enabled( $field ) && ! is_admin() ) {
			$name = static::get_name( $field );
			if ( is_numeric( $post_id ) ) {
				$all = get_post_meta( $post_id, $name, true );
			} elseif ( isset( $field['_name'] ) ) {
				$all = get_option( $field['name'] . '_all', 'no' );
			} else {
				$all = 'no';
			}

			if ( 'yes' === $all ) {
				$args = array(
					'posts_per_page' => - 1,
					'post_status'    => 'publish',
					'orderby'        => 'menu_order',
					'order'          => 'asc',
					'fields'         => 'ids',
				);

				if ( ! empty( $field['post_type'] ) ) {
					$args['post_type'] = acf_get_array( $field['post_type'] );
				} else {
					$args['post_type'] = acf_get_post_types();
				}

				if ( ! empty( $field['taxonomy'] ) ) {
					$terms             = acf_decode_taxonomy_terms( $field['taxonomy'] );
					$args['tax_query'] = array( 'relation' => 'OR' );

					foreach ( $terms as $k => $v ) {
						$args['tax_query'][] = array(
							'taxonomy' => $k,
							'field'    => 'slug',
							'terms'    => $v,
						);
					}
				}

				$value = get_posts( apply_filters( 'wld_relationship_all_args', $args, $field ) );
			}
		}

		return $value;
	}

	public static function script() : void {
		?>
		<script>
			jQuery( function( $ ) {
				$( '.wld-r-all' ).on( 'change', toggle ).each( toggle );

				function toggle() {
					const $wrap = $( this ).closest( '.acf-input' ).find( '.acf-relationship' );
					$wrap.toggle( ! $( this ).prop( 'checked' ) );
				}
			} );
		</script>
		<?php
	}

	public static function is_all( string $selector ) : bool {
		$all   = 'no';
		$field = WLD_Fields::get_field( $selector );
		if ( $field && static::enabled( $field ) ) {
			$name = static::get_name( $field );
			$all  = get_post_meta( WLD_Theme::get_post_id_taking_into_preview(), $name, true );
		}

		return 'yes' === $all;
	}
}
