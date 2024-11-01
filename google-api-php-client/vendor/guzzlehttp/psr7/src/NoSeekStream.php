<?php // @codingStandardsIgnoreLine
/**
 * This file for Stream decorator that prevents a stream from being seeked.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/composer
 * @version 2.0.0
 */

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * Stream decorator that prevents a stream from being seeked
 */
class NoSeekStream implements StreamInterface {

	use StreamDecoratorTrait;
	/**
	 * This function is seek.
	 *
	 * @param string $offset passes parameter as offset.
	 * @param string $whence passes parameter as whence.
	 * @throws \RuntimeException On error.
	 */
	public function seek( $offset, $whence = SEEK_SET ) {
		throw new \RuntimeException( 'Cannot seek a NoSeekStream' );
	}
	/**
	 * This function is isSeekable.
	 */
	public function isSeekable() {
		return false;
	}
}
