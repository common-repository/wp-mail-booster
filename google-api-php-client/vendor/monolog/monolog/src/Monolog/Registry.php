<?php //@codingStandardsIgnoreLine
/**
 * This file is Registry.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace Monolog;

use InvalidArgumentException;

/**
 * Monolog log registry
 *
 * Allows to get `Logger` instances in the global scope
 * via static method calls on this class.
 */
class Registry {

	/**
	 * List of all loggers in the registry (by named indexes)
	 *
	 * @var Logger[]
	 */
	private static $loggers = array();

	/**
	 * Adds new logging channel to the registry
	 *
	 * @param  Logger      $logger    Instance of the logging channel.
	 * @param  string|null $name      Name of the logging channel ($logger->getName() by default).
	 * @param  bool        $overwrite Overwrite instance in the registry if the given name already exists?.
	 * @throws InvalidArgumentException If $overwrite set to false and named Logger instance already exists.
	 */
	public static function addLogger( Logger $logger, $name = null, $overwrite = false ) {//@codingStandardsIgnoreLine
		$name = $name ?: $logger->getName();

		if ( isset( self::$loggers[ $name ] ) && ! $overwrite ) {
			throw new InvalidArgumentException( 'Logger with the given name already exists' );
		}

		self::$loggers[ $name ] = $logger;
	}

	/**
	 * Checks if such logging channel exists by name or instance
	 *
	 * @param string|Logger $logger Name or logger instance.
	 */
	public static function hasLogger( $logger ) {// @codingStandardsIgnoreLine
		if ( $logger instanceof Logger ) {
			$index = array_search( $logger, self::$loggers, true );

			return false !== $index;
		} else {
			return isset( self::$loggers[ $logger ] );
		}
	}

	/**
	 * Removes instance from registry by name or instance
	 *
	 * @param string|Logger $logger Name or logger instance.
	 */
	public static function removeLogger( $logger ) {// @codingStandardsIgnoreLine
		if ( $logger instanceof Logger ) {
			if ( false !== ( $idx = array_search( $logger, self::$loggers, true ) ) ) {// @codingStandardsIgnoreLine
				unset( self::$loggers[ $idx ] );
			}
		} else {
			unset( self::$loggers[ $logger ] );
		}
	}

	/**
	 * Clears the registry
	 */
	public static function clear() {
		self::$loggers = array();
	}

	/**
	 * Gets Logger instance from the registry
	 *
	 * @param  string $name Name of the requested Logger instance.
	 * @throws InvalidArgumentException If named Logger instance is not in the registry.
	 * @return Logger                    Requested instance of Logger.
	 */
	public static function getInstance( $name ) {// @codingStandardsIgnoreLine
		if ( ! isset( self::$loggers[ $name ] ) ) {
			throw new InvalidArgumentException( sprintf( 'Requested "%s" logger instance is not in the registry', $name ) );
		}

		return self::$loggers[ $name ];
	}

	/**
	 * Gets Logger instance from the registry via static method call
	 *
	 * @param  string $name      Name of the requested Logger instance.
	 * @param  array  $arguments Arguments passed to static method call.
	 * @throws InvalidArgumentException If named Logger instance is not in the registry.
	 * @return Logger                    Requested instance of Logger
	 */
	public static function __callStatic( $name, $arguments ) {
		return self::getInstance( $name );
	}
}
