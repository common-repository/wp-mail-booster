<?php // @codingStandardsIgnoreLine
/**
 * This file to Formats incoming records into a one-line string.
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
 * Formats incoming records into a one-line string
 *
 * This is especially useful for logging to files
 */
class LineFormatter extends NormalizerFormatter {

	const SIMPLE_FORMAT = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
	/**
	 * The version of this plugin.
	 *
	 * @access protected
	 * @var $format .
	 */
	protected $format;
	protected $allowInlineLineBreaks;// @codingStandardsIgnoreLine.
	protected $ignoreEmptyContextAndExtra;// @codingStandardsIgnoreLine.
	protected $includeStacktraces;// @codingStandardsIgnoreLine.

	/**
	 * This function is __construct .
	 *
	 * @param string $format                     The format of the message .
	 * @param string $dateFormat                 The format of the timestamp: one supported by DateTime::format .
	 * @param bool   $allowInlineLineBreaks      Whether to allow inline line breaks in log entries .
	 * @param bool   $ignoreEmptyContextAndExtra .
	 */
	public function __construct( $format = null, $dateFormat = null, $allowInlineLineBreaks = false, $ignoreEmptyContextAndExtra = false ) {// @codingStandardsIgnoreLine.
		$this->format                     = $format ?: static::SIMPLE_FORMAT;
		$this->allowInlineLineBreaks      = $allowInlineLineBreaks;// @codingStandardsIgnoreLine.
		$this->ignoreEmptyContextAndExtra = $ignoreEmptyContextAndExtra;// @codingStandardsIgnoreLine.
		parent::__construct( $dateFormat );// @codingStandardsIgnoreLine.
	}
	/**
	 * This function is includeStacktraces .
	 *
	 * @param string $include .
	 */
	public function includeStacktraces( $include = true ) {
		$this->includeStacktraces = $include;// @codingStandardsIgnoreLine.
		if ( $this->includeStacktraces ) {// @codingStandardsIgnoreLine.
			$this->allowInlineLineBreaks = true;// @codingStandardsIgnoreLine.
		}
	}
	/**
	 * This function is includeStacktraces .
	 *
	 * @param string $allow .
	 */
	public function allowInlineLineBreaks( $allow = true ) {
		$this->allowInlineLineBreaks = $allow;// @codingStandardsIgnoreLine.
	}
	/**
	 * This function is ignoreEmptyContextAndExtra .
	 *
	 * @param string $ignore .
	 */
	public function ignoreEmptyContextAndExtra( $ignore = true ) {
		$this->ignoreEmptyContextAndExtra = $ignore;// @codingStandardsIgnoreLine.
	}

	/**
	 * This function is format .
	 *
	 * @param array $record .
	 * {@inheritdoc} .
	 */
	public function format( array $record ) {
		$vars = parent::format( $record );

		$output = $this->format;

		foreach ( $vars['extra'] as $var => $val ) {
			if ( false !== strpos( $output, '%extra.' . $var . '%' ) ) {
				$output = str_replace( '%extra.' . $var . '%', $this->stringify( $val ), $output );
				unset( $vars['extra'][ $var ] );
			}
		}

		foreach ( $vars['context'] as $var => $val ) {
			if ( false !== strpos( $output, '%context.' . $var . '%' ) ) {
				$output = str_replace( '%context.' . $var . '%', $this->stringify( $val ), $output );
				unset( $vars['context'][ $var ] );
			}
		}

		if ( $this->ignoreEmptyContextAndExtra ) { //@codingStandardsIgnoreLine
			if ( empty( $vars['context'] ) ) {
				unset( $vars['context'] );
				$output = str_replace( '%context%', '', $output );
			}

			if ( empty( $vars['extra'] ) ) {
				unset( $vars['extra'] );
				$output = str_replace( '%extra%', '', $output );
			}
		}

		foreach ( $vars as $var => $val ) {
			if ( false !== strpos( $output, '%' . $var . '%' ) ) {
				$output = str_replace( '%' . $var . '%', $this->stringify( $val ), $output );
			}
		}

		// remove leftover %extra.xxx% and %context.xxx% if any .
		if ( false !== strpos( $output, '%' ) ) {
			$output = preg_replace( '/%(?:extra|context)\..+?%/', '', $output );
		}

		return $output;
	}

	/**
	 * This function is formatBatch .
	 *
	 * @param array $records .
	 * {@inheritdoc} .
	 */
	public function formatBatch( array $records ) {
		$message = '';
		foreach ( $records as $record ) {
			$message .= $this->format( $record );
		}

		return $message;
	}
	/**
	 * This function is stringify .
	 *
	 * @param array $value .
	 * {@inheritdoc} .
	 */
	public function stringify( $value ) {
		return $this->replaceNewlines( $this->convertToString( $value ) );
	}
	/**
	 * This function is normalizeException .
	 *
	 * @param string $e .
	 * @throws \InvalidArgumentException .
	 * {@inheritdoc} .
	 */
	protected function normalizeException( $e ) {
		// TODO 2.0 only check for Throwable .
		if ( ! $e instanceof \Exception && ! $e instanceof \Throwable ) {
			throw new \InvalidArgumentException( 'Exception/Throwable expected, got ' . gettype( $e ) . ' / ' . get_class( $e ) );
		}

		$previousText = '';//@codingStandardsIgnoreLine
		if ( $previous = $e->getPrevious() ) {//@codingStandardsIgnoreLine
			do {
				$previousText .= ', ' . get_class( $previous ) . '(code: ' . $previous->getCode() . '): ' . $previous->getMessage() . ' at ' . $previous->getFile() . ':' . $previous->getLine();//@codingStandardsIgnoreLine
			} while ( $previous = $previous->getPrevious() );//@codingStandardsIgnoreLine
		}

		$str = '[object] (' . get_class( $e ) . '(code: ' . $e->getCode() . '): ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine() . $previousText . ')';//@codingStandardsIgnoreLine
		if ( $this->includeStacktraces ) {//@codingStandardsIgnoreLine
			$str .= "\n[stacktrace]\n" . $e->getTraceAsString() . "\n";
		}

		return $str;
	}
	/**
	 * This function is convertToString .
	 *
	 * @param array $data .
	 * {@inheritdoc} .
	 */
	protected function convertToString( $data ) {
		if ( null === $data || is_bool( $data ) ) {
			return var_export( $data, true );//@codingStandardsIgnoreLine
		}

		if ( is_scalar( $data ) ) {
			return (string) $data;
		}

		if ( version_compare( PHP_VERSION, '5.4.0', '>=' ) ) {
			return $this->toJson( $data, true );
		}

		return str_replace( '\\/', '/', @json_encode( $data ) );//@codingStandardsIgnoreLine
	}
	/**
	 * This function is replaceNewlines .
	 *
	 * @param string $str .
	 * {@inheritdoc} .
	 */
	protected function replaceNewlines( $str ) {
		if ( $this->allowInlineLineBreaks ) {//@codingStandardsIgnoreLine
			if ( 0 === strpos( $str, '{' ) ) {
				return str_replace( array( '\r', '\n' ), array( "\r", "\n" ), $str );
			}

			return $str;
		}

		return str_replace( array( "\r\n", "\r", "\n" ), ' ', $str );
	}
}
