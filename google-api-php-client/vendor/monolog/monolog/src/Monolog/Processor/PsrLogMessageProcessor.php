<?php //@codingStandardsIgnoreLine
/**
 * This file is PsrLogMessageProcessor.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace Monolog\Processor;

/**
 * Processes a record's message according to PSR-3 rules
 *
 * It replaces {foo} with the value from $context['foo']
 */
class PsrLogMessageProcessor {

	/**
	 * This function is __invoke.
	 *
	 * @param  array $record passes parameter as record.
	 * @return array
	 */
	public function __invoke( array $record ) {
		if ( false === strpos( $record['message'], '{' ) ) {
			return $record;
		}

		$replacements = array();
		foreach ( $record['context'] as $key => $val ) {
			if ( is_null( $val ) || is_scalar( $val ) || ( is_object( $val ) && method_exists( $val, '__toString' ) ) ) {
				$replacements[ '{' . $key . '}' ] = $val;
			} elseif ( is_object( $val ) ) {
				$replacements[ '{' . $key . '}' ] = '[object ' . get_class( $val ) . ']';
			} else {
				$replacements[ '{' . $key . '}' ] = '[' . gettype( $val ) . ']';
			}
		}

		$record['message'] = strtr( $record['message'], $replacements );

		return $record;
	}
}
