<?php // @codingStandardsIgnoreLine
/**
 * This file for Stream decorator that can cache previously
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/composer
 * @version 2.0.0
 */

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * Stream decorator that can cache previously read bytes from a sequentially
 * read stream.
 */
class CachingStream implements StreamInterface {

	use StreamDecoratorTrait;
	private $remoteStream;// @codingStandardsIgnoreLine

	private $skipReadBytes = 0;// @codingStandardsIgnoreLine

	/**
	 * We will treat the buffer object as the body of the stream
	 *
	 * @param StreamInterface $stream Stream to cache.
	 * @param StreamInterface $target Optionally specify where data is cached.
	 */
	public function __construct(
		StreamInterface $stream,
		StreamInterface $target = null
	) {
		$this->remoteStream = $stream;// @codingStandardsIgnoreLine
		$this->stream       = $target ?: new Stream( fopen( 'php://temp', 'r+' ) );// @codingStandardsIgnoreLine
	}
	/**
	 * This function is getSize.
	 */
	public function getSize() {
		return max( $this->stream->getSize(), $this->remoteStream->getSize() );// @codingStandardsIgnoreLine
	}
	/**
	 * This function is rewind.
	 */
	public function rewind() {
		$this->seek( 0 );
	}
	/**
	 * This function is seek.
	 *
	 * @param string $offset passes parameter as offset.
	 * @param string $whence passes parameter as whence.
	 * @throws \InvalidArgumentException .
	 */
	public function seek( $offset, $whence = SEEK_SET ) {
		if ( $whence == SEEK_SET ) {// @codingStandardsIgnoreLine
			$byte = $offset;
		} elseif ( $whence == SEEK_CUR ) {// @codingStandardsIgnoreLine
			$byte = $offset + $this->tell();
		} elseif ( $whence == SEEK_END ) {// @codingStandardsIgnoreLine
			$size = $this->remoteStream->getSize();// @codingStandardsIgnoreLine
			if ( $size === null ) {// @codingStandardsIgnoreLine
				$size = $this->cacheEntireStream();
			}
			$byte = $size + $offset;
		} else {
			throw new \InvalidArgumentException( 'Invalid whence' );
		}

		$diff = $byte - $this->stream->getSize();

		if ( $diff > 0 ) {
			// Read the remoteStream until we have read in at least the amount
			// of bytes requested, or we reach the end of the file.
			while ( $diff > 0 && ! $this->remoteStream->eof() ) {// @codingStandardsIgnoreLine
				$this->read( $diff );
				$diff = $byte - $this->stream->getSize();
			}
		} else {
			// We can just do a normal seek since we've already seen this byte.
			$this->stream->seek( $byte );
		}
	}
	/**
	 * This function is read.
	 *
	 * @param string $length passes parameter as length.
	 */
	public function read( $length ) {
		// Perform a regular read on any previously read data from the buffer.
		$data      = $this->stream->read( $length );
		$remaining = $length - strlen( $data );

		// More data was requested so read from the remote stream.
		if ( $remaining ) {
			// If data was written to the buffer in a position that would have
			// been filled from the remote stream, then we must skip bytes on
			// the remote stream to emulate overwriting bytes from that
			// position. This mimics the behavior of other PHP stream wrappers.
			$remoteData = $this->remoteStream->read(// @codingStandardsIgnoreLine
				$remaining + $this->skipReadBytes// @codingStandardsIgnoreLine
			);

			if ( $this->skipReadBytes ) {// @codingStandardsIgnoreLine
				$len                 = strlen( $remoteData );// @codingStandardsIgnoreLine
				$remoteData          = substr( $remoteData, $this->skipReadBytes );// @codingStandardsIgnoreLine
				$this->skipReadBytes = max( 0, $this->skipReadBytes - $len );// @codingStandardsIgnoreLine
			}

			$data .= $remoteData;// @codingStandardsIgnoreLine
			$this->stream->write( $remoteData );// @codingStandardsIgnoreLine
		}

		return $data;
	}
	/**
	 * This function is write.
	 *
	 * @param string $string passes parameter as string.
	 */
	public function write( $string ) {
		// When appending to the end of the currently read stream, you'll want
		// to skip bytes from being read from the remote stream to emulate
		// other stream wrappers. Basically replacing bytes of data of a fixed
		// length.
		$overflow = ( strlen( $string ) + $this->tell() ) - $this->remoteStream->tell();// @codingStandardsIgnoreLine
		if ( $overflow > 0 ) {
			$this->skipReadBytes += $overflow;// @codingStandardsIgnoreLine
		}

		return $this->stream->write( $string );
	}
	/**
	 * This function is eof.
	 */
	public function eof() {
		return $this->stream->eof() && $this->remoteStream->eof();// @codingStandardsIgnoreLine
	}

	/**
	 * Close both the remote stream and buffer stream
	 */
	public function close() {
		$this->remoteStream->close() && $this->stream->close();// @codingStandardsIgnoreLine
	}
	/**
	 * This function is cacheEntireStream.
	 */
	private function cacheEntireStream() {
		$target = new FnStream( [ 'write' => 'strlen' ] );
		copy_to_stream( $this, $target );

		return $this->tell();
	}
}
