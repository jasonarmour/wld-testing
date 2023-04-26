<?php

class WLD_ACF_Contact_Link_Field extends acf_field {

	public $link_types;

	public function initialize(): void {
		$this->name       = 'wld_contact_link';
		$this->label      = __( 'Contact Link', 'parent-theme' );
		$this->category   = 'relational';
		$this->defaults   = array(
			'class_attr'    => '',
			'link_type'     => 'phone',
			'default_value' => array(
				'url'    => '',
				'title'  => '',
				'number' => '',
				'type'   => '',
				'class'  => '',
			),
			'return_format' => 'html',
		);
		$this->link_types = array(
			'phone' => __( 'Phone', 'parent-theme' ),
			'fax'   => __( 'Fax', 'parent-theme' ),
			'email' => __( 'Email', 'parent-theme' ),
		);
	}

	public function render_field_settings( array $field ): void {
		acf_render_field_setting(
			$field,
			array(
				'label' => __( 'Class Attribute', 'parent-theme' ),
				'type'  => 'text',
				'name'  => 'class_attr',
			)
		);
		acf_render_field_setting(
			$field,
			array(
				'label' => __( 'User Select Link Type', 'parent-theme' ),
				'type'  => 'true_false',
				'name'  => 'select_link_type',
				'ui'    => 1,
			)
		);
		acf_render_field_setting(
			$field,
			array(
				'label'   => __( 'Link Type', 'parent-theme' ),
				'type'    => 'radio',
				'name'    => 'link_type',
				'choices' => $this->link_types,
			)
		);
		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Return Value', 'parent-theme' ),
				'instructions' => __( 'Specify the returned value on front end', 'parent-theme' ),
				'type'         => 'radio',
				'name'         => 'return_format',
				'layout'       => 'horizontal',
				'choices'      => array(
					'array' => __( 'Link Array', 'parent-theme' ),
					'html'  => __( 'Link HTML', 'parent-theme' ),
				),
			)
		);
		acf_render_field_setting(
			$field,
			array(
				'label'         => __( 'User Custom Class', 'parent-theme' ),
				'type'          => 'true_false',
				'name'          => 'custom_class',
				'ui'            => 1,
				'default_value' => 1,
			)
		);
	}

	public function render_field( array $field ): void {
		$type        = '';
		$placeholder = '';
		$prepend     = '';
		$value       = wp_parse_args(
			$field['value'],
			array(
				'link_type' => $field['link_type'],
				'url'       => '',
				'title'     => '',
				'number'    => '',
				'type'      => '',
				'class'     => '',
			)
		);
		switch ( $value['link_type'] ) {
			case 'phone':
				$type        = 'tel';
				$placeholder = '+1234567890';
				$prepend     = 'tel:';
				break;
			case 'fax':
				$type        = 'tel';
				$placeholder = '+1234567890';
				$prepend     = 'fax:';
				break;
			case 'email':
				$type        = 'email';
				$placeholder = 'example@example.com';
				$prepend     = 'mailto:';
				break;
		}
		?>
		<div class="acf-wld-contact-link">
			<div class="wld-contact-link-wrap">
				<div class="wld-contact-link-value">
					<div>
						<label for="<?php echo $field['id']; ?>_title">
							<?php _e( 'Title:', 'parent-theme' ); ?>
						</label>
						<div class="wld-contact-link-input">
							<div class="acf-input">
								<div class="acf-input-append" data-name="paste">
									<span class="dashicons dashicons-admin-page"></span>
								</div>
								<div class="acf-input-wrap">
									<input type="text"
										   name="<?php echo $field['name']; ?>[title]"
										   id="<?php echo $field['id']; ?>_title"
										   value="<?php echo esc_attr( $value['title'] ); ?>"
										   data-name="title">
								</div>
							</div>
						</div>
						<br class="clear">
					</div>
					<div>
						<label for="<?php echo $field['id']; ?>_number">
							<?php _e( 'Number:', 'parent-theme' ); ?>
						</label>
						<div class="wld-contact-link-input">
							<div class="acf-input">
								<div class="acf-input-prepend"
									 data-name="prepend"><?php echo $prepend; ?></div>
								<div class="acf-input-wrap">
									<input type="<?php echo $type; ?>"
										   name="<?php echo $field['name']; ?>[number]"
										   id="<?php echo $field['id']; ?>_number"
										   value="<?php echo esc_attr( $value['number'] ); ?>"
										   placeholder="<?php echo $placeholder; ?>"
										   data-name="number">
								</div>
							</div>
						</div>
						<br class="clear">
					</div>
					<?php if ( isset( $field['select_link_type'] ) && 1 === $field['select_link_type'] ) : ?>
						<div>
							<label>Type : </label>
							<?php foreach ( (array) $this->link_types as $key => $label ) : ?>
								<label>
									<input type="radio" <?php checked( $key, $value['link_type'] ); ?>
										   name="<?php echo $field['name']; ?>[link_type]"
										   value="<?php echo $key; ?>" data-name="link_type">
									<?php echo $label; ?>
								</label>
							<?php endforeach; ?>
							<br class="clear">
						</div>
					<?php else : ?>
						<input type="hidden" name="<?php echo $field['name']; ?>[link_type]"
							   value="<?php echo $value['link_type']; ?>" data-name="link_type">
					<?php endif; ?>
					<?php if ( isset( $field['custom_class'] ) && 1 === $field['custom_class'] ) : ?>
						<div>
							<label for="<?php echo $field['id']; ?>_class">
								<?php _e( 'Class:', 'parent-theme' ); ?>
							</label>
							<div class="wld-contact-link-input">
								<input type="text"
									   name="<?php echo $field['name']; ?>[class]"
									   id="<?php echo $field['id']; ?>_class"
									   value="<?php echo esc_attr( $value['class'] ); ?>"
									   data-name="class">
							</div>
							<br class="clear">
						</div>
					<?php else : ?>
						<input type="hidden" name="<?php echo $field['name']; ?>[class]"
							   value="<?php echo esc_attr( $value['class'] ); ?>" data-name="class">
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
		if ( WLD_NEVER ) { // The condition is never fulfilled, only for IDE
			echo '<div class="acf-field acf-field-wld-contact-link"></div>';
		}
	}

	/** @noinspection PhpUnusedParameterInspection */
	public function update_value( $value, $post_id, array $field ) {
		if ( 1 === $field['select_link_type'] ) {
			$selector           = $field['ID'] ?: $field['key'];
			$field              = (array) acf_get_field( $selector );
			$field['link_type'] = $value['link_type'] ?? 'phone';
			acf_update_field( $field );
		}
		if ( empty( $value['title'] ) && empty( $value['number'] ) ) {
			$value = null;
		}

		return $value;
	}

	/** @noinspection PhpUnusedParameterInspection */
	public function format_value( $value, $post_id, array $field ) {
		// Fix for < 1.0.7
		if ( ! isset( $value['class'] ) ) {
			$value['class'] = '';
		}
		if ( empty( $value['link_type'] ) ) {
			$value['link_type'] = $field['link_type'];
		}
		if ( empty( $value['number'] ) ) {
			$value = null;
		} else {
			$attr   = array();
			$class  = '';
			$before = '';
			$after  = '';
			$html   = 'html' === $field['return_format'];

			if ( $field['class_attr'] ) {
				$class .= ' ' . $field['class_attr'];
			}

			if ( $value['class'] ) {
				$class .= ' ' . $value['class'];
			}
			$class = trim( $class );

			if ( $html && preg_match( '/(.*){(.*)}(.*)/', $value['title'], $matches ) ) {
				/** @noinspection PhpUnusedLocalVariableInspection */
				[ $full, $before, $value['title'], $after ] = $matches;
			}

			switch ( $value['link_type'] ) {
				case 'phone':
					$attr['href'] = 'tel:' . $value['number'];
					break;
				case 'fax':
					$attr['href'] = 'fax:' . $value['number'];
					break;
				case 'email':
					$attr['href']   = 'mailto:' . antispambot( $value['number'] );
					$value['title'] = antispambot( $value['title'] );
					break;
			}

			if ( $html ) {
				if ( $class ) {
					$attr['class'] = $class;
				}

				$attr = acf_esc_attrs( $attr );
				/** @noinspection HtmlUnknownAttribute */
				$value = "$before<a $attr>{$value['title']}</a>$after";
			} else {
				$value = array(
					'url'    => $attr['href'],
					'title'  => $value['title'],
					'number' => $value['number'],
					'type'   => $value['link_type'],
					'class'  => $class,
				);
			}
		}

		return $value;
	}

	/** @noinspection PhpUnusedParameterInspection */
	public function validate_value( $valid, $value, array $field, string $input ) {
		if ( ! empty( $value['title'] ) && empty( $value['number'] ) ) {
			$valid = __( 'Number can not be empty!', 'parent-theme' );
		}

		return $valid;
	}

	public function input_admin_enqueue_scripts(): void {
		$url = get_template_directory_uri();
		wp_enqueue_script(
			'wld-acf-contact-link-field',
			$url . '/js/wld-acf-contact-link-field.js',
			array( 'acf-input', 'wplink' ),
			WLD_VER,
			true
		);
		wp_enqueue_style(
			'wld-acf-contact-link-field',
			$url . '/css/wld-acf-contact-link-field.css',
			array( 'acf-input' ),
			WLD_VER
		);
	}
}
