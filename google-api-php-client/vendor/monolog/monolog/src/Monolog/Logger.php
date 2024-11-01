<?php //@codingStandardsIgnoreLine
/**
 * This file is Logger.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace Monolog;

use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;
use Psr\Log\InvalidArgumentException;

/**
 * Monolog log channel
 *
 * It contains a stack of Handlers and a stack of Processors,
 * and uses them to store records that are added to it.
 */
class Logger implements LoggerInterface {

	/**
	 * Detailed debug information
	 */
	const DEBUG = 100;

	/**
	 * Interesting events
	 *
	 * Examples: User logs in, SQL logs.
	 */
	const INFO = 200;

	/**
	 * Uncommon events
	 */
	const NOTICE = 250;

	/**
	 * Exceptional occurrences that are not errors
	 *
	 * Examples: Use of deprecated APIs, poor use of an API,
	 * undesirable things that are not necessarily wrong.
	 */
	const WARNING = 300;

	/**
	 * Runtime errors
	 */
	const ERROR = 400;

	/**
	 * Critical conditions
	 *
	 * Example: Application component unavailable, unexpected exception.
	 */
	const CRITICAL = 500;

	/**
	 * Action must be taken immediately
	 *
	 * Example: Entire website down, database unavailable, etc.
	 * This should trigger the SMS alerts and wake you up.
	 */
	const ALERT = 550;

	/**
	 * Urgent alert.
	 */
	const EMERGENCY = 600;

	/**
	 * Monolog API version
	 *
	 * This is only bumped when API breaks are done and should
	 * follow the major version of the library
	 *
	 * @var int
	 */
	const API = 1;

	/**
	 * Logging levels from syslog protocol defined in RFC 5424
	 *
	 * @var array $levels Logging levels
	 */
	protected static $levels = array(
		self::DEBUG     => 'DEBUG',
		self::INFO      => 'INFO',
		self::NOTICE    => 'NOTICE',
		self::WARNING   => 'WARNING',
		self::ERROR     => 'ERROR',
		self::CRITICAL  => 'CRITICAL',
		self::ALERT     => 'ALERT',
		self::EMERGENCY => 'EMERGENCY',
	);

	/**
	 * The version of this plugin.
	 *
	 * @access   protected
	 * @var      string    $timezone  .
	 */
	protected static $timezone;

	/**
	 * The version of this plugin.
	 *
	 * @access   protected
	 * @var      string    $name  .
	 */
	protected $name;

	/**
	 * The handler stack
	 *
	 * @var HandlerInterface[]
	 */
	protected $handlers;

	/**
	 * Processors that will process all log records
	 *
	 * To process records of a single handler instead, add the processor on that specific handler
	 *
	 * @var callable[]
	 */
	protected $processors;

	/**
	 * The version of this plugin.
	 *
	 * @access   protected
	 * @var      string    $timezone  .
	 */
	protected $microsecondTimestamps = true;// @codingStandardsIgnoreLine

	/**
	 * This function is __construct.
	 *
	 * @param string             $name       The logging channel.
	 * @param HandlerInterface[] $handlers   Optional stack of handlers, the first one in the array is called first, etc.
	 * @param callable[]         $processors Optional array of processors.
	 */
	public function __construct( $name, array $handlers = array(), array $processors = array() ) {
		$this->name       = $name;
		$this->handlers   = $handlers;
		$this->processors = $processors;
	}

	/**
	 * This function is getName.
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * This function is withName.
	 *
	 * @param string $name passes parameter as name.
	 */
	public function withName( $name ) {
		$new       = clone $this;
		$new->name = $name;

		return $new;
	}

	/**
	 * Pushes a handler on to the stack.
	 *
	 * @param  HandlerInterface $handler passes parameter as handler.
	 * @return $this
	 */
	public function pushHandler( HandlerInterface $handler ) {
		array_unshift( $this->handlers, $handler );

		return $this;
	}

	/**
	 * Pops a handler from the stack
	 *
	 * @throws \LogicException .
	 */
	public function popHandler() {
		if ( ! $this->handlers ) {
			throw new \LogicException( 'You tried to pop from an empty handler stack.' );
		}

		return array_shift( $this->handlers );
	}

	/**
	 * Set handlers, replacing all existing ones.
	 *
	 * If a map is passed, keys will be ignored.
	 *
	 * @param  HandlerInterface[] $handlers passes parameter as handlers.
	 * @return $this
	 */
	public function setHandlers( array $handlers ) {
		$this->handlers = array();
		foreach ( array_reverse( $handlers ) as $handler ) {
			$this->pushHandler( $handler );
		}

		return $this;
	}

	/**
	 * This function is getHandlers.
	 *
	 * @return HandlerInterface[]
	 */
	public function getHandlers() {
		return $this->handlers;
	}

	/**
	 * Adds a processor on to the stack.
	 *
	 * @param  callable $callback passes parameter as callback.
	 * @throws \InvalidArgumentException .
	 * @return $this
	 */
	public function pushProcessor( $callback ) {
		if ( ! is_callable( $callback ) ) {
			throw new \InvalidArgumentException( 'Processors must be valid callables (callback or object with an __invoke method), ' . var_export( $callback, true ) . ' given' );// @codingStandardsIgnoreLine
		}
		array_unshift( $this->processors, $callback );

		return $this;
	}

	/**
	 * Removes the processor on top of the stack and returns it.
	 *
	 * @throws \LogicException .
	 * @return callable
	 */
	public function popProcessor() {
		if ( ! $this->processors ) {
			throw new \LogicException( 'You tried to pop from an empty processor stack.' );
		}

		return array_shift( $this->processors );
	}

	/**
	 * This Function is getProcessors.
	 *
	 * @return callable[]
	 */
	public function getProcessors() {
		return $this->processors;
	}

	/**
	 * Control the use of microsecond resolution timestamps in the 'datetime'
	 * member of new records.
	 *
	 * Generating microsecond resolution timestamps by calling
	 * microtime(true), formatting the result via sprintf() and then parsing
	 * the resulting string via \DateTime::createFromFormat() can incur
	 * a measurable runtime overhead vs simple usage of DateTime to capture
	 * a second resolution timestamp in systems which generate a large number
	 * of log events.
	 *
	 * @param bool $micro True to use microtime() to create timestamps.
	 */
	public function useMicrosecondTimestamps( $micro ) {
		$this->microsecondTimestamps = (bool) $micro;// @codingStandardsIgnoreLine
	}

	/**
	 * Adds a log record.
	 *
	 * @param  int    $level   The logging level.
	 * @param  string $message The log message.
	 * @param  array  $context The log context.
	 * @return Boolean Whether the record has been processed
	 */
	public function addRecord( $level, $message, array $context = array() ) {
		if ( ! $this->handlers ) {
			$this->pushHandler( new StreamHandler( 'php://stderr', static::DEBUG ) );
		}

		$levelName = static::getLevelName( $level );// @codingStandardsIgnoreLine

		// check if any handler will handle this message so we can return early and save cycles.
		$handlerKey = null;// @codingStandardsIgnoreLine
		reset( $this->handlers );
		while ( $handler = current( $this->handlers ) ) {// @codingStandardsIgnoreLine
			if ( $handler->isHandling( array( 'level' => $level ) ) ) {
				$handlerKey = key( $this->handlers );// @codingStandardsIgnoreLine
				break;
			}

			next( $this->handlers );
		}

		if ( null === $handlerKey ) {// @codingStandardsIgnoreLine
			return false;
		}

		if ( ! static::$timezone ) {
			static::$timezone = new \DateTimeZone( date_default_timezone_get() ?: 'UTC' );
		}

		// php7.1+ always has microseconds enabled, so we do not need this hack.
		if ( $this->microsecondTimestamps && PHP_VERSION_ID < 70100 ) {// @codingStandardsIgnoreLine
			$ts = \DateTime::createFromFormat( 'U.u', sprintf( '%.6F', microtime( true ) ), static::$timezone );
		} else {
			$ts = new \DateTime( null, static::$timezone );
		}
		$ts->setTimezone( static::$timezone );

		$record = array(
			'message'    => (string) $message,
			'context'    => $context,
			'level'      => $level,
			'level_name' => $levelName,// @codingStandardsIgnoreLine
			'channel'    => $this->name,
			'datetime'   => $ts,
			'extra'      => array(),
		);

		foreach ( $this->processors as $processor ) {
			$record = call_user_func( $processor, $record );
		}

		while ( $handler = current( $this->handlers ) ) {// @codingStandardsIgnoreLine
			if ( true === $handler->handle( $record ) ) {
				break;
			}

			next( $this->handlers );
		}

		return true;
	}

	/**
	 * Adds a log record at the DEBUG level.
	 *
	 * @param  string $message The log message.
	 * @param  array  $context The log context.
	 * @return Boolean Whether the record has been processed.
	 */
	public function addDebug( $message, array $context = array() ) {
		return $this->addRecord( static::DEBUG, $message, $context );
	}

	/**
	 * Adds a log record at the INFO level.
	 *
	 * @param  string $message The log message.
	 * @param  array  $context The log context.
	 * @return Boolean Whether the record has been processed
	 */
	public function addInfo( $message, array $context = array() ) {
		return $this->addRecord( static::INFO, $message, $context );
	}

	/**
	 * Adds a log record at the NOTICE level.
	 *
	 * @param  string $message The log message.
	 * @param  array  $context The log context.
	 * @return Boolean Whether the record has been processed
	 */
	public function addNotice( $message, array $context = array() ) {
		return $this->addRecord( static::NOTICE, $message, $context );
	}

	/**
	 * Adds a log record at the WARNING level.
	 *
	 * @param  string $message The log message.
	 * @param  array  $context The log context.
	 * @return Boolean Whether the record has been processed
	 */
	public function addWarning( $message, array $context = array() ) {
		return $this->addRecord( static::WARNING, $message, $context );
	}

	/**
	 * Adds a log record at the ERROR level.
	 *
	 * @param  string $message The log message.
	 * @param  array  $context The log context.
	 * @return Boolean Whether the record has been processed
	 */
	public function addError( $message, array $context = array() ) {
		return $this->addRecord( static::ERROR, $message, $context );
	}

	/**
	 * Adds a log record at the CRITICAL level.
	 *
	 * @param  string $message The log message.
	 * @param  array  $context The log context.
	 * @return Boolean Whether the record has been processed
	 */
	public function addCritical( $message, array $context = array() ) {
		return $this->addRecord( static::CRITICAL, $message, $context );
	}

	/**
	 * Adds a log record at the ALERT level.
	 *
	 * @param  string $message The log message.
	 * @param  array  $context The log context.
	 * @return Boolean Whether the record has been processed
	 */
	public function addAlert( $message, array $context = array() ) {
		return $this->addRecord( static::ALERT, $message, $context );
	}

	/**
	 * Adds a log record at the EMERGENCY level.
	 *
	 * @param  string $message The log message.
	 * @param  array  $context The log context.
	 * @return Boolean Whether the record has been processed
	 */
	public function addEmergency( $message, array $context = array() ) {
		return $this->addRecord( static::EMERGENCY, $message, $context );
	}

	/**
	 * Gets all supported logging levels.
	 *
	 * @return array Assoc array with human-readable level names => level codes.
	 */
	public static function getLevels() {
		return array_flip( static::$levels );
	}

	/**
	 * Gets the name of the logging level.
	 *
	 * @param  int $level passes parameter as level.
	 * @return string
	 * @throws InvalidArgumentException On error.
	 */
	public static function getLevelName( $level ) {
		if ( ! isset( static::$levels[ $level ] ) ) {
			throw new InvalidArgumentException( 'Level "' . $level . '" is not defined, use one of: ' . implode( ', ', array_keys( static::$levels ) ) );
		}

		return static::$levels[ $level ];
	}

	/**
	 * Converts PSR-3 levels to Monolog ones if necessary
	 *
	 * @param string $level passes parameter as level.
	 * @return int
	 */
	public static function toMonologLevel( $level ) {
		if ( is_string( $level ) && defined( __CLASS__ . '::' . strtoupper( $level ) ) ) {
			return constant( __CLASS__ . '::' . strtoupper( $level ) );
		}

		return $level;
	}

	/**
	 * Checks whether the Logger has a handler that listens on the given level
	 *
	 * @param  int $level passes parameter as level.
	 * @return Boolean
	 */
	public function isHandling( $level ) {
		$record = array(
			'level' => $level,
		);

		foreach ( $this->handlers as $handler ) {
			if ( $handler->isHandling( $record ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Adds a log record at an arbitrary level.
	 *
	 * This method allows for compatibility with common interfaces.
	 *
	 * @param  mixed  $level   The log level.
	 * @param  string $message The log message.
	 * @param  array  $context The log context.
	 * @return Boolean Whether the record has been processed
	 */
	public function log( $level, $message, array $context = array() ) {
		$level = static::toMonologLevel( $level );

		return $this->addRecord( $level, $message, $context );
	}

	/**
	 * Adds a log record at the DEBUG level.
	 *
	 * This method allows for compatibility with common interfaces.
	 *
	 * @param  string $message The log message.
	 * @param  array  $context The log context.
	 * @return Boolean Whether the record has been processed
	 */
	public function debug( $message, array $context = array() ) {
		return $this->addRecord( static::DEBUG, $message, $context );
	}

	/**
	 * Adds a log record at the INFO level.
	 *
	 * This method allows for compatibility with common interfaces.
	 *
	 * @param  string $message The log message.
	 * @param  array  $context The log context.
	 * @return Boolean Whether the record has been processed
	 */
	public function info( $message, array $context = array() ) {
		return $this->addRecord( static::INFO, $message, $context );
	}

	/**
	 * Adds a log record at the NOTICE level.
	 *
	 * This method allows for compatibility with common interfaces.
	 *
	 * @param  string $message The log message.
	 * @param  array  $context The log context.
	 * @return Boolean Whether the record has been processed
	 */
	public function notice( $message, array $context = array() ) {
		return $this->addRecord( static::NOTICE, $message, $context );
	}

	/**
	 * Adds a log record at the WARNING level.
	 *
	 * This method allows for compatibility with common interfaces.
	 *
	 * @param  string $message The log message.
	 * @param  array  $context The log context.
	 * @return Boolean Whether the record has been processed
	 */
	public function warn( $message, array $context = array() ) {
		return $this->addRecord( static::WARNING, $message, $context );
	}

	/**
	 * Adds a log record at the WARNING level.
	 *
	 * This method allows for compatibility with common interfaces.
	 *
	 * @param  string $message The log message.
	 * @param  array  $context The log context.
	 * @return Boolean Whether the record has been processed
	 */
	public function warning( $message, array $context = array() ) {
		return $this->addRecord( static::WARNING, $message, $context );
	}

	/**
	 * Adds a log record at the ERROR level.
	 *
	 * This method allows for compatibility with common interfaces.
	 *
	 * @param  string $message The log message.
	 * @param  array  $context The log context.
	 * @return Boolean Whether the record has been processed
	 */
	public function err( $message, array $context = array() ) {
		return $this->addRecord( static::ERROR, $message, $context );
	}

	/**
	 * Adds a log record at the ERROR level.
	 *
	 * This method allows for compatibility with common interfaces.
	 *
	 * @param  string $message The log message.
	 * @param  array  $context The log context.
	 * @return Boolean Whether the record has been processed
	 */
	public function error( $message, array $context = array() ) {
		return $this->addRecord( static::ERROR, $message, $context );
	}

	/**
	 * Adds a log record at the CRITICAL level.
	 *
	 * This method allows for compatibility with common interfaces.
	 *
	 * @param  string $message The log message.
	 * @param  array  $context The log context.
	 * @return Boolean Whether the record has been processed
	 */
	public function crit( $message, array $context = array() ) {
		return $this->addRecord( static::CRITICAL, $message, $context );
	}

	/**
	 * Adds a log record at the CRITICAL level.
	 *
	 * This method allows for compatibility with common interfaces.
	 *
	 * @param  string $message The log message.
	 * @param  array  $context The log context.
	 * @return Boolean Whether the record has been processed
	 */
	public function critical( $message, array $context = array() ) {
		return $this->addRecord( static::CRITICAL, $message, $context );
	}

	/**
	 * Adds a log record at the ALERT level.
	 *
	 * This method allows for compatibility with common interfaces.
	 *
	 * @param  string $message The log message.
	 * @param  array  $context The log context.
	 * @return Boolean Whether the record has been processed
	 */
	public function alert( $message, array $context = array() ) {
		return $this->addRecord( static::ALERT, $message, $context );
	}

	/**
	 * Adds a log record at the EMERGENCY level.
	 *
	 * This method allows for compatibility with common interfaces.
	 *
	 * @param  string $message The log message.
	 * @param  array  $context The log context.
	 * @return Boolean Whether the record has been processed
	 */
	public function emerg( $message, array $context = array() ) {
		return $this->addRecord( static::EMERGENCY, $message, $context );
	}

	/**
	 * Adds a log record at the EMERGENCY level.
	 *
	 * This method allows for compatibility with common interfaces.
	 *
	 * @param  string $message The log message.
	 * @param  array  $context The log context.
	 * @return Boolean Whether the record has been processed
	 */
	public function emergency( $message, array $context = array() ) {
		return $this->addRecord( static::EMERGENCY, $message, $context );
	}

	/**
	 * Set the timezone to be used for the timestamp of log records.
	 *
	 * This is stored globally for all Logger instances
	 *
	 * @param \DateTimeZone $tz Timezone object.
	 */
	public static function setTimezone( \DateTimeZone $tz ) {
		self::$timezone = $tz;
	}
}
