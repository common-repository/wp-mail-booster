<?php // @codingStandardsIgnoreLine
/**
 * This file for Expands URI templates .
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace GuzzleHttp;

/**
 * Expands URI templates. Userland implementation of PECL uri_template.
 *
 * @link http://tools.ietf.org/html/rfc6570
 */
class UriTemplate {
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $template.
	 */
	private $template;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $variables.
	 */
	private $variables;

	private static $operatorHash = [// @codingStandardsIgnoreLine
		''  => [
			'prefix' => '',
			'joiner' => ',',
			'query'  => false,
		],
		'+' => [
			'prefix' => '',
			'joiner' => ',',
			'query'  => false,
		],
		'#' => [
			'prefix' => '#',
			'joiner' => ',',
			'query'  => false,
		],
		'.' => [
			'prefix' => '.',
			'joiner' => '.',
			'query'  => false,
		],
		'/' => [
			'prefix' => '/',
			'joiner' => '/',
			'query'  => false,
		],
		';' => [
			'prefix' => ';',
			'joiner' => ';',
			'query'  => true,
		],
		'?' => [
			'prefix' => '?',
			'joiner' => '&',
			'query'  => true,
		],
		'&' => [
			'prefix' => '&',
			'joiner' => '&',
			'query'  => true,
		],
	];
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $delims.
	 */
	private static $delims = [
		':',
		'/',
		'?',
		'#',
		'[',
		']',
		'@',
		'!',
		'$',
		'&',
		'\'',
		'(',
		')',
		'*',
		'+',
		',',
		';',
		'=',
	];

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $delimsPct.
	 */
	private static $delimsPct = [// @codingStandardsIgnoreLine
		'%3A',
		'%2F',
		'%3F',
		'%23',
		'%5B',
		'%5D',
		'%40',
		'%21',
		'%24',
		'%26',
		'%27',
		'%28',
		'%29',
		'%2A',
		'%2B',
		'%2C',
		'%3B',
		'%3D',
	];
	/**
	 * This function is expand.
	 *
	 * @param string       $template passes parameter as template.
	 * @param string array $variables passes parameter as variables.
	 */
	public function expand( $template, array $variables ) {
		if ( false === strpos( $template, '{' ) ) {
			return $template;
		}

		$this->template  = $template;
		$this->variables = $variables;

		return preg_replace_callback(
			'/\{([^\}]+)\}/',
			[ $this, 'expandMatch' ],
			$this->template
		);
	}

	/**
	 * Parse an expression into parts
	 *
	 * @param string $expression Expression to parse.
	 *
	 * @return array Returns an associative array of parts.
	 */
	private function parseExpression( $expression ) {// @codingStandardsIgnoreLine
		$result = [];

		if ( isset( self::$operatorHash[ $expression[0] ] ) ) {// @codingStandardsIgnoreLine
			$result['operator'] = $expression[0];
			$expression         = substr( $expression, 1 );
		} else {
			$result['operator'] = '';
		}

		foreach ( explode( ',', $expression ) as $value ) {
			$value   = trim( $value );
			$varspec = [];
			if ( $colonPos = strpos( $value, ':' ) ) {// @codingStandardsIgnoreLine
				$varspec['value']    = substr( $value, 0, $colonPos );// @codingStandardsIgnoreLine
				$varspec['modifier'] = ':';
				$varspec['position'] = (int) substr( $value, $colonPos + 1 );// @codingStandardsIgnoreLine
			} elseif ( substr( $value, -1 ) === '*' ) {
				$varspec['modifier'] = '*';
				$varspec['value']    = substr( $value, 0, -1 );
			} else {
				$varspec['value']    = (string) $value;
				$varspec['modifier'] = '';
			}
			$result['values'][] = $varspec;
		}

		return $result;
	}

	/**
	 * Process an expansion
	 *
	 * @param array $matches Matches met in the preg_replace_callback.
	 *
	 * @return string Returns the replacement string
	 */
	private function expandMatch( array $matches ) {// @codingStandardsIgnoreLine
		static $rfc1738to3986 = [
			'+'   => '%20',
			'%7e' => '~',
		];

		$replacements = [];
		$parsed       = self::parseExpression( $matches[1] );
		$prefix       = self::$operatorHash[ $parsed['operator'] ]['prefix'];// @codingStandardsIgnoreLine
		$joiner       = self::$operatorHash[ $parsed['operator'] ]['joiner'];// @codingStandardsIgnoreLine
		$useQuery     = self::$operatorHash[ $parsed['operator'] ]['query'];// @codingStandardsIgnoreLine

		foreach ( $parsed['values'] as $value ) {

			if ( ! isset( $this->variables[ $value['value'] ] ) ) {
				continue;
			}

			$variable         = $this->variables[ $value['value'] ];
			$actuallyUseQuery = $useQuery;// @codingStandardsIgnoreLine
			$expanded         = '';

			if ( is_array( $variable ) ) {

				$isAssoc = $this->isAssoc( $variable );// @codingStandardsIgnoreLine
				$kvp     = [];
				foreach ( $variable as $key => $var ) {

					if ( $isAssoc ) {// @codingStandardsIgnoreLine
						$key           = rawurlencode( $key );
						$isNestedArray = is_array( $var );// @codingStandardsIgnoreLine
					} else {
						$isNestedArray = false;// @codingStandardsIgnoreLine
					}

					if ( ! $isNestedArray ) {// @codingStandardsIgnoreLine
						$var = rawurlencode( $var );
						if ( $parsed['operator'] === '+' ||// @codingStandardsIgnoreLine
							$parsed['operator'] === '#'// @codingStandardsIgnoreLine
						) {
							$var = $this->decodeReserved( $var );
						}
					}

					if ( '*' === $value['modifier'] ) {
						if ( $isAssoc ) {// @codingStandardsIgnoreLine
							if ( $isNestedArray ) {// @codingStandardsIgnoreLine
								// Nested arrays must allow for deeply nested
								// structures.
								$var = strtr(
									http_build_query( [ $key => $var ] ),
									$rfc1738to3986
								);
							} else {
								$var = $key . '=' . $var;
							}
						} elseif ( $key > 0 && $actuallyUseQuery ) {// @codingStandardsIgnoreLine
							$var = $value['value'] . '=' . $var;
						}
					}

					$kvp[ $key ] = $var;
				}

				if ( empty( $variable ) ) {
					$actuallyUseQuery = false;// @codingStandardsIgnoreLine
				} elseif ( $value['modifier'] === '*' ) {// @codingStandardsIgnoreLine
					$expanded = implode( $joiner, $kvp );
					if ( $isAssoc ) {// @codingStandardsIgnoreLine
						// Don't prepend the value name when using the explode
						// modifier with an associative array.
						$actuallyUseQuery = false;// @codingStandardsIgnoreLine
					}
				} else {
					if ( $isAssoc ) {// @codingStandardsIgnoreLine
						// When an associative array is encountered and the
						// explode modifier is not set, then the result must be
						// a comma separated list of keys followed by their
						// respective values.
						foreach ( $kvp as $k => &$v ) {
							$v = $k . ',' . $v;
						}
					}
					$expanded = implode( ',', $kvp );
				}
			} else {
				if ( ':' === $value['modifier'] ) {
					$variable = substr( $variable, 0, $value['position'] );
				}
				$expanded = rawurlencode( $variable );
				if ( '+' === $parsed['operator'] || '#' === $parsed['operator'] ) {
					$expanded = $this->decodeReserved( $expanded );
				}
			}

			if ( $actuallyUseQuery ) {// @codingStandardsIgnoreLine
				if ( ! $expanded && '&' !== $joiner ) {
					$expanded = $value['value'];
				} else {
					$expanded = $value['value'] . '=' . $expanded;
				}
			}

			$replacements[] = $expanded;
		}

		$ret = implode( $joiner, $replacements );
		if ( $ret && $prefix ) {
			return $prefix . $ret;
		}

		return $ret;
	}

	/**
	 * Determines if an array is associative.
	 *
	 * This makes the assumption that input arrays are sequences or hashes.
	 * This assumption is a tradeoff for accuracy in favor of speed, but it
	 * should work in almost every case where input is supplied for a URI
	 * template.
	 *
	 * @param array $array Array to check.
	 *
	 * @return bool
	 */
	private function isAssoc( array $array ) {// @codingStandardsIgnoreLine
		return $array && array_keys( $array )[0] !== 0;// @codingStandardsIgnoreLine
	}

	/**
	 * Removes percent encoding on reserved characters (used with + and #
	 * modifiers).
	 *
	 * @param string $string String to fix.
	 *
	 * @return string
	 */
	private function decodeReserved( $string ) {// @codingStandardsIgnoreLine
		return str_replace( self::$delimsPct, self::$delims, $string );// @codingStandardsIgnoreLine
	}
}
