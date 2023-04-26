/* global acf, jQuery */
( function( $ ) {
	'use strict';

	if ( 'undefined' === typeof acf ) {
		return;
	}

	acf.addAction( 'change_field_label', changeFieldLabel );
	acf.addAction( 'change_field_type', changeFieldType );

	$( '[name="post_title"]' ).on(
		'change',
		function() {
			if ( 0 === $( this ).val().toString().indexOf( 'FC:' ) ) {
				$( '[name="acf_field_group[active]"]' )
					.prop( 'checked', false )
					.trigger( 'change' );
			}
		}
	).val( function() {
		const value = $.trim( $( this ).val().toString() );
		if ( '' === value ) {
			setTimeout( () => $( '.add-field' ).trigger( 'click' ), 1000 );

			return 'FC: ';
		}

		return value;
	} );

	/**
	 * @param {jQuery} $field
	 */
	function changeFieldLabel( $field ) {
		var value = $field.find( '[data-name="label"] input[type="text"]' ).val();

		if ( '' === value ) {
			return;
		}

		if ( 'button' === value ) {
			value = 'link';
		} else if ( 'type' === value ) {
			value = 'radio';
		} else if ( 'styles' === value ) {
			value = 'checkbox';
		} else if ( 'map' === value ) {
			value = 'google_map';
		} else if ( 'subtitle' === value || 'pre_title' === value ) {
			value = 'title';
		} else if ( 'phone' === value || 'email' === value || 'fax' === value ) {
			value = 'wld_contact_link';
		} else if ( 'form' === value ) {
			value = 'forms';
		} else if ( 'items' === value ) {
			value = 'repeater';
		} else if ( - 1 !== value.indexOf( 'text' ) ) {
			value = confirm( 'Is WYSIWYG' ) ? 'wysiwyg' : 'textarea';
		} else if ( 'all' === value ) {
			value = 'true_false';
		} else if ( 'posts' === value ) {
			value = 'relationship';
		} else if ( 'blocks' === value || 'sidebar' === value ) {
			value = 'flexible_content';
		} else if ( - 1 !== value.indexOf( 'date' ) ) {
			value = 'date_picker';
		}

		setType( $field.find( '.field-type' ), value );
	}

	/**
	 * @param {jQuery} $field
	 */
	function changeFieldType( $field ) {
		var mediaUpload, newLines, rows, delay,
			value = $field.find( '[data-name="label"] input[type="text"]' ).val(),
			type = $field.find( '.field-type' ).val();

		switch ( type ) {
			case 'image':
			case 'background':
				$field
					.find( '[data-key="preview_size"] select' )
					.val( 'thumbnail' );
				close( $field );
				break;
			case 'link':
			case 'title':
				close( $field );
				break;
			case 'wld_contact_link':
				$field
					.find( '[data-key="link_type"] input[value="' + value + '"]' )
					.prop( 'checked', true );

				close( $field );
				break;
			case 'forms':
				$field
					.find( '[data-key="allow_null"] input[value="1"]' )
					.prop( 'checked', true );

				close( $field );
				break;
			case 'repeater':
				$field
					.find( '[data-key="button_label"] input[type="text"]' )
					.val( 'Add Item' );
				break;
			case 'wysiwyg':
				mediaUpload = false; //confirm( 'Show Media Upload Buttons?' );
				delay = true; //confirm( 'Delay initialization?' );

				$field
					.find( '[data-key="media_upload"] input[type="checkbox"]' )
					.prop( 'checked', mediaUpload )
					.trigger( 'change' );

				$field
					.find( '[data-key="delay"] input[type="checkbox"]' )
					.prop( 'checked', delay )
					.trigger( 'change' );

				close( $field );
				break;
			case 'textarea':
				rows = prompt( 'How many rows?', '4' );
				newLines = confirm( 'Automatically add paragraphs?' ) ? 'wpautop' : 'br';

				$field
					.find( '[data-key="rows"] input[type="number"]' )
					.val( rows );

				$field
					.find( '[data-key="new_lines"] select' )
					.val( newLines ).change();

				close( $field );
				break;
			case 'true_false':
				if ( 'all' === value ) {
					addField( $field, 'Posts', 'relationship' );
					close( $field );
				}
				break;
			case 'relationship':
				$field
					.find( '[data-key="filters"] input[type="checkbox"][value!="search"]' )
					.prop( 'checked', false );

				break;
		}
	}

	/**
	 * @param {jQuery} $typeSelect
	 * @param {string} value
	 */
	function setType( $typeSelect, value ) {
		$typeSelect.find( 'option[value="' + value + '"]' ).prop( 'selected', true );
		$typeSelect.trigger( 'change' );
	}

	/**
	 * @param {jQuery} $field
	 */
	function close( $field ) {
		$field.find( '.acf-field-save .button.edit-field' ).trigger( 'click' );
	}

	/**
	 * @param {jQuery} $field
	 * @param {string} label
	 * @param {string} type
	 */
	function addField( $field, label, type ) {
		var add = function( field ) {
			field.prop( 'label', label );
			field.prop( 'name', acf.strSanitize( label ) );
			field.prop( 'type', type );
			field.onChangeType( null, field.$el );

			acf.removeAction( 'add_field_object', add );
		};

		acf.addAction( 'add_field_object', add );

		$field.closest( '.acf-field-list-wrap' )
			.children( '.acf-tfoot' )
			.find( '.add-field' )
			.trigger( 'click' );
	}
}( jQuery ) );
