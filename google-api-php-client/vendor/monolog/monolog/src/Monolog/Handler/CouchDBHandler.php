<?php // @codingStandardsIgnoreLine.
/**
 * This file is part of the Monolog package.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/handler
 * @version 2.0.0
 */
namespace Monolog\Handler;

use Monolog\Formatter\JsonFormatter;
use Monolog\Logger;

/**
 * CouchDB handler
 */
class CouchDBHandler extends AbstractProcessingHandler {
	/**
	 * The version of this plugin.
	 *
	 * @var $options.
	 */
	private $options;
	/**
	 * This function is __construct.
	 *
	 * @param array $options .
	 * @param array $level .
	 * @param array $bubble .
	 * {@inheritdoc}.
	 */
	public function __construct( array $options = array(), $level = Logger::DEBUG, $bubble = true ) {
		$this->options = array_merge(
			array(
				'host'     => 'localhost',
				'port'     => 5984,
				'dbname'   => 'logger',
				'username' => null,
				'password' => null,
			), $options
		);

		parent::__construct( $level, $bubble );
	}

	/**
	 * This function is __construct.
	 *
	 * @param array $record .
	 * {@inheritdoc}.
	 * @throws \RuntimeException .
	 */
	protected function write( array $record ) {
		$basicAuth = null;// @codingStandardsIgnoreLine.
		if ( $this->options['username'] ) {
			$basicAuth = sprintf( '%s:%s@', $this->options['username'], $this->options['password'] );// @codingStandardsIgnoreLine.
		}

		$url     = 'http://' . $basicAuth . $this->options['host'] . ':' . $this->options['port'] . '/' . $this->options['dbname'];// @codingStandardsIgnoreLine.
		$context = stream_context_create(
			array(
				'http' => array(
					'method'        => 'POST',
					'content'       => $record['formatted'],
					'ignore_errors' => true,
					'max_redirects' => 0,
					'header'        => 'Content-type: application/json',
				),
			)
		);

		if ( false === @file_get_contents( $url, null, $context ) ) {// @codingStandardsIgnoreLine.
			throw new \RuntimeException( sprintf( 'Could not connect to %s', $url ) );
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function getDefaultFormatter() {
		return new JsonFormatter( JsonFormatter::BATCH_MODE_JSON, false );
	}
}
