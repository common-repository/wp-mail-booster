<?php // @codingStandardsIgnoreLine.
/**
 * This file Stores to any socket
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
 * Stores to any socket - uses fsockopen() or pfsockopen().
 */
class SocketHandler extends AbstractProcessingHandler {
	/**
	 * Variable connection string
	 *
	 * @var string
	 */
	private $connectionString; // @codingStandardsIgnoreLine.
	/**
	 * Variable connection timeout
	 *
	 * @var string
	 */
	private $connectionTimeout; // @codingStandardsIgnoreLine.
	/**
	 * Variable resource
	 *
	 * @var string
	 */
	private $resource;
	/**
	 * Variable timeout
	 *
	 * @var int
	 */
	private $timeout = 0;
	/**
	 * Variable writing timeout
	 *
	 * @var int
	 */
	private $writingTimeout = 10; // @codingStandardsIgnoreLine.
	/**
	 * Variable last sent byte
	 *
	 * @var string
	 */
	private $lastSentBytes = null; // @codingStandardsIgnoreLine.
	/**
	 * Variable persistent
	 *
	 * @var bool
	 */
	private $persistent = false;
	/**
	 * Variable errno
	 *
	 * @var string
	 */
	private $errno;
	/**
	 * Variable errstr
	 *
	 * @var string
	 */
	private $errstr;
	/**
	 * Variable last writing
	 *
	 * @var string
	 */
	private $lastWritingAt; // @codingStandardsIgnoreLine.

	/**
	 * Public constructor
	 *
	 * @param string  $connectionString Socket connection string .
	 * @param int     $level            The minimum logging level at which this handler will be triggered .
	 * @param Boolean $bubble           Whether the messages that are handled can bubble up the stack or not .
	 */
	public function __construct( $connectionString, $level = Logger::DEBUG, $bubble = true ) { // @codingStandardsIgnoreLine.
		parent::__construct( $level, $bubble );
		$this->connectionString  = $connectionString; // @codingStandardsIgnoreLine.
		$this->connectionTimeout = (float) ini_get( 'default_socket_timeout' ); // @codingStandardsIgnoreLine.
	}

	/**
	 * Connect (if necessary) and write to the socket
	 *
	 * @param array $record .
	 *
	 * @throws \UnexpectedValueException .
	 * @throws \RuntimeException .
	 */
	protected function write( array $record ) {
		$this->connectIfNotConnected();
		$data = $this->generateDataStream( $record );
		$this->writeToSocket( $data );
	}

	/**
	 * We will not close a PersistentSocket instance so it can be reused in other requests.
	 */
	public function close() {
		if ( ! $this->isPersistent() ) {
			$this->closeSocket();
		}
	}

	/**
	 * Close socket, if open
	 */
	public function closeSocket() {
		if ( is_resource( $this->resource ) ) {
			fclose( $this->resource ); // @codingStandardsIgnoreLine.
			$this->resource = null;
		}
	}

	/**
	 * Set socket connection to nbe persistent. It only has effect before the connection is initiated.
	 *
	 * @param bool $persistent .
	 */
	public function setPersistent( $persistent ) {
		$this->persistent = (boolean) $persistent;
	}

	/**
	 * Set connection timeout.  Only has effect before we connect.
	 *
	 * @param float $seconds .
	 *
	 * @see http://php.net/manual/en/function.fsockopen.php
	 */
	public function setConnectionTimeout( $seconds ) {
		$this->validateTimeout( $seconds );
		$this->connectionTimeout = (float) $seconds; // @codingStandardsIgnoreLine.
	}

	/**
	 * Set write timeout. Only has effect before we connect.
	 *
	 * @param float $seconds .
	 */
	public function setTimeout( $seconds ) {
		$this->validateTimeout( $seconds );
		$this->timeout = (float) $seconds;
	}

	/**
	 * Set writing timeout. Only has effect during connection in the writing cycle.
	 *
	 * @param float $seconds 0 for no timeout .
	 */
	public function setWritingTimeout( $seconds ) {
		$this->validateTimeout( $seconds );
		$this->writingTimeout = (float) $seconds; // @codingStandardsIgnoreLine.
	}

	/**
	 * Get current connection string
	 *
	 * @return string
	 */
	public function getConnectionString() {
		return $this->connectionString; // @codingStandardsIgnoreLine.
	}

	/**
	 * Get persistent setting
	 *
	 * @return bool
	 */
	public function isPersistent() {
		return $this->persistent;
	}

	/**
	 * Get current connection timeout setting
	 *
	 * @return float
	 */
	public function getConnectionTimeout() {
		return $this->connectionTimeout; // @codingStandardsIgnoreLine.
	}

	/**
	 * Get current in-transfer timeout
	 *
	 * @return float
	 */
	public function getTimeout() {
		return $this->timeout;
	}

	/**
	 * Get current local writing timeout
	 *
	 * @return float
	 */
	public function getWritingTimeout() {
		return $this->writingTimeout; // @codingStandardsIgnoreLine.
	}

	/**
	 * Check to see if the socket is currently available.
	 *
	 * UDP might appear to be connected but might fail when writing.  See http://php.net/fsockopen for details.
	 *
	 * @return bool
	 */
	public function isConnected() {
		return is_resource( $this->resource )
			&& ! feof( $this->resource );  // on TCP - other party can close connection.
	}

	/**
	 * Wrapper to allow mocking
	 */
	protected function pfsockopen() {
		return @pfsockopen( $this->connectionString, -1, $this->errno, $this->errstr, $this->connectionTimeout ); // @codingStandardsIgnoreLine.
	}

	/**
	 * Function to socket open
	 */
	protected function fsockopen() {
		return @fsockopen( $this->connectionString, -1, $this->errno, $this->errstr, $this->connectionTimeout ); // @codingStandardsIgnoreLine.
	}

	/**
	 * Function to set stream timeout
	 *
	 * @see http://php.net/manual/en/function.stream-set-timeout.php
	 */
	protected function streamSetTimeout() {
		$seconds      = floor( $this->timeout );
		$microseconds = round( ( $this->timeout - $seconds ) * 1e6 );

		return stream_set_timeout( $this->resource, $seconds, $microseconds );
	}

	/**
	 * Function to file write
	 *
	 * @param string $data .
	 */
	protected function fwrite( $data ) {
		return @fwrite( $this->resource, $data ); // @codingStandardsIgnoreLine.
	}

	/**
	 * Function to get stream meta data
	 */
	protected function streamGetMetadata() {
		return stream_get_meta_data( $this->resource );
	}
	/**
	 * Function for valid time out
	 *
	 * @param string $value .
	 * @throws \InvalidArgumentException .
	 */
	private function validateTimeout( $value ) {
		$ok = filter_var( $value, FILTER_VALIDATE_FLOAT );
		if ( false === $ok || $value < 0 ) {
			throw new \InvalidArgumentException( "Timeout must be 0 or a positive float (got $value)" );
		}
	}
	/**
	 * Function to connect if its not connected
	 */
	private function connectIfNotConnected() {
		if ( $this->isConnected() ) {
			return;
		}
		$this->connect();
	}
	/**
	 * Function to generate data stream
	 *
	 * @param string $record .
	 */
	protected function generateDataStream( $record ) {
		return (string) $record['formatted'];
	}

	/**
	 * Function to get resource
	 *
	 * @return resource|null
	 */
	protected function getResource() {
		return $this->resource;
	}
	/**
	 * Function for connect
	 */
	private function connect() {
		$this->createSocketResource();
		$this->setSocketTimeout();
	}
	/**
	 * Function for create socket resource
	 *
	 * @throws \UnexpectedValueException .
	 */
	private function createSocketResource() {
		if ( $this->isPersistent() ) {
			$resource = $this->pfsockopen();
		} else {
			$resource = $this->fsockopen();
		}
		if ( ! $resource ) {
			throw new \UnexpectedValueException( "Failed connecting to $this->connectionString ($this->errno: $this->errstr)" );
		}
		$this->resource = $resource;
	}
	/**
	 * Function for set socket timeout
	 *
	 * @throws \UnexpectedValueException .
	 */
	private function setSocketTimeout() {
		if ( ! $this->streamSetTimeout() ) {
			throw new \UnexpectedValueException( 'Failed setting timeout with stream_set_timeout()' );
		}
	}
	/**
	 * Function for write to socket
	 *
	 * @param string $data .
	 * @throws \RuntimeException .
	 */
	private function writeToSocket( $data ) {
		$length              = strlen( $data );
		$sent                = 0;
		$this->lastSentBytes = $sent; // @codingStandardsIgnoreLine.
		while ( $this->isConnected() && $sent < $length ) {
			if ( 0 == $sent ) { // WPCS:Loose comparison ok.
				$chunk = $this->fwrite( $data );
			} else {
				$chunk = $this->fwrite( substr( $data, $sent ) );
			}
			if ( false === $chunk ) {
				throw new \RuntimeException( 'Could not write to socket' );
			}
			$sent      += $chunk;
			$socketInfo = $this->streamGetMetadata(); // @codingStandardsIgnoreLine.
			if ( $socketInfo['timed_out'] ) { // @codingStandardsIgnoreLine.
				throw new \RuntimeException( 'Write timed-out' );
			}
			if ( $this->writingIsTimedOut( $sent ) ) {
				throw new \RuntimeException( "Write timed-out, no data sent for `{$this->writingTimeout}` seconds, probably we got disconnected (sent $sent of $length)" );
			}
		}
		if ( ! $this->isConnected() && $sent < $length ) {
			throw new \RuntimeException( "End-of-file reached, probably we got disconnected (sent $sent of $length)" );
		}
	}
	/**
	 * Function forwriting is time out
	 *
	 * @param string $sent .
	 */
	private function writingIsTimedOut( $sent ) {
		$writingTimeout = (int) floor( $this->writingTimeout ); // @codingStandardsIgnoreLine.
		if ( 0 === $writingTimeout ) { // @codingStandardsIgnoreLine.
			return false;
		}
		if ( $sent !== $this->lastSentBytes ) { // @codingStandardsIgnoreLine.
			$this->lastWritingAt = time(); // @codingStandardsIgnoreLine.
			$this->lastSentBytes = $sent; // @codingStandardsIgnoreLine.
			return false;
		} else {
			usleep( 100 );
		}
		if ( ( time() - $this->lastWritingAt ) >= $writingTimeout ) { // @codingStandardsIgnoreLine.
			$this->closeSocket();
			return true;
		}
		return false;
	}
}
