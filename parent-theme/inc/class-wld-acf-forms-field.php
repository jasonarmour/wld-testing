<?php /** @noinspection SqlDialectInspection */

class WLD_ACF_Forms_Field extends acf_field {

	public $forms = array();

	public function initialize(): void {
		$this->name     = 'forms';
		$this->label    = __( 'Forms', 'parent-theme' );
		$this->category = 'relational';
		$this->defaults = array(
			'return_format' => 'post_object',
			'multiple'      => 0,
			'allow_null'    => 1,
		);
		$this->set_forms();
	}

	/** @noinspection SqlNoDataSourceInspection, SqlResolve */
	public function set_forms(): void {
		if ( class_exists( 'GFFormsModel' ) ) {
			global $wpdb;

			$wpdb->wld_gf_forms = GFFormsModel::get_form_table_name();
			if ( GFCommon::table_exists( $wpdb->wld_gf_forms ) ) {
				$results = $wpdb->get_results( "SELECT id, title FROM $wpdb->wld_gf_forms WHERE is_trash = 0" );
				if ( $results ) {
					foreach ( $results as $form ) {
						$this->forms[ $form->id ] = $form->title;
					}
				}
			}
		}
	}

	public function render_field_settings( array $field ): void {
		acf_render_field_setting(
			$field,
			array(
				'label'   => __( 'Return Value', 'parent-theme' ),
				'type'    => 'radio',
				'name'    => 'return_format',
				'layout'  => 'horizontal',
				'choices' => [
					'post_object' => __( 'Form Object', 'parent-theme' ),
					'id'          => __( 'Form ID', 'parent-theme' ),
				],
			)
		);
		acf_render_field_setting(
			$field,
			array(
				'label'   => __( 'Select multiple values?', 'parent-theme' ),
				'type'    => 'radio',
				'name'    => 'multiple',
				'choices' => [
					1 => __( 'Yes', 'parent-theme' ),
					0 => __( 'No', 'parent-theme' ),
				],
				'layout'  => 'horizontal',
			)
		);
	}

	/** @noinspection HtmlUnknownAttribute */
	public function render_field( array $field ): void {
		$html     = '';
		$hidden   = '';
		$multiple = '';
		$options  = '';
		$field    = array_merge( $this->defaults, $field );
		if ( $field['multiple'] ) {
			$forms = (array) $field['value'];
		} else {
			$field['value'] = (array) $field['value'];
			$forms          = (array) reset( $field['value'] );
		}

		$forms = wp_parse_id_list( $forms );
		foreach ( $this->forms as $form_id => $form_title ) {
			$selected = '';
			if ( $forms && in_array( $form_id, $forms, true ) ) {
				$selected = 'selected="selected"';
			}

			$options .= sprintf(
				'<option value="%s" %s>%s</option>',
				$form_id,
				$selected,
				$form_title
			);
		}
		if ( $field['multiple'] ) {
			$hidden   = '<input type="hidden" name="' . $field['name'] . '">';
			$multiple = '[]" multiple="multiple" data-multiple="1';
		}
		$html .= $hidden;
		$html .= '<select name="' . $field['name'] . $multiple . '">';
		$html .= '<option value="">' . __( '- Select a form -', 'parent-theme' ) . '</option>';
		$html .= $options;
		$html .= '</select>';

		echo $html;
	}

	/** @noinspection MultipleReturnStatementsInspection */
	/** @noinspection PhpUnusedParameterInspection */
	public function format_value( $value, $post_id, array $field ) {
		if ( $field['multiple'] ) {
			$_value = array();
			if ( $value ) {
				if ( 'post_object' === $field['return_format'] ) {
					foreach ( (array) $value as $form_id ) {
						$form = GFFormsModel::get_form( $form_id );
						if ( $form ) {
							$_value[ $form_id ] = (array) $form;
						}
					}
				} else {
					$_value = $value;
				}
			}

			return $_value;
		}

		if ( is_array( $value ) ) {
			$value = reset( $value );
		}

		if ( $value && 'post_object' === $field['return_format'] ) {
			$form = GFFormsModel::get_form( $value );
			if ( $form ) {
				return (array) $form;
			}

			return false;
		}

		return (int) $value;
	}

	public function update_value( $value ) {
		if ( is_array( $value ) ) {
			$value = array_values( array_filter( $value ) );
		}

		return $value;
	}
}
