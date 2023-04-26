<?php

class WLD_Not_A_Page {

	public static function init(): void {
		acf_add_local_field_group(
			array(
				'key'                   => 'not_a_page',
				'title'                 => __( 'Not a Page', 'parent-theme' ),
				'fields'                => array(
					array(
						'key'               => 'not_a_page_redirect_type',
						'label'             => __( 'Redirect Type', 'parent-theme' ),
						'name'              => 'redirect_type',
						'type'              => 'button_group',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '20',
							'class' => '',
							'id'    => '',
						),
						'choices'           => array(
							'first-child' => __( 'First Child', 'parent-theme' ),
							'url'         => __( 'URL', 'parent-theme' ),
						),
						'allow_null'        => 0,
						'default_value'     => 'first-child',
						'layout'            => 'horizontal',
						'return_format'     => 'value',
					),
					array(
						'key'               => 'not_a_page_redirect_url',
						'label'             => __( 'Redirect URL', 'parent-theme' ),
						'name'              => 'redirect_url',
						'type'              => 'url',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'not_a_page_redirect_type',
									'operator' => '==',
									'value'    => 'url',
								),
							),
						),
						'wrapper'           => array(
							'width' => '80',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => '',
						'placeholder'       => '',
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_template',
							'operator' => '==',
							'value'    => 'not-a-page',
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'seamless',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => array(
					0  => 'the_content',
					1  => 'excerpt',
					2  => 'discussion',
					3  => 'comments',
					4  => 'revisions',
					5  => 'slug',
					6  => 'author',
					7  => 'format',
					8  => 'featured_image',
					9  => 'categories',
					10 => 'tags',
					11 => 'send-trackbacks',
				),
				'active'                => 1,
				'description'           => '',
			)
		);
		add_filter( 'theme_templates', array( self::class, 'add_template' ), 10, 4 );
		add_filter( 'template_redirect', array( self::class, 'redirect' ) );
		add_filter( 'display_post_states', array( self::class, 'post_states' ), 10, 2 );
	}

	/** @noinspection PhpUnusedParameterInspection */
	public static function add_template( array $post_templates, WP_Theme $wp_theme, ?WP_Post $post, string $post_type
	): array {
		if ( 'page' === $post_type ) {
			$post_templates['not-a-page'] = __( 'Not a Page', 'parent-theme' );
		}

		return $post_templates;
	}

	public static function redirect(): void {
		if ( 'not-a-page' === get_page_template_slug() ) {
			$redirect = '';
			$type     = get_field( 'redirect_type' );
			if ( empty( $type ) || 'first-child' === $type ) {
				$posts = get_posts(
					array(
						'post_parent' => get_the_ID(),
						'post_type'   => 'page',
						'order'       => 'asc',
						'orderby'     => 'menu_order',
					)
				);
				if ( $posts ) {
					$redirect = get_permalink( $posts[0]->ID );
				}
			} elseif ( 'url' === $type ) {
				$url = get_field( 'redirect_url' );
				if ( ! empty( $url ) ) {
					$redirect = $url;
				}
			}
			if ( $redirect ) {
				wp_redirect( $redirect, 301 ); // phpcs:ignore WordPress.Security.SafeRedirect
				exit;
			}
		}
	}

	public static function post_states( array $post_states, WP_Post $post ): array {
		if ( 'not-a-page' === get_page_template_slug( $post->ID ) ) {
			$post_states['not-a-page'] = __( 'Not a Page', 'parent-theme' );
		}

		return $post_states;
	}
}
