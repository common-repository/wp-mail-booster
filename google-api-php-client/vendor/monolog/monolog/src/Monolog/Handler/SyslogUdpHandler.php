<?php // @codingStandardsIgnoreLine.
/**
 * This file for Handler for logging to a remote syslogd server
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\Logger;
use Monolog\Handler\SyslogUdp\UdpSocket;

/**
 * A Handler for logging to a remote syslogd server.
 */
class SyslogUdpHandler extends AbstractSyslogHandler {
	/**
	 * Variable for socket
	 *
	 * @var $socket .
	 */
	protected $socket;
	/**
	 * Variable for indent
	 *
	 * @var $indent .
	 */
	protected $ident;

	/**
	 * Public constructor
	 *
	 * @param string  $host .
	 * @param int     $port .
	 * @param mixed   $facility .
	 * @param int     $level    The minimum logging level at which this handler will be triggered .
	 * @param Boolean $bubble   Whether the messages that are handled can bubble up the stack or not .
	 * @param string  $ident    Program name or tag for each log message.
	 */
	public function __construct( $host, $port = 514, $facility = LOG_USER, $level = Logger::DEBUG, $bubble = true, $ident = 'php' ) {
		parent::__construct( $facility, $level, $bubble );

		$this->ident = $ident;

		$this->socket = new UdpSocket( $host, $port ?: 514 );
	}
	/**
	 * Function for write
	 *
	 * @param array $record .
	 */
	protected function write( array $record ) {
		$lines  = $this->splitMessageIntoLines( $record['formatted'] );
		$header = $this->makeCommonSyslogHeader( $this->logLevels[ $record['level'] ] ); // @codingStandardsIgnoreLine.
		foreach ( $lines as $line ) {
			$this->socket->write( $line, $header );
		}
	}
	/**
	 * Function for close
	 */
	public function close() {
		$this->socket->close();
	}
	/**
	 * Function split message into line
	 *
	 * @param string $message .
	 */
	private function splitMessageIntoLines( $message ) {
		if ( is_array( $message ) ) {
			$message = implode( "\n", $message );
		}

		return preg_split( '/$\R?^/m', $message, -1, PREG_SPLIT_NO_EMPTY );
	}

	/**
	 * Make common syslog header (see rfc5424)
	 *
	 * @param string $severity .
	 */
	protected function makeCommonSyslogHeader( $severity ) {
		$priority = $severity + $this->facility;

		if ( ! $pid = getmypid() ) { // @codingStandardsIgnoreLine
			$pid = '-';
		}

		if ( ! $hostname = gethostname() ) { // @codingStandardsIgnoreLine.
			$hostname = '-';
		}
		return "<$priority>1 " .
			$this->getDateTime() . ' ' .
			$hostname . ' ' .
			$this->ident . ' ' .
			$pid . ' - - ';
	}
	/**
	 * Function to get time
	 */
	protected function getDateTime() {
		return date( \DateTime::RFC3339 );
	}

	/**
	 * Inject your own socket, mainly used for testing
	 *
	 * @param string $socket .
	 */
	public function setSocket( $socket ) {
		$this->socket = $socket;
	}
}
