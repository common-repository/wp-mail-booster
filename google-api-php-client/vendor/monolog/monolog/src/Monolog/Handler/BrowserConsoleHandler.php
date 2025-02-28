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

/**
 * Handler sending logs to browser's javascript console with no browser extension required
 */
class BrowserConsoleHandler extends AbstractProcessingHandler {
	/**
	 * The version of this plugin.
	 *
	 * @var $initialized.
	 */
	protected static $initialized = false;
	/**
	 * The version of this plugin.
	 *
	 * @var $records.
	 */
	protected static $records = array();

	/**
	 * {@inheritDoc}
	 *
	 * Formatted output may contain some formatting markers to be transferred to `console.log` using the %c format.
	 *
	 * Example of formatted string:
	 *
	 *     You can do [[blue text]]{color: blue} or [[green background]]{background-color: green; color: white}
	 */
	protected function getDefaultFormatter() {
		return new LineFormatter( '[[%channel%]]{macro: autolabel} [[%level_name%]]{font-weight: bold} %message%' );
	}

	/**
	 * This function is .
	 *
	 * @param array $record .
	 * {@inheritDoc}.
	 */
	protected function write( array $record ) {
		// Accumulate records .
		self::$records[] = $record;

		// Register shutdown handler if not already done .
		if ( ! self::$initialized ) {
			self::$initialized = true;
			$this->registerShutdownFunction();
		}
	}

	/**
	 * Convert records to javascript console commands and send it to the browser.
	 * This method is automatically called on PHP shutdown if output is HTML or Javascript.
	 */
	public static function send() {
		$format = self::getResponseFormat();
		if ( $format === 'unknown' ) {// @codingStandardsIgnoreLine.
			return;
		}

		if ( count( self::$records ) ) {
			if ( $format === 'html' ) {// @codingStandardsIgnoreLine.
				self::writeOutput( '<script>' . self::generateScript() . '</script>' );
			} elseif ( $format === 'js' ) {// @codingStandardsIgnoreLine.
				self::writeOutput( self::generateScript() );
			}
			self::reset();
		}
	}

	/**
	 * Forget all logged records
	 */
	public static function reset() {
		self::$records = array();
	}

	/**
	 * Wrapper for register_shutdown_function to allow overriding
	 */
	protected function registerShutdownFunction() {
		if ( PHP_SAPI !== 'cli' ) {
			register_shutdown_function( array( 'Monolog\Handler\BrowserConsoleHandler', 'send' ) );
		}
	}

	/**
	 * Wrapper for echo to allow overriding
	 *
	 * @param string $str .
	 */
	protected static function writeOutput( $str ) {
		echo $str;// @codingStandardsIgnoreLine.
	}

	/**
	 * Checks the format of the response
	 *
	 * If Content-Type is set to application/javascript or text/javascript -> js
	 * If Content-Type is set to text/html, or is unset -> html
	 * If Content-Type is anything else -> unknown
	 *
	 * @return string One of 'js', 'html' or 'unknown'
	 */
	protected static function getResponseFormat() {
		// Check content type .
		foreach ( headers_list() as $header ) {
			if ( stripos( $header, 'content-type:' ) === 0 ) {
				// This handler only works with HTML and javascript outputs
				// text/javascript is obsolete in favour of application/javascript, but still used .
				if ( stripos( $header, 'application/javascript' ) !== false || stripos( $header, 'text/javascript' ) !== false ) {
					return 'js';
				}
				if ( stripos( $header, 'text/html' ) === false ) {
					return 'unknown';
				}
				break;
			}
		}

		return 'html';
	}
	/**
	 * This function is generateScript.
	 */
	private static function generateScript() {
		$script = array();
		foreach ( self::$records as $record ) {
			$context = self::dump( 'Context', $record['context'] );
			$extra   = self::dump( 'Extra', $record['extra'] );

			if ( empty( $context ) && empty( $extra ) ) {
				$script[] = self::call_array( 'log', self::handleStyles( $record['formatted'] ) );
			} else {
				$script = array_merge(
					$script,
					array( self::call_array( 'groupCollapsed', self::handleStyles( $record['formatted'] ) ) ),
					$context,
					$extra,
					array( self::call( 'groupEnd' ) )
				);
			}
		}

		return "(function (c) {if (c && c.groupCollapsed) {\n" . implode( "\n", $script ) . "\n}})(console);";
	}
	/**
	 * This function is handleStyles.
	 *
	 * @param string $formatted .
	 */
	private static function handleStyles( $formatted ) {
		$args   = array( self::quote( 'font-weight: normal' ) );
		$format = '%c' . $formatted;
		preg_match_all( '/\[\[(.*?)\]\]\{([^}]*)\}/s', $format, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER );

		foreach ( array_reverse( $matches ) as $match ) {
			$args[] = self::quote( self::handleCustomStyles( $match[2][0], $match[1][0] ) );
			$args[] = '"font-weight: normal"';

			$pos    = $match[0][1];
			$format = substr( $format, 0, $pos ) . '%c' . $match[1][0] . '%c' . substr( $format, $pos + strlen( $match[0][0] ) );
		}

		array_unshift( $args, self::quote( $format ) );

		return $args;
	}
	/**
	 * This function is handleCustomStyles.
	 *
	 * @param string $style .
	 * @param string $string .
	 */
	private static function handleCustomStyles( $style, $string ) {
		static $colors = array( 'blue', 'green', 'red', 'magenta', 'orange', 'black', 'grey' );
		static $labels = array();

		return preg_replace_callback(
			'/macro\s*:(.*?)(?:;|$)/', function ( $m ) use ( $string, &$colors, &$labels ) {
				if ( trim( $m[1] ) === 'autolabel' ) {
					// Format the string as a label with consistent auto assigned background color .
					if ( ! isset( $labels[ $string ] ) ) {
						$labels[ $string ] = $colors[ count( $labels ) % count( $colors ) ];
					}
					$color = $labels[ $string ];

					return "background-color: $color; color: white; border-radius: 3px; padding: 0 2px 0 2px";
				}

				return $m[1];
			}, $style
		);
	}
	/**
	 * This function is handleCustomStyles.
	 *
	 * @param string       $title .
	 * @param string array $dict .
	 */
	private static function dump( $title, array $dict ) {
		$script = array();
		$dict   = array_filter( $dict );
		if ( empty( $dict ) ) {
			return $script;
		}
		$script[] = self::call( 'log', self::quote( '%c%s' ), self::quote( 'font-weight: bold' ), self::quote( $title ) );
		foreach ( $dict as $key => $value ) {
			$value = json_encode( $value );// @codingStandardsIgnoreLine.
			if ( empty( $value ) ) {
				$value = self::quote( '' );
			}
			$script[] = self::call( 'log', self::quote( '%s: %o' ), self::quote( $key ), $value );
		}

		return $script;
	}
	/**
	 * This function is quote.
	 *
	 * @param string $arg .
	 */
	private static function quote( $arg ) {
		return '"' . addcslashes( $arg, "\"\n\\" ) . '"';
	}
	/**
	 * This function is call.
	 */
	private static function call() {
		$args   = func_get_args();
		$method = array_shift( $args );

		return self::call_array( $method, $args );
	}
	/**
	 * This function is quote.
	 *
	 * @param string $method .
	 * @param array  $args .
	 */
	private static function call_array( $method, array $args ) {
		return 'c.' . $method . '(' . implode( ', ', $args ) . ');';
	}
}
