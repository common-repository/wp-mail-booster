<?php // @codingStandardsIgnoreLine
/**
 * This file for Stream decorator that begins dropping data.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/composer
 * @version 2.0.0
 */

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * Stream decorator that begins dropping data once the size of the underlying
 * stream becomes too full.
 */
class DroppingStream implements StreamInterface {

	use StreamDecoratorTrait;

	private $maxLength;// @codingStandardsIgnoreLine

	/**
	 * This function is __construct.
	 *
	 * @param StreamInterface $stream    Underlying stream to decorate.
	 * @param int             $maxLength Maximum size before dropping data.
	 */
	public function __construct( StreamInterface $stream, $maxLength ) {// @codingStandardsIgnoreLine
		$this->stream    = $stream;
		$this->maxLength = $maxLength;// @codingStandardsIgnoreLine
	}
	/**
	 * This function is write.
	 *
	 * @param string $string passes parameter as string.
	 */
	public function write( $string ) {
		$diff = $this->maxLength - $this->stream->getSize();// @codingStandardsIgnoreLine

		// Begin returning 0 when the underlying stream is too large.
		if ( $diff <= 0 ) {
			return 0;
		}

		// Write the stream or a subset of the stream if needed.
		if ( strlen( $string ) < $diff ) {
			return $this->stream->write( $string );
		}

		return $this->stream->write( substr( $string, 0, $diff ) );
	}
}
