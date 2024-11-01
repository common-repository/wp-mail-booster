<?php // @codingStandardsIgnoreLine.
/**
 * This file used to Proxies log messages to an existing PSR-3 compliant logger.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Proxies log messages to an existing PSR-3 compliant logger.
 */
class PsrHandler extends AbstractHandler {

	/**
	 * PSR-3 compliant logger
	 *
	 * @var LoggerInterface
	 */
	protected $logger;

	/**
	 * Public constructor
	 *
	 * @param LoggerInterface $logger The underlying PSR-3 compliant logger to which messages will be proxied .
	 * @param int             $level  The minimum logging level at which this handler will be triggered .
	 * @param Boolean         $bubble Whether the messages that are handled can bubble up the stack or not .
	 */
	public function __construct( LoggerInterface $logger, $level = Logger::DEBUG, $bubble = true ) {
		parent::__construct( $level, $bubble );

		$this->logger = $logger;
	}

	/**
	 * Function o handle record
	 *
	 * @param array $record .
	 */
	public function handle( array $record ) {
		if ( ! $this->isHandling( $record ) ) {
			return false;
		}

		$this->logger->log( strtolower( $record['level_name'] ), $record['message'], $record['context'] );

		return false === $this->bubble;
	}
}
