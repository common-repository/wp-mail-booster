<?php // @codingStandardsIgnoreLine.
/**
 * This file is part of the Monolog package.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/handler
 * @version 2.0.0
 */

namespace Monolog\Handler;

use Monolog\Logger;
use Monolog\Formatter\NormalizerFormatter;

/**
 * Logs to a MongoDB database.
 */
class MongoDBHandler extends AbstractProcessingHandler {

	protected $mongoCollection;// @codingStandardsIgnoreLine.
	/**
	 * This function is __construct.
	 *
	 * @param string $mongo .
	 * @param string $database .
	 * @param string $collection .
	 * @param array  $level .
	 * @param array  $bubble .
	 * @throws \InvalidArgumentException .
	 */
	public function __construct( $mongo, $database, $collection, $level = Logger::DEBUG, $bubble = true ) {
		if ( ! ( $mongo instanceof \MongoClient || $mongo instanceof \Mongo || $mongo instanceof \MongoDB\Client ) ) {
			throw new \InvalidArgumentException( 'MongoClient, Mongo or MongoDB\Client instance required' );
		}

		$this->mongoCollection = $mongo->selectCollection( $database, $collection );// @codingStandardsIgnoreLine.

		parent::__construct( $level, $bubble );
	}
	/**
	 * This function is write.
	 *
	 * @param array $record .
	 */
	protected function write( array $record ) {
		if ( $this->mongoCollection instanceof \MongoDB\Collection ) {// @codingStandardsIgnoreLine.
			$this->mongoCollection->insertOne( $record['formatted'] );// @codingStandardsIgnoreLine.
		} else {
			$this->mongoCollection->save( $record['formatted'] );// @codingStandardsIgnoreLine.
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function getDefaultFormatter() {
		return new NormalizerFormatter();
	}
}
