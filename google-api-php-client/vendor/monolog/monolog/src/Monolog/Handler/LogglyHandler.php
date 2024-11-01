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
use Monolog\Formatter\LogglyFormatter;

/**
 * Sends errors to Loggly.
 */
class LogglyHandler extends AbstractProcessingHandler {

	const HOST            = 'logs-01.loggly.com';
	const ENDPOINT_SINGLE = 'inputs';
	const ENDPOINT_BATCH  = 'bulk';

		/**
		 * The version of the plugin.
		 *
		 * @var string
		 */
	protected $token;
	/**
	 * The version of the plugin.
	 *
	 * @var string
	 */
	protected $tag = array();
	/**
	 * This function is __construct.
	 *
	 * @param string $token .
	 * @param string $level .
	 * @param string $bubble .
	 * @var string
	 * @throws \LogicException .
	 */
	public function __construct( $token, $level = Logger::DEBUG, $bubble = true ) {
		if ( ! extension_loaded( 'curl' ) ) {
			throw new \LogicException( 'The curl extension is needed to use the LogglyHandler' );
		}

		$this->token = $token;

		parent::__construct( $level, $bubble );
	}
	/**
	 * This function is __construct.
	 *
	 * @param string $tag .
	 */
	public function setTag( $tag ) {
		$tag       = ! empty( $tag ) ? $tag : array();
		$this->tag = is_array( $tag ) ? $tag : array( $tag );
	}
	/**
	 * This function is __construct.
	 *
	 * @param string $tag .
	 */
	public function addTag( $tag ) {
		if ( ! empty( $tag ) ) {
			$tag       = is_array( $tag ) ? $tag : array( $tag );
			$this->tag = array_unique( array_merge( $this->tag, $tag ) );
		}
	}
	/**
	 * This function is __construct.
	 *
	 * @param  array $record .
	 */
	protected function write( array $record ) {
		$this->send( $record['formatted'], self::ENDPOINT_SINGLE );
	}
	/**
	 * This function is handleBatch.
	 *
	 * @param  array $records .
	 */
	public function handleBatch( array $records ) {
		$level = $this->level;

		$records = array_filter(
			$records, function ( $record ) use ( $level ) {
				return ( $record['level'] >= $level );
			}
		);

		if ( $records ) {
			$this->send( $this->getFormatter()->formatBatch( $records ), self::ENDPOINT_BATCH );
		}
	}
	/**
	 * This function is send.
	 *
	 * @param string $data .
	 * @param string $endpoint .
	 */
	protected function send( $data, $endpoint ) {
		$url = sprintf( 'https://%s/%s/%s/', self::HOST, $endpoint, $this->token );

		$headers = array( 'Content-Type: application/json' );

		if ( ! empty( $this->tag ) ) {
			$headers[] = 'X-LOGGLY-TAG: ' . implode( ',', $this->tag );
		}

		$ch = curl_init();// @codingStandardsIgnoreLine.

		curl_setopt( $ch, CURLOPT_URL, $url );// @codingStandardsIgnoreLine.
		curl_setopt( $ch, CURLOPT_POST, true );// @codingStandardsIgnoreLine.
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );// @codingStandardsIgnoreLine.
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );// @codingStandardsIgnoreLine.
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );// @codingStandardsIgnoreLine.

		Curl\Util::execute( $ch );
	}
	/**
	 * This function is send.
	 */
	protected function getDefaultFormatter() {
		return new LogglyFormatter();
	}
}
