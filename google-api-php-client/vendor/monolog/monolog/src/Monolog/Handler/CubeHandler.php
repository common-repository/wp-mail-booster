<?php // @codingStandardsIgnoreLine.
/**
 * This file is part of the Monolog package.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/handler
 * @version 2.0.0
 */
namespace Monolog\Handler;

use Monolog\Logger;

/**
 * Logs to Cube.
 *
 * @link http://square.github.com/cube/
 */
class CubeHandler extends AbstractProcessingHandler {

	private $udpConnection;// @codingStandardsIgnoreLine.
	private $httpConnection;// @codingStandardsIgnoreLine.
	/**
	 * The version of this plugin.
	 *
	 * @var $scheme.
	 */
	private $scheme;
	/**
	 * The version of this plugin.
	 *
	 * @var $host.
	 */
	private $host;
	/**
	 * The version of this plugin.
	 *
	 * @var $port.
	 */
	private $port;
	private $acceptedSchemes = array( 'http', 'udp' );// @codingStandardsIgnoreLine.

	/**
	 * Create a Cube handler
	 *
	 * @param string $url .
	 * @param string $level .
	 * @param string $bubble .
	 * @throws \UnexpectedValueException .
	 */
	public function __construct( $url, $level = Logger::DEBUG, $bubble = true ) {
		$urlInfo = parse_url( $url );// @codingStandardsIgnoreLine.

		if ( ! isset( $urlInfo['scheme'], $urlInfo['host'], $urlInfo['port'] ) ) {// @codingStandardsIgnoreLine.
			throw new \UnexpectedValueException( 'URL "' . $url . '" is not valid' );
		}

		if ( ! in_array( $urlInfo['scheme'], $this->acceptedSchemes ) ) {// @codingStandardsIgnoreLine.
			throw new \UnexpectedValueException(
				'Invalid protocol (' . $urlInfo['scheme'] . ').'// @codingStandardsIgnoreLine.
				. ' Valid options are ' . implode( ', ', $this->acceptedSchemes )// @codingStandardsIgnoreLine.
			);
		}

		$this->scheme = $urlInfo['scheme'];// @codingStandardsIgnoreLine.
		$this->host   = $urlInfo['host'];// @codingStandardsIgnoreLine.
		$this->port   = $urlInfo['port'];// @codingStandardsIgnoreLine.

		parent::__construct( $level, $bubble );
	}

	/**
	 * Establish a connection to an UDP socket
	 *
	 * @throws \LogicException           When unable to connect to the socket .
	 * @throws MissingExtensionException When there is no socket extension .
	 */
	protected function connectUdp() {
		if ( ! extension_loaded( 'sockets' ) ) {
			throw new MissingExtensionException( 'The sockets extension is required to use udp URLs with the CubeHandler' );
		}

		$this->udpConnection = socket_create( AF_INET, SOCK_DGRAM, 0 );// @codingStandardsIgnoreLine.
		if ( ! $this->udpConnection ) {// @codingStandardsIgnoreLine.
			throw new \LogicException( 'Unable to create a socket' );
		}

		if ( ! socket_connect( $this->udpConnection, $this->host, $this->port ) ) {// @codingStandardsIgnoreLine.
			throw new \LogicException( 'Unable to connect to the socket at ' . $this->host . ':' . $this->port );
		}
	}

	/**
	 * Establish a connection to a http server .
	 *
	 * @throws \LogicException .
	 */
	protected function connectHttp() {
		if ( ! extension_loaded( 'curl' ) ) {
			throw new \LogicException( 'The curl extension is needed to use http URLs with the CubeHandler' );
		}

		$this->httpConnection = curl_init( 'http://' . $this->host . ':' . $this->port . '/1.0/event/put' );// @codingStandardsIgnoreLine.

		if ( ! $this->httpConnection ) {// @codingStandardsIgnoreLine.
			throw new \LogicException( 'Unable to connect to ' . $this->host . ':' . $this->port );
		}

		curl_setopt( $this->httpConnection, CURLOPT_CUSTOMREQUEST, 'POST' );// @codingStandardsIgnoreLine.
		curl_setopt( $this->httpConnection, CURLOPT_RETURNTRANSFER, true );// @codingStandardsIgnoreLine.
	}

	/**
	 * The version of this plugin.
	 *
	 * @param array $record s.
	 * {@inheritdoc}.
	 */
	protected function write( array $record ) {
		$date = $record['datetime'];

		$data = array( 'time' => $date->format( 'Y-m-d\TH:i:s.uO' ) );
		unset( $record['datetime'] );

		if ( isset( $record['context']['type'] ) ) {
			$data['type'] = $record['context']['type'];
			unset( $record['context']['type'] );
		} else {
			$data['type'] = $record['channel'];
		}

		$data['data']          = $record['context'];
		$data['data']['level'] = $record['level'];

		if ( $this->scheme === 'http' ) {// @codingStandardsIgnoreLine.
			$this->writeHttp( json_encode( $data ) );// @codingStandardsIgnoreLine.
		} else {
			$this->writeUdp( json_encode( $data ) );// @codingStandardsIgnoreLine.
		}
	}
	/**
	 * This function is writeUdp.
	 *
	 * @param array $data .
	 * {@inheritdoc}.
	 */
	private function writeUdp( $data ) {
		if ( ! $this->udpConnection ) {// @codingStandardsIgnoreLine.
			$this->connectUdp();
		}

		socket_send( $this->udpConnection, $data, strlen( $data ), 0 );// @codingStandardsIgnoreLine.
	}
	/**
	 * This function is writeUdp.
	 *
	 * @param array $data .
	 */
	private function writeHttp( $data ) {
		if ( ! $this->httpConnection ) {// @codingStandardsIgnoreLine.
			$this->connectHttp();
		}

		curl_setopt( $this->httpConnection, CURLOPT_POSTFIELDS, '[' . $data . ']' );// @codingStandardsIgnoreLine.
		curl_setopt(// @codingStandardsIgnoreLine.
			$this->httpConnection, CURLOPT_HTTPHEADER, array(// @codingStandardsIgnoreLine.
				'Content-Type: application/json',
				'Content-Length: ' . strlen( '[' . $data . ']' ),
			)
		);

		Curl\Util::execute( $this->httpConnection, 5, false );// @codingStandardsIgnoreLine.
	}
}
