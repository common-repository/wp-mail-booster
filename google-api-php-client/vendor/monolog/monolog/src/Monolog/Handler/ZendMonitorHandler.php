<?php // @codingStandardsIgnoreLine.
/**
 * This file is part of the Monolog package
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

use Monolog\Formatter\NormalizerFormatter;
use Monolog\Logger;

/**
 * Handler sending logs to Zend Monitor
 *
 * @author  Christian Bergau <cbergau86@gmail.com>
 */
class ZendMonitorHandler extends AbstractProcessingHandler {

	/**
	 * Monolog level / ZendMonitor Custom Event priority map
	 *
	 * @var array
	 */
	protected $levelMap = array( // @codingStandardsIgnoreLine.
		Logger::DEBUG     => 1,
		Logger::INFO      => 2,
		Logger::NOTICE    => 3,
		Logger::WARNING   => 4,
		Logger::ERROR     => 5,
		Logger::CRITICAL  => 6,
		Logger::ALERT     => 7,
		Logger::EMERGENCY => 0,
	);

	/**
	 * Construct
	 *
	 * @param  int  $level .
	 * @param  bool $bubble .
	 * @throws MissingExtensionException .
	 */
	public function __construct( $level = Logger::DEBUG, $bubble = true ) {
		if ( ! function_exists( 'zend_monitor_custom_event' ) ) {
			throw new MissingExtensionException( 'You must have Zend Server installed in order to use this handler' );
		}
		parent::__construct( $level, $bubble );
	}

	/**
	 * Function to write
	 *
	 * @param array $record .
	 */
	protected function write( array $record ) {
		$this->writeZendMonitorCustomEvent(
			$this->levelMap[ $record['level'] ], // @codingStandardsIgnoreLine.
			$record['message'],
			$record['formatted']
		);
	}

	/**
	 * Write a record to Zend Monitor
	 *
	 * @param int    $level .
	 * @param string $message .
	 * @param array  $formatted .
	 */
	protected function writeZendMonitorCustomEvent( $level, $message, $formatted ) {
		zend_monitor_custom_event( $level, $message, $formatted );
	}

	/**
	 * To get default format
	 */
	public function getDefaultFormatter() {
		return new NormalizerFormatter();
	}

	/**
	 * Get the level map
	 *
	 * @return array
	 */
	public function getLevelMap() {
		return $this->levelMap; // @codingStandardsIgnoreLine.
	}
}
