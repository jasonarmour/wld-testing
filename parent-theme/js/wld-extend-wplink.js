/* global wpLink, acf, jQuery */
jQuery(
	function( $ ) {
		'use strict';

		var $inputClass = $( '#wp-link-class' ),
			$inputBtn = $( '#wp-link-btn' );

		if ( 0 === $inputClass.length ) {
			return;
		}

		// WordPress
		if ( 'undefined' !== typeof wpLink ) {
			extendWpLink();
		}

		// ACF
		if ( 'undefined' !== typeof acf && 'undefined' !== typeof acf.wpLink ) {
			extendAcfLink();
		}

		function extendWpLink() {
			var _wpLink = $.extend( {}, wpLink );

			wpLink.getAttrs = function() {
				var attrs = _wpLink.getAttrs();

				attrs.class = ( ' ' + $.trim( $inputClass.val().toString() ) + ' ' ).replace( / btn /g, ' ' );
				if ( $inputBtn.is( ':checked' ) ) {
					attrs.class += 'btn';
				}

				attrs.class = $.trim( attrs.class );

				return attrs;
			};

			wpLink.buildHtml = function() {
				var attrs = wpLink.getAttrs(),
					html = _wpLink.buildHtml( attrs );

				if ( attrs.class ) {
					html = html.replace( /<a/g, '<a class="' + attrs.class + '"' );
				}

				return html;
			};

			wpLink.mceRefresh = function( searchStr, text ) {
				var linkNode, classStr,
					editor = window.tinymce.get( window.wpActiveEditor );

				_wpLink.mceRefresh( searchStr, text );

				if ( editor ) {
					linkNode = editor.dom.getParent( editor.selection.getNode(), 'a[href]' );

					if ( linkNode ) {
						classStr = ' ' + editor.dom.getAttrib( linkNode, 'class', '' ) + ' ';

						if ( - 1 !== classStr.indexOf( ' btn ' ) ) {
							$inputBtn.prop( 'checked', true );
						} else {
							$inputBtn.prop( 'checked', false );
						}

						$inputClass.val( $.trim( classStr.replace( / btn /g, ' ' ) ) );
					}
				}
			};

			wpLink.setDefaultValues = function( selection ) {
				_wpLink.setDefaultValues( selection );

				$inputClass.val( '' );
				$inputBtn.prop( 'checked', false );
			};

			wpLink.close = function( reset ) {
				_wpLink.close( reset );

				if ( 'noReset' !== reset ) {
					if ( wpLink.isMCE() ) {
						$inputClass.val( '' );
						$inputBtn.prop( 'checked', false );
					}
				}
			};

			// Change Update Event
			$( '#wp-link-submit' ).off( 'click' ).on(
				'click',
				function( e ) {
					e.preventDefault();
					wpLink.update();
				}
			);

			$( '#wp-link .query-results' ).css( 'top', '228px' );
		}

		function extendAcfLink() {
			window.acfLinkOpen = '';

			$.each(
				acf.getFieldTypes(),
				function( i, v ) {
					if ( 'link' === v.prototype.type ) {
						v.prototype.getValue = function() {
							var $node = this.$node();

							if ( ! $node.attr( 'href' ) ) {
								return false;
							}

							return {
								title: $node.html(),
								url: $node.attr( 'href' ),
								target: $node.attr( 'target' ),
								className: $node.attr( 'data-class' )
							};
						};

						v.prototype.setValue = function( val ) {
							var $div = this.$control(),
								$node = this.$node();

							val = acf.parseArgs(
								val,
								{
									title: '',
									url: '',
									target: '',
									className: ''
								}
							);

							$div.removeClass( '-value -external' );

							if ( val.url ) {
								$div.addClass( '-value' );
							}

							if ( '_blank' === val.target ) {
								$div.addClass( '-external' );
							}

							// update text
							this.$( '.link-title' ).html( val.title );
							this.$( '.link-url' ).attr( 'href', val.url ).html( val.url );

							// update node
							$node.html( val.title );
							$node.attr( 'href', val.url );
							$node.attr( 'target', val.target );
							$node.attr( 'data-class', val.className );

							// update inputs
							this.$( '.input-title' ).val( val.title );
							this.$( '.input-target' ).val( val.target );
							this.$( '.input-url' ).val( val.url ).trigger( 'change' );
							this.$( '.input-class' ).val( val.className );
						};
					}
				}
			);

			acf.wpLink.getNodeValue = function() {
				var $node = this.get( 'node' );
				return {
					title: $node.html(),
					url: $node.attr( 'href' ),
					target: $node.attr( 'target' ),
					className: $node.attr( 'data-class' )
				};
			};

			acf.wpLink.setNodeValue = function( val ) {
				var $node = this.get( 'node' );
				$node.html( val.title );
				$node.attr( 'href', val.url );
				$node.attr( 'target', val.target );
				$node.attr( 'data-class', val.className );
				$node.trigger( 'change' );
			};

			acf.wpLink.getInputValue = function() {
				return {
					title: $( '#wp-link-text' ).val(),
					url: $( '#wp-link-url' ).val(),
					target: $( '#wp-link-target' ).prop( 'checked' ) ? '_blank' : '',
					className: $inputClass.val()
				};
			};

			acf.wpLink.setInputValue = function( val ) {
				$( '#wp-link-text' ).val( val.title );
				$( '#wp-link-url' ).val( val.url );
				$( '#wp-link-target' ).prop( 'checked', '_blank' === val.target );
				$inputClass.val( val.className );
			};

			acf.wpLink.showBtn = function() {
				$inputBtn.parent().show();
			};

			acf.wpLink.hideBtn = function() {
				if ( $( '#acf-link-textarea' ).length ) {
					$inputBtn.parent().hide();
				}
			};

			$( document ).on( 'wplink-open', acf.wpLink.hideBtn );
			$( document ).on( 'wplink-close', acf.wpLink.showBtn );
		}
	}
);
