<?php // @codingStandardsIgnoreLine.
/**
 * This file is part of the Monolog package.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/handler
 * @version 2.0.0
 */

namespace Monolog\Handler;

use Monolog\Logger;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;

/**
 * Base Handler class providing the Handler structure
 */
abstract class AbstractHandler implements HandlerInterface {
	/**
	 * The version of the plugin.
	 *
	 * @var string $level.
	 */
	protected $level = Logger::DEBUG;
	/**
	 * The version of the plugin.
	 *
	 * @var string $level.
	 */
	protected $bubble = true;

	/**
	 * The version of the plugin .
	 *
	 * @var $formatter
	 */
	protected $formatter;
	/**
	 * The version of the plugin .
	 *
	 * @var $processors
	 */
	protected $processors = array();

	/**
	 * The version of the plugin.
	 *
	 * @param int     $level  The minimum logging level at which this handler will be triggered .
	 * @param Boolean $bubble Whether the messages that are handled can bubble up the stack or not .
	 */
	public function __construct( $level = Logger::DEBUG, $bubble = true ) {
		$this->setLevel( $level );
		$this->bubble = $bubble;
	}

	/**
	 * The version of the plugin.
	 *
	 * @param  array $record .
	 */
	public function isHandling( array $record ) {
		return $record['level'] >= $this->level;
	}

	/**
	 * This function is handleBatch.
	 *
	 * @param  array $records .
	 * {@inheritdoc}.
	 */
	public function handleBatch( array $records ) {
		foreach ( $records as $record ) {
			$this->handle( $record );
		}
	}

	/**
	 * Closes the handler.
	 *
	 * This will be called automatically when the object is destroyed
	 */
	public function close() {
	}

	/**
	 * This function is $callback.
	 *
	 * @param string $callback .
	 * @throws \InvalidArgumentException .
	 * {@inheritdoc}.
	 */
	public function pushProcessor( $callback ) {
		if ( ! is_callable( $callback ) ) {
			throw new \InvalidArgumentException( 'Processors must be valid callables (callback or object with an __invoke method), ' . var_export( $callback, true ) . ' given' ); // @codingStandardsIgnoreLine.
		}
		array_unshift( $this->processors, $callback );

		return $this;
	}
	/**
	 * This function is popProcessor.
	 *
	 * @throws \LogicException .
	 * {@inheritdoc}.
	 */
	public function popProcessor() {
		if ( ! $this->processors ) {
			throw new \LogicException( 'You tried to pop from an empty processor stack.' );
		}

		return array_shift( $this->processors );
	}
	/**
	 * This function is setFormatter.
	 *
	 * @param FormatterInterface $formatter .
	 * {@inheritdoc}.
	 */
	public function setFormatter( FormatterInterface $formatter ) {
		$this->formatter = $formatter;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFormatter() {
		if ( ! $this->formatter ) {
			$this->formatter = $this->getDefaultFormatter();
		}

		return $this->formatter;
	}

	/**
	 * Sets minimum logging level at which this handler will be triggered.
	 *
	 * @param  int|string $level Level or level name .
	 * @return self
	 */
	public function setLevel( $level ) {
		$this->level = Logger::toMonologLevel( $level );

		return $this;
	}

	/**
	 * Gets minimum logging level at which this handler will be triggered.
	 *
	 * @return int
	 */
	public function getLevel() {
		return $this->level;
	}

	/**
	 * Sets the bubbling behavior.
	 *
	 * @param  Boolean $bubble true means that this handler allows bubbling.
	 *                         false means that bubbling is not permitted.
	 * @return self
	 */
	public function setBubble( $bubble ) {
		$this->bubble = $bubble;

		return $this;
	}

	/**
	 * Gets the bubbling behavior.
	 *
	 * @return Boolean true means that this handler allows bubbling.
	 *                 false means that bubbling is not permitted.
	 */
	public function getBubble() {
		return $this->bubble;
	}
	/**
	 * This function is  __destruct.
	 *
	 * {@inheritdoc}.
	 */
	public function __destruct() {
		try {
			$this->close();
		} catch ( \Exception $e ) {// @codingStandardsIgnoreLine.
			// do nothing .
		} catch ( \Throwable $e ) {// @codingStandardsIgnoreLine.
			// do nothing .
		}
	}

	/**
	 * Gets the default formatter.
	 *
	 * @return FormatterInterface
	 */
	protected function getDefaultFormatter() {
		return new LineFormatter();
	}
}
