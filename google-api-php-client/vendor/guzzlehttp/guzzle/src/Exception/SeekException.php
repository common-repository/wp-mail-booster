<?php // @codingStandardsIgnoreLine
/**
 * This file for exception thrown when seek .
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace GuzzleHttp\Exception;

use Psr\Http\Message\StreamInterface;

/**
 * Exception thrown when a seek fails on a stream.
 */
class SeekException extends \RuntimeException implements GuzzleException {

	/**
	 * Variable for stream.
	 *
	 * @var string
	 */
	private $stream;
	/**
	 * PPublic construstor
	 *
	 * @param StreamInterface $stream .
	 * @param int             $pos .
	 * @param string          $msg .
	 */
	public function __construct( StreamInterface $stream, $pos = 0, $msg = '' ) {
		$this->stream = $stream;
		$msg          = $msg ?: 'Could not seek the stream to position ' . $pos;
		parent::__construct( $msg );
	}

	/**
	 * Function to get stream
	 *
	 * @return StreamInterface
	 */
	public function getStream() {
		return $this->stream;
	}
}
