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
 * Forwards records to multiple handlers
 */
class GroupHandler extends AbstractHandler {
	/**
	 * The version of this plugin.
	 *
	 * @var Publisher $handlers .
	 */
	protected $handlers;

	/**
	 * This function is __construct.
	 *
	 * @param array   $handlers Array of Handlers.
	 * @param Boolean $bubble   Whether the messages that are handled can bubble up the stack or not .
	 * @throws  \InvalidArgumentException .
	 */
	public function __construct( array $handlers, $bubble = true ) {
		foreach ( $handlers as $handler ) {
			if ( ! $handler instanceof HandlerInterface ) {
				throw new \InvalidArgumentException( 'The first argument of the GroupHandler must be an array of HandlerInterface instances.' );
			}
		}

		$this->handlers = $handlers;
		$this->bubble   = $bubble;
	}

	/**
	 * The version of the plugin .
	 *
	 * @param array $record .
	 * {@inheritdoc}.
	 */
	public function isHandling( array $record ) {
		foreach ( $this->handlers as $handler ) {
			if ( $handler->isHandling( $record ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * This function is handle.
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

		foreach ( $this->handlers as $handler ) {
			$handler->handle( $record );
		}

		return false === $this->bubble;
	}

	/**
	 * This function is  handleBatch
	 *
	 * @param array $records .
	 * {@inheritdoc}.
	 */
	public function handleBatch( array $records ) {
		if ( $this->processors ) {
			$processed = array();
			foreach ( $records as $record ) {
				foreach ( $this->processors as $processor ) {
					$processed[] = call_user_func( $processor, $record );
				}
			}
			$records = $processed;
		}

		foreach ( $this->handlers as $handler ) {
			$handler->handleBatch( $records );
		}
	}

	/**
	 * This function is setFormatter.
	 *
	 * @param FormatterInterface $formatter .
	 * {@inheritdoc}.
	 */
	public function setFormatter( FormatterInterface $formatter ) {
		foreach ( $this->handlers as $handler ) {
			$handler->setFormatter( $formatter );
		}

		return $this;
	}
}
