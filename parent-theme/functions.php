<?php
defined( 'WLD_VER' ) || define( 'WLD_VER', wp_get_theme( 'parent-theme' )->get( 'Version' ) );
defined( 'WLD_NEVER' ) || define( 'WLD_NEVER', false );
defined( 'WLD_DISABLE_COMMENTS' ) || define( 'WLD_DISABLE_COMMENTS', true );
defined( 'DISALLOW_FILE_EDIT' ) || define( 'DISALLOW_FILE_EDIT', true );

locate_template( 'inc/helpers.php', true );
locate_template( 'inc/deprecated.php', true );

spl_autoload_register( 'wld_class_file_autoloader' );

WLD_Log::init();

wld_init();
