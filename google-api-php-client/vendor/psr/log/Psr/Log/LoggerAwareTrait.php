<?php // @codingStandardsIgnoreLine
/**
 * This file used for Implementation of LoggerAwareInterface.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/
 * @version 2.0.0
 */

namespace Psr\Log;

/**
 * Basic Implementation of LoggerAwareInterface.
 */
trait LoggerAwareTrait {

	/**
	 * The logger instance.
	 *
	 * @var LoggerInterface
	 */
	protected $logger;

	/**
	 * Sets a logger.
	 *
	 * @param LoggerInterface $logger .
	 */
	public function setLogger( LoggerInterface $logger ) { // @codingStandardsIgnoreLine
		$this->logger = $logger;
	}
}
