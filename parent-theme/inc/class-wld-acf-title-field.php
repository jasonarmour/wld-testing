<?php

class WLD_ACF_Title_Field extends WLD_ACF_Replace_Text_Field {
	public static $has_main_auto_title;

	public function initialize(): void {
		parent::initialize();
		$this->name                      = 'title';
		$this->label                     = __( 'Title', 'parent-theme' );
		$this->defaults['default_level'] = apply_filters( 'wld_acf_title_default_level', false, 0 );
	}

	/** @noinspection DuplicatedCode */
	public function render_field( array $field ): void {
		$value = $field['value'];
		$level = $field['default_level'] ?? 0;

		if ( is_array( $value ) ) {
			$field['value'] = $value['text'];
			$level          = (int) $value['level'];
		}

		$atts  = array();
		$keys  = array( 'id', 'class', 'name', 'value', 'placeholder', 'rows', 'maxlength' );
		$keys2 = array( 'readonly', 'disabled', 'required' );
		foreach ( $keys as $k ) {
			if ( isset( $field[ $k ] ) ) {
				$atts[ $k ] = $field[ $k ];
			}
		}
		foreach ( $keys2 as $k ) {
			if ( ! empty( $field[ $k ] ) ) {
				$atts[ $k ] = $k;
			}
		}
		$atts['name'] .= '[text]';

		$atts = acf_clean_atts( $atts );

		$only_auto_level = apply_filters( 'wld_acf_title_only_auto_level', true, $field );
		$level_atts      = array(
			'name'  => $field['name'] . '[level]',
			'label' => __( 'Level', 'parent-theme' ),
			'value' => $level,
		);

		if ( ! $only_auto_level ) {
			$level_atts['choices'] = array( 'Auto', '1', '2', '3', '4', '5', '6' );
		}
		?>
		<div class="acf-wld-title">
			<div>
				<?php acf_textarea_input( $atts ); ?>
			</div>
			<?php if ( $only_auto_level ) : ?>
				<?php acf_hidden_input( $level_atts ); ?>
			<?php else : ?>
				<div>
					<label>
						<?php esc_html_e( 'Level', 'parent-theme' ); ?>
						<?php acf_select_input( $level_atts ); ?>
					</label>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	public function render_field_settings( array $field ): void {
		parent::render_field_settings( $field );
		acf_render_field_setting(
			$field,
			array(
				'label'         => __( 'Default Level', 'parent-theme' ),
				'type'          => 'select',
				'choices'       => array( 'Auto', '1', '2', '3', '4', '5', '6' ),
				'name'          => 'default_level',
				'default_value' => $this->defaults['default_level'],
			)
		);
	}

	/** @noinspection CallableParameterUseCaseInTypeContextInspection */
	public function format_value( $value, $post_id, array $field ): string {
		if ( ! is_array( $value ) ) {
			$value = array(
				'text'  => $value,
				'level' => '0',
			);
		}

		if ( apply_filters( 'wld_acf_title_only_auto_level', true, $field ) ) {
			$level = (int) ( $field['default_level'] ?? '0' );
		} else {
			$level = (int) ( $value['level'] ?? '0' );
		}

		if ( 0 === $level ) {
			$level = self::$has_main_auto_title ? 2 : 1;
		}

		$title = $this->pre_formatting( $value['text'], $field );

		if ( $title && 1 === $level ) {
			self::$has_main_auto_title = true;
		}

		return $title ? sprintf( '<h%2$s>%1$s</h%2$s>', $title, $level ) : '';
	}

	public function input_admin_enqueue_scripts(): void {
		$url = get_template_directory_uri();
		wp_enqueue_style(
			'wld-acf-title-field',
			$url . '/css/wld-acf-title-field.css',
			array( 'acf-input' ),
			WLD_VER
		);
	}
}
