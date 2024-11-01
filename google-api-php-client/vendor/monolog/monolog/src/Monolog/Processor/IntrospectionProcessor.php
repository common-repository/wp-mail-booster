<?php //@codingStandardsIgnoreLine
/**
 * This file is IntrospectionProcessor.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace Monolog\Processor;

use Monolog\Logger;

/**
 * Injects line/file:class/function where the log message came from
 *
 * Warning: This only works if the handler processes the logs directly.
 * If you put the processor on a handler that is behind a FingersCrossedHandler
 * for example, the processor will only be called once the trigger level is reached,
 * and all the log records will have the same file/line/.. data from the call that
 * triggered the FingersCrossedHandler.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class IntrospectionProcessor {
	/**
	 * The version of thie plugin.
	 *
	 * @access private
	 * @var string $level.
	 */
	private $level;

	private $skipClassesPartials;// @codingStandardsIgnoreLine

	private $skipStackFramesCount;// @codingStandardsIgnoreLine

	private $skipFunctions = array(// @codingStandardsIgnoreLine
		'call_user_func',
		'call_user_func_array',
	);
	/**
	 * This function is __construct.
	 *
	 * @param string $level passes parameter as level.
	 * @param array  $skipClassesPartials passes parameter as skipClassesPartials.
	 * @param string $skipStackFramesCount passes parameter as skipStackFramesCount.
	 */
	public function __construct( $level = Logger::DEBUG, array $skipClassesPartials = array(), $skipStackFramesCount = 0 ) {// @codingStandardsIgnoreLine
		$this->level                = Logger::toMonologLevel( $level );
		$this->skipClassesPartials  = array_merge( array( 'Monolog\\' ), $skipClassesPartials );// @codingStandardsIgnoreLine
		$this->skipStackFramesCount = $skipStackFramesCount;// @codingStandardsIgnoreLine
	}

	/**
	 * This function is __invoke.
	 *
	 * @param array $record passes parameter as record.
	 */
	public function __invoke( array $record ) {
		// return if the level is not high enough.
		if ( $record['level'] < $this->level ) {
			return $record;
		}

		/*
		* http://php.net/manual/en/function.debug-backtrace.php
		* As of 5.3.6, DEBUG_BACKTRACE_IGNORE_ARGS option was added.
		* Any version less than 5.3.6 must use the DEBUG_BACKTRACE_IGNORE_ARGS constant value '2'.
		*/
		$trace = debug_backtrace( ( PHP_VERSION_ID < 50306 ) ? 2 : DEBUG_BACKTRACE_IGNORE_ARGS );// @codingStandardsIgnoreLine

		// skip first since it's always the current method.
		array_shift( $trace );
		// the call_user_func call is also skipped.
		array_shift( $trace );

		$i = 0;

		while ( $this->isTraceClassOrSkippedFunction( $trace, $i ) ) {
			if ( isset( $trace[ $i ]['class'] ) ) {
				foreach ( $this->skipClassesPartials as $part ) {// @codingStandardsIgnoreLine
					if ( strpos( $trace[ $i ]['class'], $part ) !== false ) {
						$i++;
						continue 2;
					}
				}
			} elseif ( in_array( $trace[ $i ]['function'], $this->skipFunctions ) ) {// @codingStandardsIgnoreLine
				$i++;
				continue;
			}

			break;
		}

		$i += $this->skipStackFramesCount;// @codingStandardsIgnoreLine

		// we should have the call source now.
		$record['extra'] = array_merge(
			$record['extra'],
			array(
				'file'     => isset( $trace[ $i - 1 ]['file'] ) ? $trace[ $i - 1 ]['file'] : null,
				'line'     => isset( $trace[ $i - 1 ]['line'] ) ? $trace[ $i - 1 ]['line'] : null,
				'class'    => isset( $trace[ $i ]['class'] ) ? $trace[ $i ]['class'] : null,
				'function' => isset( $trace[ $i ]['function'] ) ? $trace[ $i ]['function'] : null,
			)
		);

		return $record;
	}
	/**
	 * This function is isTraceClassOrSkippedFunction.
	 *
	 * @param array  $trace passes parameter as trace.
	 * @param string $index passes parameter as index.
	 */
	private function isTraceClassOrSkippedFunction( array $trace, $index ) {// @codingStandardsIgnoreLine
		if ( ! isset( $trace[ $index ] ) ) {
			return false;
		}

		return isset( $trace[ $index ]['class'] ) || in_array( $trace[ $index ]['function'], $this->skipFunctions );// @codingStandardsIgnoreLine
	}
}
