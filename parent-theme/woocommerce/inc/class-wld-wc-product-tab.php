<?php /** @noinspection UnknownInspectionInspection, PhpUnused, PhpUndefinedMethodInspection, PhpUndefinedFunctionInspection, PhpUndefinedClassInspection */


class WLD_WC_Product_Tab {
	public const CUSTOM = 'custom';

	protected $title;
	protected $key;
	protected $prefix;
	protected $is_custom_title;
	protected $priority;
	protected $fields;
	protected $callback;
	protected $show_title_in_tab;

	public function __construct( $title = '', $args = array() ) {
		$this->title           = ucwords( wp_strip_all_tags( $title ) );
		$this->key             = sanitize_key( $this->title ) ?: static::CUSTOM;
		$this->prefix          = '_tab_' . $this->key;
		$this->is_custom_title = static::CUSTOM === $this->key;

		$args = array_merge(
			array(
				'fields'            => array(
					'text' => array(
						'label' => esc_html__( 'Text', 'parent-theme' ),
						'type'  => 'wysiwyg',
					),
				),
				'priority'          => 0,
				'callback'          => '',
				'show_title_in_tab' => true,
			),
			$args
		);

		$this->priority          = absint( $args['priority'] );
		$this->fields            = $args['fields'];
		$this->callback          = $args['callback'];
		$this->show_title_in_tab = $args['show_title_in_tab'];

		WLD_WC_Single_Product::$has_admin_fields = true;

		$this->hooks();
	}

	public function add_in_tabs( array $tabs ): array {
		/** @var WC_Product $product */
		global $product;

		$output = $this->get_output( $product );
		if ( $output ) {
			$tab                = $this;
			$tabs[ $this->key ] = array(
				'title'    => $this->get_title( $product ),
				'priority' => $this->priority ?: static::get_priority(),
				'callback' => static function () use ( $tab, $product ) {
					$tab->callback( $product );
				},
			);
		}

		return $tabs;
	}

	public function show_fields(): void {
		/** @var WC_Product $product_object */
		global $product_object;

		$title = $this->is_custom_title ? esc_html__( 'Custom', 'parent-theme' ) : $this->title;
		$tab   = esc_html__( 'Tab', 'parent-theme' );

		echo '<div class="options_group custom-tab">';
		echo '<h2><strong>' . $title . ' ' . $tab . '</strong></h2>';
		if ( $this->is_custom_title ) {
			woocommerce_wp_text_input(
				array(
					'id'    => $this->prefix . '_tab_title',
					'label' => esc_html__( 'Title', 'parent-theme' ),
				)
			);
		}

		foreach ( $this->fields as $id => $field ) {
			$field['id'] = $this->prefix . '_' . $id;
			$type        = $field['type'] ?? 'text';

			if ( 'select' === $type ) {
				woocommerce_wp_select( $field );
			} elseif ( 'textarea' === $type ) {
				woocommerce_wp_textarea_input( $field );
			} elseif ( 'checkbox' === $type ) {
				woocommerce_wp_checkbox( $field );
			} elseif ( 'radio' === $type ) {
				woocommerce_wp_radio( $field );
			} elseif ( 'upload' === $type ) {
				$uploads         = '';
				$value           = $product_object->get_meta( $field['id'] );
				$attachments_ids = wp_parse_id_list( $value );
				if ( $attachments_ids ) {
					foreach ( $attachments_ids as $attachment_id ) {
						$uploads .= sprintf(
							'<span class="thumbnail">%s</span>',
							wp_get_attachment_image( $attachment_id, 'thumbnail', true )
						);
					}
				}

				$this->wrap_field(
					$field,
					sprintf(
						'
						<div class="form-field-upload" data-multiple="%s">
							%s
							<button type="button" class="button button-primary button-small select-uploads" id="%s">
								Select
							</button>
							<button type="button" class="button button-default button-small clear-uploads" style="%s">
								Clear
							</button>
							<input type="hidden" name="%s" value="%s">
						</div>
						',
						empty( $field['multiple'] ) ? 'false' : 'true',
						$uploads,
						$field['id'],
						$uploads ? 'display:inline-block' : 'display: none;',
						$field['id'],
						$value
					)
				);
			} elseif ( 'wysiwyg' === $type ) {
				ob_start();
				wp_editor(
					$product_object->get_meta( $field['id'] ),
					$this->prefix . '_text',
					array( 'textarea_rows' => 6 )
				);

				$this->wrap_field( $field, ob_get_clean() );
			} else {
				woocommerce_wp_text_input( $field );
			}
		}
		echo '</div>';
	}

	public function save_fields( WC_Product $product ): void {
		if ( $this->is_custom_title ) {
			$key = $this->prefix . '_tab_title';
			$product->update_meta_data( $key, wc_clean( $_POST[ $key ] ?? '' ) ); // phpcs:ignore
		}

		foreach ( $this->fields as $id => $field ) {
			$key   = $this->prefix . '_' . $id;
			$type  = $field['type'] ?? 'text';
			$value = wp_unslash( $_POST[ $key ] ?? '' ); // phpcs:ignore

			if ( 'select' === $type ) {
				if ( ! in_array( $value, $field, true ) ) {
					$value = '';
				}
			} elseif ( 'textarea' === $type ) {
				$value = wc_sanitize_textarea( $value );
			} elseif ( 'checkbox' === $type ) {
				$value = $value ? 'yes' : 'no';
			} elseif ( 'wysiwyg' === $type ) {
				$value = wp_kses( $value, 'post' );
			} elseif ( 'upload' === $type ) {
				$value = implode( ',', wp_parse_id_list( $value ) );
			} else {
				$value = wc_clean( $value );
			}

			$product->update_meta_data( $key, $value );
		}
	}

	public function get_meta_key( $id ): string {
		return $this->prefix . '_' . $id;
	}

	protected function hooks(): void {
		add_filter(
			'woocommerce_product_tabs',
			array( $this, 'add_in_tabs' )
		);
		add_action(
			'woocommerce_product_options_theme',
			array( $this, 'show_fields' )
		);
		add_action(
			'woocommerce_admin_process_product_object',
			array( $this, 'save_fields' )
		);
	}

	protected function get_title( $product ): string {
		return $this->is_custom_title ? $product->get_meta( $this->prefix . '_tab_title' ) : $this->title;
	}

	protected function get_output( $product ): string {
		$output = '';
		foreach ( $this->fields as $id => $field ) {
			$value = $product->get_meta( $this->get_meta_key( $id ) );
			$type  = $field['type'] ?? 'text';
			$label = $field['label'] ?? '';

			if ( $value ) {
				if ( 'select' === $type || 'checkbox' === $type ) {
					$output .= wpautop( $label . ': ', $value );
				} elseif ( 'upload' === $type ) {
					$ids = wp_parse_id_list( $value );
					if ( $ids ) {
						foreach ( $ids as $post_id ) {
							$output .= wpautop( wp_get_attachment_link( $post_id ) );
						}
					}
				} else {
					$output .= wpautop( $value );
				}
			}
		}

		return $output;
	}

	protected function callback( WC_Product $product ): void {
		if ( is_callable( $this->callback ) ) {
			call_user_func( $this->callback, $this, $product );
		} else {
			$output = $this->get_output( $product );
			if ( $output ) {
				if ( $this->show_title_in_tab ) {
					echo '<h2>' . $this->get_title( $product ) . '</h2>';
				}

				echo $output;
			}
		}
	}

	protected function wrap_field( $field, $input ): void {
		$this->start_field( $field );
		echo $input;
		$this->end_field( $field );
	}

	protected function start_field( $field ): void {
		$id          = esc_attr( $field['id'] ?? '' );
		$class       = esc_attr( $field['wrapper_class'] ?? '' );
		$label       = wp_kses_post( $field['label'] ?? '' );
		$description = $field['description'] ?? '';
		$tip         = (bool) ( $field['desc_tip'] ?? false );

		echo '<div class="form-field ' . $id . '_field ' . $class . '"><label for="' . $id . '">' . $label . '</label>';
		if ( $description && $tip ) {
			echo wc_help_tip( $description );
		}
	}

	protected function end_field( $field ): void {
		$description = wp_kses_post( $field['description'] ?? '' );
		$tip         = (bool) ( $field['desc_tip'] ?? false );
		if ( $description && $tip ) {
			echo '<span class="description">' . wp_kses_post( $description ) . '</span>';
		}
		echo '</div>';
	}

	protected static function get_priority(): int {
		static $priority = 1;

		return $priority ++;
	}
}
