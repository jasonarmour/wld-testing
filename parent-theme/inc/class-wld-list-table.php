<?php /** @noinspection SqlNoDataSourceInspection, SqlDialectInspection, PhpIncludeInspection, PhpUnused, UnknownInspectionInspection */

require_once ABSPATH . '/wp-admin/includes/class-wp-list-table.php';

class WLD_List_Table extends WP_List_Table {
	public const TITLE      = '';
	public const TABLE      = '';
	public const SLUG       = '';
	public const ID_COLUMN  = 'id';
	public const ICON       = 'dashicons-admin-post';
	public const POSITION   = 21;
	public const FIELDS     = array();
	public const OPTIONS    = array(
		'per_page' => array(
			'default' => 50,
		),
	);
	public const REL_TABLES = array();

	/** @var static $table */
	public static $table;
	public static $screen_id    = '';
	public static $page_title   = '';
	public static $page_content = '';
	public static $single_id    = 0;
	public static $single_data  = array();

	public static function init(): void {
		static::$screen_id = 'toplevel_page_' . static::get_slug();

		if ( is_admin() ) {
			add_action( 'admin_menu', array( static::class, 'admin_menu' ) );
			add_action( 'load-' . static::$screen_id, array( static::class, 'load' ) );
			add_filter( 'set-screen-option', array( static::class, 'save_screen_option' ), 10, 3 );
		}
	}

	public static function get_slug(): string {
		return static::SLUG ?: sanitize_title( static::TITLE );
	}

	public static function save_screen_option( bool $keep, string $option, string $value ) {
		$option = str_replace( static::$screen_id . '_', '', $option );
		if ( array_key_exists( $option, static::OPTIONS ) ) {
			return $value;
		}

		return $keep;
	}

	public static function load(): void {
		$view = sanitize_title( $_GET['view'] ?? 'table' );

		if ( 'table' === $view ) {
			foreach ( static::OPTIONS as $option => $args ) {
				$args['option'] = static::$screen_id . '_' . $option;
				add_screen_option( $option, $args );
			}

			static::$table = new static();
			static::process_bulk_action();
			static::$table->prepare_items();
		} else {
			static::process_action( $view );
			static::set_single_data( $view );
		}

		static::set_page_title( $view );
		static::set_page_content( $view );
	}

	public static function process_bulk_action(): void {
		$action = static::$table->current_action();
		if ( $action ) {
			check_admin_referer( 'bulk-' . static::$table->_args['plural'] );

			if ( 'delete' === $action ) {
				static::process_bulk_action_delete();
			}

			wp_safe_redirect( $_GET['_wp_http_referer'] );
			exit;
		}
	}

	public static function process_bulk_action_delete(): void {
		$ids = array_map( 'absint', $_GET['ids'] ?? array() );

		if ( $ids ) {
			global $wpdb;

			$wpdb->hide_errors();
			$wpdb->table = static::TABLE;
			$ids         = implode( ', ', $ids );
			$column      = static::ID_COLUMN;
			$query       = "DELETE FROM $wpdb->table WHERE `$column` IN ( $ids )";

			$wpdb->query( $query ); // phpcs:ignore
		}
	}

	public function prepare_items(): void {
		global $wpdb;

		$wpdb->hide_errors();
		$wpdb->table = static::TABLE;

		$where    = '1=1';
		$order    = '';
		$limit    = '';
		$offset   = '';
		$orderby  = $this->get_orderby();
		$per_page = $this->get_items_per_page( static::$screen_id . '_per_page', 50 );
		$page     = $this->get_pagenum();

		if ( $orderby ) {
			$order .= ' ORDER BY ' . $orderby . ' ' . $this->get_order();
		}

		if ( $per_page ) {
			$limit .= ' LIMIT ' . $per_page;
		}

		if ( $page > 1 ) {
			$offset .= ' OFFSET ' . ( $page - 1 ) * $per_page;
		}

		foreach ( static::FIELDS as $key => $field ) {
			if ( ! empty( $field['filter'] ) ) {
				$value = sanitize_text_field( $_GET[ $key ] ?? '' );
				if ( '' !== $value ) {
					if ( '&#8212;' === $value || '—' === $value || 'null' === mb_strtolower( $value ) ) {
						$where .= " AND `$key` IS NULL";
					} else {
						$where .= $wpdb->prepare( " AND `$key` = %s", $value ); // phpcs:ignore
					}
				}
			}
		}

		$query  = "SELECT * FROM `$wpdb->table` WHERE $where $order $limit $offset";
		$result = $wpdb->get_results( $query, ARRAY_A ); // phpcs:ignore

		if ( $wpdb->last_error ) {
			$this->items = array();
			WLD_Admin_Notices::add_notice( $wpdb->last_error, 'error' );
		} else {
			$this->items = $result;
			$total_query = "SELECT COUNT(1) FROM `$wpdb->table` WHERE $where";
			$total_items = $wpdb->get_var( $total_query ); // phpcs:ignore

			$this->set_pagination_args( compact( 'total_items', 'per_page' ) );
		}
	}

	public function get_orderby(): string {
		return sanitize_text_field( $_GET['orderby'] ?? '' );
	}

	public function get_order(): string {
		$order = sanitize_text_field( $_GET['order'] ?? 'asc' );

		return 'asc' === mb_strtolower( $order ) ? 'ASC' : 'DESC';
	}

	public static function process_action( string $view ): void {
		$is_edit   = 'edit' === $view;
		$is_add    = 'add' === $view;
		$is_single = true;

		if ( $is_edit || $is_add ) {
			$single_nonce = static::$screen_id . '_nonce';
			$table_nonce  = static::$screen_id . '_table_nonce';
			if ( isset( $_POST[ $single_nonce ] ) ) {
				check_admin_referer( static::$screen_id . '_' . $view, $single_nonce );
			} elseif ( isset( $_POST[ $table_nonce ] ) ) {
				check_admin_referer( static::$screen_id . '_table_' . $view, $table_nonce );
				$is_single = false;
			} else {
				return;
			}

			global $wpdb;

			$wpdb->hide_errors();
			$wpdb->table = static::TABLE;

			$id   = absint( $_GET[ static::ID_COLUMN ] ?? '' );
			$data = array();

			if ( $is_single ) {
				foreach ( static::FIELDS as $name => $field ) {
					if ( static::ID_COLUMN === $name ) {
						continue;
					}
					$data[ $name ] = sanitize_text_field( $_POST[ $name ] ?? '' );
				}
			}

			if ( $is_edit ) {
				if ( 0 === $id ) {
					WLD_Admin_Notices::add_notice( 'Error empty ' . static::ID_COLUMN, 'error' );

					return;
				}

				if ( $is_single ) {
					$wpdb->update(
						static::TABLE,
						wp_unslash( $data ),
						array( static::ID_COLUMN => $id ),
						array(),
						array( '%d' )
					);
				} else {
					foreach ( static::REL_TABLES as $table_data ) {
						$table       = $table_data['name'];
						$wpdb->table = $table;
						$fields      = $table_data['fields'];
						$id_column   = $table_data['id_column'];
						$rel_column  = $table_data['rel_column'];
						$new_data    = array();
						$old_data    = array();
						$count       = count( $_POST[ $table ][ $id_column ] ?? array() );

						for ( $i = 0; $count >= $i; $i ++ ) {
							$_id  = absint( $_POST[ $table ][ $id_column ][ $i ] ?? '' );
							$data = array();
							foreach ( $fields as $name => $field ) {
								if ( $rel_column === $name ) {
									continue;
								}

								if ( $id_column === $name ) {
									$value = $_id;
								} else {
									$value = sanitize_text_field( $_POST[ $table ][ $name ][ $i ] ?? '' );
								}

								$data[ $name ] = $value;
							}

							$test = array_filter( $data );
							if ( $test ) {
								$data[ $rel_column ] = $id;
								if ( 0 === $_id ) {
									$new_data[] = $data;
								} else {
									$old_data[] = $data;
								}
							}
						}

						$old_ids = implode( ', ', array_column( $old_data, $id_column ) );
						$query   = "
						DELETE FROM `$wpdb->table`
						WHERE `$rel_column` = $id" . ( $old_ids ? " AND `$id_column` NOT IN ( $old_ids )" : '' );

						$wpdb->query( $query );
						if ( $wpdb->last_error ) {
							WLD_Admin_Notices::add_notice( $wpdb->last_error, 'error' );
						}

						foreach ( $old_data as $data ) {
							$_id = $data[ $id_column ];
							unset( $data[ $id_column ] );
							$wpdb->update(
								$wpdb->table,
								wp_unslash( $data ),
								array( $id_column => $_id ),
								array(),
								array( '%d' )
							);

							if ( $wpdb->last_error ) {
								WLD_Admin_Notices::add_notice( $wpdb->last_error, 'error' );
							}
						}

						foreach ( $new_data as $data ) {
							unset( $data[ $id_column ] );
							$wpdb->insert(
								$wpdb->table,
								wp_unslash( $data ),
								array()
							);

							if ( $wpdb->last_error ) {
								WLD_Admin_Notices::add_notice( $wpdb->last_error, 'error' );
							}
						}
					}

					$wpdb->last_error = '';
				}
			} else {
				$wpdb->insert( static::TABLE, wp_unslash( $data ) );

				if ( ! $wpdb->last_error ) {
					$id = $wpdb->insert_id;
				}
			}

			if ( $wpdb->last_error ) {
				WLD_Admin_Notices::add_notice( $wpdb->last_error, 'error' );
			} else {
				WLD_Admin_Notices::add_notice( $is_edit ? 'Updated' : 'Created', 'success' );
				wp_safe_redirect( static::get_view_url( 'edit', $id ) );
				exit();
			}
		}
	}

	public static function get_view_url( string $view, int $id = 0 ): string {
		return add_query_arg(
			array(
				'page'          => static::get_slug(),
				'view'          => $view,
				'id'            => $id ?: false,
				'back_to_table' => rawurlencode( static::get_back_to_table() ),
			),
			admin_url( 'admin.php' )
		);
	}

	public static function get_back_to_table(): string {
		$view = sanitize_title( $_GET['view'] ?? 'table' );
		if ( 'table' === $view ) {
			$referer = wp_unslash( $_SERVER['REQUEST_URI'] );
		} else {
			$referer = wp_validate_redirect( $_GET['back_to_table'] ?? '' ); // phpcs:ignore
			if ( $referer ) {
				$referer = rawurldecode( $referer );
			}
		}

		if ( $referer ) {
			$referer = remove_query_arg(
				array(
					'_wp_http_referer',
					'_wpnonce',
					'action',
					'action2',
					'submit',
				),
				$referer
			);
		} else {
			$referer = add_query_arg( 'page', static::get_slug(), admin_url( 'admin.php' ) );
		}

		return $referer;
	}

	public static function set_single_data( string $view ): void {
		if ( 'edit' !== $view && 'single' !== $view ) {
			return;
		}

		$id = absint( $_GET[ static::ID_COLUMN ] ?? '' );
		if ( 0 === $id ) {
			WLD_Admin_Notices::add_notice( 'Error ' . static::ID_COLUMN, 'error' );

			return;
		}

		global $wpdb;

		$wpdb->hide_errors();
		$wpdb->table         = static::TABLE;
		$column              = static::ID_COLUMN;
		$query               = "SELECT * FROM $wpdb->table WHERE `$column` = %d";
		static::$single_data = $wpdb->get_row( $wpdb->prepare( $query, $id ), ARRAY_A ); // phpcs:ignore
		static::$single_id   = $id;

		if ( $wpdb->last_error ) {
			WLD_Admin_Notices::add_notice( $wpdb->last_error, 'error' );
		}

		foreach ( static::REL_TABLES as $table_data ) {
			$wpdb->table = $table_data['name'];
			$column      = $table_data['rel_column'];
			$query       = "SELECT * FROM `$wpdb->table` WHERE `$column` = %d";
			$data        = $wpdb->get_results( $wpdb->prepare( $query, $id ), ARRAY_A ); // phpcs:ignore

			if ( $wpdb->last_error ) {
				WLD_Admin_Notices::add_notice( $wpdb->last_error, 'error' );
			}

			static::$single_data[ $table_data['name'] ] = $data;
		}
	}

	public static function set_page_title( string $view ): void {
		if ( 'single' === $view ) {
			static::$page_title = esc_html__( 'View', 'parent-theme' );
		} elseif ( 'edit' === $view ) {
			static::$page_title = esc_html__( 'Edit', 'parent-theme' );
		} elseif ( 'add' === $view ) {
			static::$page_title = esc_html__( 'Add', 'parent-theme' );
		} else {
			static::$page_title = get_admin_page_title();
		}
	}

	public static function set_page_content( string $view ): void {
		ob_start();
		if ( 'single' === $view ) {
			static::single_view();
		} elseif ( 'edit' === $view ) {
			static::edit_view();
		} elseif ( 'add' === $view ) {
			static::add_view();
		} else {
			static::table_view();
		}

		static::$page_content = ob_get_clean();
	}

	public static function single_view(): void {
		if ( empty( static::$single_data ) ) {
			return;
		}
		?>
		<style>
			@media print {
				#adminmenuwrap,
				#adminmenuback,
				#wpfooter,
				.wp-heading-inline,
				.page-title-action,
				.submit {
					display: none !important;
				}

				#wpcontent {
					margin: 0;
				}
			}
		</style>
		<form method="post">
			<table class="form-table">
				<tbody>
				<?php
				foreach ( static::FIELDS as $name => $field ) {
					$field['readonly'] = true;
					if ( static::ID_COLUMN !== $name ) {
						$value = static::$single_data[ $name ] ?? '';
						?>
						<tr>
							<th scope="row"><?php echo esc_html( $field['label'] ?? '' ); ?></th>
							<td><?php static::the_input( $name, $field, $value ); ?></td>
						</tr>
						<?php
					}
				}

				foreach ( static::REL_TABLES as $table_data ) {
					$table      = $table_data['name'];
					$title      = $table_data['title'] ?? '';
					$items      = static::$single_data[ $table ] ?? array();
					$fields     = $table_data['fields'] ?? array();
					$id_column  = $table_data['id_column'];
					$rel_column = $table_data['rel_column'];
					?>
					<tr>
						<th scope="row"><?php echo esc_html( $title ); ?></th>
						<td>
							<ol>
								<?php
								foreach ( $items as $item ) {
									echo '<li>';
									foreach ( $fields as $name => $field ) {
										if ( $id_column === $name || $rel_column === $name ) {
											continue;
										}
										$label = $field['label'] ?? '';
										$value = $item[ $name ] ?? '';
										if ( $value ) {
											echo esc_html( $label ) . ': ' . esc_html( $value ) . '<br>';
										}
									}
									echo '</li>';
								}
								?>
							</ol>
						</td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
			<?php
			echo '<p class="submit">';
			$edit_url = static::get_view_url( 'edit', static::$single_id );
			if ( $edit_url ) {
				echo '<a href="' . esc_url( $edit_url ) . '" class="button button-primary">';
				echo esc_html__( 'Edit', 'parent-theme' );
				echo '</a>';
			}
			static::back_button();
			do_action( static::$screen_id . '_view_submit' );
			echo '</p>';
			?>
		</form>
		<?php
	}

	public static function the_input( string $name, array $field, string $value ): void {
		$id          = $name;
		$class       = $field['class'] ?? 'regular-text';
		$title       = $field['title'] ?? '';
		$placeholder = $field['placeholder'] ?? '';
		$pattern     = $field['pattern'] ?? '';
		$required    = $field['required'] ?? '';
		$readonly    = $field['readonly'] ?? '';
		$disabled    = $field['disabled'] ?? '';

		$attr = ' type="text" id="' . $id . '" name="' . $name . '" value="' . esc_attr( $value ) . '"';
		if ( $class ) {
			$attr .= ' class="' . esc_attr( $class ) . '"';
		}
		if ( $title ) {
			$attr .= ' title="' . esc_attr( $title ) . '"';
		}
		if ( $placeholder ) {
			$attr .= ' placeholder="' . esc_attr( $placeholder ) . '"';
		}
		if ( $pattern ) {
			$attr .= ' pattern="' . esc_attr( $pattern ) . '"';
		}
		if ( $required ) {
			$attr .= ' required';
		}
		if ( $readonly ) {
			$attr .= ' readonly';
		}
		if ( $disabled ) {
			$attr .= ' disabled';
		}

		echo '<input' . $attr . '>';
	}

	public static function back_button(): void {
		$back_url = static::get_back_to_table();
		if ( $back_url ) {
			echo '<input type="hidden" name="_wp_http_referer" value="' . esc_attr( $back_url ) . '">';
			echo ' <a href="' . esc_url( $back_url ) . '" class="button">';
			echo esc_html__( 'Back to Table', 'parent-theme' );
			echo '</a>';
		}
	}

	public static function edit_view(): void {
		if ( empty( static::$single_data ) ) {
			return;
		}
		?>
		<form method="post">
			<table class="form-table">
				<tbody>
				<?php
				foreach ( static::FIELDS as $name => $field ) {
					if ( static::ID_COLUMN !== $name ) {
						static::the_field( $name, $field, static::$single_data[ $name ] ?? '' );
					}
				}
				?>
				</tbody>
			</table>
			<?php
			echo '<p class="submit">';
			wp_nonce_field( static::$screen_id . '_edit', static::$screen_id . '_nonce' );
			submit_button( __( 'Save', 'parent-theme' ), 'primary', 'submit', false );
			static::back_button();
			do_action( static::$screen_id . '_edit_submit' );
			echo '</p>';
			?>
		</form>
		<?php
		foreach ( static::REL_TABLES as $table_data ) {
			static::the_rel_table( $table_data );
		}
	}

	public static function the_field( string $name, array $field, string $value ): void {
		$label = $field['label'] ?? '';
		$id    = $name;
		?>
		<tr>
			<th scope="row">
				<label for="<?php echo $id; ?>"><?php echo esc_html( $label ); ?></label>
			</th>
			<td>
				<?php static::the_input( $name, $field, $value ); ?>
			</td>
		</tr>
		<?php
	}

	public static function the_rel_table( array $table_data ): void {
		wp_enqueue_script( 'jquery-ui-sortable' );

		$table      = $table_data['name'];
		$title      = $table_data['title'] ?? '';
		$icon       = $table_data['icon'] ?? '';
		$fields     = $table_data['fields'] ?? array();
		$rows       = static::$single_data[ $table ] ?? array();
		$id_column  = $table_data['id_column'];
		$rel_column = $table_data['rel_column'];
		?>
		<hr>
		<?php if ( $title ) : ?>
			<h2 class="title">
				<?php if ( $icon ) : ?>
					<span class="dashicons <?php echo sanitize_html_class( $icon ); ?>"></span>
				<?php endif; ?>
				<?php echo esc_html( $title ); ?>
			</h2>
		<?php endif; ?>
		<!--suppress CssUnusedSymbol -->
		<style>
			.sortable {
				padding-bottom: 8px;
				margin-bottom: -24px;
			}

			.wp-list-table {
				padding-bottom: 8px;
			}

			.wp-list-table tbody tr:first-child {
				display: none;
			}

			.wp-list-table input {
				border-radius: 0;
				border-width: 0 0 1px;
				width: 100%;
			}

			.wp-list-table input:focus {
				box-shadow: none;
				border-width: 0 0 2px;
			}

			.wp-list-table tr:nth-child(odd) input {
				background: inherit;
			}

			.wp-list-table .button.button-small {
				display: inline-flex;
			}

			.ui-sortable-helper {
				display: table;
				background-color: #F9F9F9;
				border-top: 1px solid #DFDFDF;
			}

			.ui-sortable-placeholder td {
				padding: 11px 0;
			}
		</style>
		<form method="post" class="table-form">
			<div class="sortable">
				<table class="wp-list-table widefat fixed">
					<thead>
					<tr>
						<?php
						$primary = 'column-primary';
						foreach ( $fields as $name => $field ) {
							if ( $id_column === $name || $rel_column === $name ) {
								continue;
							}
							$label = $field['label'] ?? '';
							echo '<th scope="col" class="' . $primary . '">' . esc_html( $label ) . '</th>';
							$primary = '';
						}
						?>
						<th><?php esc_html_e( 'Actions', 'parent-theme' ); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php
					static::the_rel_table_row( array_fill_keys( array_keys( $fields ), '' ), $table_data, true );
					foreach ( $rows as $row ) {
						static::the_rel_table_row( $row, $table_data );
					}
					?>
					</tbody>
				</table>
			</div>
			<?php
			echo '<p class="submit">';
			$back_url = static::get_back_to_table();
			echo '<input type="hidden" name="_wp_http_referer" value="' . esc_attr( $back_url ) . '">';
			echo '<input type="hidden" name="table" value="' . esc_attr( $table ) . '">';
			wp_nonce_field( static::$screen_id . '_table_edit', static::$screen_id . '_table_nonce' );
			submit_button( __( 'Save', 'parent-theme' ), 'primary', 'submit', false );
			echo ' <button type="button" class="button button-add">';
			echo esc_html__( 'Add', 'parent-theme' );
			echo '</button>';
			do_action( static::$screen_id . '_table_edit_submit' );
			do_action( static::$screen_id . '_' . $table . '_table_edit_submit' );
			echo '</p>';
			?>
		</form>
		<script>
			jQuery( function( $ ) {
				const
					$form = $( '.table-form' ),
					$sortable = $form.find( '.sortable' ),
					$table = $form.find( 'table' ),
					$tpl = $table.find( 'tbody tr:first-child' ).clone();

				$tpl.find( '[data-required="1"] input' ).prop( 'required', true );

				$form.on( 'click', 'button', function() {
					const $button = $( this );

					if ( $button.hasClass( 'button-trash' ) ) {
						$button.closest( 'tr' ).remove();
					} else if ( $button.hasClass( 'button-add' ) ) {
						$table.append( $tpl.clone() );
					}
				} );

				$sortable.sortable( {
					items: 'tr',
					cursor: 'move',
					containment: $sortable,
					axis: 'y',
					handle: '.button-move',
					cancel: '',
					helper: function( e, ui ) {
						ui.children().each( function() {
							$( this ).width( $( this ).width() );
						} );

						return ui;
					},
				} );
			} );
		</script>
		<?php
	}

	public static function the_rel_table_row( array $row, array $table_data, bool $tpl = false ): void {
		$table      = $table_data['name'];
		$fields     = $table_data['fields'] ?? array();
		$id_column  = $table_data['id_column'];
		$rel_column = $table_data['rel_column'];
		echo '<tr>';
		$primary = 'column-primary';
		foreach ( $row as $name => $value ) {
			if ( ! isset( $fields[ $name ] ) || $id_column === $name || $rel_column === $name ) {
				continue;
			}

			$label    = $fields[ $name ]['label'] ?? '';
			$field    = $fields[ $name ];
			$value    = $tpl ? '' : $value;
			$required = absint( $field['required'] ?? '' );
			if ( $tpl && $required ) {
				$field['required'] = false;
			}
			echo '<td class="' . $primary . '" data-colname="' . esc_attr( $label ) . '" data-required="' . $required . '">';
			static::the_input( $table . '[' . $name . '][]', $field, $value );
			if ( $primary ) {
				$id = $row[ $id_column ] ?? '0';
				?>
				<button type="button" class="toggle-row">
					<span class="screen-reader-text">
						<?php esc_html_e( 'Show more details', 'parent-theme' ); ?>
					</span>
				</button>
				<input type="hidden" name="<?php echo esc_attr( $table . '[' . $id_column . '][]' ); ?>"
					   value="<?php echo esc_attr( $id ); ?>">
				<?php
				$primary = '';
			}
			echo '</td>';
		}
		?>
		<td>
			<button type="button" class="button button-small button-trash">
				<span class="dashicons dashicons-trash"></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Trash', 'parent-theme' ); ?></span>
			</button>
			<button type="button" class="button button-small button-move">
				<span class="dashicons dashicons-move"></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Move', 'parent-theme' ); ?></span>
			</button>
		</td>
		<?php
		do_action( static::$screen_id . '_table_edit_actions' );
		do_action( static::$screen_id . '_' . $table . '_table_edit_edit_actions' );
		echo '</tr>';
	}

	public static function add_view(): void {
		?>
		<form method="post">
			<table class="form-table">
				<tbody>
				<?php
				foreach ( static::FIELDS as $name => $field ) {
					if ( static::ID_COLUMN !== $name ) {
						$value = sanitize_text_field( $_POST[ $name ] ?? '' );
						static::the_field( $name, $field, $value );
					}
				}
				?>
				</tbody>
			</table>

			<?php
			echo '<p class="submit">';
			wp_nonce_field( static::$screen_id . '_add', static::$screen_id . '_nonce' );
			submit_button( __( 'Add', 'parent-theme' ), 'primary', 'submit', false );
			static::back_button();
			do_action( static::$screen_id . '_add_submit' );
			echo '</p>';
			?>
		</form>
		<?php
	}

	public static function table_view(): void {
		?>
		<form id="posts-filter" method="get">
			<?php
			static::hidden_inputs();
			static::filters();
			static::$table->display();
			?>
		</form>
		<?php
	}

	public static function hidden_inputs(): void {
		$orderby = sanitize_text_field( $_GET['orderby'] ?? '' );
		if ( $orderby ) {
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $orderby ) . '">';
		}

		$order = sanitize_text_field( $_GET['order'] ?? '' );
		if ( $order ) {
			echo '<input type="hidden" name="order" value="' . esc_attr( $order ) . '">';
		}

		echo '<input type="hidden" name="page" value="' . static::get_slug() . '">';
	}

	public static function filters( string $text = 'Filter' ): void {
		?>
		<style>
			.search-box input[type] {
				vertical-align: middle;
			}
		</style>
		<script>
			( {
				init() {
					jQuery( '#adv-settings .hide-column-tog' ).on( 'change', e => this.change( e ) );
				},
				change( e ) {
					e.target.checked ? this.show( e.target.value ) : this.hide( e.target.value );
				},
				show( column ) {
					jQuery( '[name="' + column + '"]' ).parent().removeClass( 'hidden' );
				},
				hide( column ) {
					jQuery( '[name="' + column + '"]' ).parent().addClass( 'hidden' );
				}
			} ).init();
		</script>
		<?php
		echo '<p class="search-box">';
		foreach ( static::FIELDS as $key => $field ) {
			if ( ! empty( $field['filter'] ) ) {
				static::the_search_field( $key, $field );
			}
		}
		submit_button( $text, 'primary', 'submit', false, array( 'id' => 'search-submit' ) );
		echo '</p>';
		echo '<div style="padding:10px;clear:both"></div>';
	}

	/** @noinspection HtmlUnknownAttribute */
	public static function the_search_field( string $name, array $field ): void {
		$hidden  = get_hidden_columns( static::$table->screen );
		$value   = sanitize_text_field( $_GET[ $name ] ?? '' );
		$id      = sanitize_html_class( $name . '_search_input' );
		$label   = $field['label'] ?? '';
		$type    = $field['filter']['type'] ?? 'text';
		$options = $field['filter']['options'] ?? array();
		$class   = in_array( $name, $hidden, true ) ? 'hidden' : '';

		if ( 'column' === $options ) {
			$options = static::get_options( $name );
		}

		echo '<span class="' . $class . '">';
		echo '<label class="screen-reader-text" for="' . $id . '">' . esc_html( $label ) . ' :</label>';
		if ( 'select' === $type ) {
			$options = array( '' => $label ) + $options;
			echo '<select name="' . esc_attr( $name ) . '" id="' . $id . '">';
			foreach ( $options as $key => $option ) {
				printf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $key ),
					selected( $key, $value, false ),
					esc_html( $option )
				);
			}
			echo '</select>';
		} else {
			printf(
				'<input type="search" id="%s" placeholder="%s" name="%s" value="%s">',
				esc_attr( $id ),
				esc_attr( $label ),
				esc_attr( $name ),
				esc_attr( $value )
			);
		}
		echo '</span>';
	}

	public static function get_options( string $column ): array {
		global $wpdb;

		$wpdb->table = static::TABLE;

		$column  = static::esc_sql_column( $column );
		$query   = "SELECT DISTINCT `$column` FROM `$wpdb->table` ORDER BY `$column`";
		$options = $wpdb->get_col( $query ); // phpcs:ignore

		if ( $options ) {
			$options = array_combine( $options, $options );
		}

		return $options;
	}

	public static function esc_sql_column( string $column ): string {
		return str_replace( '`', '``', $column );
	}

	public static function page(): void {
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php echo static::$page_title; ?></h1>
			<a href="<?php echo static::get_view_url( 'add' ); ?>" class="page-title-action">
				<?php esc_html_e( 'Add New', 'parent-theme' ); ?>
			</a>
			<hr class="wp-header-end">
			<?php do_action( 'all_admin_notices' ); ?>
			<?php echo static::$page_content; ?>
		</div>
		<?php
	}

	public static function admin_menu(): void {
		add_menu_page(
			static::TITLE,
			static::TITLE,
			'manage_options',
			static::get_slug(),
			array( static::class, 'page' ),
			static::ICON,
			static::POSITION
		);
	}

	public function get_columns(): array {
		$columns = array( 'cb' => true );

		foreach ( static::FIELDS as $key => $field ) {
			$columns[ $key ] = $field['label'];
		}

		return $columns;
	}

	protected function get_sortable_columns(): array {
		$columns = array();

		foreach ( static::FIELDS as $key => $field ) {
			if ( isset( $field['order'] ) ) {
				$columns[ $key ] = array( $key, 'asc' === mb_strtolower( $field['order'] ) );
			}
		}

		return $columns;
	}

	protected function column_default( $item, $column_name ): string {
		$primary = $this->get_primary_column_name();
		$value   = trim( $item[ $column_name ] ?? '' ) ?: '&#8212;';

		if ( $primary === $column_name ) {
			$id = absint( $item[ static::ID_COLUMN ] ?? 0 );

			return '<a href="' . static::get_view_url( 'edit', $id ) . '">' . $value . '</a>';
		}

		return $value;
	}

	protected function column_cb( $item ): string {
		return sprintf( '<input type="checkbox" name="ids[]" value="%s">', $item[ static::ID_COLUMN ] );
	}

	protected function get_bulk_actions(): array {
		return array(
			'delete' => 'Delete',
		);
	}

	protected function handle_row_actions( $item, $column_name, $primary ): string {
		if ( $column_name !== $primary ) {
			return '';
		}

		$id                = absint( $item[ static::ID_COLUMN ] ?? 0 );
		$single_url        = static::get_view_url( 'single', $id );
		$edit_url          = static::get_view_url( 'edit', $id );
		$delete_url        = static::get_action_url( 'delete', $id );
		$actions           = array();
		$actions['view']   = '<a href="' . $single_url . '">' . __( 'View', 'parent-theme' ) . '</a>';
		$actions['edit']   = '<a href="' . $edit_url . '">' . __( 'Edit', 'parent-theme' ) . '</a>';
		$actions['delete'] = '<a href="' . $delete_url . '">' . __( 'Delete', 'parent-theme' ) . '</a>';

		return $this->row_actions( $actions );
	}

	public static function get_action_url( string $action, int $id = 0 ): string {
		return wp_nonce_url(
			add_query_arg(
				array(
					'page'          => static::get_slug(),
					'action'        => $action,
					'ids'           => array( $id ),
					'back_to_table' => rawurlencode( static::get_back_to_table() ),
				),
				admin_url( 'admin.php' )
			),
			'bulk-' . static::$table->_args['plural']
		);
	}
}
