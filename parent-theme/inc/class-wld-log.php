<?php
/**
 * @noinspection ForgottenDebugOutputInspection, PhpUndefinedClassInspection, PhpUndefinedMethodInspection, PhpRedundantVariableDocTypeInspection
 * phpcs:disable WordPress.PHP.DevelopmentFunctions
 */


class WLD_Log {
	protected static $old_error_handler;
	protected static $has_changes     = false;
	protected static $logs            = array();
	protected static $ignore_levels   = array();
	protected static $ignore_messages = array();
	protected static $ignore_paths    = array();

	public static function init() : void {
		add_action(
			'shutdown',
			array( static::class, 'action_shutdown' ),
			PHP_INT_MAX
		);

		// phpcs:ignore WordPress.PHP.DevelopmentFunctions
		static::$old_error_handler = set_error_handler( array( static::class, 'error_handler' ) );

		static::add_ignore_messages(
			array(
				'Non-static method WP_Feed_Cache::create() should not be called statically',
			)
		);

		static::add_ignore_paths(
			array(
				WP_PLUGIN_DIR . '/gravityforms/gravityforms.php',
				WP_PLUGIN_DIR . '/webp-express/vendor/rosell-dk/dom-util-for-webp/src-vendor/simple_html_dom/simple_html_dom.inc',
			)
		);
	}

	/**
	 * @param WP_Error|string $wp_error_or_message
	 * @param string          $type
	 * @param string          $handle
	 */
	public static function write( $wp_error_or_message, string $type = 'error', string $handle = 'wld' ) : void {
		if ( is_wp_error( $wp_error_or_message ) ) {
			if ( $wp_error_or_message->has_errors() ) {
				foreach ( $wp_error_or_message->errors as $code => $messages ) {
					$message   = array();
					$message[] = array_pop( $messages );
					foreach ( $messages as $m ) {
						$message[] = $m;
					}

					$message[] = 'CODE: ' . $code;

					if ( $wp_error_or_message->get_error_data( $code ) ) {
						$data      = $wp_error_or_message->get_error_data( $code );
						$data      = 'DATA: ' . ( is_scalar( $data ) ? $data : wp_json_encode( $data ) );
						$message[] = $data;
					}

					static::add_log( 'error', implode( PHP_EOL, $message ), $handle );
				}
			}
		} elseif ( is_string( $wp_error_or_message ) ) {
			static::add_log( $type, $wp_error_or_message, $handle );
		} else {
			static::add_log( $type, 'Unsupported error type passed', $handle );
		}
	}

	/** @noinspection PhpUnused, UnknownInspectionInspection */
	public static function add_ignore_levels( array $levels ) : void {
		foreach ( $levels as $level ) {
			static::$ignore_levels[ $level ] = true;
		}
	}

	public static function add_ignore_messages( array $messages ) : void {
		foreach ( $messages as $message ) {
			static::$ignore_messages[ $message ] = true;
		}
	}

	public static function add_ignore_paths( array $paths ) : void {
		foreach ( $paths as $path ) {
			$path                          = str_replace( array( '\\', '/' ), DIRECTORY_SEPARATOR, $path );
			static::$ignore_paths[ $path ] = true;
		}
	}

	public static function action_shutdown() : void {
		$error                 = error_get_last();
		$error_types_to_handle = array(
			E_ERROR             => true,
			E_PARSE             => true,
			E_USER_ERROR        => true,
			E_COMPILE_ERROR     => true,
			E_RECOVERABLE_ERROR => true,
		);

		if ( isset( $error['type'], $error_types_to_handle[ $error['type'] ] ) ) {
			static::error_handler( $error['type'], $error['message'] );
		}

		if ( static::$has_changes ) {
			foreach ( static::$logs as $handle => $contents ) {
				WLD_Theme::put_file_contents( static::get_log_path( $handle ), $contents );
			}
		}
	}

	public static function error_handler( int $level, string $message, $file = null, $line = null, $context = null ) : bool {
		if (
			! isset( static::$ignore_paths[ $file ] ) &&
			! isset( static::$ignore_levels[ $level ] ) &&
			! isset( static::$ignore_messages[ $message ] ) &&
			! static::has_duplicate( $message, 'php' )
		) {
			switch ( $level ) {
				case E_WARNING:
				case E_USER_WARNING:
				case E_STRICT:
				case E_DEPRECATED:
				case E_USER_DEPRECATED:
					$type = 'warning';
					break;
				case E_NOTICE:
				case E_USER_NOTICE:
					$type = 'info';
					break;
				default:
					$type = 'error';
			}

			static::add_log( $type, $message, 'php' );
		}

		if ( static::is_need_qm_log() && class_exists( 'QM_Collectors' ) ) {
			/** @see QM_Collector_PHP_Errors */
			QM_Collectors::get( 'php_errors' )->error_handler( $level, $message, $file, $line, $context );
		}

		if ( static::$old_error_handler ) {
			return call_user_func_array( static::$old_error_handler, func_get_args() );
		}

		return false;
	}

	protected static function add_log( string $type, string $message, string $handle = 'wld' ) : void {
		$type    = str_pad( $type, 7 );
		$message = wp_date( 'c' ) . ' ' . $type . ' ' . static::get_formatted_message( $message );

		$message .= static::get_log_user_line();
		$message .= static::get_log_url_line();
		$message .= static::get_log_referrer_line();
		$message .= static::get_log_trace_lines();

		static::$logs[ $handle ] = static::get_log( $handle ) . $message . PHP_EOL . PHP_EOL;
		static::$has_changes     = true;
	}

	protected static function get_log( $handle ) : string {
		if ( ! isset( static::$logs[ $handle ] ) ) {
			static::$logs[ $handle ] = WLD_Theme::get_file_contents( static::get_log_path( $handle ) );
		}

		return static::$logs[ $handle ];
	}

	protected static function get_log_path( string $handle = 'wld' ) : string {
		return static::get_log_dir() . static::get_log_name( $handle );
	}

	protected static function get_log_dir() : string {
		static $dir;

		if ( null === $dir ) {
			$dir = wp_upload_dir()['basedir'] . '/wld-logs/';
		}

		return $dir;
	}

	protected static function get_log_name( string $handle = 'wld' ) : string {
		static $names = array();

		if ( ! isset( $names[ $handle ] ) ) {
			$date             = wp_date( 'Y-m-d' );
			$hash             = wp_hash( $handle );
			$names[ $handle ] = sanitize_file_name( implode( '-', array( $handle, $date, $hash ) ) . '.log' );
		}

		return $names[ $handle ];
	}

	protected static function get_offset() : string {
		static $offset;
		if ( null === $offset ) {
			$offset = str_repeat( ' ', 34 );
		}

		return $offset;
	}

	protected static function get_log_user_line() : string {
		static $user_line;
		if ( null === $user_line ) {
			$user_id = get_current_user_id();
			if ( $user_id ) {
				$user_line = PHP_EOL . PHP_EOL . static::get_offset() . 'USER: ' . $user_id;
			} else {
				$user_line = '';
			}
		}

		return $user_line;
	}

	protected static function get_log_url_line() : string {
		static $url_line;
		if ( null === $url_line ) {
			$url      = trim( wp_unslash( $_SERVER['REQUEST_URI'] ), '/' );
			$url_line = PHP_EOL . static::get_offset() . 'URL: ' . ( $url ?: '/' );
		}

		return $url_line;
	}

	protected static function get_log_referrer_line() : string {
		static $referrer_line;
		if ( null === $referrer_line ) {
			$home_url = home_url( '/' );
			$referrer = wp_get_raw_referer();

			if ( $referrer === $home_url ) {
				$referrer = '/';
			} elseif ( 0 === strrpos( $referrer, $home_url ) ) {
				$referrer = trim( substr( $referrer, strlen( $home_url ) ), '/' );
			}

			if ( $referrer ) {
				$referrer_line = PHP_EOL . static::get_offset() . 'REFERRER: ' . $referrer;
			} else {
				$referrer_line = '';
			}
		}

		return $referrer_line;
	}

	protected static function get_log_trace_lines() : string {
		$offset          = static::get_offset();
		$rows            = array_reverse( debug_backtrace() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		$max_path_length = 0;
		$trace           = array();
		$last            = '';

		foreach ( $rows as $i => $r ) {
			$class = $r['class'] ?? '';
			$line  = (int) ( $r['line'] ?? 0 );
			$file  = isset( $r['file'] ) && is_string( $r['file'] ) ? $r['file'] : '';

			$this_log = static::class === $class;
			if ( $this_log || 'WP_Hook' === $class ) {
				if ( $this_log && 'write' === $r['function'] ) {
					$path            = static::get_formatted_path( $file, 30 );
					$last            = $path . ':' . $line;
					$max_path_length = strlen( $last );
				}

				unset( $rows[ $i ] );
				continue;
			}

			if ( $file ) {
				$path               = static::get_formatted_path( $file, 30 );
				$rows[ $i ]['path'] = $path . ':' . $line;
				$path_length        = strlen( $rows[ $i ]['path'] );
				if ( $path_length > $max_path_length ) {
					$max_path_length = $path_length;
				}
			} else {
				$rows[ $i ]['path'] = 'N/A';
				$rows[ $i ]['line'] = $line;
			}
		}

		if ( 'shutdown_action_hook' === $rows[0]['function'] && empty( $last ) ) {
			$error    = error_get_last();
			$file     = isset( $error['file'] ) && is_string( $error['file'] ) ? $error['file'] : 'N/A';
			$line     = (int) ( $error['line'] ?? 0 );
			$shutdown = sprintf( '%s:%s', $file, $line );

			return PHP_EOL . $offset . 'SHUTDOWN: ' . static::get_formatted_path( $shutdown, 76 );
		}

		$max_path_length ++;
		foreach ( $rows as $r ) {
			$path     = str_pad( $r['path'], $max_path_length );
			$class    = $r['class'] ?? '';
			$type     = $r['type'] ?? '';
			$function = $r['function'] ?? '';
			$format   = '%s%s %s%s%s(%s)';
			$hooks    = array( 'do_action', 'do_action_ref_array', 'apply_filters', 'apply_filters_ref_array' );
			$includes = array( 'require_once', 'require', 'include_once', 'include' );

			$args         = '';
			$path_or_hook = isset( $r['args'][0] ) && is_string( $r['args'][0] ) ? $r['args'][0] : '';
			if ( in_array( $function, $includes, true ) ) {
				$args = ' "' . static::get_formatted_path( $path_or_hook, 30 ) . '" ';
			} elseif ( in_array( $function, $hooks, true ) ) {
				$args = ' "' . $path_or_hook . '" ';
			}

			$trace[] = sprintf( $format, $offset, $path, $class, $type, $function, $args );
		}

		if ( $last ) {
			$trace[] = $offset . $last;
		}

		return PHP_EOL . $offset . 'TRACE:' . PHP_EOL . implode( PHP_EOL, $trace );
	}

	protected static function get_formatted_path( string $path, int $max_length = 0 ) : string {
		$home = array(
			'\\',
			trailingslashit( str_replace( '\\', '/', ABSPATH ) ),
			trailingslashit( str_replace( '\\', '/', dirname( WP_CONTENT_DIR ) ) ),
		);

		$path   = str_replace( $home, array( '/', '' ), $path );
		$length = strlen( $path );

		if ( $max_length && $length > $max_length ) {
			$path = '...' . substr( $path, - $max_length );
		}

		return $path;
	}

	protected static function get_formatted_message( string $message ) : string {
		$offset  = static::get_offset();
		$message = preg_replace( '/\R/', PHP_EOL . $offset, $message );

		return wordwrap( $message, 86, PHP_EOL . $offset );
	}

	protected static function has_duplicate( string $message, string $handle = 'wld' ) : bool {
		static $duplicates = array();

		$message = static::get_formatted_message( $message );
		if ( isset( $duplicates[ $message ] ) ) {
			return true;
		}

		$has = false !== strpos( static::get_log( $handle ), $message );
		if ( $has ) {
			$duplicates[ $message ] = true;
		}

		return $has;
	}

	protected static function is_need_qm_log() : bool {
		static $need_qm_log;

		if ( null === $need_qm_log ) {
			$need_qm_log =
				defined( 'QM_DISABLE_ERROR_HANDLER' ) &&
				true === QM_DISABLE_ERROR_HANDLER &&
				class_exists( 'QM_Collector_PHP_Errors' );
		}

		return $need_qm_log;
	}
}
