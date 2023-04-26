<?php

class WLD_ACF_Menu_Field extends acf_field {
	public function initialize(): void {
		$this->name     = 'menu';
		$this->label    = __( 'Menu', 'parent-theme' );
		$this->category = 'relational';
		$this->defaults = array(
			'return_format' => 'object',
		);
	}

	public function render_field_settings( array $field ): void {
		acf_render_field_setting(
			$field,
			array(
				'label'   => __( 'Return Value', 'parent-theme' ),
				'type'    => 'radio',
				'name'    => 'return_format',
				'layout'  => 'horizontal',
				'choices' => array(
					'object' => __( 'Menu Object', 'parent-theme' ),
					'id'     => __( 'Menu ID', 'parent-theme' ),
				),
			)
		);
	}

	public function render_field( array $field ): void {
		$terms = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
		?>
		<!--suppress HtmlFormInputWithoutLabel -->
		<select id="<?php esc_attr( $field['id'] ); ?>" class="<?php echo esc_attr( $field['class'] ); ?>"
				name="<?php echo esc_attr( $field['name'] ); ?>">
			<option value=""><?php _e( '- Select a menu -', 'parent-theme' ); ?></option>
			<?php foreach ( $terms as $term ) : ?>
				<option
					value="<?php echo $term->term_id; ?>" <?php selected( $field['value'], $term->term_id ); ?>>
					<?php echo esc_html( $term->name ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/** @noinspection PhpUnusedParameterInspection */
	public function format_value( $value, $post_id, array $field ) {
		if ( ! empty( $value ) && 'object' === $field['return_format'] ) {
			$wp_menu_object = wp_get_nav_menu_object( $value );
			if ( ! empty( $wp_menu_object ) ) {
				$menu_object        = new stdClass();
				$menu_object->ID    = $wp_menu_object->term_id;
				$menu_object->name  = $wp_menu_object->name;
				$menu_object->slug  = $wp_menu_object->slug;
				$menu_object->count = $wp_menu_object->count;

				return $menu_object;
			}
		}

		return (int) $value;
	}
}
