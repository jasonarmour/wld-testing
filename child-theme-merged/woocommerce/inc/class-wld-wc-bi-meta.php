<?php

use Automattic\WooCommerce\Utilities\OrderUtil;

class WLD_WC_BI_Meta {
	public static function init(): void {
		add_action(
			'woocommerce_product_options_general_product_data',
			array( self::class, 'woo_add_custom_general_fields' )
		);

		add_action(
			'woocommerce_process_product_meta',
			array( self::class, 'woo_save_custom_general_fields' ),
			30,
			1
		);

		add_action(
			'woocommerce_product_meta_start',
			array( self::class, 'woo_display_custom_general_fields_values' ),
			50
		);

		add_action(
			'admin_enqueue_scripts',
			array( self::class, 'bi_admin_enqueue_scripts_action' )
		);

	}

	public static function bi_admin_enqueue_scripts_action() {
		global $post;
		if ( $post && 'product' === $post->post_type ) {
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_add_inline_script( 'jquery-ui-datepicker', /** @lang js */ '
				jQuery( function($) {
					console.log(123);
        			$( "#_date_published_display" ).datepicker({dateFormat: "m/d/yy", altFormat: "yy-mm-dd", altField: "#_date_published"});
        			$(document).ready(function() {
        			    if($("_donwloadable").prop("checked")) {
        			       $("#_link_view_event").fadeOut();
        			    } else {
        			        $("#_link_view_event").fadeIn();
        			    }
        			});
        			$("_donwloadable").on("change",function() {
        			    if($(this).prop("checked")) {
        			       $("#_link_view_event").fadeOut();
        			    } else {
        			        $("#_link_view_event").fadeIn();
        			    }
        			});
        		} );
			' );
		}
	}

	public static function woo_add_custom_general_fields() {

		global $post;

		$meta_options = get_field( 'wld_meta_attributes', 'option' );

		wp_nonce_field( 'theberylinstitute', '_prodattr' );

		foreach ( $meta_options as $meta ) :

			$opt = $meta['options'];

			$opt_str = explode( PHP_EOL, $opt );

			$opt_array = array();

			foreach ( $opt_str as $str ) :
				$column            = explode( ' : ', $str );
				$key               = $column[0];
				$value             = $column[1];
				$opt_array[ $key ] = $value;
			endforeach;

			$opt_array = array( '' => 'Select a value' ) + $opt_array;

			echo '<div class="options_group">';

			woocommerce_wp_select(
				array(
					'id'          => '_' . $meta['name'],
					'label'       => $meta['name'],
					'description' => $meta['description'],
					'desc_tip'    => true,
					'options'     => $opt_array,
				)
			);

			echo '</div>';
		endforeach;

		$data_val = '';

		if ( date_create_from_format( 'Y-m-d', OrderUtil::get_post_or_object_meta( $post, null, '_date_published', true ) ) ) {
			$data_val = date_format( date_create_from_format( 'Y-m-d', OrderUtil::get_post_or_object_meta( $post, null, '_date_published', true ) ), 'm/d/Y' );
		}

		woocommerce_wp_text_input( array(
			'id'    => '_date_published_display',
			'label' => 'Date of event',
			'value' => $data_val,
			'name'  => '',

		) );
		woocommerce_wp_hidden_input( array(
			'id'    => '_date_published',
			'label' => 'Date of event',
		) );

		woocommerce_wp_text_input( array(
			'id'    => '_link_view_event',
			'label' => 'Link for view',
		) );
	}

	public static function woo_save_custom_general_fields( $post_id ) {

		if ( ! isset( $_POST['_prodattr'] ) || ! wp_verify_nonce( $_POST['_prodattr'], 'theberylinstitute' ) ) {
			return $post_id;
		}

		$meta_options = get_field( 'wld_meta_attributes', 'option' );

		foreach ( $meta_options as $meta ) :

			$posted_field_value = $_POST[ '_' . $meta['name'] ];

			if ( ! empty( $posted_field_value ) ) {
				update_post_meta( $post_id, '_' . $meta['name'], esc_attr( $posted_field_value ) );
			} else {
				delete_post_meta( $post_id, '_' . $meta['name'] );
			}

		endforeach;
		update_post_meta( $post_id, '_date_published', sanitize_text_field( $_POST['_date_published'] ) );
		update_post_meta( $post_id, '_link_view_event', sanitize_text_field( $_POST['_link_view_event'] ) );
	}

	public static function woo_display_custom_general_fields_values() {
		global $product;

		$product_id = method_exists( $product, 'get_id' ) ? $product->get_id() : $product->ID;
		echo '<div class="topic"><strong>Topic</strong>: <a href=/topic?topic=' . get_post_meta( $product_id, '_Topic', true ) . '>' . self::display( get_post_meta( $product_id, '_Topic', true ) ) . '</a></div>';
		echo '<div class="topic"><strong>Type</strong>: <a href=/type?type=' . get_post_meta( $product_id, '_Type', true ) . '>' . self::display( get_post_meta( $product_id, '_Type', true ) ) . '</a></div>';
		echo '<div class="topic"><strong>Presentation</strong>: <a href=/presentation?presentation=' . get_post_meta( $product_id, '_Presentation', true ) . '>' . self::display( get_post_meta( $product_id, '_Presentation', true ) ) . '</a></div>';
	}

	public static function display( $value ): string {
		return ucwords( str_replace( '-', ' ', $value ) );
	}

}
