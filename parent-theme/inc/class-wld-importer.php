<?php /** @noinspection UnknownInspectionInspection, HtmlUnknownAttribute */


class WLD_Importer {
	public const ID = 'wld_importer';

	public static $current = array();

	protected static $imports = array();

	public static function init() : void {
		add_action( 'admin_init', array( self::class, 'admin_init' ) );
	}

	public static function admin_init() : void {
		if ( self::$imports ) {
			if ( defined( 'WP_LOAD_IMPORTERS' ) ) {
				add_action( 'admin_head', array( self::class, 'style' ) );
				add_action( 'admin_footer', array( self::class, 'script' ) );

				self::register_importer();
			}

			if ( wp_doing_ajax() ) {
				add_action( 'wp_ajax_wld_import_step', array( self::class, 'ajax_step' ) );
				add_action( 'wp_ajax_wld_import_stop', array( self::class, 'ajax_stop' ) );
			}
		}
	}

	protected static function register_importer() : void {
		register_importer(
			self::ID,
			// translators: %s Site Name
			sprintf( __( '%s Imports', 'parent-theme' ), get_bloginfo( 'name' ) ),
			'',
			array( self::class, 'page' )
		);
	}

	public static function add( string $name, callable $callback, int $count = 100, array $fields = array() ) : void {
		self::$imports[ $name ] = array(
			'label'    => ucwords( str_replace( array( '_', '-' ), ' ', $name ) ),
			'callback' => $callback,
			'count'    => $count,
			'fields'   => $fields,
		);
	}

	public static function page() : void {
		// translators: %s Site Name
		$title = sprintf( __( '%s Imports', 'parent-theme' ), get_bloginfo( 'name' ) );

		echo '<div class="wrap wld-import">';
		echo '<h1 class="wp-heading-inline">' . $title . '</h1>';
		echo '<hr class="wp-header-end">';
		echo '<div id="poststuff">';
		do_action( 'wld_importer_before_blocks' );
		foreach ( self::$imports as $name => $import ) {
			self::the_block( $name, $import['label'], $import['fields'], $import['count'] );
		}
		do_action( 'wld_importer_after_blocks' );
		echo '</div>';
		echo '</div>';
	}

	protected static function the_block( string $name, string $label, array $fields, int $count ) : void {
		$transient      = get_transient( self::get_transient_name( $name ) );
		$values         = $transient['fields'] ?? array();
		$log            = $transient['log'] ?? array();
		$remaining_rows = $transient['remaining_rows'] ?? array();
		$full_rows      = $transient['full_rows'] ?? array();
		$count          = absint( $transient['count'] ?? $count );
		$main_info      = $transient['main_info'] ?? '';
		$has_progress   = false !== $transient;
		$progress       = $full_rows ? 100 - round( count( $remaining_rows ) * 100 / count( $full_rows ) ) : 0;
		$file_id        = $name . '-file';
		$progress_id    = $name . '-progress';
		$class          = $has_progress ? 'has-progress' : '';
		$file_enable    = apply_filters( 'wld_importer_file_enable', true, $name );
		$file_required  = apply_filters( 'wld_importer_file_required', $file_enable, $name );
		?>
		<div class="postbox wld-import-block hide-if-no-js <?php echo $class; ?>" data-name="<?php echo $name; ?>">
			<h2 class="hndle" style="cursor: default"><span><?php echo esc_html( $label ); ?></span></h2>
			<div class="inside">
				<form>
					<div class="row stopped">Stopped!</div>
					<div class="row progress">
						<label for="<?php echo $progress_id; ?>">Progress:</label>
						<progress id="<?php echo $progress_id; ?>" max="100" value="<?php echo $progress; ?>">
					</div>
					<?php if ( $file_enable ) : ?>
						<div class="row field field-file">
							<label for="<?php echo $file_id; ?>">
								<?php esc_html_e( 'XLSX or CSV file', 'parent-theme' ); ?>
							</label>
							<input
								name="<?php echo $file_id; ?>"
								id="<?php echo $file_id; ?>"
								type="file"
								class="<?php echo $file_required ? 'required' : ''; ?>"
								accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,.csv">
						</div>
					<?php endif; ?>
					<?php
					foreach ( $fields as $field ) {
						if ( empty( $field['name'] ) ) {
							continue;
						}

						$id          = esc_attr( $name . '-' . $field['name'] );
						$label       = esc_html( $field['label'] ?? '' );
						$description = esc_html( $field['description'] ?? '' );
						$type        = esc_attr( $field['type'] ?? 'text' );
						$placeholder = esc_attr( $field['placeholder'] ?? '' );
						$value       = $values[ $field['name'] ] ?? $field['default_value'] ?? '';
						$required    = $field['required'] ?? false;
						$options     = $field['options'] ?? array();
						$checked     = '';
						$class       = esc_attr( $field['class'] ?? '' );

						if ( empty( $class ) && ( 'text' === $type || 'textarea' === $type ) ) {
							$class = 'large-text';
						}

						if ( 'checkbox' === $type || 'radio' === $type ) {
							$checked = $value ? true : ( $field['checked'] ?? false ? 'checked' : '' );
						}

						if ( $value && 'textarea' === $type ) {
							$value = esc_textarea( $value );
						} elseif ( 'checkbox' === $type ) {
							$value = 'on';
						} else {
							$value = esc_attr( $value );
						}

						if ( $required ) {
							$class .= ' required';
						}

						echo '<div class="row field">';
						printf(
							'<label for="%s">%s</label>',
							$id,
							$label
						);
						if ( 'select' === $type ) {
							$options_html = '';
							foreach ( $options as $option_value => $text ) {
								$option_value = esc_attr( $option_value );
								$options_html = printf(
									'<option value="%s" %s>%s</option>',
									$option_value,
									selected( $option_value, $value, false ),
									esc_html( $text )
								);
							}

							printf(
								'<select name="%s" id="%s" class="%s"><option value="">%s</option>%s</select>',
								$id,
								$id,
								$class,
								$placeholder ?: __( 'Select...', 'parent-theme' ),
								$options_html
							);
						} elseif ( 'textarea' === $type ) {
							$height = absint( $field['height'] ?? 100 );
							printf(
								'<textarea name="%s" id="%s" placeholder="%s" class="%s" style="%s">%s</textarea>',
								$id,
								$id,
								$placeholder,
								$class,
								'height:' . $height . 'px;',
								$value
							);
						} elseif ( 'upload' === $type ) {
							$images          = '';
							$attachments_ids = wp_parse_id_list( $value );
							if ( $attachments_ids ) {
								foreach ( $attachments_ids as $attachment_id ) {
									$images .= sprintf(
										'<span class="thumbnail">%s</span>',
										wp_get_attachment_image( $attachment_id, 'thumbnail', true )
									);
								}
							}

							printf(
								'
								<div class="uploads %s" data-multiple="%s">
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
								$class,
								empty( $field['multiple'] ) ? 'false' : 'true',
								$images,
								$id,
								$images ? 'display:inline-block' : 'display: none;',
								$id,
								$value
							);
						} else {
							printf(
								'<input %s name="%s" id="%s" placeholder="%s" value="%s" class="%s" %s>',
								'type="' . $type . '"',
								$id,
								$id,
								$placeholder,
								$value,
								$class,
								$checked
							);
						}

						if ( $description ) {
							echo '<p class="description">' . $description . '</p>';
						}
						echo '</div>';
					}
					?>
					<div class="row log">
						<?php foreach ( $log as $row ) : ?>
							<?php $type = $row[2] ?? 'info'; ?>
							<div class="log-row log-row-<?php echo sanitize_html_class( $type ); ?>">
								<span class="log-row-date"><?php echo esc_html( $row[0] ?? '' ); ?></span>
								<span class="log-row-text"><?php echo esc_html( $row[1] ?? '' ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
					<div class="row main-info"><?php echo esc_html( $main_info ); ?></div>
					<?php if ( apply_filters( 'wld_importer_show_step_count_field', true, $name ) ) : ?>
						<div class="row count">
							<?php $id = esc_attr( $name . '-count' ); ?>
							<label for="<?php echo $id; ?>">
								<?php esc_html_e( 'Step rows:', 'parent-theme' ); ?>
							</label>
							<input type="number" name="count" class="small-text" min="0"
								   id="<?php echo $id; ?>" value="<?php echo esc_attr( $count ); ?>">
						</div>
					<?php endif; ?>
					<div class="row buttons">
						<button type="submit" class="button button-primary start">
							<?php esc_html_e( 'Start', 'parent-theme' ); ?>
						</button>
						<button type="button" class="button button-default button-disabled pause">
							<?php esc_html_e( 'Pause', 'parent-theme' ); ?>
						</button>
						<button type="button"
								class="button button-default <?php echo $has_progress ? '' : 'button-disabled'; ?> stop">
							<?php esc_html_e( 'Stop', 'parent-theme' ); ?>
						</button>
					</div>
				</form>
			</div>
		</div>
		<?php
	}

	public static function get_transient_name( string $name ) : string {
		return 'wld_importer_' . $name;
	}

	public static function style() : void {
		?>
		<!--suppress CssUnusedSymbol -->
		<style>
			.wld-import * {
				box-sizing: border-box;
			}

			.wld-import .row:not(:first-child) {
				margin-top: 1em;
			}

			.wld-import .field-file [type="file"] {
				width: 100%;
			}

			.wld-import .postbox.in-progress .field,
			.wld-import .postbox.has-progress .field,
			.wld-import .postbox.in-progress .count,
			.wld-import .postbox.has-progress .count,
			.wld-import .postbox .stopped,
			.wld-import .postbox .progress {
				display: none !important;
			}

			.wld-import .postbox.in-progress .progress {
				display: block !important;
			}

			.wld-import .postbox.has-progress .stopped {
				display: block !important;
				color: red;
				font-size: 1.2em;
				font-weight: bold;
			}

			.wld-import .field:after {
				content: '';
				display: block;
				border-bottom: .1em #ccc solid;
				margin-top: 1em;
			}

			.wld-import .field .description {
				margin-bottom: -0.5em;
			}

			.wld-import .field .thumbnail {
				position: relative;
				display: inline-flex;
				width: 150px;
				height: 150px;
				box-shadow: inset 0 0 15px rgba(0, 0, 0, 0.1), inset 0 0 0 1px rgba(0, 0, 0, 0.05);
				background: #eee;
				margin: 0 .3em .3em 0;
				vertical-align: top;
			}

			.wld-import .field .thumbnail:after {
				content: "";
				display: block;
				position: absolute;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.1);
				overflow: hidden;
			}

			.wld-import .field .thumbnail img {
				display: block;
				margin: auto;
				max-width: 100%;
				height: auto;
			}

			.wld-import .field label {
				display: inline-block;
				padding: 0 .2em;
				vertical-align: top;
			}

			.wld-import .invalid-field label {
				color: red;
			}

			.wld-import .invalid-field input,
			.wld-import .invalid-field textarea,
			.wld-import .invalid-field select {
				border-color: red;
			}

			.wld-import .progress {
				display: flex;
				flex-wrap: nowrap;
				justify-content: space-between;
				align-items: center;
			}

			.wld-import .progress progress {
				transition: all 1s ease;
				width: calc(100% - 5em);
			}

			.wld-import .in-progress .main-info {
				font-weight: 600;
			}

			.wld-import .log-row {
				display: flex;
			}

			.wld-import .log-row-date {
				display: inline-block;
				width: 6em;
				flex-shrink: 0;
			}

			.wld-import .log-row-text {
				display: inline-block;
			}

			.wld-import .log-row-success {
				color: green;
			}

			.wld-import .log-row-warning {
				color: orangered;
			}

			.wld-import .log-row-error {
				color: red;
			}

			.wld-import .in-progress .log-row-init:after,
			.wld-import .in-progress .main-info:not(:empty):after {
				content: '.';
				animation: loading 1s ease alternate infinite;
			}

			@keyframes loading {
				60% {
					text-shadow: 0.35em 0 0 currentColor;
				}
				100% {
					text-shadow: 0.35em 0 0 currentColor, 0.75em 0 0 currentColor;
				}
			}

			@media all and ( min-width: 376px ) {
				.wld-import .log-row-date {
					width: 10em;
				}

				.wld-import .row.count {
					float: right;
					margin-top: 0;
				}
			}

			@media all and ( min-width: 768px ) {
				.wld-import .field label {
					width: 10em;
				}

				.wld-import .field .uploads,
				.wld-import .field input,
				.wld-import .field textarea,
				.wld-import .field select {
					max-width: calc(100% - 10em);
				}

				.wld-import .field .uploads {
					display: inline-block;
				}

				.wld-import .field .description {
					padding-left: 10em;
				}
			}
		</style>
		<?php
	}

	public static function script() : void {
		?>
		<!--suppress HtmlRequiredAltAttribute, HtmlRequiredAltAttribute, RequiredAttributes, JSCheckFunctionSignatures, JSUnresolvedVariable, JSUnresolvedFunction -->
		<script>
			( function( $ ) {
				$.fn.wldImport = function() {
					if ( this && this.length ) {
						this.each( function() {
							const
								$block = $( this ),
								$form = $( 'form', this ),
								$start = $( 'button.start', this ),
								$pause = $( 'button.pause', this ),
								$stop = $( 'button.stop', this ),
								$log = $( '.log', this ),
								$info = $( '.main-info', this ),
								$progress = $( '.progress progress', this ),
								$selectUploads = $( '.select-uploads', this ),
								$clearUploads = $( '.clear-uploads', this );

							let jqXHR = null;

							$form.on( 'submit', function( e ) {
								e.preventDefault();

								if ( ! $start.hasClass( 'button-disabled' ) ) {
									const selectors = [
										'input.required:visible',
										'select.required:visible',
										'textarea.required:visible',
										'.required:visible input',
									].join( ',' );

									const $required = $( selectors, this );

									let valid = true;
									$required.each( function() {
										if ( ! $.trim( $( this ).val() ) ) {
											valid = false;
											$( this ).closest( '.field' ).addClass( 'invalid-field' );
										}
									} );

									if ( valid ) {
										$log.html( '<div class="log-row log-row-init">Processing</div>' );
										$info.empty();
										step();
										$( 'html, body' ).animate( {
											scrollTop: $( '.log-row-init', this ).offset().top - 200
										}, 500 );
									} else {
										$( 'html, body' ).animate( {
											scrollTop: $( '.invalid-field', this ).offset().top - 200
										}, 500 );
									}
								}
							} );

							$form.on( 'change', '.invalid-field', function() {
								const $input = $( ':input:not(button)', this );
								if ( $.trim( $input.val().toString() ) ) {
									$( this ).removeClass( 'invalid-field' );
								}
							} );

							$pause.on( 'click', function() {
								if ( ! $pause.hasClass( 'button-disabled' ) ) {
									pause();
								}
							} );

							$stop.on( 'click', function() {
								if ( ! $stop.hasClass( 'button-disabled' ) ) {
									stop();
								}
							} );

							$selectUploads.on( 'click', function() {
								const
									$wrap = $( this ).parent(),
									multiple = $wrap.attr( 'data-multiple' ) === 'true',
									uploader = wp.media( { multiple: multiple } );

								uploader.on( 'select', function() {
									const values = [],
										$thumbnails = [];

									if ( multiple ) {
										uploader.state().get( 'selection' ).each( function( attachment ) {
											values.push( attachment.get( 'id' ) );
											$thumbnails.push( getThumbnail( attachment ) );
										} );
									} else {
										const attachment = uploader.state().get( 'selection' ).first();

										values.push( attachment.get( 'id' ) );
										$thumbnails.push( getThumbnail( attachment ) );
									}

									$wrap.find( '.thumbnail' ).remove().end().prepend( $thumbnails );
									$wrap.find( 'input' ).val( values.join( ',' ) ).trigger( 'change' );
									$wrap.find( '.clear-uploads' ).toggle( $thumbnails.length > 0 );
								} ).open();
							} );

							$clearUploads.on( 'click', function() {
								$( this ).hide();
								$( this ).parent().find( '.thumbnail' ).remove();
								$( this ).parent().find( 'input' ).val( '' );
							} );

							function step() {
								if ( jqXHR ) {
									jqXHR.abort();
									jqXHR = null;
								}

								const data = new FormData( $form.get( 0 ) );

								data.append( 'action', 'wld_import_step' );
								data.append( '_ajax_nonce', '<?php echo wp_create_nonce( 'wld_importer' ); ?>' );
								data.append( 'name', $block.attr( 'data-name' ) );

								jqXHR = $.ajax( {
									url: ajaxurl,
									data: data,
									type: 'post',
									dataType: 'json',
									contentType: false,
									processData: false
								} );

								jqXHR.done( function( response ) {
									$block.trigger( 'wld_import_response', response );

									const
										success = response.success || false,
										data = response.data || {};

									update( data );
									if ( success ) {
										if ( data.progress > - 1 ) {
											step();
										} else {
											stop();
										}
									} else {
										alert( 'Error' );
										stop();
									}
								} );

								jqXHR.fail( function( response ) {
									if ( 504 === response.status ) {
										step();
									} else if ( 0 !== response.status ) {
										alert( 'Error' );
										stop();
									}
								} );

								setInProgress();
							}

							function pause() {
								if ( jqXHR ) {
									jqXHR.abort();
									jqXHR = null;
								}

								setHasProgress();
							}

							function stop() {
								if ( jqXHR ) {
									jqXHR.abort();
									jqXHR = null;
								}

								$.ajax( {
									url: ajaxurl,
									data: {
										action: 'wld_import_stop',
										name: $block.attr( 'data-name' )
									},
									type: 'post',
									dataType: 'json',
								} );

								setNotProgress();
							}

							function setHasProgress() {
								$block.removeClass( 'in-progress' );
								$block.addClass( 'has-progress' );

								$start.removeClass( 'button-disabled' );
								$pause.addClass( 'button-disabled' );
								$stop.removeClass( 'button-disabled' );
							}

							function setInProgress() {
								$block.addClass( 'in-progress' );
								$block.removeClass( 'has-progress' );

								$start.addClass( 'button-disabled' );
								$pause.removeClass( 'button-disabled' );
								$stop.removeClass( 'button-disabled' );
							}

							function setNotProgress() {
								$block.removeClass( 'in-progress' );
								$block.removeClass( 'has-progress' );

								$start.removeClass( 'button-disabled' );
								$pause.addClass( 'button-disabled' );
								$stop.addClass( 'button-disabled' );

								$progress.val( 0 );

								$( '.log-row-init' ).remove();
							}

							function update( data ) {
								if ( data.log ) {
									const $logs = [];
									$.each( data.log, function( i, row ) {
										const
											$date = $( '<span>', { class: 'log-row-date', text: row[0] } ),
											$text = $( '<span>', { class: 'log-row-text', text: row[1] } ),
											$row = $( '<div>', { class: 'log-row log-row-' + ( row[2] || 'info' ) } );

										$logs.push( $row.append( $date ).append( $text ) );
									} );

									$log.empty().append( $logs );
								}

								$info.text( data.main_info || '' );

								if ( data.progress ) {
									$progress.val( data.progress );
								}
							}

							function getThumbnail( attachment ) {
								const type = attachment.get( 'type' );

								let src;

								if ( 'image' === type ) {
									const sizes = attachment.get( 'sizes' );
									if ( sizes && sizes['thumbnail'] ) {
										src = sizes['thumbnail']['url'];
									} else {
										src = attachment.get( 'url' );
									}
								} else {
									src = attachment.get( 'icon' );
								}

								const $img = $( '<img>', {
									src: src,
									alt: attachment.get( 'alt' )
								} );

								return $( '<span>', { class: 'thumbnail', append: $img } );
							}
						} );
					}

					return this;
				};

				$( '.wld-import-block' ).wldImport();
			} )( jQuery );
		</script>
		<?php
	}

	public static function ajax_stop() : void {
		$name = sanitize_text_field( $_POST['name'] ?? '' ); // phpcs:ignore WordPress.Security.NonceVerification
		if ( ! isset( self::$imports[ $name ] ) ) {
			wp_die( 'No Import' );
		}

		delete_transient( self::get_transient_name( $name ) );

		wp_send_json_success();
	}

	public static function ajax_step() : void {
		check_ajax_referer( 'wld_importer' );

		ignore_user_abort( false );

		$name = sanitize_text_field( $_POST['name'] ?? '' );
		if ( ! isset( self::$imports[ $name ] ) ) {
			wp_die( 'No Import' );
		}

		$import        = self::$imports[ $name ];
		self::$current = get_transient( self::get_transient_name( $name ) );

		if ( empty( self::$current ) ) {
			$file_name     = $name . '-file';
			$file          = ! empty( $_FILES[ $file_name ]['size'] ) ? $_FILES[ $file_name ] : array();
			$file_enable   = apply_filters( 'wld_importer_file_enable', true, $name );
			$file_required = apply_filters( 'wld_importer_file_required', $file_enable, $name );
			$fields        = array();

			if ( $file_required && empty( $file ) ) {
				wp_die( 'No File' );
			}

			$rows = array();
			if ( $file ) {
				$rows = WLD_XLSX_CSV::to_array( $file['tmp_name'], $file['name'] );

				unlink( $file['tmp_name'] );

				if ( is_wp_error( $rows ) ) {
					wp_die( 'Invalid Ext' );
				}

				if ( $rows ) {
					array_shift( $rows );
				}
			}

			foreach ( $import['fields'] as $field ) {
				if ( empty( $field['name'] ) ) {
					continue;
				}

				$post_key = $name . '-' . $field['name'];
				$type     = esc_attr( $field['type'] ?? 'text' );
				$value    = wp_kses( $_POST[ $post_key ] ?? '', 'post' );

				if ( 'checkbox' === $type ) {
					$value = $value ? 'on' : 'off';
				}

				$fields[ $field['name'] ] = $value;
			}

			$rows          = apply_filters( 'wld_importer_get_rows', $rows, $name );
			self::$current = array(
				'fields'         => $fields,
				'log'            => array(),
				'remaining_rows' => $rows,
				'full_rows'      => $rows,
				'count'          => absint( $_POST['count'] ?? $import['count'] ),
			);

			set_transient( self::get_transient_name( $name ), self::$current, DAY_IN_SECONDS );

			self::$current['main_info'] = sprintf( // translators: %d all rows
				esc_html__( 'Processed 0 rows of %d', 'parent-theme' ),
				count( $rows )
			);

			wp_send_json_success(
				array(
					'log'       => self::$current['log'],
					'main_info' => self::$current['main_info'],
					'progress'  => 0,
				)
			);
		}

		$count = absint( self::$current['count'] ?? 0 );
		if ( $count ) {
			$current_rows         = array_slice( self::$current['remaining_rows'], 0, $count );
			$after_remaining_rows = array_slice( self::$current['remaining_rows'], $count );
		} else {
			$current_rows         = self::$current['remaining_rows'];
			$after_remaining_rows = array();
		}

		ob_flush();
		flush();
		if ( connection_aborted() ) {
			return;
		}

		/** @noinspection PhpUnhandledExceptionInspection */
		$callback_info = new ReflectionFunction( $import['callback'] );
		$args          = array_slice(
			array(
				$current_rows,
				$after_remaining_rows,
			),
			0,
			$callback_info->getNumberOfParameters()
		);

		call_user_func_array( $import['callback'], $args );

		if ( $after_remaining_rows ) {
			$full                            = count( self::$current['full_rows'] );
			$remaining                       = count( $after_remaining_rows );
			$done                            = $full - $remaining;
			self::$current['remaining_rows'] = $after_remaining_rows;
			self::$current['main_info']      = sprintf( // translators: %1$d - done rows, %2$d all rows
				esc_html__( 'Processed %1$d rows of %2$d', 'parent-theme' ),
				$done,
				$full
			);
			set_transient( self::get_transient_name( $name ), self::$current, DAY_IN_SECONDS );
			$progress = 100 - round( $remaining * 100 / $full );
		} else {
			self::log( 'Complete!' );
			self::$current['main_info'] = '';
			delete_transient( self::get_transient_name( $name ) );
			$progress = - 1;
		}

		wp_send_json_success(
			array(
				'log'       => self::$current['log'],
				'main_info' => self::$current['main_info'],
				'progress'  => $progress,
			)
		);
	}

	public static function log( string $message, string $type = 'info', $id = null ) : void {
		if ( self::$current ) {
			if ( 'progress' === $type ) {
				$type = 'init';
			}

			$log_item = array(
				current_time( 'mysql' ),
				$message,
				$type, // info, success, warning, error, progress, init
			);

			if ( null !== $id ) {
				self::$current['log'][ $id ] = $log_item;
			} else {
				self::$current['log'][] = $log_item;
			}
		}
	}
}
