<?php // @codingStandardsIgnoreLine.
/**
 * This Template is TaskQueueInterface.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client\vendor\guzzlehttp\promises\src
 * @version 2.0.0
 */

namespace GuzzleHttp\Promise;

interface TaskQueueInterface {

	/**
	 * Returns true if the queue is empty.
	 *
	 * @return bool
	 */
	public function isEmpty(); // @codingStandardsIgnoreLine.

	/**
	 * Adds a task to the queue that will be executed the next time run is
	 * called.
	 *
	 * @param callable $task .
	 */
	public function add( callable $task);

	/**
	 * Execute all of the pending task in the queue.
	 */
	public function run();
}
