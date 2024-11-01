<?php // @codingStandardsIgnoreLine.
/**
 * This Template is TaskQueueInterface.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client\vendor\guzzlehttp\promises\src
 * @version 2.0.0
 */

namespace GuzzleHttp\Promise;

use Exception;
use Generator;
use Throwable;

/**
 * Creates a promise that is resolved using a generator that yields values or
 * promises (somewhat similar to C#'s async keyword).
 *
 * When called, the coroutine function will start an instance of the generator
 * and returns a promise that is fulfilled with its final yielded value.
 *
 * Control is returned back to the generator when the yielded promise settles.
 * This can lead to less verbose code when doing lots of sequential async calls
 * with minimal processing in between.
 *
 *     use GuzzleHttp\Promise;
 *
 *     function createPromise($value) {
 *         return new Promise\FulfilledPromise($value);
 *     }
 *
 *     $promise = Promise\coroutine(function () {
 *         $value = (yield createPromise('a'));
 *         try {
 *             $value = (yield createPromise($value . 'b'));
 *         } catch (\Exception $e) {
 *             // The promise was rejected.
 *         }
 *         yield $value . 'c';
 *     });
 *
 *     // Outputs "abc"
 *     $promise->then(function ($v) { echo $v; });
 *
 * @param callable $generatorFn Generator function to wrap into a promise.
 *
 * @return Promise
 * @link https://github.com/petkaantonov/bluebird/blob/master/API.md#generators inspiration
 */
final class Coroutine implements PromiseInterface {

	/**
	 * The version of this plugin.
	 *
	 * @var PromiseInterface|null $currentPromise .
	 */
	private $currentPromise; // @codingStandardsIgnoreLine.

	/**
	 * The version of this plugin.
	 *
	 * @var Generator $generator .
	 */
	private $generator;

	/**
	 * The version of this plugin.
	 *
	 * @var Promise $result.
	 */
	private $result;

	/**
	 * The function is __construct.
	 *
	 * @param callback $generatorFn .
	 */
	public function __construct( callable $generatorFn ) { // @codingStandardsIgnoreLine.
		$this->generator = $generatorFn();// @codingStandardsIgnoreLine.
		$this->result    = new Promise(
			function () {
				while ( isset( $this->currentPromise ) ) {// @codingStandardsIgnoreLine.
					$this->currentPromise->wait();// @codingStandardsIgnoreLine.
				}
			}
		);
		$this->nextCoroutine( $this->generator->current() );
	}
	/**
	 * The function is then.
	 *
	 * @param callback $onFulfilled .
	 * @param callback $onRejected .
	 */
	public function then( callable $onFulfilled = null, callable $onRejected = null ) { // @codingStandardsIgnoreLine.
		return $this->result->then( $onFulfilled, $onRejected ); // @codingStandardsIgnoreLine.
	}
	/**
	 * The function is otherwise.
	 *
	 * @param callback $onRejected .
	 */
	public function otherwise( callable $onRejected ) { // @codingStandardsIgnoreLine.
		return $this->result->otherwise( $onRejected ); // @codingStandardsIgnoreLine.
	}
	/**
	 * The function is otherwise.
	 *
	 * @param string $unwrap .
	 */
	public function wait( $unwrap = true ) {
		return $this->result->wait( $unwrap );
	}
	/**
	 * The function is getState.
	 */
	public function getState() {
		return $this->result->getState();
	}
	/**
	 * The function is resolve.
	 *
	 * @param string $value .
	 */
	public function resolve( $value ) {
		$this->result->resolve( $value );
	}
	/**
	 * The function is reject.
	 *
	 * @param string $reason .
	 */
	public function reject( $reason ) {
		$this->result->reject( $reason );
	}
	/**
	 * The function is cancel.
	 */
	public function cancel() {
		$this->currentPromise->cancel(); // @codingStandardsIgnoreLine.
		$this->result->cancel();
	}
	/**
	 * The function is nextCoroutine.
	 *
	 * @param string $yielded .
	 */
	private function nextCoroutine( $yielded ) {
		$this->currentPromise = promise_for( $yielded ) // @codingStandardsIgnoreLine.
			->then( [ $this, '_handleSuccess' ], [ $this, '_handleFailure' ] );
	}

	/**
	 * The function is _handleSuccess .
	 *
	 * @param string $value .
	 */
	public function _handleSuccess( $value ) { // @codingStandardsIgnoreLine.
		unset( $this->currentPromise );// @codingStandardsIgnoreLine.
		try {
			$next = $this->generator->send( $value );
			if ( $this->generator->valid() ) {
				$this->nextCoroutine( $next );
			} else {
				$this->result->resolve( $value );
			}
		} catch ( Exception $exception ) {
			$this->result->reject( $exception );
		} catch ( Throwable $throwable ) {
			$this->result->reject( $throwable );
		}
	}

	/**
	 * The function is _handleFailure .
	 *
	 * @param string $reason .
	 */
	public function _handleFailure( $reason ) { // @codingStandardsIgnoreLine.
		unset( $this->currentPromise );  // @codingStandardsIgnoreLine.
		try {
			$nextYield = $this->generator->throw( exception_for( $reason ) ); // @codingStandardsIgnoreLine.
			// The throw was caught, so keep iterating on the coroutine.
			$this->nextCoroutine( $nextYield );// @codingStandardsIgnoreLine.
		} catch ( Exception $exception ) {
			$this->result->reject( $exception );
		} catch ( Throwable $throwable ) {
			$this->result->reject( $throwable );
		}
	}
}
