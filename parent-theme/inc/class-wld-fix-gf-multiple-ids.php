<?php

class WLD_Fix_GF_Multiple_IDs {
	protected static array $ids = array();

	public static function init() : void {
		add_filter(
			'gform_get_form_filter',
			array( static::class, 'fix' ),
			PHP_INT_MAX - 1,
			2
		);
		add_filter(
			'gform_confirmation',
			array( static::class, 'fix' ),
			PHP_INT_MAX - 1,
			2
		);
		add_filter(
			'gform_footer_init_scripts_filter',
			array( static::class, 'fix' ),
			PHP_INT_MAX - 1,
			2
		);

		add_action(
			'init',
			static function () {
				remove_action( 'wp_enqueue_scripts', array( 'GFForms', 'enqueue_scripts' ), 11 );
			}
		);
	}

	public static function fix( $form_html_or_confirmation_or_scripts, $form ) {
		$id           = (int) $form['id'];
		$hidden_field = "<input type='hidden' name='gform_field_values'";

		if ( doing_action( 'gform_get_form_filter' ) || doing_action( 'gform_confirmation' ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$post_id            = isset( $_POST['gform_random_id'] ) ? (int) $_POST['gform_random_id'] : 0;
			$random_id          = $post_id ?: wp_rand();
			static::$ids[ $id ] = $random_id;
		} else {
			$random_id = static::$ids[ $id ] ?? 0;
		}

		// phpcs:disable WordPress.Arrays.MultipleStatementAlignment
		$search_replace = array(
			"'#gform_target_page_number_$id'"   => "'#gform_target_page_number_$random_id'",
			"'#gform_source_page_number_$id'"   => "'#gform_source_page_number_$random_id'",
			"'#gform_confirmation_wrapper_$id'" => "'#gform_confirmation_wrapper_$random_id'",
			"'#gform_confirmation_message_$id'" => "'#gform_confirmation_message_$random_id'",
			"#gf_$id'"                          => "#gf_$random_id'",
			"'#gform_ajax_frame_$id'"           => "'#gform_ajax_frame_$random_id'",
			"'#gform_wrapper_$id'"              => "'#gform_wrapper_$random_id'",
			"\"#gform_$id\""                    => "\"#gform_$random_id\"",
			"'#gform_$id'"                      => "'gform_$random_id'",

			'choice_' . $id . '_'                     => 'choice_' . $random_id . '_',
			'input_' . $id . '_'                      => 'input_' . $random_id . '_',
			'label_' . $id . '_'                      => 'label_' . $random_id . '_',
			'field_' . $id . '_'                      => 'field_' . $random_id . '_',
			'ginput_base_price_' . $id . '_'          => 'ginput_base_price_' . $random_id . '_',
			'ginput_quantity_' . $id . '_'            => 'ginput_quantity_' . $random_id . '_',
			'gforms_calendar_icon_input_' . $id . '_' => 'gforms_calendar_icon_input_' . $random_id . '_',
			'gfield_price_' . $id . '_'               => 'gfield_price_' . $random_id . '_',
			'gfield_quantity_' . $id . '_'            => 'gfield_quantity_' . $random_id . '_',
			'gfield_product_' . $id . '_'             => 'gfield_product_' . $random_id . '_',
			'gform_next_button_' . $id . '_'          => 'gform_next_button_' . $random_id . '_',
			'gform_previous_button_' . $id . '_'      => 'gform_previous_button_' . $random_id . '_',
			'gfield_description_' . $id . '_'         => 'gfield_description_' . $random_id . '_',

			"'gform_target_page_number_$id'"    => "'gform_target_page_number_$random_id'",
			"'gform_source_page_number_$id'"    => "'gform_source_page_number_$random_id'",
			"'gform_confirmation_wrapper_$id'"  => "'gform_confirmation_wrapper_$random_id'",
			"'gforms_confirmation_message_$id'" => "'gforms_confirmation_message_$random_id'",
			"'gf_$id'"                          => "'gf_$random_id'",
			"'gform_ajax_frame_$id'"            => "'gform_ajax_frame_$random_id'",
			"'gform_wrapper_$id'"               => "'gform_wrapper_$random_id'",
			"'gform_$id'"                       => "'gform_$random_id'",
			"'ginput_total_$id'"                => "'ginput_total_$random_id'",
			"'gform_fields_$id'"                => "'gform_fields_$random_id'",
			"'gform_submit_button_$id'"         => "'gform_submit_button_$random_id'",
			"\"gf_submitting_$id\""             => "\"gf_submitting_$random_id\"",
			"'gf_submitting_$id'"               => "'gf_submitting_$random_id'",

			"trigger('gform_post_render', [$id,"            => "trigger('gform_post_render', [$random_id,",
			"trigger('gform_page_loaded', [$id,"            => "trigger('gform_page_loaded', [$random_id,",
			"trigger('gform_post_conditional_logic', [$id," => "trigger('gform_post_conditional_logic', [$random_id,",
			"gformInitSpinner( $id, "                       => "gformInitSpinner( $random_id, ",
			"gf_apply_rules($id, "                          => "gf_apply_rules($random_id, ",
			'gformShowPasswordStrength("input_' . $id . '_' => 'gformShowPasswordStrength("input_' . $random_id . '_',
			"gformInitChosenFields('#input_{$id}_"          => "gformInitChosenFields('#input_{$random_id}_",
			"if(formId == $id)"                             => "if(formId == $random_id)",
			"window['gf_form_conditional_logic'][$id]"      => "window['gf_form_conditional_logic'][$random_id]",
			"'gform_confirmation_loaded', [$id]"            => "'gform_confirmation_loaded', [$random_id]",
			"jQuery('#input_{$id}_"                         => "jQuery('#input_{$random_id}_",
			"GFCalc($id, "                                  => "GFCalc($random_id, ",
			'gf_global["number_formats"][' . $id . ']'      => 'gf_global["number_formats"][' . $random_id . ']',
			"wld_invisible_reload( $id )"                   => "wld_invisible_reload( $random_id )",
			'"formId":' . $id                               => '"formId":' . $random_id,

			$hidden_field => "<input type='hidden' name='gform_random_id' value='$random_id'>" . $hidden_field,
		);

		// phpcs:enable WordPress.Arrays.MultipleStatementAlignment

		return str_replace(
			array_keys( $search_replace ),
			array_values( $search_replace ),
			$form_html_or_confirmation_or_scripts
		);
	}
}
