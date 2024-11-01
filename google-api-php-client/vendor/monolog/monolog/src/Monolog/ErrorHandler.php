<?php //@codingStandardsIgnoreLine
/**
 * This file is ErrorHandler.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace Monolog;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Monolog\Handler\AbstractHandler;

/**
 * Monolog error handler
 *
 * A facility to enable logging of runtime errors, exceptions and fatal errors.
 */
class ErrorHandler {
	/**
	 * The version of this plugin.
	 *
	 * @access private
	 * @var string      $logger.
	 */
	private $logger;

	private $previousExceptionHandler;// @codingStandardsIgnoreLine
	private $uncaughtExceptionLevel;// @codingStandardsIgnoreLine

	private $previousErrorHandler;// @codingStandardsIgnoreLine
	private $errorLevelMap;// @codingStandardsIgnoreLine
	private $handleOnlyReportedErrors;// @codingStandardsIgnoreLine

	private $hasFatalErrorHandler;// @codingStandardsIgnoreLine
	private $fatalLevel;// @codingStandardsIgnoreLine
	private $reservedMemory;// @codingStandardsIgnoreLine
	private static $fatalErrors = array( E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR );// @codingStandardsIgnoreLine

	/**
	 * This function is __construct.
	 *
	 * @param LoggerInterface $logger passes parameter as logger.
	 */
	public function __construct( LoggerInterface $logger ) {
		$this->logger = $logger;
	}

	/**
	 * Registers a new ErrorHandler for a given Logger
	 *
	 * By default it will handle errors, exceptions and fatal errors
	 *
	 * @param  LoggerInterface $logger passes parameter as logger.
	 * @param  array|false     $errorLevelMap  an array of E_* constant to LogLevel::* constant mapping, or false to disable error handling.
	 * @param  int|false       $exceptionLevel a LogLevel::* constant, or false to disable exception handling.
	 * @param  int|false       $fatalLevel     a LogLevel::* constant, or false to disable fatal error handling.
	 * @return ErrorHandler
	 */
	public static function register( LoggerInterface $logger, $errorLevelMap = array(), $exceptionLevel = null, $fatalLevel = null ) {// @codingStandardsIgnoreLine
		// Forces the autoloader to run for LogLevel. Fixes an autoload issue at compile-time on PHP5.3. See https://github.com/Seldaek/monolog/pull/929.
		class_exists( '\\Psr\\Log\\LogLevel', true );

		$handler = new static( $logger );
		if ( $errorLevelMap !== false ) {// @codingStandardsIgnoreLine
			$handler->registerErrorHandler( $errorLevelMap );// @codingStandardsIgnoreLine
		}
		if ( $exceptionLevel !== false ) {// @codingStandardsIgnoreLine
			$handler->registerExceptionHandler( $exceptionLevel );// @codingStandardsIgnoreLine
		}
		if ( $fatalLevel !== false ) {// @codingStandardsIgnoreLine
			$handler->registerFatalHandler( $fatalLevel );// @codingStandardsIgnoreLine
		}

		return $handler;
	}

	public function registerExceptionHandler( $level = null, $callPrevious = true ) {// @codingStandardsIgnoreLine
		$prev                         = set_exception_handler( array( $this, 'handleException' ) );
		$this->uncaughtExceptionLevel = $level;// @codingStandardsIgnoreLine
		if ( $callPrevious && $prev ) {// @codingStandardsIgnoreLine
			$this->previousExceptionHandler = $prev;// @codingStandardsIgnoreLine
		}
	}

	public function registerErrorHandler( array $levelMap = array(), $callPrevious = true, $errorTypes = -1, $handleOnlyReportedErrors = true ) {// @codingStandardsIgnoreLine
		$prev                = set_error_handler( array( $this, 'handleError' ), $errorTypes );// @codingStandardsIgnoreLine
		$this->errorLevelMap = array_replace( $this->defaultErrorLevelMap(), $levelMap );// @codingStandardsIgnoreLine
		if ( $callPrevious ) {// @codingStandardsIgnoreLine
			$this->previousErrorHandler = $prev ?: true;// @codingStandardsIgnoreLine
		}

		$this->handleOnlyReportedErrors = $handleOnlyReportedErrors;// @codingStandardsIgnoreLine
	}

	public function registerFatalHandler( $level = null, $reservedMemorySize = 20 ) {// @codingStandardsIgnoreLine
		register_shutdown_function( array( $this, 'handleFatalError' ) );

		$this->reservedMemory       = str_repeat( ' ', 1024 * $reservedMemorySize );// @codingStandardsIgnoreLine
		$this->fatalLevel           = $level;// @codingStandardsIgnoreLine
		$this->hasFatalErrorHandler = true;// @codingStandardsIgnoreLine
	}
	/**
	 * This function is defaultErrorLevelMap.
	 */
	protected function defaultErrorLevelMap() {// @codingStandardsIgnoreLine
		return array(
			E_ERROR             => LogLevel::CRITICAL,
			E_WARNING           => LogLevel::WARNING,
			E_PARSE             => LogLevel::ALERT,
			E_NOTICE            => LogLevel::NOTICE,
			E_CORE_ERROR        => LogLevel::CRITICAL,
			E_CORE_WARNING      => LogLevel::WARNING,
			E_COMPILE_ERROR     => LogLevel::ALERT,
			E_COMPILE_WARNING   => LogLevel::WARNING,
			E_USER_ERROR        => LogLevel::ERROR,
			E_USER_WARNING      => LogLevel::WARNING,
			E_USER_NOTICE       => LogLevel::NOTICE,
			E_STRICT            => LogLevel::NOTICE,
			E_RECOVERABLE_ERROR => LogLevel::ERROR,
			E_DEPRECATED        => LogLevel::NOTICE,
			E_USER_DEPRECATED   => LogLevel::NOTICE,
		);
	}

	/**
	 * This function is handleException.
	 *
	 * @param string $e passes parameter as e.
	 */
	public function handleException( $e ) {// @codingStandardsIgnoreLine
		$this->logger->log(
			$this->uncaughtExceptionLevel === null ? LogLevel::ERROR : $this->uncaughtExceptionLevel,// @codingStandardsIgnoreLine
			sprintf( 'Uncaught Exception %s: "%s" at %s line %s', get_class( $e ), $e->getMessage(), $e->getFile(), $e->getLine() ),
			array( 'exception' => $e )
		);

		if ( $this->previousExceptionHandler ) {// @codingStandardsIgnoreLine
			call_user_func( $this->previousExceptionHandler, $e );// @codingStandardsIgnoreLine
		}

		exit( 255 );
	}

	/**
	 * This function is handleError.
	 *
	 * @param string $code passes parameter as code.
	 * @param string $message passes parameter as message.
	 * @param null   $file passes parameter as file.
	 * @param int    $line passes parameter as line.
	 * @param array  $context passes parameter as context.
	 */
	public function handleError( $code, $message, $file = '', $line = 0, $context = array() ) {// @codingStandardsIgnoreLine
		if ( $this->handleOnlyReportedErrors && ! ( error_reporting() & $code ) ) {// @codingStandardsIgnoreLine
			return;
		}

		// fatal error codes are ignored if a fatal error handler is present as well to avoid duplicate log entries.
		if ( ! $this->hasFatalErrorHandler || ! in_array( $code, self::$fatalErrors, true ) ) {// @codingStandardsIgnoreLine
			$level = isset( $this->errorLevelMap[ $code ] ) ? $this->errorLevelMap[ $code ] : LogLevel::CRITICAL;// @codingStandardsIgnoreLine
			$this->logger->log(
				$level, self::codeToString( $code ) . ': ' . $message, array(
					'code'    => $code,
					'message' => $message,
					'file'    => $file,
					'line'    => $line,
				)
			);
		}

		if ( $this->previousErrorHandler === true ) {// @codingStandardsIgnoreLine
			return false;
		} elseif ( $this->previousErrorHandler ) {// @codingStandardsIgnoreLine
			return call_user_func( $this->previousErrorHandler, $code, $message, $file, $line, $context );// @codingStandardsIgnoreLine
		}
	}

	/**
	 * This function is handleFatalError.
	 */
	public function handleFatalError() {// @codingStandardsIgnoreLine
		$this->reservedMemory = null;// @codingStandardsIgnoreLine

		$lastError = error_get_last();// @codingStandardsIgnoreLine
		if ( $lastError && in_array( $lastError['type'], self::$fatalErrors, true ) ) {// @codingStandardsIgnoreLine
			$this->logger->log(
				$this->fatalLevel === null ? LogLevel::ALERT : $this->fatalLevel,// @codingStandardsIgnoreLine
				'Fatal Error (' . self::codeToString( $lastError['type'] ) . '): ' . $lastError['message'],// @codingStandardsIgnoreLine
				array(
					'code'    => $lastError['type'],// @codingStandardsIgnoreLine
					'message' => $lastError['message'],// @codingStandardsIgnoreLine
					'file'    => $lastError['file'],// @codingStandardsIgnoreLine
					'line'    => $lastError['line'],// @codingStandardsIgnoreLine
				)
			);

			if ( $this->logger instanceof Logger ) {
				foreach ( $this->logger->getHandlers() as $handler ) {
					if ( $handler instanceof AbstractHandler ) {
						$handler->close();
					}
				}
			}
		}
	}
	/**
	 * This function is codeToString.
	 *
	 * @param string $code passes parameter as code.
	 */
	private static function codeToString( $code ) {// @codingStandardsIgnoreLine
		switch ( $code ) {
			case E_ERROR:
				return 'E_ERROR';
			case E_WARNING:
				return 'E_WARNING';
			case E_PARSE:
				return 'E_PARSE';
			case E_NOTICE:
				return 'E_NOTICE';
			case E_CORE_ERROR:
				return 'E_CORE_ERROR';
			case E_CORE_WARNING:
				return 'E_CORE_WARNING';
			case E_COMPILE_ERROR:
				return 'E_COMPILE_ERROR';
			case E_COMPILE_WARNING:
				return 'E_COMPILE_WARNING';
			case E_USER_ERROR:
				return 'E_USER_ERROR';
			case E_USER_WARNING:
				return 'E_USER_WARNING';
			case E_USER_NOTICE:
				return 'E_USER_NOTICE';
			case E_STRICT:
				return 'E_STRICT';
			case E_RECOVERABLE_ERROR:
				return 'E_RECOVERABLE_ERROR';
			case E_DEPRECATED:
				return 'E_DEPRECATED';
			case E_USER_DEPRECATED:
				return 'E_USER_DEPRECATED';
		}

		return 'Unknown PHP error';
	}
}
