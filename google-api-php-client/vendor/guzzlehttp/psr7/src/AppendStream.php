<?php // @codingStandardsIgnoreLine
/**
 * This file Reads from multiple streams, one after the other.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/composer
 * @version 2.0.0
 */

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * Reads from multiple streams, one after the other.
 *
 * This is a read-only stream decorator.
 */
class AppendStream implements StreamInterface {

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $streams  .
	 */
	private $streams = [];
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      boolean    $seekable  .
	 */
	private $seekable = true;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      int    $current  .
	 */
	private $current = 0;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      int    $pos  .
	 */
	private $pos = 0;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      int    $detached  .
	 */
	private $detached = false;

	/**
	 * This function is __construct.
	 *
	 * @param StreamInterface[] $streams Streams to decorate. Each stream must
	 *                                   be readable.
	 */
	public function __construct( array $streams = [] ) {
		foreach ( $streams as $stream ) {
			$this->addStream( $stream );
		}
	}
	/**
	 * This function is __toString.
	 */
	public function __toString() {
		try {
			$this->rewind();
			return $this->getContents();
		} catch ( \Exception $e ) {
			return '';
		}
	}

	/**
	 * Add a stream to the AppendStream
	 *
	 * @param StreamInterface $stream Stream to append. Must be readable.
	 *
	 * @throws \InvalidArgumentException If the stream is not readable.
	 */
	public function addStream( StreamInterface $stream ) {
		if ( ! $stream->isReadable() ) {
			throw new \InvalidArgumentException( 'Each stream must be readable' );
		}

		// The stream is only seekable if all streams are seekable.
		if ( ! $stream->isSeekable() ) {
			$this->seekable = false;
		}

		$this->streams[] = $stream;
	}
	/**
	 * This function is getContents.
	 */
	public function getContents() {
		return copy_to_string( $this );
	}

	/**
	 * Closes each attached stream.
	 *
	 * {@inheritdoc}
	 */
	public function close() {
		$this->pos = $this->current = 0;// @codingStandardsIgnoreLine

		foreach ( $this->streams as $stream ) {
			$stream->close();
		}

		$this->streams = [];
	}

	/**
	 * Detaches each attached stream
	 *
	 * {@inheritdoc}
	 */
	public function detach() {
		$this->close();
		$this->detached = true;
	}
	/**
	 * This function is tell.
	 */
	public function tell() {
		return $this->pos;
	}

	/**
	 * Tries to calculate the size by adding the size of each stream.
	 *
	 * If any of the streams do not return a valid number, then the size of the
	 * append stream cannot be determined and null is returned.
	 *
	 * {@inheritdoc}
	 */
	public function getSize() {
		$size = 0;

		foreach ( $this->streams as $stream ) {
			$s = $stream->getSize();
			if ( null === $s ) {
				return null;
			}
			$size += $s;
		}

		return $size;
	}
	/**
	 * This function is eof.
	 */
	public function eof() {
		return ! $this->streams ||
			( $this->current >= count( $this->streams ) - 1 &&
			$this->streams[ $this->current ]->eof() );
	}
	/**
	 * This function is rewind.
	 */
	public function rewind() {
		$this->seek( 0 );
	}

	/**
	 * Attempts to seek to the given position. Only supports SEEK_SET.
	 *
	 * @param string $offset passes parameter as offset.
	 * @param string $whence passes parameter as whence.
	 * @throws \RuntimeException .
	 */
	public function seek( $offset, $whence = SEEK_SET ) {
		if ( ! $this->seekable ) {
			throw new \RuntimeException( 'This AppendStream is not seekable' );
		} elseif ( $whence !== SEEK_SET ) {// @codingStandardsIgnoreLine
			throw new \RuntimeException( 'The AppendStream can only seek with SEEK_SET' );
		}

		$this->pos = $this->current = 0;// @codingStandardsIgnoreLine

		// Rewind each stream.
		foreach ( $this->streams as $i => $stream ) {
			try {
				$stream->rewind();
			} catch ( \Exception $e ) {
				throw new \RuntimeException(
					'Unable to seek stream '
					. $i . ' of the AppendStream', 0, $e
				);
			}
		}

		// Seek to the actual position by reading from each stream.
		while ( $this->pos < $offset && ! $this->eof() ) {
			$result = $this->read( min( 8096, $offset - $this->pos ) );
			if ( '' === $result ) {
				break;
			}
		}
	}

	/**
	 * Reads from all of the appended streams until the length is met or EOF.
	 *
	 * @param string $length passes parameter as length.
	 */
	public function read( $length ) {
		$buffer         = '';
		$total          = count( $this->streams ) - 1;
		$remaining      = $length;
		$progressToNext = false;// @codingStandardsIgnoreLine

		while ( $remaining > 0 ) {

			// Progress to the next stream if needed.
			if ( $progressToNext || $this->streams[ $this->current ]->eof() ) {// @codingStandardsIgnoreLine
				$progressToNext = false;// @codingStandardsIgnoreLine
				if ( $this->current === $total ) {
					break;
				}
				$this->current++;
			}

			$result = $this->streams[ $this->current ]->read( $remaining );

			// Using a loose comparison here to match on '', false, and null.
			if ( null == $result ) {// WPCS: loose comparison ok.
				$progressToNext = true;// @codingStandardsIgnoreLine
				continue;
			}

			$buffer   .= $result;
			$remaining = $length - strlen( $buffer );
		}

		$this->pos += strlen( $buffer );

		return $buffer;
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
		return false;
	}
	/**
	 * This function is isSeekable.
	 */
	public function isSeekable() {
		return $this->seekable;
	}
	/**
	 * This function is write.
	 *
	 * @param string $string passes parameter as string.
	 * @throws \RuntimeException .
	 */
	public function write( $string ) {
		throw new \RuntimeException( 'Cannot write to an AppendStream' );
	}
	/**
	 * This function is used to get meta data.
	 *
	 * @param null $key passes parameter as key.
	 */
	public function getMetadata( $key = null ) {
		return $key ? null : [];
	}
}
