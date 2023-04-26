<?php /** @noinspection StaticInvocationViaThisInspection */


class WLD_XLSX_CSV {
	public static $path = '';

	public static function to_array( string $file_path, string $file_name ) {
		$data_file_path = self::get_extract_path( md5_file( $file_path ) );

		if ( file_exists( $data_file_path ) ) {
			/** @noinspection PhpIncludeInspection */
			$rows = include $data_file_path;
		} else {
			$check = wp_check_filetype_and_ext(
				$file_path,
				$file_name,
				array(
					'csv'  => 'text/csv',
					'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
				)
			);

			if ( $check['ext'] ) {
				if ( 'csv' === $check['ext'] ) {
					$rows = self::csv_to_array( $file_path );
				} else {
					$rows = self::xlsx_to_array( $file_path );
				}
			} else {
				return new WP_Error( 'invalid_ext', 'Invalid Ext' );
			}

			wld_put_file_content(
				$data_file_path,
				sprintf( "<?php\n\nreturn %s;\n", var_export( $rows, true ) ) // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			);
		}

		return $rows;
	}

	public static function csv_to_array( string $file_path ) : array {
		$rows     = array();
		$file     = fopen( $file_path, 'rb' ); // phpcs:ignore
		$index    = 1;
		$max_cell = 0;

		if ( false !== $file ) {
			while ( false !== ( $raw_row = fgetcsv( $file ) ) ) {  // phpcs:ignore
				self::set_row( $raw_row, $rows, $index, $max_cell );
			}

			fclose( $file );  // phpcs:ignore
		}

		return $rows;
	}

	protected static function set_row( array $raw_row, array &$rows, int &$index, int &$max_cell ) : void {
		$row  = array();
		$test = array_filter( array_map( 'trim', $raw_row ) );
		if ( $test ) {
			array_unshift( $raw_row, $index );
			foreach ( $raw_row as $cell => $cell_value ) {
				$row[ $cell ] = self::fix_utf8_non_breaking( mb_convert_encoding( $cell_value, 'UTF-8' ) );
			}

			$rows[] = $row;

			$cell_count = count( $row );
			if ( $max_cell < $cell_count ) {
				$max_cell = $cell_count;
			}
		}

		$index ++;
	}

	protected static function fix_utf8_non_breaking( string $string ) : string {
		return preg_replace( '/\x{00a0}/u', ' ', $string );
	}

	/** @noinspection PhpUnused, UnknownInspectionInspection */
	public static function xlsx_to_csv( string $file_path, int $sheet_id = 1 ) : bool {
		$csv_dir = self::get_path( pathinfo( $file_path, PATHINFO_FILENAME ) );
		$rows    = self::xlsx_to_array( $file_path, $sheet_id, $sheet_name );

		self::mkdir( $csv_dir );

		$csv_open = fopen( $csv_dir . DIRECTORY_SEPARATOR . $sheet_name . '.csv', 'wb' ); // phpcs:ignore
		if ( false === $csv_open ) {
			return false;
		}

		foreach ( $rows as $row ) {
			self::file_put_csv( $csv_open, $row );
		}

		return true;
	}

	public static function get_path( string $filename = '' ) : string {
		if ( empty( self::$path ) ) {
			self::$path = wp_get_upload_dir()['basedir'] . '/wld-importer/';
		}

		return str_replace(
			'/',
			DIRECTORY_SEPARATOR,
			self::$path . $filename
		);
	}

	public static function xlsx_to_array( string $file_path, int $sheet_id = 1, string &$sheet_name = '' ) : array {
		self::xlsx_unzip( $file_path );

		$strings    = self::get_strings();
		$sheets     = self::get_sheets();
		$numbers    = range( '0', '9' );
		$rows       = array();
		$xml_path   = self::get_extract_path( 'xl/worksheets/sheet' . $sheet_id . '.xml' );
		$xml_reader = new XMLReader();
		$xml_open   = $xml_reader->open( $xml_path );
		$index      = 1;
		$max_cell   = 0;

		if ( ! isset( $sheets[ $sheet_id ] ) || ! $xml_open ) {
			return $rows;
		}

		while ( $xml_reader->read() && 'row' !== $xml_reader->name ) {
			continue;
		}

		while ( 'row' === $xml_reader->name ) {
			$raw_row     = array();
			$col_alpha   = 'A';
			$xml_element = new SimpleXMLElement( $xml_reader->readOuterXML() );
			$xml_array   = self::xml_to_array( $xml_element );
			$cells       = $xml_array['children']['c'] ?? array();

			foreach ( $cells as $cell ) {
				if ( array_key_exists( 'v', $cell['children'] ) ) {
					$cell_alpha = str_replace( $numbers, '', $cell['attributes']['r'] );

					while ( $col_alpha !== $cell_alpha ) {
						$raw_row[] = '';
						$col_alpha ++;
					}

					if ( array_key_exists( 't', $cell['attributes'] ) && 's' === $cell['attributes']['t'] ) {
						$raw_row[] = $strings[ $cell['children']['v'][0]['text'] ];
					} else {
						$raw_row[] = $cell['children']['v'][0]['text'];
					}
				} else {
					$raw_row[] = '';
				}

				$col_alpha ++;
			}

			self::set_row( $raw_row, $rows, $index, $max_cell );

			$xml_reader->next( 'row' );
		}

		$xml_reader->close();

		$sheet_name = $sheets[ $sheet_id ];

		return self::normalize_rows( $rows, $max_cell );
	}

	protected static function xlsx_unzip( string $file_path ) : void {
		if ( ! class_exists( 'PclZip' ) ) {
			/** @noinspection PhpIncludeInspection, RedundantSuppression */
			require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';
		}

		$md5 = md5_file( $file_path );

		if ( ! file_exists( self::get_extract_path( $md5 ) ) ) {
			self::clear();

			self::mkdir( self::get_extract_path() );

			file_put_contents( self::get_extract_path( $md5 ), '' ); // phpcs:ignore

			$archive = new PclZip( $file_path );

			$archive->extract( PCLZIP_OPT_PATH, self::get_extract_path() );
		}
	}

	public static function get_extract_path( string $filename = '' ) : string {
		return str_replace(
			'/',
			DIRECTORY_SEPARATOR,
			self::get_path( 'extract' ) . '/' . $filename
		);
	}

	public static function clear( string $dir = '' ) : bool {
		if ( empty( $dir ) ) {
			$dir = self::get_extract_path();
		}

		if ( ! is_dir( $dir ) ) {
			return false;
		}

		$files = scandir( $dir );

		foreach ( $files as $file ) {
			if ( ! in_array( $file, array( '.', '..' ), true ) ) {
				$path = $dir . DIRECTORY_SEPARATOR . $file;
				if ( is_dir( $path ) ) {
					self::clear( $path );
				} else {
					unlink( $path );
				}
			}
		}

		return rmdir( $dir );
	}

	public static function mkdir( string $dir ) : void {
		if ( ! file_exists( $dir ) && ! wp_mkdir_p( $dir ) && ! is_dir( $dir ) ) {
			throw new RuntimeException( sprintf( 'Directory "%s" was not created', $dir ) );
		}
	}

	protected static function get_strings() : array {
		$file_name = self::get_extract_path( 'strings.json' );
		if ( file_exists( $file_name ) ) {
			return json_decode( file_get_contents( $file_name ), true ); // phpcs:ignore
		}

		$xml_path   = self::get_extract_path( 'xl/sharedStrings.xml' );
		$strings    = array();
		$xml_reader = new XMLReader();

		$xml_reader->open( $xml_path );

		while ( $xml_reader->read() && 'si' !== $xml_reader->name ) {
			continue;
		}

		while ( 'si' === $xml_reader->name ) {
			$xml_element = new SimpleXMLElement( $xml_reader->readOuterXML() );
			$xml_array   = self::xml_to_array( $xml_element );

			if (
				isset( $xml_array['children']['t'][0] ) &&
				is_array( $xml_array['children']['t'][0] ) &&
				array_key_exists( 'text', $xml_array['children']['t'][0] )
			) {
				$strings[] = $xml_array['children']['t'][0]['text'];
			}

			$xml_reader->next( 'si' );
		}

		$xml_reader->close();

		// phpcs:ignore
		file_put_contents( self::get_extract_path( 'strings.json' ), wp_json_encode( $strings ) );

		return $strings;
	}

	protected static function xml_to_array( SimpleXMLElement $xml_element ) : array {
		$namespace         = $xml_element->getDocNamespaces( true );
		$namespace[ null ] = null;

		$children   = array();
		$attributes = array();

		$text = trim( (string) $xml_element );
		if ( strlen( $text ) <= 0 ) {
			$text = null;
		}

		// get info for all namespaces
		if ( is_object( $xml_element ) ) {
			foreach ( $namespace as $ns => $ns_url ) {
				// attributes
				$_attributes = $xml_element->attributes( $ns, true );
				foreach ( $_attributes as $attribute_name => $attribute_value ) {
					$attribute_name  = strtolower( trim( (string) $attribute_name ) );
					$attribute_value = trim( (string) $attribute_value );
					if ( ! empty( $ns ) ) {
						$attribute_name = $ns . ':' . $attribute_name;
					}
					$attributes[ $attribute_name ] = $attribute_value;
				}

				// children
				$_children = $xml_element->children( $ns, true );
				foreach ( $_children as $child_name => $child ) {
					$child_name = strtolower( (string) $child_name );
					if ( ! empty( $ns ) ) {
						$child_name = $ns . ':' . $child_name;
					}
					$children[ $child_name ][] = self::xml_to_array( $child );
				}
			}
		}

		return compact( 'text', 'attributes', 'children' );
	}

	protected static function get_sheets( string $file_path = '' ) : array {
		if ( $file_path ) {
			self::xlsx_unzip( $file_path );
		}

		$xml_path   = self::get_extract_path( 'xl/workbook.xml' );
		$sheets     = array();
		$xml_reader = new XMLReader();
		$index      = 1;

		$xml_reader->open( $xml_path );

		while ( $xml_reader->read() && 'sheet' !== $xml_reader->name ) {
			continue;
		}

		while ( 'sheet' === $xml_reader->name ) {
			$xml_element         = new SimpleXMLElement( $xml_reader->readOuterXML() );
			$xml_array           = self::xml_to_array( $xml_element );
			$sheets[ $index ++ ] = $xml_array['attributes']['name'];

			$xml_reader->next( 'sheet' );
		}

		$xml_reader->close();

		return $sheets;
	}

	protected static function normalize_rows( array $rows, int $max_cell ) : array {
		foreach ( $rows as $i => $row ) {
			if ( $max_cell > count( $row ) ) {
				$rows[ $i ] = array_pad( $row, $max_cell, '' );
			}
		}

		return $rows;
	}

	protected static function file_put_csv( $handle, array $fields ) : void {
		$delimiter = ',';
		$enclosure = '"';
		$escape    = '\\';

		$first = 1;
		foreach ( $fields as $field ) {
			if ( 0 === $first ) {
				fwrite( $handle, ',' ); // phpcs:ignore
			}

			$f = str_replace(
				array( $enclosure, $escape . $enclosure ),
				array( $enclosure . $enclosure, $escape ),
				$field
			);

			$test_1 = strpbrk( $f, " \t\n\r" . $delimiter . $enclosure . $escape );
			$test_2 = strstr( $f, "\000" );

			if ( $test_1 || $test_2 ) {
				fwrite( $handle, $enclosure . $f . $enclosure ); // phpcs:ignore
			} else {
				fwrite( $handle, $f ); // phpcs:ignore
			}

			$first = 0;
		}

		fwrite( $handle, "\n" ); // phpcs:ignore
	}
}
