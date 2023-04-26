<?php
/**
 * This file is more for a better understanding of what has changed, in case you need to update an old theme.
 * We do not have 100% support for all functions.
 */

if ( ! function_exists( 'wld_replace' ) ) {
	function wld_replace( string $text, string $replace = 'strong' ): string {
		_deprecated_function( __FUNCTION__, '4.0.0', 'wld_get_the_replace()' );

		return wld_get_the_replace( $text, $replace );
	}
}
if ( ! function_exists( 'wld_get_archive_url' ) ) {
	function wld_get_archive_url( $post = null ): string {
		_deprecated_function( __FUNCTION__, '4.0.0', 'wld_get_the_archive_url()' );

		return wld_get_the_archive_url( $post );
	}
}
if ( ! function_exists( 'wld_get_archive_title' ) ) {
	function wld_get_archive_title( $post = null ): string {
		_deprecated_function( __FUNCTION__, '4.0.0', 'wld_get_the_archive_title()' );

		return wld_get_the_archive_title( $post );
	}
}
if ( ! function_exists( 'wld_get_archive_link' ) ) {
	function wld_get_archive_link( $post = null, string $class = 'back-link' ): string {
		_deprecated_function( __FUNCTION__, '4.0.0', 'wld_get_the_archive_link()' );

		return wld_get_the_archive_link( $post, $class );
	}
}
if ( ! function_exists( 'wld_the_socials_links' ) ) {
	function wld_the_socials_links(
		string $selector = 'wld_footer_social_links', string $post_id = 'options', string $class = ''
	) {
		_deprecated_function( __FUNCTION__, '4.0.0', 'WLD_ACF_Social_Links_Field()' );
		$class = $class ?: 'social-links f-soc';
		?>
		<?php if ( have_rows( $selector, $post_id ) ) : ?>
			<ul class="<?php echo $class; ?>">
				<?php while ( have_rows( $selector, $post_id ) ) : ?>
					<?php
					the_row();
					$url  = get_sub_field( 'url' );
					$icon = wld_get_sub_image( 'icon' );
					?>
					<li><a href="<?php echo $url; ?>" target="_blank" rel="noopener"><?php echo $icon; ?></a></li>
				<?php endwhile; ?>
			</ul>
		<?php endif; ?>
		<?php
	}
}
if ( ! function_exists( 'wld_the_nav_links' ) ) {
	function wld_the_nav_links(
		string $location = '', string $separator = ' <span>|</span> ', bool $empty_first_before = false
	) {
		_deprecated_function( __FUNCTION__, '4.0.0', 'wld_the_nav()' );
		if ( empty( $location ) ) {
			$location = WLD_Nav::get_location( 'Footer Links' );
		}
		wp_nav_menu(
			array(
				'container'          => false,
				'depth'              => 1,
				'theme_location'     => $location,
				'fallback_cb'        => '__return_empty_string',
				'items_wrap'         => '%3$s',
				'walker'             => new WLD_Walker_A_Only(),
				'before'             => $separator,
				'empty_first_before' => $empty_first_before,
			)
		);
	}
}
if ( ! function_exists( 'wld_get_copyright' ) ) {
	function wld_get_copyright( bool $wpautop = false ): string {
		_deprecated_function( __FUNCTION__, '4.0.0', 'WLD_ACF_Copyright_Field()' );
		$text = str_replace(
			'%Y%',
			date( 'Y' ),
			get_field( 'wld_footer_copyright', 'options' )
		);
		if ( $wpautop ) {
			$text = wpautop( $text );
		}

		return $text;
	}
}
if ( ! function_exists( 'wld_the_copyright' ) ) {
	function wld_the_copyright( bool $wpautop = false ) {
		_deprecated_function( __FUNCTION__, '4.0.0', 'WLD_ACF_Copyright_Field()' );
		$text = str_replace(
			'%Y%',
			date( 'Y' ),
			get_field( 'wld_footer_copyright', 'options' )
		);
		if ( $wpautop ) {
			$text = wpautop( $text );
		}

		echo $text;
	}
}
if ( ! function_exists( 'wld_the_share_links' ) ) {
	function wld_the_share_links() {
		_deprecated_function( __FUNCTION__, '4.0.0', 'get_template_part()' );

		get_template_part( 'templates/archive/share-links' );
	}
}
if ( ! function_exists( 'wld_get_maybe_link' ) ) {
	function wld_get_maybe_link( string $content, $link, string $class = '' ): string {
		_deprecated_function( __FUNCTION__, '4.0.0', 'wld_get_the_maybe_link()' );

		return wld_get_the_maybe_link( $content, (array) $link, $class );
	}
}
if ( ! function_exists( 'wld_get_excerpt' ) ) {
	function wld_get_excerpt( int $length, bool $filter = false, bool $trim = false, string $after = '' ): string {
		_deprecated_function( __FUNCTION__, '4.0.0', 'wld_get_the_excerpt()' );

		return wld_get_the_excerpt( $length, $filter, $trim, $after );
	}
}
if ( ! function_exists( 'wld_get_the_btn' ) ) {
	function wld_get_the_btn( $button_array, string $class = '', bool $empty = false ): string {
		_deprecated_function( __FUNCTION__, '5.0.0', 'wld_get_link_html_from_array()' );

		return wld_get_link_html_from_array( (array) $button_array, $class, $empty );
	}
}
if ( ! function_exists( 'wld_the_btn' ) ) {
	function wld_the_btn( $button_array, string $class = '', bool $empty = false ): void {
		_deprecated_function( __FUNCTION__, '5.0.0', 'echo wld_get_link_html_from_array()' );

		echo wld_get_link_html_from_array( (array) $button_array, $class, $empty );
	}
}
if ( ! function_exists( 'wld_get_the_gf' ) ) {
	function wld_get_the_gf( $form_id_or_array, bool $display_title = true, bool $display_description = true
	): string {
		_deprecated_function( __FUNCTION__, '5.0.0', 'wld_get_form()|wld_get_sub_form()' );

		if ( function_exists( 'gravity_form' ) ) {
			$form_id = 0;
			if ( is_array( $form_id_or_array ) ) {
				$form_id = isset( $form_id_or_array['id'] ) ? absint( $form_id_or_array['id'] ) : 0;
			} elseif ( is_numeric( $form_id_or_array ) ) {
				$form_id = absint( $form_id_or_array );
			}

			if ( $form_id ) {
				return gravity_form(
					$form_id,
					$display_title,
					$display_description,
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
if ( ! function_exists( 'wld_the_gf' ) ) {
	function wld_the_gf( $form_id_or_array, bool $display_title = true, bool $display_description = true ): void {
		_deprecated_function( __FUNCTION__, '5.0.0', 'wld_the_form()|wld_the_sub_form()' );

		$form = '';
		if ( function_exists( 'gravity_form' ) ) {
			$form_id = 0;
			if ( is_array( $form_id_or_array ) ) {
				$form_id = isset( $form_id_or_array['id'] ) ? absint( $form_id_or_array['id'] ) : 0;
			} elseif ( is_numeric( $form_id_or_array ) ) {
				$form_id = absint( $form_id_or_array );
			}

			if ( $form_id ) {
				$form = gravity_form(
					$form_id,
					$display_title,
					$display_description,
					false,
					null,
					true,
					false,
					false
				);
			}
		}

		echo $form;
	}
}
if ( ! function_exists( 'wld_get_the_link' ) ) {
	function wld_get_the_link( $link_array, string $class = '', bool $empty = false ): string {
		_deprecated_function( __FUNCTION__, '5.0.0', 'wld_get_link_html_from_array()' );

		return wld_get_link_html_from_array( (array) $link_array, $class, $empty );
	}
}
