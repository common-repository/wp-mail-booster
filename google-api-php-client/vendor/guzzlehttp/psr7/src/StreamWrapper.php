<?php // @codingStandardsIgnoreLine
/**
 * This file to Converts Guzzle streams into PHP stream resources
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/composer
 * @version 2.0.0
 */

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * Converts Guzzle streams into PHP stream resources.
 */
class StreamWrapper {
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $context  .
	 */
	public $context;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $stream  .
	 */
	private $stream;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $mode  .
	 */
	private $mode;

	/**
	 * Returns a resource representing the stream.
	 *
	 * @param StreamInterface $stream The stream to get a resource for.
	 *
	 * @return resource
	 * @throws \InvalidArgumentException If stream is not readable or writable.
	 */
	public static function getResource( StreamInterface $stream ) {// @codingStandardsIgnoreLine
		self::register();

		if ( $stream->isReadable() ) {
			$mode = $stream->isWritable() ? 'r+' : 'r';
		} elseif ( $stream->isWritable() ) {
			$mode = 'w';
		} else {
			throw new \InvalidArgumentException(
				'The stream must be readable, '
				. 'writable, or both.'
			);
		}

		return fopen(// @codingStandardsIgnoreLine
			'guzzle://stream', $mode, null, stream_context_create(
				[
					'guzzle' => [ 'stream' => $stream ],
				]
			)
		);
	}

	/**
	 * Registers the stream wrapper if needed
	 */
	public static function register() {
		if ( ! in_array( 'guzzle', stream_get_wrappers() ) ) {// @codingStandardsIgnoreLine
			stream_wrapper_register( 'guzzle', __CLASS__ );
		}
	}
	/**
	 * This function is used to open stream.
	 *
	 * @param string $path passes parameter as path.
	 * @param string $mode passes parameter as mode.
	 * @param string $options passes parameter as options.
	 * @param string $opened_path passes parameter as opened_path.
	 */
	public function stream_open( $path, $mode, $options, &$opened_path ) {
		$options = stream_context_get_options( $this->context );

		if ( ! isset( $options['guzzle']['stream'] ) ) {
			return false;
		}

		$this->mode   = $mode;
		$this->stream = $options['guzzle']['stream'];

		return true;
	}
	/**
	 * This function is stream_read.
	 *
	 * @param string $count passes parameter as count.
	 */
	public function stream_read( $count ) {
		return $this->stream->read( $count );
	}
	/**
	 * This function is stream_write.
	 *
	 * @param string $data passes parameter as data.
	 */
	public function stream_write( $data ) {
		return (int) $this->stream->write( $data );
	}
	/**
	 * This function is stream_tell.
	 */
	public function stream_tell() {
		return $this->stream->tell();
	}
	/**
	 * This function is stream_eof.
	 */
	public function stream_eof() {
		return $this->stream->eof();
	}
	/**
	 * This function is stream_seek.
	 *
	 * @param string $offset passes parameter as offset.
	 * @param string $whence passes parameter as whence.
	 */
	public function stream_seek( $offset, $whence ) {
		$this->stream->seek( $offset, $whence );

		return true;
	}
	/**
	 * This function is stream_stat.
	 */
	public function stream_stat() {
		static $modeMap = [// @codingStandardsIgnoreLine
			'r'  => 33060,
			'r+' => 33206,
			'w'  => 33188,
		];

		return [
			'dev'     => 0,
			'ino'     => 0,
			'mode'    => $modeMap[ $this->mode ],// @codingStandardsIgnoreLine
			'nlink'   => 0,
			'uid'     => 0,
			'gid'     => 0,
			'rdev'    => 0,
			'size'    => $this->stream->getSize() ?: 0,
			'atime'   => 0,
			'mtime'   => 0,
			'ctime'   => 0,
			'blksize' => 0,
			'blocks'  => 0,
		];
	}
}
