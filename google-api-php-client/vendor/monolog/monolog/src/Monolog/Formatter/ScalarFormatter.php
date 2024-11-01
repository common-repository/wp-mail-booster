<?php // @codingStandardsIgnoreLine
/**
 * This file to Formats data into an associative array of scalar values.
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
 * Formats data into an associative array of scalar values.
 * Objects and arrays will be JSON encoded.
 */
class ScalarFormatter extends NormalizerFormatter {

	/**
	 * This function is format.
	 *
	 * @param array $record .
	 * {@inheritdoc}.
	 */
	public function format( array $record ) {
		foreach ( $record as $key => $value ) {
			$record[ $key ] = $this->normalizeValue( $value );
		}

		return $record;
	}

	/**
	 * This function is normalizeValue.
	 *
	 * @param  mixed $value .
	 * @return mixed
	 */
	protected function normalizeValue( $value ) {
		$normalized = $this->normalize( $value );

		if ( is_array( $normalized ) || is_object( $normalized ) ) {
			return $this->toJson( $normalized, true );
		}

		return $normalized;
	}
}
