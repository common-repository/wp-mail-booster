<?php // @codingStandardsIgnoreLine
/**
 * This file Provides a read only stream that pumps data
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/composer
 * @version 2.0.0
 */

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * Provides a read only stream that pumps data from a PHP callable.
 *
 * When invoking the provided callable, the PumpStream will pass the amount of
 * data requested to read to the callable. The callable can choose to ignore
 * this value and return fewer or more bytes than requested. Any extra data
 * returned by the provided callable is buffered internally until drained using
 * the read() function of the PumpStream. The provided callable MUST return
 * false when there is no more data to read.
 */
class PumpStream implements StreamInterface {
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $source  .
	 */
	private $source;

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $size  .
	 */
	private $size;

	private $tellPos = 0;// @codingStandardsIgnoreLine

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $metadata  .
	 */
	private $metadata;

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $buffer  .
	 */
	private $buffer;

	/**
	 * This function is __construct.
	 *
	 * @param callable $source Source of the stream data. The callable MAY.
	 * @param array    $options   Stream options:
	 *                            - metadata: Hash of metadata to use with stream.
	 *                            - size: Size of the stream, if known.
	 */
	public function __construct( callable $source, array $options = [] ) {
		$this->source   = $source;
		$this->size     = isset( $options['size'] ) ? $options['size'] : null;
		$this->metadata = isset( $options['metadata'] ) ? $options['metadata'] : [];
		$this->buffer   = new BufferStream();
	}
	/**
	 * This function is __toString.
	 */
	public function __toString() {
		try {
			return copy_to_string( $this );
		} catch ( \Exception $e ) {
			return '';
		}
	}
	/**
	 * This function is close.
	 */
	public function close() {
		$this->detach();
	}
	/**
	 * This function is detach.
	 */
	public function detach() {
		$this->tellPos = false;// @codingStandardsIgnoreLine
		$this->source  = null;
	}
	/**
	 * This function is getSize.
	 */
	public function getSize() {
		return $this->size;
	}
	/**
	 * This function is tell.
	 */
	public function tell() {
		return $this->tellPos;// @codingStandardsIgnoreLine
	}
	/**
	 * This function is eof.
	 */
	public function eof() {
		return ! $this->source;
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
	 * @throws \RuntimeException On error.
	 */
	public function seek( $offset, $whence = SEEK_SET ) {
		throw new \RuntimeException( 'Cannot seek a PumpStream' );
	}
	/**
	 * This function is isWritable.
	 */
	public function isWritable() {
		return false;
	}
	/**
	 * This function is write.
	 *
	 * @param string $string passes parameter as string.
	 * @throws \RuntimeException On error.
	 */
	public function write( $string ) {
		throw new \RuntimeException( 'Cannot write to a PumpStream' );
	}
	/**
	 * This function is isReadable.
	 */
	public function isReadable() {
		return true;
	}
	/**
	 * This function is read.
	 *
	 * @param string $length passes parameter as length.
	 */
	public function read( $length ) {
		$data           = $this->buffer->read( $length );
		$readLen        = strlen( $data );// @codingStandardsIgnoreLine
		$this->tellPos += $readLen;// @codingStandardsIgnoreLine
		$remaining      = $length - $readLen;// @codingStandardsIgnoreLine

		if ( $remaining ) {
			$this->pump( $remaining );
			$data          .= $this->buffer->read( $remaining );
			$this->tellPos += strlen( $data ) - $readLen;// @codingStandardsIgnoreLine
		}

		return $data;
	}
	/**
	 * This function is getContents.
	 */
	public function getContents() {
		$result = '';
		while ( ! $this->eof() ) {
			$result .= $this->read( 1000000 );
		}

		return $result;
	}
	/**
	 * This function is used to get meta data.
	 *
	 * @param null $key passes parameter as key.
	 */
	public function getMetadata( $key = null ) {
		if ( ! $key ) {
			return $this->metadata;
		}

		return isset( $this->metadata[ $key ] ) ? $this->metadata[ $key ] : null;
	}
	/**
	 * This function is pump.
	 *
	 * @param null $length passes parameter as length.
	 */
	private function pump( $length ) {
		if ( $this->source ) {
			do {
				$data = call_user_func( $this->source, $length );
				if ( false === $data || null === $data ) {
					$this->source = null;
					return;
				}
				$this->buffer->write( $data );
				$length -= strlen( $data );
			} while ( $length > 0 );
		}
	}
}
