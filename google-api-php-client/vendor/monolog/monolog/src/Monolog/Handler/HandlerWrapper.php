<?php // @codingStandardsIgnoreLine.
/**
 * This file is part of the Monolog package.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/handler
 * @version 2.0.0
 */
namespace Monolog\Handler;

use Monolog\Formatter\FormatterInterface;

/**
 * This simple wrapper class can be used to extend handlers functionality.
 */
class HandlerWrapper implements HandlerInterface {

	/**
	 * The version of this plugin .
	 *
	 * @var HandlerInterface
	 */
	protected $handler;

	/**
	 * HandlerWrapper constructor.
	 *
	 * @param HandlerInterface $handler .
	 */
	public function __construct( HandlerInterface $handler ) {
		$this->handler = $handler;
	}

	/**
	 * The version of this plugin.
	 *
	 * @param array $record .
	 * {@inheritdoc}.
	 */
	public function isHandling( array $record ) {
		return $this->handler->isHandling( $record );
	}

	/**
	 * The version of this plugin.
	 *
	 * @param array $record .
	 * {@inheritdoc}.
	 */
	public function handle( array $record ) {
		return $this->handler->handle( $record );
	}

	/**
	 * This function is handleBatch.
	 *
	 * @param array $records .
	 * {@inheritdoc}.
	 */
	public function handleBatch( array $records ) {
		return $this->handler->handleBatch( $records );
	}

	/**
	 * This function is pushProcessor .
	 *
	 * @param string $callback .
	 * {@inheritdoc}.
	 */
	public function pushProcessor( $callback ) {
		$this->handler->pushProcessor( $callback );

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function popProcessor() {
		return $this->handler->popProcessor();
	}

	/**
	 * This function is pushProcessor .
	 *
	 * @param FormatterInterface $formatter .
	 * {@inheritdoc}.
	 */
	public function setFormatter( FormatterInterface $formatter ) {
		$this->handler->setFormatter( $formatter );

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFormatter() {
		return $this->handler->getFormatter();
	}
}
