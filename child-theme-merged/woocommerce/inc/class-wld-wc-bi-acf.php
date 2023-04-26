<?php

class WLD_WC_BI_Acf {
	public static function init(): void {
		/*add_action(
			'woocommerce_after_shop_loop_item_title',
			array( self::class, 'display_excerpt_in_loop' ),
			50
		);*/
		add_action(
			'acf/init',
			array( self::class, 'add_acf_attribute_field' )
		);
	}

	public static function add_acf_attribute_field(): void {
		while ( wld_loop( 'wld_meta_attributes' ) ) {
			$label   = wld_get( 'name' );
			$choices = wld_get( 'options' );
			$name    = sanitize_title( $label );
			$name    = strtolower( $name );

			acf_add_local_field(
				array(
					'label'         => $label,
					'name'          => $name,
					'parent'        => 'group_62beb4c9f0735',
					'type'          => 'checkbox',
					'layout'        => 'vertical',
					'choices'       => acf_decode_choices( $choices ),
					'default_value' => '',
					'allow_custom'  => 0,
					'save_custom'   => 0,
					'toggle'        => 0,
					'return_format' => 'value',
				),
			);

			acf_add_local_field(
				array(
					'label'         => $label,
					'name'          => $name,
					'parent'        => 'group_62beba598cc28',
					'type'          => 'checkbox',
					'layout'        => 'vertical',
					'choices'       => acf_decode_choices( $choices ),
					'default_value' => '',
					'allow_custom'  => 0,
					'save_custom'   => 0,
					'toggle'        => 0,
					'return_format' => 'value',
				),
			);

			acf_add_local_field(
				array(
					'label'         => $label,
					'name'          => $name,
					'parent'        => 'group_62da70c24879a',
					'type'          => 'checkbox',
					'layout'        => 'vertical',
					'choices'       => acf_decode_choices( $choices ),
					'default_value' => '',
					'allow_custom'  => 0,
					'save_custom'   => 0,
					'toggle'        => 0,
					'return_format' => 'value',
				),
			);

			acf_add_local_field(
				array(
					'label'         => $label,
					'name'          => $name,
					'parent'        => 'group_63c7d5f4d1415',
					'type'          => 'checkbox',
					'layout'        => 'vertical',
					'choices'       => acf_decode_choices( $choices ),
					'default_value' => '',
					'allow_custom'  => 0,
					'save_custom'   => 0,
					'toggle'        => 0,
					'return_format' => 'value',
				),
			);
		}
	}
}

