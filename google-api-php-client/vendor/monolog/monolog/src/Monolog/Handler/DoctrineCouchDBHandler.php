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
use Doctrine\CouchDB\CouchDBClient;

/**
 * CouchDB handler for Doctrine CouchDB ODM
 */
class DoctrineCouchDBHandler extends AbstractProcessingHandler {
	/**
	 * The version of this plugin.
	 *
	 * @var bool
	 */
	private $client;
	/**
	 * This function is __construct.
	 *
	 * @param CouchDBClient $client .
	 * @param string        $level .
	 * @param string        $bubble .
	 * @var bool
	 */
	public function __construct( CouchDBClient $client, $level = Logger::DEBUG, $bubble = true ) {
		$this->client = $client;
		parent::__construct( $level, $bubble );
	}

	/**
	 * This function is __construct.
	 *
	 * @param array $record .
	 * {@inheritDoc}.
	 */
	protected function write( array $record ) {
		$this->client->postDocument( $record['formatted'] );
	}
	/**
	 * This function is __construct.
	 *
	 * {@inheritDoc}.
	 */
	protected function getDefaultFormatter() {
		return new NormalizerFormatter();
	}
}
