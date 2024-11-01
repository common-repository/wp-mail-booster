<?php // @codingStandardsIgnoreLine
/**
 * This file to Compose stream implementations based on a hash of functions.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/composer
 * @version 2.0.0
 */

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * Compose stream implementations based on a hash of functions.
 *
 * Allows for easy testing and extension of a provided stream without needing
 * to create a concrete class for a simple extension point.
 */
class FnStream implements StreamInterface {

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $methods  .
	 */
	private $methods;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $slots  .
	 */
	private static $slots = [
		'__toString',
		'close',
		'detach',
		'rewind',
		'getSize',
		'tell',
		'eof',
		'isSeekable',
		'seek',
		'isWritable',
		'write',
		'isReadable',
		'read',
		'getContents',
		'getMetadata',
	];

	/**
	 * This function is __construct.
	 *
	 * @param array $methods Hash of method name to a callable.
	 */
	public function __construct( array $methods ) {
		$this->methods = $methods;

		// Create the functions on the class.
		foreach ( $methods as $name => $fn ) {
			$this->{'_fn_' . $name} = $fn;
		}
	}

	/**
	 * Lazily determine which methods are not implemented.
	 *
	 * @param string $name passes parameter as name.
	 * @throws \BadMethodCallException .
	 */
	public function __get( $name ) {
		throw new \BadMethodCallException(
			str_replace( '_fn_', '', $name )
			. '() is not implemented in the FnStream'
		);
	}

	/**
	 * The close method is called on the underlying stream only if possible.
	 */
	public function __destruct() {
		if ( isset( $this->_fn_close ) ) {
			call_user_func( $this->_fn_close );
		}
	}

	/**
	 * Adds custom functionality to an underlying stream by intercepting
	 * specific method calls.
	 *
	 * @param StreamInterface $stream  Stream to decorate.
	 * @param array           $methods Hash of method name to a closure.
	 *
	 * @return FnStream
	 */
	public static function decorate( StreamInterface $stream, array $methods ) {
		// If any of the required methods were not provided, then simply
		// proxy to the decorated stream.
		foreach ( array_diff( self::$slots, array_keys( $methods ) ) as $diff ) {
			$methods[ $diff ] = [ $stream, $diff ];
		}

		return new self( $methods );
	}
	/**
	 * This function is __toString.
	 */
	public function __toString() {
		return call_user_func( $this->_fn___toString );// @codingStandardsIgnoreLine
	}
	/**
	 * This function is close.
	 */
	public function close() {
		return call_user_func( $this->_fn_close );
	}
	/**
	 * This function is detach.
	 */
	public function detach() {
		return call_user_func( $this->_fn_detach );
	}
	/**
	 * This function is getSize.
	 */
	public function getSize() {
		return call_user_func( $this->_fn_getSize );// @codingStandardsIgnoreLine
	}
	/**
	 * This function is tell.
	 */
	public function tell() {
		return call_user_func( $this->_fn_tell );
	}
	/**
	 * This function is eof.
	 */
	public function eof() {
		return call_user_func( $this->_fn_eof );
	}
	/**
	 * This function is isSeekable.
	 */
	public function isSeekable() {
		return call_user_func( $this->_fn_isSeekable );// @codingStandardsIgnoreLine
	}
	/**
	 * This function is rewind.
	 */
	public function rewind() {
		call_user_func( $this->_fn_rewind );
	}
	/**
	 * This function is seek.
	 *
	 * @param string $offset passes parameter as offset.
	 * @param string $whence passes parameter as whence.
	 */
	public function seek( $offset, $whence = SEEK_SET ) {
		call_user_func( $this->_fn_seek, $offset, $whence );
	}
	/**
	 * This function is
	 */
	public function isWritable() {
		return call_user_func( $this->_fn_isWritable );// @codingStandardsIgnoreLine
	}
	/**
	 * This function is write.
	 *
	 * @param string $string passes parameter as whence.
	 */
	public function write( $string ) {
		return call_user_func( $this->_fn_write, $string );
	}
	/**
	 * This function is isReadable.
	 */
	public function isReadable() {
		return call_user_func( $this->_fn_isReadable );// @codingStandardsIgnoreLine
	}
	/**
	 * This function is isReadable.
	 *
	 * @param string $length passes parameter as length.
	 */
	public function read( $length ) {
		return call_user_func( $this->_fn_read, $length );
	}
	/**
	 * This function is getContents.
	 */
	public function getContents() {
		return call_user_func( $this->_fn_getContents );// @codingStandardsIgnoreLine
	}
	/**
	 * This function is used to get meta data.
	 *
	 * @param null $key passes parameter as key.
	 */
	public function getMetadata( $key = null ) {
		return call_user_func( $this->_fn_getMetadata, $key );// @codingStandardsIgnoreLine
	}
}
