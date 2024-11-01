<?php
/**
 * This file used to load the files .
 *
 * @author  Tech Banker
 * @package wp-mail-booster/php-mailer
 * @version 2.0.0
 */

/**
 * PHPMailer SPL autoloader.
 *
 * @param string $classname The name of the class to load .
 */
function phpmailer_autoload( $classname ) {
	// Can't use __DIR__ as it's only in PHP 5.3+.
	$filename = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'class.' . strtolower( $classname ) . '.php';
	if ( is_readable( $filename ) ) {
			require $filename;
	}
}

if ( version_compare( PHP_VERSION, '5.1.2', '>=' ) ) {
	// SPL autoloading was introduced in PHP 5.1.2.
	if ( version_compare( PHP_VERSION, '5.3.0', '>=' ) ) {
		spl_autoload_register( 'phpmailer_autoload', true, true );
	} else {
		spl_autoload_register( 'phpmailer_autoload' );
	}
} else {
	/**
	 * Fall back to traditional autoload for old PHP versions
	 *
	 * @param string $classname The name of the class to load .
	 */
	function spl_autoload_register( $classname ) {
		phpmailer_autoload( $classname );
	}
}
