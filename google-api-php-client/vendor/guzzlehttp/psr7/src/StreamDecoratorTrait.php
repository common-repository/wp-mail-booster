<?php // @codingStandardsIgnoreLine
/**
 * This file for stream decorator trait
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/composer
 * @version 2.0.0
 */

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * Stream decorator trait
 *
 * @property StreamInterface stream
 */
trait StreamDecoratorTrait {

	/**
	 * This function is __construct.
	 *
	 * @param StreamInterface $stream Stream to decorate.
	 */
	public function __construct( StreamInterface $stream ) {
		$this->stream = $stream;
	}

	/**
	 * Magic method used to create a new stream if streams are not added in
	 * the constructor of a decorator (e.g., LazyOpenStream).
	 *
	 * @param string $name Name of the property (allows "stream" only).
	 * @throws \UnexpectedValueException On error.
	 * @return StreamInterface
	 */
	public function __get( $name ) {
		if ( 'stream' == $name ) {// WPCS: Loose comparison ok.
			$this->stream = $this->createStream();
			return $this->stream;
		}

		throw new \UnexpectedValueException( "$name not found on class" );
	}
	/**
	 * This function is __toString.
	 */
	public function __toString() {
		try {
			if ( $this->isSeekable() ) {
				$this->seek( 0 );
			}
			return $this->getContents();
		} catch ( \Exception $e ) {
			// Really, PHP? https://bugs.php.net/bug.php?id=53648.
			trigger_error(// @codingStandardsIgnoreLine
				'StreamDecorator::__toString exception: '
				. (string) $e, E_USER_ERROR
			);// WPCS: XSS ok.
			return '';
		}
	}
	/**
	 * This function is getContents.
	 */
	public function getContents() {// @codingStandardsIgnoreLine
		return copy_to_string( $this );
	}

	/**
	 * Allow decorators to implement custom methods
	 *
	 * @param string $method Missing method name.
	 * @param array  $args   Method arguments.
	 *
	 * @return mixed
	 */
	public function __call( $method, array $args ) {
		$result = call_user_func_array( [ $this->stream, $method ], $args );

		// Always return the wrapped object if the result is a return $this.
		return $result === $this->stream ? $this : $result;
	}
	/**
	 * This function is close.
	 */
	public function close() {
		$this->stream->close();
	}
	/**
	 * This function is used to get meta data.
	 *
	 * @param null $key passes parameter as key.
	 */
	public function getMetadata( $key = null ) {// @codingStandardsIgnoreLine
		return $this->stream->getMetadata( $key );
	}
	/**
	 * This function is detach.
	 */
	public function detach() {
		return $this->stream->detach();
	}
	/**
	 * This function is getSize.
	 */
	public function getSize() {// @codingStandardsIgnoreLine
		return $this->stream->getSize();
	}
	/**
	 * This function is eof.
	 */
	public function eof() {
		return $this->stream->eof();
	}
	/**
	 * This function is tell.
	 */
	public function tell() {
		return $this->stream->tell();
	}
	/**
	 * This function is isReadable.
	 */
	public function isReadable() {// @codingStandardsIgnoreLine
		return $this->stream->isReadable();
	}
	/**
	 * This function is isWritable.
	 */
	public function isWritable() {// @codingStandardsIgnoreLine
		return $this->stream->isWritable();
	}
	/**
	 * This function is isSeekable.
	 */
	public function isSeekable() {// @codingStandardsIgnoreLine
		return $this->stream->isSeekable();
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
	 */
	public function seek( $offset, $whence = SEEK_SET ) {
		$this->stream->seek( $offset, $whence );
	}
	/**
	 * This function is read.
	 *
	 * @param string $length passes parameter as length.
	 */
	public function read( $length ) {
		return $this->stream->read( $length );
	}
	/**
	 * This function is write.
	 *
	 * @param string $string passes parameter as string.
	 */
	public function write( $string ) {
		return $this->stream->write( $string );
	}

	/**
	 * Implement in subclasses to dynamically create streams when requested.
	 *
	 * @throws \BadMethodCallException On error.
	 */
	protected function createStream() {// @codingStandardsIgnoreLine
		throw new \BadMethodCallException( 'Not implemented' );
	}
}
