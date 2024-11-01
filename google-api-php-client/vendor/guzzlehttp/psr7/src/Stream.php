<?php // @codingStandardsIgnoreLine
/**
 * This file to implement PHP stream
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/composer
 * @version 2.0.0
 */

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * PHP stream implementation.
 *
 * @var $stream
 */
class Stream implements StreamInterface {
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $stream  .
	 */
	private $stream;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $size  .
	 */
	private $size;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $seekable  .
	 */
	private $seekable;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $readable  .
	 */
	private $readable;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $writable  .
	 */
	private $writable;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $uri  .
	 */
	private $uri;
	private $customMetadata;// @codingStandardsIgnoreLine

	private static $readWriteHash = [// @codingStandardsIgnoreLine
		'read'  => [
			'r'   => true,
			'w+'  => true,
			'r+'  => true,
			'x+'  => true,
			'c+'  => true,
			'rb'  => true,
			'w+b' => true,
			'r+b' => true,
			'x+b' => true,
			'c+b' => true,
			'rt'  => true,
			'w+t' => true,
			'r+t' => true,
			'x+t' => true,
			'c+t' => true,
			'a+'  => true,
		],
		'write' => [
			'w'   => true,
			'w+'  => true,
			'rw'  => true,
			'r+'  => true,
			'x+'  => true,
			'c+'  => true,
			'wb'  => true,
			'w+b' => true,
			'r+b' => true,
			'x+b' => true,
			'c+b' => true,
			'w+t' => true,
			'r+t' => true,
			'x+t' => true,
			'c+t' => true,
			'a'   => true,
			'a+'  => true,
		],
	];

	/**
	 * This constructor accepts an associative array of options.
	 *
	 * - size: (int) If a read stream would otherwise have an indeterminate
	 *   size, but the size is known due to foreknowledge, then you can
	 *   provide that size, in bytes.
	 * - metadata: (array) Any additional metadata to return when the metadata
	 *   of the stream is accessed.
	 *
	 * @param resource $stream  Stream resource to wrap.
	 * @param array    $options Associative array of options.
	 *
	 * @throws \InvalidArgumentException If the stream is not a stream resource.
	 */
	public function __construct( $stream, $options = [] ) {
		if ( ! is_resource( $stream ) ) {
			throw new \InvalidArgumentException( 'Stream must be a resource' );
		}

		if ( isset( $options['size'] ) ) {
			$this->size = $options['size'];
		}

		$this->customMetadata = isset( $options['metadata'] )// @codingStandardsIgnoreLine
			? $options['metadata']
			: [];

		$this->stream   = $stream;
		$meta           = stream_get_meta_data( $this->stream );
		$this->seekable = $meta['seekable'];
		$this->readable = isset( self::$readWriteHash['read'][ $meta['mode'] ] );// @codingStandardsIgnoreLine
		$this->writable = isset( self::$readWriteHash['write'][ $meta['mode'] ] );// @codingStandardsIgnoreLine
		$this->uri      = $this->getMetadata( 'uri' );
	}
	/**
	 * This function is __get.
	 *
	 * @param string $name passes parameter as name.
	 * @throws \RuntimeException On error.
	 * @throws \BadMethodCallException On error.
	 */
	public function __get( $name ) {
		if ( 'stream' == $name ) {// WPCS: Loose comparison ok.
			throw new \RuntimeException( 'The stream is detached' );
		}

		throw new \BadMethodCallException( 'No value for ' . $name );
	}

	/**
	 * Closes the stream when the destructed
	 */
	public function __destruct() {
		$this->close();
	}
	/**
	 * This function is __toString.
	 */
	public function __toString() {
		try {
			$this->seek( 0 );
			return (string) stream_get_contents( $this->stream );
		} catch ( \Exception $e ) {
			return '';
		}
	}
	/**
	 * This function is getContents.
	 *
	 * @throws \RuntimeException On error.
	 */
	public function getContents() {
		$contents = stream_get_contents( $this->stream );

		if ( false === $contents ) {
			throw new \RuntimeException( 'Unable to read stream contents' );
		}

		return $contents;
	}
	/**
	 * This function is close.
	 */
	public function close() {
		if ( isset( $this->stream ) ) {
			if ( is_resource( $this->stream ) ) {
				fclose( $this->stream );// @codingStandardsIgnoreLine
			}
			$this->detach();
		}
	}
	/**
	 * This function is detach.
	 */
	public function detach() {
		if ( ! isset( $this->stream ) ) {
			return null;
		}

		$result = $this->stream;
		unset( $this->stream );
		$this->size     = $this->uri = null;// @codingStandardsIgnoreLine
		$this->readable = $this->writable = $this->seekable = false;// @codingStandardsIgnoreLine

		return $result;
	}
	/**
	 * This function is used to get the size.
	 */
	public function getSize() {
		if ( $this->size !== null ) {// @codingStandardsIgnoreLine
			return $this->size;
		}

		if ( ! isset( $this->stream ) ) {
			return null;
		}

		// Clear the stat cache if the stream has a URI.
		if ( $this->uri ) {
			clearstatcache( true, $this->uri );
		}

		$stats = fstat( $this->stream );
		if ( isset( $stats['size'] ) ) {
			$this->size = $stats['size'];
			return $this->size;
		}

		return null;
	}
	/**
	 * This function is readable.
	 */
	public function isReadable() {
		return $this->readable;
	}
	/**
	 * This function is isWritable.
	 */
	public function isWritable() {
		return $this->writable;
	}
	/**
	 * This function is isSeekable.
	 */
	public function isSeekable() {
		return $this->seekable;
	}
	/**
	 * This function is eof.
	 */
	public function eof() {
		return ! $this->stream || feof( $this->stream );
	}
	/**
	 * This function is tell.
	 *
	 * @throws \RuntimeException On error.
	 */
	public function tell() {
		$result = ftell( $this->stream );

		if ( false === $result ) {
			throw new \RuntimeException( 'Unable to determine stream position' );
		}

		return $result;
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
		if ( ! $this->seekable ) {
			throw new \RuntimeException( 'Stream is not seekable' );
		} elseif ( fseek( $this->stream, $offset, $whence ) === -1 ) {
			throw new \RuntimeException(
				'Unable to seek to stream position '
				. $offset . ' with whence ' . var_export( $whence, true )// @codingStandardsIgnoreLine
			);
		}
	}
	/**
	 * This function is read.
	 *
	 * @param string $length passes parameter as length.
	 * @throws \RuntimeException On error.
	 */
	public function read( $length ) {
		if ( ! $this->readable ) {
			throw new \RuntimeException( 'Cannot read from non-readable stream' );
		}
		if ( $length < 0 ) {
			throw new \RuntimeException( 'Length parameter cannot be negative' );
		}

		if ( 0 === $length ) {
			return '';
		}

		$string = fread( $this->stream, $length );// @codingStandardsIgnoreLine
		if ( false === $string ) {
			throw new \RuntimeException( 'Unable to read from stream' );
		}

		return $string;
	}
	/**
	 * This function is read.
	 *
	 * @param string $string passes parameter as string.
	 * @throws \RuntimeException On error.
	 */
	public function write( $string ) {
		if ( ! $this->writable ) {
			throw new \RuntimeException( 'Cannot write to a non-writable stream' );
		}

		// We can't know the size after writing anything.
		$this->size = null;
		$result     = fwrite( $this->stream, $string );// @codingStandardsIgnoreLine

		if ( false === $result ) {
			throw new \RuntimeException( 'Unable to write to stream' );
		}

		return $result;
	}
	/**
	 * This function is getMetadata.
	 *
	 * @param null $key passes parameter as key.
	 */
	public function getMetadata( $key = null ) {
		if ( ! isset( $this->stream ) ) {
			return $key ? null : [];
		} elseif ( ! $key ) {
			return $this->customMetadata + stream_get_meta_data( $this->stream );// @codingStandardsIgnoreLine
		} elseif ( isset( $this->customMetadata[ $key ] ) ) {// @codingStandardsIgnoreLine
			return $this->customMetadata[ $key ];// @codingStandardsIgnoreLine
		}

		$meta = stream_get_meta_data( $this->stream );

		return isset( $meta[ $key ] ) ? $meta[ $key ] : null;
	}
}
