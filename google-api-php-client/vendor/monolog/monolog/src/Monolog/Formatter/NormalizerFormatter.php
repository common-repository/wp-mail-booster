<?php // @codingStandardsIgnoreLine
/**
 * This file to Normalizes incoming records.
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

use Exception;

/**
 * Normalizes incoming records to remove objects/resources so it's easier to dump to various targets
 */
class NormalizerFormatter implements FormatterInterface {

	const SIMPLE_DATE = 'Y-m-d H:i:s';

	protected $dateFormat;// @codingStandardsIgnoreLine.

	/**
	 * This function is __construct.
	 *
	 * @param string $dateFormat The format of the timestamp: one supported by DateTime::format .
	 * @throws \RuntimeException .
	 */
	public function __construct( $dateFormat = null ) {// @codingStandardsIgnoreLine.
		$this->dateFormat = $dateFormat ?: static::SIMPLE_DATE;// @codingStandardsIgnoreLine.
		if ( ! function_exists( 'json_encode' ) ) {
			throw new \RuntimeException( 'PHP\'s json extension is required to use Monolog\'s NormalizerFormatter' );
		}
	}

	/**
	 * This function is format .
	 *
	 * @param  array $record .
	 * {@inheritdoc}.
	 */
	public function format( array $record ) {
		return $this->normalize( $record );
	}

	/**
	 * This function is formatBatch .
	 *
	 * @param  array $records .
	 * {@inheritdoc}.
	 */
	public function formatBatch( array $records ) {
		foreach ( $records as $key => $record ) {
			$records[ $key ] = $this->format( $record );
		}

		return $records;
	}
	/**
	 * This function is normalize .
	 *
	 * @param  array $data .
	 * {@inheritdoc}.
	 */
	protected function normalize( $data ) {
		if ( null === $data || is_scalar( $data ) ) {
			if ( is_float( $data ) ) {
				if ( is_infinite( $data ) ) {
					return ( $data > 0 ? '' : '-' ) . 'INF';
				}
				if ( is_nan( $data ) ) {
					return 'NaN';
				}
			}

			return $data;
		}

		if ( is_array( $data ) ) {
			$normalized = array();

			$count = 1;
			foreach ( $data as $key => $value ) {
				if ( $count++ >= 1000 ) {
					$normalized['...'] = 'Over 1000 items (' . count( $data ) . ' total), aborting normalization';
					break;
				}
				$normalized[ $key ] = $this->normalize( $value );
			}

			return $normalized;
		}

		if ( $data instanceof \DateTime ) {
			return $data->format( $this->dateFormat );// @codingStandardsIgnoreLine.
		}

		if ( is_object( $data ) ) {
			// TODO 2.0 only check for Throwable .
			if ( $data instanceof Exception || ( PHP_VERSION_ID > 70000 && $data instanceof \Throwable ) ) {
				return $this->normalizeException( $data );
			}

			// non-serializable objects that implement __toString stringified .
			if ( method_exists( $data, '__toString' ) && ! $data instanceof \JsonSerializable ) {
				$value = $data->__toString();
			} else {
				// the rest is json-serialized in some way .
				$value = $this->toJson( $data, true );
			}

			return sprintf( '[object] (%s: %s)', get_class( $data ), $value );
		}

		if ( is_resource( $data ) ) {
			return sprintf( '[resource] (%s)', get_resource_type( $data ) );
		}

		return '[unknown(' . gettype( $data ) . ')]';
	}
	/**
	 * This function is normalizeException .
	 *
	 * @param  string $e .
	 * @throws \InvalidArgumentException .
	 * {@inheritdoc}.
	 */
	protected function normalizeException( $e ) {
		// TODO 2.0 only check for Throwable .
		if ( ! $e instanceof Exception && ! $e instanceof \Throwable ) {
			throw new \InvalidArgumentException( 'Exception/Throwable expected, got ' . gettype( $e ) . ' / ' . get_class( $e ) );
		}

		$data = array(
			'class'   => get_class( $e ),
			'message' => $e->getMessage(),
			'code'    => $e->getCode(),
			'file'    => $e->getFile() . ':' . $e->getLine(),
		);

		if ( $e instanceof \SoapFault ) {
			if ( isset( $e->faultcode ) ) {
				$data['faultcode'] = $e->faultcode;
			}

			if ( isset( $e->faultactor ) ) {
				$data['faultactor'] = $e->faultactor;
			}

			if ( isset( $e->detail ) ) {
				$data['detail'] = $e->detail;
			}
		}

		$trace = $e->getTrace();
		foreach ( $trace as $frame ) {
			if ( isset( $frame['file'] ) ) {
				$data['trace'][] = $frame['file'] . ':' . $frame['line'];
			} elseif ( isset( $frame['function'] ) && $frame['function'] === '{closure}' ) {// @codingStandardsIgnoreLine.
				// We should again normalize the frames, because it might contain invalid items
				$data['trace'][] = $frame['function'];
			} else {
				// We should again normalize the frames, because it might contain invalid items .
				$data['trace'][] = $this->toJson( $this->normalize( $frame ), true );
			}
		}

		if ( $previous = $e->getPrevious() ) {// @codingStandardsIgnoreLine.
			$data['previous'] = $this->normalizeException( $previous );
		}

		return $data;
	}

	/**
	 * Return the JSON representation of a value
	 *
	 * @param  mixed $data .
	 * @param  bool  $ignoreErrors .
	 * @throws \RuntimeException .
	 * @return string
	 */
	protected function toJson( $data, $ignoreErrors = false ) {// @codingStandardsIgnoreLine.
		// suppress json_encode errors since it's twitchy with some inputs .
		if ( $ignoreErrors ) {// @codingStandardsIgnoreLine.
			return @$this->jsonEncode( $data );// @codingStandardsIgnoreLine.
		}

		$json = $this->jsonEncode( $data );

		if ( $json === false ) {// @codingStandardsIgnoreLine.
			$json = $this->handleJsonError( json_last_error(), $data );
		}

		return $json;
	}

	/**
	 * This function is jsonEncode.
	 *
	 * @param  mixed $data .
	 * @return string JSON encoded data or null on failure .
	 */
	private function jsonEncode( $data ) {
		if ( version_compare( PHP_VERSION, '5.4.0', '>=' ) ) {
			return json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );// @codingStandardsIgnoreLine.
		}

		return json_encode( $data );// @codingStandardsIgnoreLine.
	}

	/**
	 * Handle a json_encode failure.
	 *
	 * If the failure is due to invalid string encoding, try to clean the
	 * input and encode again. If the second encoding attempt fails, the
	 * inital error is not encoding related or the input can't be cleaned then
	 * raise a descriptive exception.
	 *
	 * @param  int   $code return code of json_last_error function .
	 * @param  mixed $data data that was meant to be encoded .
	 * @throws \RuntimeException .
	 * @return string            JSON encoded data after error correction .
	 */
	private function handleJsonError( $code, $data ) {
		if ( $code !== JSON_ERROR_UTF8 ) {// @codingStandardsIgnoreLine.
			$this->throwEncodeError( $code, $data );
		}

		if ( is_string( $data ) ) {
			$this->detectAndCleanUtf8( $data );
		} elseif ( is_array( $data ) ) {
			array_walk_recursive( $data, array( $this, 'detectAndCleanUtf8' ) );
		} else {
			$this->throwEncodeError( $code, $data );
		}

		$json = $this->jsonEncode( $data );

		if ( $json === false ) {// @codingStandardsIgnoreLine.
			$this->throwEncodeError( json_last_error(), $data );
		}

		return $json;
	}

	/**
	 * Throws an exception according to a given code with a customized message
	 *
	 * @param  int   $code return code of json_last_error function .
	 * @param  mixed $data data that was meant to be encoded .
	 * @throws \RuntimeException .
	 */
	private function throwEncodeError( $code, $data ) {
		switch ( $code ) {
			case JSON_ERROR_DEPTH:
				$msg = 'Maximum stack depth exceeded';
				break;
			case JSON_ERROR_STATE_MISMATCH:
				$msg = 'Underflow or the modes mismatch';
				break;
			case JSON_ERROR_CTRL_CHAR:
				$msg = 'Unexpected control character found';
				break;
			case JSON_ERROR_UTF8:
				$msg = 'Malformed UTF-8 characters, possibly incorrectly encoded';
				break;
			default:
				$msg = 'Unknown error';
		}

		throw new \RuntimeException( 'JSON encoding failed: ' . $msg . '. Encoding: ' . var_export( $data, true ) );// @codingStandardsIgnoreLine.
	}

	/**
	 * This function is detectAndCleanUtf8 .
	 *
	 * Detect invalid UTF-8 string characters and convert to valid UTF-8.
	 *
	 * Valid UTF-8 input will be left unmodified, but strings containing
	 * invalid UTF-8 codepoints will be reencoded as UTF-8 with an assumed
	 * original encoding of ISO-8859-15. This conversion may result in
	 * incorrect output if the actual encoding was not ISO-8859-15, but it
	 * will be clean UTF-8 output and will not rely on expensive and fragile
	 * detection algorithms.
	 *
	 * Function converts the input in place in the passed variable so that it
	 * can be used as a callback for array_walk_recursive.
	 *
	 * @param mixed $data .
	 */
	public function detectAndCleanUtf8( &$data ) {
		if ( is_string( $data ) && ! preg_match( '//u', $data ) ) {
			$data = preg_replace_callback(
				'/[\x80-\xFF]+/',
				function ( $m ) {
					return utf8_encode( $m[0] ); },
				$data
			);
			$data = str_replace(
				array( '¤', '¦', '¨', '´', '¸', '¼', '½', '¾' ),
				array( '€', 'Š', 'š', 'Ž', 'ž', 'Œ', 'œ', 'Ÿ' ),
				$data
			);
		}
	}
}
