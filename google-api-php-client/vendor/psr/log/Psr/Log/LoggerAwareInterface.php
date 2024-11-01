<?php // @codingStandardsIgnoreLine
/**
 * This file used to Describes a logger-aware instance.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/
 * @version 2.0.0
 */

namespace Psr\Log;

/**
 * Describes a logger-aware instance.
 */
interface LoggerAwareInterface {

	/**
	 * Sets a logger instance on the object.
	 *
	 * @param LoggerInterface $logger .
	 *
	 * @return void
	 */
	public function setLogger( LoggerInterface $logger); // @codingStandardsIgnoreLine
}
