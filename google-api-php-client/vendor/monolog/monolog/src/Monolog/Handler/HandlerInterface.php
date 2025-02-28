<?php  // @codingStandardsIgnoreLine.
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
 * Interface that all Monolog Handlers must implement
 */
interface HandlerInterface {

	/**
	 * Checks whether the given record will be handled by this handler.
	 *
	 * This is mostly done for performance reasons, to avoid calling processors for nothing.
	 *
	 * Handlers should still check the record levels within handle(), returning false in isHandling()
	 * is no guarantee that handle() will not be called, and isHandling() might not be called
	 * for a given record.
	 *
	 * @param array $record Partial log record containing only a level key .
	 *
	 * @return Boolean
	 */
	public function isHandling( array $record);// @codingStandardsIgnoreLine.

	/**
	 * Handles a record.
	 *
	 * All records may be passed to this method, and the handler should discard
	 * those that it does not want to handle.
	 *
	 * The return value of this function controls the bubbling process of the handler stack.
	 * Unless the bubbling is interrupted (by returning true), the Logger class will keep on
	 * calling further handlers in the stack with a given log record.
	 *
	 * @param  array $record The record to handle .
	 * @return Boolean true means that this handler handled the record, and that bubbling is not permitted.
	 *                        false means the record was either not processed or that this handler allows bubbling.
	 */
	public function handle( array $record);

	/**
	 * Handles a set of records at once.
	 *
	 * @param array $records The records to handle (an array of record arrays).
	 */
	public function handleBatch( array $records);// @codingStandardsIgnoreLine.

	/**
	 * Adds a processor in the stack.
	 *
	 * @param  callable $callback .
	 * @return self
	 */
	public function pushProcessor( $callback);// @codingStandardsIgnoreLine.

	/**
	 * Removes the processor on top of the stack and returns it.
	 *
	 * @return callable
	 */
	public function popProcessor();// @codingStandardsIgnoreLine.

	/**
	 * Sets the formatter.
	 *
	 * @param  FormatterInterface $formatter .
	 * @return self
	 */
	public function setFormatter( FormatterInterface $formatter);// @codingStandardsIgnoreLine.

	/**
	 * Gets the formatter.
	 *
	 * @return FormatterInterface
	 */
	public function getFormatter();// @codingStandardsIgnoreLine.
}
