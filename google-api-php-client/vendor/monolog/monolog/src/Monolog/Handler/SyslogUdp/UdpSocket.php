<?php // @codingStandardsIgnoreLine.
/**
 * This file is part of the Monolog package.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/slack
 * @version 2.0.0
 */

namespace Monolog\Handler\SyslogUdp;

	/**
	 * This class is UdpSocket.
	 */
class UdpSocket {

	const DATAGRAM_MAX_LENGTH = 65023;
	/**
	 * The version of the plugin.
	 *
	 * @var string $level.
	 */
	protected $ip;
	/**
	 * The version of the plugin.
	 *
	 * @var string $port.
	 */
	protected $port;
	/**
	 * The version of the plugin.
	 *
	 * @var string $socket.
	 */
	protected $socket;
	/**
	 * This function is $socket.
	 *
	 * @param string $ip .
	 * @param string $port .
	 * @var string $socket.
	 */
	public function __construct( $ip, $port = 514 ) {
		$this->ip     = $ip;
		$this->port   = $port;
		$this->socket = socket_create( AF_INET, SOCK_DGRAM, SOL_UDP );
	}
	/**
	 * This function is $socket.
	 *
	 * @param string $line .
	 * @param string $header .
	 */
	public function write( $line, $header = '' ) {
		$this->send( $this->assembleMessage( $line, $header ) );
	}
	/**
	 * This function is $socket.
	 */
	public function close() {
		if ( is_resource( $this->socket ) ) {
			socket_close( $this->socket );
			$this->socket = null;
		}
	}
	/**
	 * This function is $socket.
	 *
	 * @param string $chunk .
	 * @throws \LogicException .
	 */
	protected function send( $chunk ) {
		if ( ! is_resource( $this->socket ) ) {
			throw new \LogicException( 'The UdpSocket to ' . $this->ip . ':' . $this->port . ' has been closed and can not be written to anymore' );
		}
		socket_sendto( $this->socket, $chunk, strlen( $chunk ), $flags = 0, $this->ip, $this->port );// @codingStandardsIgnoreLine.
	}
	/**
	 * This function is $socket.
	 *
	 * @param string $line .
	 * @param string $header .
	 * @throws \LogicException .
	 */
	protected function assembleMessage( $line, $header ) {// @codingStandardsIgnoreLine.
		$chunkSize = self::DATAGRAM_MAX_LENGTH - strlen( $header );// @codingStandardsIgnoreLine.

		return $header . substr( $line, 0, $chunkSize );// @codingStandardsIgnoreLine.
	}
}
