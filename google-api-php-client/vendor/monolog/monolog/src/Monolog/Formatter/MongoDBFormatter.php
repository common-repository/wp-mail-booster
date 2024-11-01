<?php // @codingStandardsIgnoreLine
/**
 * This file to Formats a record for use with the MongoDBHandler.
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

/**
 * Formats a record for use with the MongoDBHandler.
 */
class MongoDBFormatter implements FormatterInterface {

	private $exceptionTraceAsString;// @codingStandardsIgnoreLine.
	private $maxNestingLevel;// @codingStandardsIgnoreLine.

	/**
	 * This function is __construct.
	 *
	 * @param int  $maxNestingLevel        0 means infinite nesting, the $record itself is level 1, $record['context'] is 2 .
	 * @param bool $exceptionTraceAsString set to false to log exception traces as a sub documents instead of strings .
	 */
	public function __construct( $maxNestingLevel = 3, $exceptionTraceAsString = true ) {// @codingStandardsIgnoreLine.
		$this->maxNestingLevel        = max( $maxNestingLevel, 0 );// @codingStandardsIgnoreLine.
		$this->exceptionTraceAsString = (bool) $exceptionTraceAsString;// @codingStandardsIgnoreLine.
	}

	/**
	 * This function is format.
	 *
	 * @param array $record .
	 * {@inheritDoc}.
	 */
	public function format( array $record ) {
		return $this->formatArray( $record );
	}

	/**
	 * This function is formatBatch.
	 *
	 * @param array $records .
	 * {@inheritDoc}.
	 */
	public function formatBatch( array $records ) {
		foreach ( $records as $key => $record ) {
			$records[ $key ] = $this->format( $record );
		}

		return $records;
	}

	protected function formatArray( array $record, $nestingLevel = 0 ) {// @codingStandardsIgnoreLine.
		if ( $this->maxNestingLevel == 0 || $nestingLevel <= $this->maxNestingLevel ) {// @codingStandardsIgnoreLine.
			foreach ( $record as $name => $value ) {
				if ( $value instanceof \DateTime ) {
					$record[ $name ] = $this->formatDate( $value, $nestingLevel + 1 );// @codingStandardsIgnoreLine.
				} elseif ( $value instanceof \Exception ) {
					$record[ $name ] = $this->formatException( $value, $nestingLevel + 1 );// @codingStandardsIgnoreLine.
				} elseif ( is_array( $value ) ) {
					$record[ $name ] = $this->formatArray( $value, $nestingLevel + 1 );// @codingStandardsIgnoreLine.
				} elseif ( is_object( $value ) ) {
					$record[ $name ] = $this->formatObject( $value, $nestingLevel + 1 );// @codingStandardsIgnoreLine.
				}
			}
		} else {
			$record = '[...]';
		}

		return $record;
	}

	protected function formatObject( $value, $nestingLevel ) {// @codingStandardsIgnoreLine.
		$objectVars          = get_object_vars( $value );// @codingStandardsIgnoreLine.
		$objectVars['class'] = get_class( $value );// @codingStandardsIgnoreLine.

		return $this->formatArray( $objectVars, $nestingLevel );// @codingStandardsIgnoreLine.
	}

	protected function formatException( \Exception $exception, $nestingLevel ) {// @codingStandardsIgnoreLine.
		$formattedException = array(// @codingStandardsIgnoreLine.
			'class'   => get_class( $exception ),
			'message' => $exception->getMessage(),
			'code'    => $exception->getCode(),
			'file'    => $exception->getFile() . ':' . $exception->getLine(),
		);

		if ( $this->exceptionTraceAsString === true ) {// @codingStandardsIgnoreLine.
			$formattedException['trace'] = $exception->getTraceAsString();// @codingStandardsIgnoreLine.
		} else {
			$formattedException['trace'] = $exception->getTrace();// @codingStandardsIgnoreLine.
		}
// @codingStandardsIgnoreLine.
	}

	protected function formatDate( \DateTime $value, $nestingLevel ) {// @codingStandardsIgnoreLine.
		return new \MongoDate( $value->getTimestamp() );
	}
}
