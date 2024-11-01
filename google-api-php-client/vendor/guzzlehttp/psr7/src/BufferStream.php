<?php // @codingStandardsIgnoreLine
/**
 * This file  Provides a buffer stream.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/composer
 * @version 2.0.0
 */

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * Provides a buffer stream that can be written to to fill a buffer, and read
 * from to remove bytes from the buffer.
 *
 * This stream returns a "hwm" metadata value that tells upstream consumers
 * what the configured high water mark of the stream is, or the maximum
 * preferred size of the buffer.
 */
class BufferStream implements StreamInterface {
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $hwm  .
	 */
	private $hwm;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $buffer  .
	 */
	private $buffer = '';

	/**
	 * This function is __construct.
	 *
	 * @param string $hwm passes parameter as hwm.
	 */
	public function __construct( $hwm = 16384 ) {
		$this->hwm = $hwm;
	}
	/**
	 * This function is __toString.
	 */
	public function __toString() {
		return $this->getContents();
	}
	/**
	 * This function is getContents.
	 */
	public function getContents() {
		$buffer       = $this->buffer;
		$this->buffer = '';

		return $buffer;
	}
	/**
	 * This function is close.
	 */
	public function close() {
		$this->buffer = '';
	}
	/**
	 * This function is detach.
	 */
	public function detach() {
		$this->close();
	}
	/**
	 * This function is getSize.
	 */
	public function getSize() {
		return strlen( $this->buffer );
	}
	/**
	 * This function is isReadable.
	 */
	public function isReadable() {
		return true;
	}
	/**
	 * This function is isWritable.
	 */
	public function isWritable() {
		return true;
	}
	/**
	 * This function is isSeekable.
	 */
	public function isSeekable() {
		return false;
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
	 * @throws \RuntimeException .
	 */
	public function seek( $offset, $whence = SEEK_SET ) {
		throw new \RuntimeException( 'Cannot seek a BufferStream' );
	}
	/**
	 * This function is eof.
	 */
	public function eof() {
		return strlen( $this->buffer ) === 0;
	}
	/**
	 * This function is tell.
	 *
	 * @throws \RuntimeException .
	 */
	public function tell() {
		throw new \RuntimeException( 'Cannot determine the position of a BufferStream' );
	}

	/**
	 * Reads data from the buffer.
	 *
	 * @param string $length passes parameter as length.
	 */
	public function read( $length ) {
		$currentLength = strlen( $this->buffer );// @codingStandardsIgnoreLine.

		if ( $length >= $currentLength ) {// @codingStandardsIgnoreLine.
			// No need to slice the buffer because we don't have enough data.
			$result       = $this->buffer;
			$this->buffer = '';
		} else {
			// Slice up the result to provide a subset of the buffer.
			$result       = substr( $this->buffer, 0, $length );
			$this->buffer = substr( $this->buffer, $length );
		}

		return $result;
	}

	/**
	 * Writes data to the buffer.
	 *
	 * @param string $string passes parameter as string.
	 */
	public function write( $string ) {
		$this->buffer .= $string;

		// TODO: What should happen here?
		if ( strlen( $this->buffer ) >= $this->hwm ) {
			return false;
		}

		return strlen( $string );
	}
	/**
	 * This function is used to get meta data.
	 *
	 * @param null $key passes parameter as key.
	 */
	public function getMetadata( $key = null ) {
		if ( $key == 'hwm' ) {// @codingStandardsIgnoreLine
			return $this->hwm;
		}

		return $key ? null : [];
	}
}
