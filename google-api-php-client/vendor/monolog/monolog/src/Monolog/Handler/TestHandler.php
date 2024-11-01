<?php // @codingStandardsIgnoreLine.
/**
 * This file for testing purpose
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

/**
 * Used for testing purposes.
 *
 * It records all records and gives you access to them for verification.
 *
 * @method bool hasEmergency($record)
 * @method bool hasAlert($record)
 * @method bool hasCritical($record)
 * @method bool hasError($record)
 * @method bool hasWarning($record)
 * @method bool hasNotice($record)
 * @method bool hasInfo($record)
 * @method bool hasDebug($record)
 *
 * @method bool hasEmergencyRecords()
 * @method bool hasAlertRecords()
 * @method bool hasCriticalRecords()
 * @method bool hasErrorRecords()
 * @method bool hasWarningRecords()
 * @method bool hasNoticeRecords()
 * @method bool hasInfoRecords()
 * @method bool hasDebugRecords()
 *
 * @method bool hasEmergencyThatContains($message)
 * @method bool hasAlertThatContains($message)
 * @method bool hasCriticalThatContains($message)
 * @method bool hasErrorThatContains($message)
 * @method bool hasWarningThatContains($message)
 * @method bool hasNoticeThatContains($message)
 * @method bool hasInfoThatContains($message)
 * @method bool hasDebugThatContains($message)
 *
 * @method bool hasEmergencyThatMatches($message)
 * @method bool hasAlertThatMatches($message)
 * @method bool hasCriticalThatMatches($message)
 * @method bool hasErrorThatMatches($message)
 * @method bool hasWarningThatMatches($message)
 * @method bool hasNoticeThatMatches($message)
 * @method bool hasInfoThatMatches($message)
 * @method bool hasDebugThatMatches($message)
 *
 * @method bool hasEmergencyThatPasses($message)
 * @method bool hasAlertThatPasses($message)
 * @method bool hasCriticalThatPasses($message)
 * @method bool hasErrorThatPasses($message)
 * @method bool hasWarningThatPasses($message)
 * @method bool hasNoticeThatPasses($message)
 * @method bool hasInfoThatPasses($message)
 * @method bool hasDebugThatPasses($message)
 */
class TestHandler extends AbstractProcessingHandler {

	/**
	 * Variable for records
	 *
	 * @var array
	 */
	protected $records = array();
	/**
	 * Variable record by level
	 *
	 * @var array
	 */
	protected $recordsByLevel = array(); // @codingStandardsIgnoreLine.
	/**
	 * Function to get records
	 */
	public function getRecords() {
		return $this->records;
	}
	/**
	 * Function to clear
	 */
	public function clear() {
		$this->records        = array();
		$this->recordsByLevel = array(); // @codingStandardsIgnoreLine.
	}
	/**
	 * Function for records
	 *
	 * @param array $level .
	 */
	public function hasRecords( $level ) {
		return isset( $this->recordsByLevel[ $level ] ); // @codingStandardsIgnoreLine.
	}
	/**
	 * Function for record
	 *
	 * @param array  $record .
	 * @param string $level .
	 */
	public function hasRecord( $record, $level ) {
		if ( is_array( $record ) ) {
			$record = $record['message'];
		}

		return $this->hasRecordThatPasses(
			function ( $rec ) use ( $record ) {
				return $rec['message'] === $record;
			}, $level
		);
	}
	/**
	 * Function for record
	 *
	 * @param array  $message .
	 * @param string $level .
	 */
	public function hasRecordThatContains( $message, $level ) {
		return $this->hasRecordThatPasses(
			function ( $rec ) use ( $message ) {
				return strpos( $rec['message'], $message ) !== false;
			}, $level
		);
	}
	/**
	 * Function for record
	 *
	 * @param string $regex .
	 * @param string $level .
	 */
	public function hasRecordThatMatches( $regex, $level ) {
		return $this->hasRecordThatPasses(
			function ( $rec ) use ( $regex ) {
				return preg_match( $regex, $rec['message'] ) > 0;
			}, $level
		);
	}
	/**
	 * Function for record
	 *
	 * @param string $predicate .
	 * @param string $level .
	 * @throws \InvalidArgumentException .
	 */
	public function hasRecordThatPasses( $predicate, $level ) {
		if ( ! is_callable( $predicate ) ) {
			throw new \InvalidArgumentException( 'Expected a callable for hasRecordThatSucceeds' );
		}

		if ( ! isset( $this->recordsByLevel[ $level ] ) ) { // @codingStandardsIgnoreLine.
			return false;
		}

		foreach ( $this->recordsByLevel[ $level ] as $i => $rec ) { // @codingStandardsIgnoreLine.
			if ( call_user_func( $predicate, $rec, $i ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Function for record
	 *
	 * @param array $record .
	 */
	protected function write( array $record ) {
		$this->recordsByLevel[ $record['level'] ][] = $record; // @codingStandardsIgnoreLine.
		$this->records[]                            = $record;
	}
	/**
	 * Function for call
	 *
	 * @param string $method .
	 * @param string $args .
	 * @throws \BadMethodCallException .
	 */
	public function __call( $method, $args ) {
		if ( preg_match( '/(.*)(Debug|Info|Notice|Warning|Error|Critical|Alert|Emergency)(.*)/', $method, $matches ) > 0 ) {
			$genericMethod = $matches[1] . ( 'Records' !== $matches[3] ? 'Record' : '' ) . $matches[3]; // @codingStandardsIgnoreLine.
			$level         = constant( 'Monolog\Logger::' . strtoupper( $matches[2] ) );
			if ( method_exists( $this, $genericMethod ) ) { // @codingStandardsIgnoreLine.
				$args[] = $level;

				return call_user_func_array( array( $this, $genericMethod ), $args ); // @codingStandardsIgnoreLine.
			}
		}

		throw new \BadMethodCallException( 'Call to undefined method ' . get_class( $this ) . '::' . $method . '()' );
	}
}
