<?php // @codingStandardsIgnoreLine
/**
 * This file to Decorator used to return only a subset of a stream.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/composer
 * @version 2.0.0
 */

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\StreamInterface;


/**
 * Decorator used to return only a subset of a stream
 */
class LimitStream implements StreamInterface {

	use StreamDecoratorTrait;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $offset  .
	 */
	private $offset;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $limit  .
	 */
	private $limit;

	/**
	 * This function is __construct.
	 *
	 * @param StreamInterface $stream Stream to wrap.
	 * @param int             $limit  Total number of bytes to allow to be read
	 *                                from the stream. Pass -1 for no limit.
	 * @param int             $offset Position to seek to before reading (only
	 *                                works on seekable streams).
	 */
	public function __construct(
		StreamInterface $stream,
		$limit = -1,
		$offset = 0
	) {
		$this->stream = $stream;
		$this->setLimit( $limit );
		$this->setOffset( $offset );
	}
	/**
	 * This function is eof.
	 */
	public function eof() {
		// Always return true if the underlying stream is EOF.
		if ( $this->stream->eof() ) {
			return true;
		}

		// No limit and the underlying stream is not at EOF.
		if ( $this->limit == -1 ) {// @codingStandardsIgnoreLine
			return false;
		}

		return $this->stream->tell() >= $this->offset + $this->limit;
	}

	/**
	 * Returns the size of the limited subset of data
	 * {@inheritdoc}
	 */
	public function getSize() {
		if ( null === ( $length = $this->stream->getSize() ) ) {// @codingStandardsIgnoreLine
			return null;
		} elseif ( $this->limit == -1 ) {// @codingStandardsIgnoreLine
			return $length - $this->offset;
		} else {
			return min( $this->limit, $length - $this->offset );
		}
	}

	/**
	 * Allow for a bounded seek on the read limited stream
	 * {@inheritdoc}
	 *
	 * @param string $offset passes parameter as offset.
	 * @param string $whence passes parameter as whence.
	 * @throws \RuntimeException On error.
	 */
	public function seek( $offset, $whence = SEEK_SET ) {
		if ( SEEK_SET !== $whence || $offset < 0 ) {
			throw new \RuntimeException(
				sprintf(
					'Cannot seek to offset % with whence %s',
					$offset,
					$whence
				)
			);
		}

		$offset += $this->offset;

		if ( $this->limit !== -1 ) {// @codingStandardsIgnoreLine
			if ( $offset > $this->offset + $this->limit ) {
				$offset = $this->offset + $this->limit;
			}
		}

		$this->stream->seek( $offset );
	}

	/**
	 * Give a relative tell()
	 * {@inheritdoc}
	 */
	public function tell() {
		return $this->stream->tell() - $this->offset;
	}

	/**
	 * Set the offset to start limiting from
	 *
	 * @param int $offset Offset to seek to and begin byte limiting from.
	 *
	 * @throws \RuntimeException If the stream cannot be seeked.
	 */
	public function setOffset( $offset ) {
		$current = $this->stream->tell();

		if ( $current !== $offset ) {
			// If the stream cannot seek to the offset position, then read to it.
			if ( $this->stream->isSeekable() ) {
				$this->stream->seek( $offset );
			} elseif ( $current > $offset ) {
				throw new \RuntimeException( "Could not seek to stream offset $offset" );
			} else {
				$this->stream->read( $offset - $current );
			}
		}

		$this->offset = $offset;
	}

	/**
	 * Set the limit of bytes that the decorator allows to be read from the
	 * stream.
	 *
	 * @param int $limit Number of bytes to allow to be read from the stream.
	 *                   Use -1 for no limit.
	 */
	public function setLimit( $limit ) {
		$this->limit = $limit;
	}
	/**
	 * This function is used to reading.
	 *
	 * @param string $length passes parameter as length.
	 */
	public function read( $length ) {
		if ( $this->limit == -1 ) {// @codingStandardsIgnoreLine
			return $this->stream->read( $length );
		}

		// Check if the current position is less than the total allowed
		// bytes + original offset.
		$remaining = ( $this->offset + $this->limit ) - $this->stream->tell();
		if ( $remaining > 0 ) {
			// Only return the amount of requested data, ensuring that the byte
			// limit is not exceeded.
			return $this->stream->read( min( $remaining, $length ) );
		}

		return '';
	}
}
