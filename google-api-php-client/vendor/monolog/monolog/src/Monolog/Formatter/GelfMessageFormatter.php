<?php // @codingStandardsIgnoreLine
/**
 * This file to Serializes a log message to GELF.
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
use Gelf\Message;

/**
 * Serializes a log message to GELF
 */
class GelfMessageFormatter extends NormalizerFormatter {

	const DEFAULT_MAX_LENGTH = 32766;

	/**
	 * The version of this plugin.
	 *
	 * @var string the name of the system for the Gelf log message
	 */
	protected $systemName;// @codingStandardsIgnoreLine.

	/**
	 * The version of this plugin.
	 *
	 * @access protected
	 * @var string a prefix for 'extra' fields from the Monolog record (optional) .
	 */
	protected $extraPrefix;// @codingStandardsIgnoreLine.

	/**
	 * The version of this plugin.
	 *
	 * @var string a prefix for 'context' fields from the Monolog record (optional)
	 */
	protected $contextPrefix;// @codingStandardsIgnoreLine.

	/**
	 * The version of this plugin.
	 *
	 * @var int max length per field
	 */
	protected $maxLength;// @codingStandardsIgnoreLine.

	/**
	 * Translates Monolog log levels to Graylog2 log priorities.
	 *
	 * @var string $logLevels .
	 */
	private $logLevels = array(// @codingStandardsIgnoreLine.
		Logger::DEBUG     => 7,
		Logger::INFO      => 6,
		Logger::NOTICE    => 5,
		Logger::WARNING   => 4,
		Logger::ERROR     => 3,
		Logger::CRITICAL  => 2,
		Logger::ALERT     => 1,
		Logger::EMERGENCY => 0,
	);
	/**
	 * This function is __construct.
	 *
	 * @param string $systemName .
	 * @param string $extraPrefix .
	 * @param string $contextPrefix .
	 * @param string $maxLength .
	 */
	public function __construct( $systemName = null, $extraPrefix = null, $contextPrefix = 'ctxt_', $maxLength = null ) {// @codingStandardsIgnoreLine.
		parent::__construct( 'U.u' );

		$this->systemName = $systemName ?: gethostname();// @codingStandardsIgnoreLine.

		$this->extraPrefix   = $extraPrefix;// @codingStandardsIgnoreLine.
		$this->contextPrefix = $contextPrefix;// @codingStandardsIgnoreLine.
		$this->maxLength     = is_null( $maxLength ) ? self::DEFAULT_MAX_LENGTH : $maxLength;// @codingStandardsIgnoreLine.
	}

	/**
	 * This function is format.
	 *
	 * @param array $record .
	 * {@inheritdoc}.
	 * @throws \InvalidArgumentException .
	 */
	public function format( array $record ) {
		$record = parent::format( $record );

		if ( ! isset( $record['datetime'], $record['message'], $record['level'] ) ) {
			throw new \InvalidArgumentException( 'The record should at least contain datetime, message and level keys, ' . var_export( $record, true ) . ' given' );// @codingStandardsIgnoreLine .
		} // @codingStandardsIgnoreLine .

		$message = new Message();
		$message
			->setTimestamp( $record['datetime'] )
			->setShortMessage( (string) $record['message'] )
			->setHost( $this->systemName )// @codingStandardsIgnoreLine .
			->setLevel( $this->logLevels[ $record['level'] ] );// @codingStandardsIgnoreLine .

		// message length + system name length + 200 for padding / metadata .
		$len = 200 + strlen( (string) $record['message'] ) + strlen( $this->systemName );// @codingStandardsIgnoreLine .

		if ( $len > $this->maxLength ) {// @codingStandardsIgnoreLine .
			$message->setShortMessage( substr( $record['message'], 0, $this->maxLength ) );// @codingStandardsIgnoreLine .
		}

		if ( isset( $record['channel'] ) ) {
			$message->setFacility( $record['channel'] );
		}
		if ( isset( $record['extra']['line'] ) ) {
			$message->setLine( $record['extra']['line'] );
			unset( $record['extra']['line'] );
		}
		if ( isset( $record['extra']['file'] ) ) {
			$message->setFile( $record['extra']['file'] );
			unset( $record['extra']['file'] );
		}

		foreach ( $record['extra'] as $key => $val ) {
			$val = is_scalar( $val ) || null === $val ? $val : $this->toJson( $val );
			$len = strlen( $this->extraPrefix . $key . $val );// @codingStandardsIgnoreLine .
			if ( $len > $this->maxLength ) {// @codingStandardsIgnoreLine .
				$message->setAdditional( $this->extraPrefix . $key, substr( $val, 0, $this->maxLength ) );// @codingStandardsIgnoreLine .
				break;
			}
			$message->setAdditional( $this->extraPrefix . $key, $val );// @codingStandardsIgnoreLine .
		}

		foreach ( $record['context'] as $key => $val ) {
			$val = is_scalar( $val ) || null === $val ? $val : $this->toJson( $val );
			$len = strlen( $this->contextPrefix . $key . $val );// @codingStandardsIgnoreLine .
			if ( $len > $this->maxLength ) {// @codingStandardsIgnoreLine .
				$message->setAdditional( $this->contextPrefix . $key, substr( $val, 0, $this->maxLength ) );// @codingStandardsIgnoreLine .
				break;
			}
			$message->setAdditional( $this->contextPrefix . $key, $val );// @codingStandardsIgnoreLine .s
		}

		if ( null === $message->getFile() && isset( $record['context']['exception']['file'] ) ) {
			if ( preg_match( '/^(.+):([0-9]+)$/', $record['context']['exception']['file'], $matches ) ) {
				$message->setFile( $matches[1] );
				$message->setLine( $matches[2] );
			}
		}

		return $message;
	}
}
