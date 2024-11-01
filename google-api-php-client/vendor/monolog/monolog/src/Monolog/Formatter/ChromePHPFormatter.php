<?php // @codingStandardsIgnoreLine
/**
 * This file for Formats a log message according to the ChromePHP array format.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Formatter;

use Monolog\Logger;

/**
 * Formats a log message according to the ChromePHP array format
 */
class ChromePHPFormatter implements FormatterInterface {

	/**
	 * Translates Monolog log levels to Wildfire levels.
	 *
	 * @var      array    $logLevels  .
	 */
	private $logLevels = array(// @codingStandardsIgnoreLine.
		Logger::DEBUG     => 'log',
		Logger::INFO      => 'info',
		Logger::NOTICE    => 'info',
		Logger::WARNING   => 'warn',
		Logger::ERROR     => 'error',
		Logger::CRITICAL  => 'error',
		Logger::ALERT     => 'error',
		Logger::EMERGENCY => 'error',
	);

	/**
	 * This function is format .
	 *
	 * @param array $record .
	 * {@inheritdoc} .
	 */
	public function format( array $record ) {
		// Retrieve the line and file if set and remove them from the formatted extra .
		$backtrace = 'unknown';
		if ( isset( $record['extra']['file'], $record['extra']['line'] ) ) {
			$backtrace = $record['extra']['file'] . ' : ' . $record['extra']['line'];
			unset( $record['extra']['file'], $record['extra']['line'] );
		}

		$message = array( 'message' => $record['message'] );
		if ( $record['context'] ) {
			$message['context'] = $record['context'];
		}
		if ( $record['extra'] ) {
			$message['extra'] = $record['extra'];
		}
		if ( count( $message ) === 1 ) {
			$message = reset( $message );
		}

		return array(
			$record['channel'],
			$message,
			$backtrace,
			$this->logLevels[ $record['level'] ],// @codingStandardsIgnoreLine.
		);
	}
	/**
	 * This function is formatBatch .
	 *
	 * @param array $records .
	 * {@inheritdoc} .
	 */
	public function formatBatch( array $records ) {
		$formatted = array();

		foreach ( $records as $record ) {
			$formatted[] = $this->format( $record );
		}

		return $formatted;
	}
}
