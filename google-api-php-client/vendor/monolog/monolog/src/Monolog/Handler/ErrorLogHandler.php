<?php // @codingStandardsIgnoreLine.
/**
 * This file is part of the Monolog package.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/handler
 * @version 2.0.0
 */
namespace Monolog\Handler;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;

/**
 * Stores to PHP error_log() handler.
 */
class ErrorLogHandler extends AbstractProcessingHandler {

	const OPERATING_SYSTEM = 0;
	const SAPI             = 4;

	protected $messageType;// @codingStandardsIgnoreLine.
	protected $expandNewlines;// @codingStandardsIgnoreLine.

	/**
	 * This function is __construct.
	 *
	 * @param int     $messageType    Says where the error should go .
	 * @param int     $level          The minimum logging level at which this handler will be triggered .
	 * @param Boolean $bubble         Whether the messages that are handled can bubble up the stack or not .
	 * @param Boolean $expandNewlines If set to true, newlines in the message will be expanded to be take multiple log entries .
	 * @throws \InvalidArgumentException .
	 */
	public function __construct( $messageType = self::OPERATING_SYSTEM, $level = Logger::DEBUG, $bubble = true, $expandNewlines = false ) {// @codingStandardsIgnoreLine.
		parent::__construct( $level, $bubble );

		if ( false === in_array( $messageType, self::getAvailableTypes() ) ) {// @codingStandardsIgnoreLine.
			$message = sprintf( 'The given message type "%s" is not supported', print_r( $messageType, true ) );// @codingStandardsIgnoreLine.
			throw new \InvalidArgumentException( $message );
		}

		$this->messageType    = $messageType;// @codingStandardsIgnoreLine.
		$this->expandNewlines = $expandNewlines;// @codingStandardsIgnoreLine.
	}

	/**
	 * This function is getAvailableTypes .
	 *
	 * @return array With all available types
	 */
	public static function getAvailableTypes() {
		return array(
			self::OPERATING_SYSTEM,
			self::SAPI,
		);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function getDefaultFormatter() {
		return new LineFormatter( '[%datetime%] %channel%.%level_name%: %message% %context% %extra%' );
	}

	/**
	 * This function is getAvailableTypes .
	 *
	 * @param array $record .
	 * {@inheritdoc}.
	 */
	protected function write( array $record ) {
		if ( $this->expandNewlines ) {// @codingStandardsIgnoreLine.
			$lines = preg_split( '{[\r\n]+}', (string) $record['formatted'] );
			foreach ( $lines as $line ) {
				error_log( $line, $this->messageType );// @codingStandardsIgnoreLine.
			}
		} else {
			error_log( (string) $record['formatted'], $this->messageType );// @codingStandardsIgnoreLine.
		}
	}
}
