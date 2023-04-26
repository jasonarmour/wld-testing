<?php /** @noinspection UnknownInspectionInspection, PhpUnused */


class WLD_User_Avatar {
	public static function init(): void {
		add_filter(
			'pre_get_avatar',
			array( self::class, 'get_avatar' ),
			10,
			3
		);
		add_action(
			'acf/input/admin_footer',
			array( self::class, 'script' )
		);

		acf_add_local_field_group(
			array(
				'key'                   => 'group_user_avatar',
				'title'                 => 'User',
				'fields'                => array(
					array(
						'key'               => 'field_user_avatar',
						'label'             => 'Avatar',
						'name'              => 'avatar',
						'type'              => 'image',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'return_format'     => 'array',
						'preview_size'      => 'thumbnail',
						'library'           => 'all',
						'min_width'         => '',
						'min_height'        => '',
						'min_size'          => '',
						'max_width'         => '',
						'max_height'        => '',
						'max_size'          => '',
						'mime_types'        => '',
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'user_form',
							'operator' => '==',
							'value'    => 'all',
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'seamless',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => '',
				'modified'              => 1596117658,
			)
		);
	}

	/** @noinspection ReturnTypeCanBeDeclaredInspection, MultipleReturnStatementsInspection, PhpMissingReturnTypeInspection */
	public static function get_avatar( ?string $avatar, $id_or_email, array $args ) {
		if ( $args['force_default'] ) {
			return $avatar;
		}

		$image_id = 0;

		if ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
			$user = get_user_by( 'email', $id_or_email );
			if ( $user ) {
				$image_id = get_field( 'avatar', 'user_' . $user->ID, false );
			}
		} elseif ( is_numeric( $id_or_email ) ) {
			$image_id = get_field( 'avatar', 'user_' . $id_or_email, false );
		}

		if ( $image_id ) {
			$width  = (int) $args['width'];
			$height = (int) $args['height'];
			$url    = wp_get_attachment_image_url( $image_id, $width . 'x' . $height );
			$url2x  = wp_get_attachment_image_url( $image_id, $width * 2 . 'x' . $height * 2 );
			$class  = array( 'avatar', 'avatar-' . (int) $args['size'], 'photo' );

			if ( $url ) {
				/** @noinspection HtmlUnknownAttribute, HtmlUnknownTarget */
				return sprintf(
					"<img alt='%s' src='%s' srcset='%s' class='%s' height='%d' width='%d' %s/>",
					esc_attr( $args['alt'] ),
					esc_url( $url ),
					esc_url( $url2x ) . ' 2x',
					esc_attr( implode( ' ', $class ) ),
					(int) $args['height'],
					(int) $args['width'],
					$args['extra_attr']
				);
			}
		}

		if ( $args['default'] || is_admin() ) {
			return $avatar;
		}

		return '';
	}

	public static function script(): void {
		if ( 'profile' === get_current_screen()->id ) {
			?>
			<script>
				jQuery( function( $ ) {
					const
						$gravatar = $( '.user-profile-picture' ).closest( '.form-table' ),
						$avatar = $( '[data-key="field_user_avatar"]' ).closest( '.form-table' ),
						$label = $avatar.find( '.acf-label' );

					$gravatar.after( $avatar );
					$label.replaceWith( $( '<th class="acf-label">' + $label.text() + '</th>' ) );
				} );
			</script>
			<?php
		}
	}
}
