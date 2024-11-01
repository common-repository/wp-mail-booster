<?php // @codingStandardsIgnoreLine.
/**
 * This file is part of the Monolog package.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/handler
 * @version 2.0.0
 */
namespace Monolog\Handler;

use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossed\ActivationStrategyInterface;
use Monolog\Logger;

/**
 * Buffers all records until a certain level is reached
 *
 * The advantage of this approach is that you don't get any clutter in your log files.
 * Only requests which actually trigger an error (or whatever your actionLevel is) will be
 * in the logs, but they will contain all records, not only those above the level threshold.
 *
 * You can find the various activation strategies in the
 * Monolog\Handler\FingersCrossed\ namespace.
 */
class FingersCrossedHandler extends AbstractHandler {
	/**
	 * The version of this plugin
	 *
	 * @var array Handler config options
	 */
	protected $handler;
	protected $activationStrategy;// @codingStandardsIgnoreLine.
	/**
	 * The version of this plugin
	 *
	 * @var array $buffering .
	 */
	protected $buffering = true;
	protected $bufferSize;// @codingStandardsIgnoreLine.
	/**
	 * The version of this plugin
	 *
	 * @var array $buffering .
	 */
	protected $buffer = array();
	protected $stopBuffering;// @codingStandardsIgnoreLine.
	protected $passthruLevel;// @codingStandardsIgnoreLine.

	/**
	 * This function is__construct.
	 *
	 * @param callable|HandlerInterface       $handler            Handler or factory callable($record, $fingersCrossedHandler) .
	 * @param int|ActivationStrategyInterface $activationStrategy Strategy which determines when this handler takes action .
	 * @param int                             $bufferSize         How many entries should be buffered at most, beyond that the oldest items are removed from the buffer .
	 * @param Boolean                         $bubble             Whether the messages that are handled can bubble up the stack or not .
	 * @param Boolean                         $stopBuffering      Whether the handler should stop buffering after being triggered (default true) .
	 * @param int                             $passthruLevel      Minimum level to always flush to handler on close, even if strategy not triggered .
	 * @throws \RuntimeException .
	 */
	public function __construct( $handler, $activationStrategy = null, $bufferSize = 0, $bubble = true, $stopBuffering = true, $passthruLevel = null ) {// @codingStandardsIgnoreLine.
		if ( null === $activationStrategy ) {// @codingStandardsIgnoreLine.
			$activationStrategy = new ErrorLevelActivationStrategy( Logger::WARNING );// @codingStandardsIgnoreLine.
		}

		// convert simple int activationStrategy to an object .
		if ( ! $activationStrategy instanceof ActivationStrategyInterface ) {// @codingStandardsIgnoreLine.
			$activationStrategy = new ErrorLevelActivationStrategy( $activationStrategy );// @codingStandardsIgnoreLine.
		}

		$this->handler            = $handler;
		$this->activationStrategy = $activationStrategy;// @codingStandardsIgnoreLine.
		$this->bufferSize         = $bufferSize;// @codingStandardsIgnoreLine.
		$this->bubble             = $bubble;
		$this->stopBuffering      = $stopBuffering;// @codingStandardsIgnoreLine.

		if ( $passthruLevel !== null ) {// @codingStandardsIgnoreLine.
			$this->passthruLevel = Logger::toMonologLevel( $passthruLevel );// @codingStandardsIgnoreLine.
		}

		if ( ! $this->handler instanceof HandlerInterface && ! is_callable( $this->handler ) ) {
			throw new \RuntimeException( 'The given handler (' . json_encode( $this->handler ) . ') is not a callable nor a Monolog\Handler\HandlerInterface object' );// @codingStandardsIgnoreLine.
		}
	}

	/**
	 * This function is isHandling.
	 *
	 * @param array $record .
	 * {@inheritdoc} .
	 */
	public function isHandling( array $record ) {
		return true;
	}

	/**
	 * Manually activate this logger regardless of the activation strategy
	 *
	 * @throws \RuntimeException .
	 */
	public function activate() {
		if ( $this->stopBuffering ) {// @codingStandardsIgnoreLine.
			$this->buffering = false;
		}
		if ( ! $this->handler instanceof HandlerInterface ) {
			$record = end( $this->buffer ) ?: null;

			$this->handler = call_user_func( $this->handler, $record, $this );
			if ( ! $this->handler instanceof HandlerInterface ) {
				throw new \RuntimeException( 'The factory callable should return a HandlerInterface' );
			}
		}
		$this->handler->handleBatch( $this->buffer );
		$this->buffer = array();
	}

	/**
	 * This function is handle .
	 *
	 * @param array $record .
	 * {@inheritdoc}.
	 */
	public function handle( array $record ) {
		if ( $this->processors ) {
			foreach ( $this->processors as $processor ) {
				$record = call_user_func( $processor, $record );
			}
		}

		if ( $this->buffering ) {
			$this->buffer[] = $record;
			if ( $this->bufferSize > 0 && count( $this->buffer ) > $this->bufferSize ) {// @codingStandardsIgnoreLine.
				array_shift( $this->buffer );
			}
			if ( $this->activationStrategy->isHandlerActivated( $record ) ) {// @codingStandardsIgnoreLine.
				$this->activate();
			}
		} else {
			$this->handler->handle( $record );
		}

		return false === $this->bubble;
	}

	/**
	 * {@inheritdoc}
	 */
	public function close() {
		if ( null !== $this->passthruLevel ) {// @codingStandardsIgnoreLine.
			$level        = $this->passthruLevel;// @codingStandardsIgnoreLine.
			$this->buffer = array_filter(
				$this->buffer, function ( $record ) use ( $level ) {
					return $record['level'] >= $level;
				}
			);
			if ( count( $this->buffer ) > 0 ) {
				$this->handler->handleBatch( $this->buffer );
				$this->buffer = array();
			}
		}
	}

	/**
	 * Resets the state of the handler. Stops forwarding records to the wrapped handler.
	 */
	public function reset() {
		$this->buffering = true;
	}

	/**
	 * Clears the buffer without flushing any messages down to the wrapped handler.
	 *
	 * It also resets the handler to its initial buffering state.
	 */
	public function clear() {
		$this->buffer = array();
		$this->reset();
	}
}
