<?php // @codingStandardsIgnoreLine.
/**
 * This file Stores to any stream resource
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\Logger;

/**
 * Stores to any stream resource
 */
class StreamHandler extends AbstractProcessingHandler {

	/**
	 * Variable
	 *
	 * @var string
	 */
	protected $stream;
	/**
	 * Variable
	 *
	 * @var string
	 */
	protected $url;
	/**
	 * Variable for error message
	 *
	 * @var string
	 */
	private $errorMessage; // @codingStandardsIgnoreLine.
	/**
	 * Variable for file permission
	 *
	 * @var int
	 */
	protected $filePermission; // @codingStandardsIgnoreLine.
	/**
	 * Variable for use locking
	 *
	 * @var bool
	 */
	protected $useLocking; // @codingStandardsIgnoreLine.
	/**
	 * Variable for dir created
	 *
	 * @var string
	 */
	private $dirCreated; // @codingStandardsIgnoreLine.

	/**
	 * Public constructor
	 *
	 * @param resource|string $stream .
	 * @param int             $level          The minimum logging level at which this handler will be triggered .
	 * @param Boolean         $bubble         Whether the messages that are handled can bubble up the stack or not .
	 * @param int|null        $filePermission Optional file permissions (default (0644) are only for owner read/write) .
	 * @param Boolean         $useLocking     Try to lock log file before doing any writes .
	 * @throws \InvalidArgumentException If stream is not a resource or string .
	 */
	public function __construct( $stream, $level = Logger::DEBUG, $bubble = true, $filePermission = null, $useLocking = false ) { // @codingStandardsIgnoreLine.
		parent::__construct( $level, $bubble );
		if ( is_resource( $stream ) ) {
			$this->stream = $stream;
		} elseif ( is_string( $stream ) ) {
			$this->url = $stream;
		} else {
			throw new \InvalidArgumentException( 'A stream must either be a resource or a string.' );
		}

		$this->filePermission = $filePermission; // @codingStandardsIgnoreLine.
		$this->useLocking     = $useLocking; // @codingStandardsIgnoreLine.
	}

	/**
	 * {@inheritdoc}
	 */
	public function close() {
		if ( $this->url && is_resource( $this->stream ) ) {
			fclose( $this->stream ); // @codingStandardsIgnoreLine.
		}
		$this->stream = null;
	}

	/**
	 * Return the currently active stream if it is open
	 *
	 * @return resource|null
	 */
	public function getStream() {
		return $this->stream;
	}

	/**
	 * Return the stream URL if it was configured with a URL and not an active resource
	 *
	 * @return string|null
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * Function for write record
	 *
	 * @param array $record .
	 * @throws \LogicException .
	 * @throws \UnexpectedValueException .
	 */
	protected function write( array $record ) {
		if ( ! is_resource( $this->stream ) ) {
			if ( null === $this->url || '' === $this->url ) {
				throw new \LogicException( 'Missing stream url, the stream can not be opened. This may be caused by a premature call to close().' );
			}
			$this->createDir();
			$this->errorMessage = null; // @codingStandardsIgnoreLine.
			set_error_handler( array( $this, 'customErrorHandler' ) ); // @codingStandardsIgnoreLine.
			$this->stream = fopen( $this->url, 'a' ); // @codingStandardsIgnoreLine.
			if ( $this->filePermission !== null ) { // @codingStandardsIgnoreLine.
				@chmod( $this->url, $this->filePermission ); // @codingStandardsIgnoreLine.
			}
			restore_error_handler();
			if ( ! is_resource( $this->stream ) ) {
				$this->stream = null;
				throw new \UnexpectedValueException( sprintf( 'The stream or file "%s" could not be opened: ' . $this->errorMessage, $this->url ) ); // @codingStandardsIgnoreLine.
			}
		}

		if ( $this->useLocking ) { // @codingStandardsIgnoreLine.
			// ignoring errors here, there's not much we can do about them .
			flock( $this->stream, LOCK_EX ); // @codingStandardsIgnoreLine.
		}

		$this->streamWrite( $this->stream, $record );

		if ( $this->useLocking ) { // @codingStandardsIgnoreLine.
			flock( $this->stream, LOCK_UN ); // @codingStandardsIgnoreLine.
		}
	}

	/**
	 * Write to stream
	 *
	 * @param resource $stream .
	 * @param array    $record .
	 */
	protected function streamWrite( $stream, array $record ) {
		fwrite( $stream, (string) $record['formatted'] ); // @codingStandardsIgnoreLine.
	}
	/**
	 * Function to handle custom errors
	 *
	 * @param string $code .
	 * @param string $msg .
	 */
	private function customErrorHandler( $code, $msg ) {
		$this->errorMessage = preg_replace( '{^(fopen|mkdir)\(.*?\): }', '', $msg ); // @codingStandardsIgnoreLine.
	}

	/**
	 * Function to get dir from stream
	 *
	 * @param string $stream .
	 * @return null|string
	 */
	private function getDirFromStream( $stream ) {
		$pos = strpos( $stream, '://' );
		if ( false === $pos ) {
			return dirname( $stream );
		}

		if ( 'file://' === substr( $stream, 0, 7 ) ) {
			return dirname( substr( $stream, 7 ) );
		}
		return; // @codingStandardsIgnoreLine.
	}
	/**
	 * This function is used to create dir
	 *
	 * @throws \UnexpectedValueException .
	 */
	private function createDir() {
		// Do not try to create dir if it has already been tried.
		if ( $this->dirCreated ) { // @codingStandardsIgnoreLine.
			return;
		}

		$dir = $this->getDirFromStream( $this->url );
		if ( null !== $dir && ! is_dir( $dir ) ) {
			$this->errorMessage = null; // @codingStandardsIgnoreLine.
			set_error_handler( array( $this, 'customErrorHandler' ) ); // @codingStandardsIgnoreLine.
			$status = mkdir( $dir, 0777, true ); // @codingStandardsIgnoreLine.
			restore_error_handler();
			if ( false === $status ) {
				throw new \UnexpectedValueException( sprintf( 'There is no existing directory at "%s" and its not buildable: ' . $this->errorMessage, $dir ) ); // @codingStandardsIgnoreLine.
			}
		}
		$this->dirCreated = true; // @codingStandardsIgnoreLine.
	}
}
