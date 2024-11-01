<?php // @codingStandardsIgnoreLine.
/**
 * This file to handle redis
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

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;

/**
 * Logs to a Redis key using rpush
 */
class RedisHandler extends AbstractProcessingHandler {
	/**
	 * Variable redis client
	 *
	 * @var string
	 */
	private $redisClient; // @codingStandardsIgnoreLine.
	/**
	 * Variable redis client
	 *
	 * @var string
	 */
	private $redisKey; // @codingStandardsIgnoreLine.
	/**
	 * Variable redis client
	 *
	 * @var string
	 */
	protected $capSize; // @codingStandardsIgnoreLine.

	/**
	 * Public constructor
	 *
	 * @param \Predis\Client|\Redis $redis   The redis instance .
	 * @param string                $key     The key name to push records to .
	 * @param int                   $level   The minimum logging level at which this handler will be triggered .
	 * @param bool                  $bubble  Whether the messages that are handled can bubble up the stack or not .
	 * @param int                   $capSize Number of entries to limit list size to .
	 * @throws \InvalidArgumentException .
	 */
	public function __construct( $redis, $key, $level = Logger::DEBUG, $bubble = true, $capSize = false ) { // @codingStandardsIgnoreLine.
		if ( ! ( ( $redis instanceof \Predis\Client ) || ( $redis instanceof \Redis ) ) ) {
			throw new \InvalidArgumentException( 'Predis\Client or Redis instance required' );
		}
		$this->redisClient = $redis; // @codingStandardsIgnoreLine.
		$this->redisKey    = $key; // @codingStandardsIgnoreLine.
		$this->capSize     = $capSize; // @codingStandardsIgnoreLine.
		parent::__construct( $level, $bubble );
	}

	/**
	 * To write record
	 *
	 * @param array $record .
	 */
	protected function write( array $record ) {
		if ( $this->capSize ) { // @codingStandardsIgnoreLine.
			$this->writeCapped( $record );
		} else {
			$this->redisClient->rpush( $this->redisKey, $record['formatted'] ); // @codingStandardsIgnoreLine.
		}
	}

	/**
	 * Write and cap the collection
	 * Writes the record to the redis list and caps its
	 *
	 * @param  array $record associative record array .
	 * @return void
	 */
	protected function writeCapped( array $record ) {
		if ( $this->redisClient instanceof \Redis ) { // @codingStandardsIgnoreLine.
			$this->redisClient->multi() // @codingStandardsIgnoreLine.
				->rpush( $this->redisKey, $record['formatted'] ) // @codingStandardsIgnoreLine.
				->ltrim( $this->redisKey, -$this->capSize, -1 ) // @codingStandardsIgnoreLine.
				->exec();
		} else {
			$redisKey = $this->redisKey; // @codingStandardsIgnoreLine.
			$capSize  = $this->capSize; // @codingStandardsIgnoreLine.
			$this->redisClient->transaction( // @codingStandardsIgnoreLine.
				function ( $tx ) use ( $record, $redisKey, $capSize ) { // @codingStandardsIgnoreLine.
					$tx->rpush( $redisKey, $record['formatted'] ); // @codingStandardsIgnoreLine.
					$tx->ltrim( $redisKey, -$capSize, -1 ); // @codingStandardsIgnoreLine.
				}
			);
		}
	}

	/**
	 * To get default format
	 */
	protected function getDefaultFormatter() {
		return new LineFormatter();
	}
}
