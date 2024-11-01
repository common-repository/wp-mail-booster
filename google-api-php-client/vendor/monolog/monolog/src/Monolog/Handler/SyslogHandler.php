<?php // @codingStandardsIgnoreLine.
/**
 * This file for log to syslog services
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
 * Logs to syslog service.
 */
class SyslogHandler extends AbstractSyslogHandler {
	/**
	 * Variable for indent
	 *
	 * @var $indent .
	 */
	protected $ident;
	/**
	 * Variable for logopts
	 *
	 * @var $logopts.
	 */
	protected $logopts;

	/**
	 * Public constructor
	 *
	 * @param string  $ident .
	 * @param mixed   $facility .
	 * @param int     $level    The minimum logging level at which this handler will be triggered .
	 * @param Boolean $bubble   Whether the messages that are handled can bubble up the stack or not .
	 * @param int     $logopts  Option flags for the openlog() call, defaults to LOG_PID .
	 */
	public function __construct( $ident, $facility = LOG_USER, $level = Logger::DEBUG, $bubble = true, $logopts = LOG_PID ) {
		parent::__construct( $facility, $level, $bubble );

		$this->ident   = $ident;
		$this->logopts = $logopts;
	}

	/**
	 * Function for close
	 */
	public function close() {
		closelog();
	}

	/**
	 * Function for write
	 *
	 * @param array $record .
	 * @throws \LogicException .
	 */
	protected function write( array $record ) {
		if ( ! openlog( $this->ident, $this->logopts, $this->facility ) ) {
			throw new \LogicException( 'Can\'t open syslog for ident "' . $this->ident . '" and facility "' . $this->facility . '"' );
		}
		syslog( $this->logLevels[ $record['level'] ], (string) $record['formatted'] ); // @codingStandardsIgnoreLine.
	}
}
