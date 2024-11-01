<?php // @codingStandardsIgnoreLine.
/**
 * This Template is promise.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client\vendor\guzzlehttp\promises\src
 * @version 2.0.0
 */
namespace GuzzleHttp\Promise;

/**
 * Promises/A+ implementation that avoids recursion when possible.
 *
 * @link https://promisesaplus.com/
 */
class Promise implements PromiseInterface {
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $state  .
	 */
	private $state = self::PENDING;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $result  .
	 */
	private $result;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $cancelFn  .
	 */
	private $cancelFn;// @codingStandardsIgnoreLine.
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $waitFn  .
	 */
	private $waitFn;// @codingStandardsIgnoreLine.
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $waitList  .
	 */
	private $waitList; // @codingStandardsIgnoreLine.
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $handlers  .
	 */
	private $handlers = [];

	/**
	 * This is __construct function.
	 *
	 * @param callable $waitFn   Fn that when invoked resolves the promise.
	 * @param callable $cancelFn Fn that when invoked cancels the promise.
	 */
	public function __construct( callable $waitFn = null, callable $cancelFn = null ) { // @codingStandardsIgnoreLine.
		$this->waitFn   = $waitFn; // @codingStandardsIgnoreLine.
		$this->cancelFn = $cancelFn; // @codingStandardsIgnoreLine.
	}
	/**
	 * The function is then.
	 *
	 * @param callback $onFulfilled .
	 * @param callback $onRejected .
	 */
	public function then( callable $onFulfilled = null, callable $onRejected = null ) {// @codingStandardsIgnoreLine.
		if ( self::PENDING === $this->state ) {
			$p                = new Promise( null, [ $this, 'cancel' ] );
			$this->handlers[] = [ $p, $onFulfilled, $onRejected ]; // @codingStandardsIgnoreLine.
			$p->waitList      = $this->waitList; // @codingStandardsIgnoreLine.
			$p->waitList[]    = $this; // @codingStandardsIgnoreLine.
			return $p;
		}

		// Return a fulfilled promise and immediately invoke any callbacks.
		if ( self::FULFILLED === $this->state ) {
			return $onFulfilled // @codingStandardsIgnoreLine.
				? promise_for( $this->result )->then( $onFulfilled ) // @codingStandardsIgnoreLine.
				: promise_for( $this->result );
		}

		// It's either cancelled or rejected, so return a rejected promise
		// and immediately invoke any callbacks.
		$rejection = rejection_for( $this->result );
		return $onRejected ? $rejection->then( null, $onRejected ) : $rejection;// @codingStandardsIgnoreLine.
	}
	/**
	 * The function is otherwise.
	 *
	 * @param callback $onRejected .
	 */
	public function otherwise( callable $onRejected ) { // @codingStandardsIgnoreLine.
		return $this->then( null, $onRejected ); // @codingStandardsIgnoreLine.
	}
	/**
	 * The function is wait.
	 *
	 * @param string $unwrap .
	 *
	 * @throws exception_for Exception.
	 */
	public function wait( $unwrap = true ) {
		$this->waitIfPending();

		$inner = $this->result instanceof PromiseInterface
			? $this->result->wait( $unwrap )
			: $this->result;

		if ( $unwrap ) {
			if ( $this->result instanceof PromiseInterface
				|| self::FULFILLED === $this->state
			) {
				return $inner;
			} else {
				// It's rejected so "unwrap" and throw an exception.
				throw exception_for( $inner );
			}
		}
	}
	/**
	 * The function is getState.
	 */
	public function getState() {
		return $this->state;
	}
	/**
	 * The function is cancel.
	 */
	public function cancel() {
		if ( self::PENDING !== $this->state ) {
			return;
		}

		$this->waitFn = $this->waitList = null; // @codingStandardsIgnoreLine.

		if ( $this->cancelFn ) { // @codingStandardsIgnoreLine.
			$fn             = $this->cancelFn; // @codingStandardsIgnoreLine.
			$this->cancelFn = null; // @codingStandardsIgnoreLine.
			try {
				$fn();
			} catch ( \Throwable $e ) {
				$this->reject( $e );
			} catch ( \Exception $e ) {
				$this->reject( $e );
			}
		}

		// Reject the promise only if it wasn't rejected in a then callback.
		if ( self::PENDING === $this->state ) {
			$this->reject( new CancellationException( 'Promise has been cancelled' ) );
		}
	}
	/**
	 * The function is resolve.
	 *
	 * @param string $value .
	 */
	public function resolve( $value ) {
		$this->settle( self::FULFILLED, $value );
	}
	/**
	 * The function is reject.
	 *
	 * @param string $reason .
	 */
	public function reject( $reason ) {
		$this->settle( self::REJECTED, $reason );
	}
	/**
	 * The function is settle.
	 *
	 * @param string $state .
	 * @param string $value .
	 *
	 * @throws \LogicException Exception.
	 */
	private function settle( $state, $value ) {
		if ( self::PENDING !== $this->state ) {
			// Ignore calls with the same resolution.
			if ( $state === $this->state && $value === $this->result ) {
				return;
			}
			throw $this->state === $state
				? new \LogicException( "The promise is already {$state}." )
				: new \LogicException( "Cannot change a {$this->state} promise to {$state}" );
		}

		if ( $value === $this ) {
			throw new \LogicException( 'Cannot fulfill or reject a promise with itself' );
		}

		// Clear out the state of the promise but stash the handlers.
		$this->state    = $state;
		$this->result   = $value;
		$handlers       = $this->handlers;
		$this->handlers = null;
		$this->waitList = $this->waitFn = null;// @codingStandardsIgnoreLine.
		$this->cancelFn = null; // @codingStandardsIgnoreLine.

		if ( ! $handlers ) {
			return;
		}

		// If the value was not a settled promise or a thenable, then resolve
		// it in the task queue using the correct ID.
		if ( ! method_exists( $value, 'then' ) ) {
			$id = self::FULFILLED === $state ? 1 : 2;
			// It's a success, so resolve the handlers in the queue.
			queue()->add(
				static function () use ( $id, $value, $handlers ) {
					foreach ( $handlers as $handler ) {
						self::callHandler( $id, $value, $handler );
					}
				}
			);
		} elseif ( $value instanceof Promise
			&& $value->getState() === self::PENDING
		) {
			// We can just merge our handlers onto the next promise.
			$value->handlers = array_merge( $value->handlers, $handlers );
		} else {
			// Resolve the handlers when the forwarded promise is resolved.
			$value->then(
				static function ( $value ) use ( $handlers ) {
					foreach ( $handlers as $handler ) {
						self::callHandler( 1, $value, $handler );
					}
				},
				static function ( $reason ) use ( $handlers ) {
					foreach ( $handlers as $handler ) {
						self::callHandler( 2, $reason, $handler );
					}
				}
			);
		}
	}

	/**
	 * Call a stack of handlers using a specific callback index and value.
	 *
	 * @param int   $index   1 (resolve) or 2 (reject).
	 * @param mixed $value   Value to pass to the callback.
	 * @param array $handler Array of handler data (promise and callbacks).
	 *
	 * @return array Returns the next group to resolve.
	 */
	private static function callHandler( $index, $value, array $handler ) {
		$promise = $handler[0];

		// The promise may have been cancelled or resolved before placing
		// this thunk in the queue.
		if ( $promise->getState() !== self::PENDING ) {
			return;
		}

		try {
			if ( isset( $handler[ $index ] ) ) {
				$promise->resolve( $handler[ $index ]($value) );
			} elseif ( 1 === $index ) {
				// Forward resolution values as-is.
				$promise->resolve( $value );
			} else {
				// Forward rejections down the chain.
				$promise->reject( $value );
			}
		} catch ( \Throwable $reason ) {
			$promise->reject( $reason );
		} catch ( \Exception $reason ) {
			$promise->reject( $reason );
		}
	}
	/**
	 * The function is waitIfPending .
	 */
	private function waitIfPending() {
		if ( self::PENDING !== $this->state ) {
			return;
		} elseif ( $this->waitFn ) { // @codingStandardsIgnoreLine.
			$this->invokeWaitFn();
		} elseif ( $this->waitList ) { // @codingStandardsIgnoreLine.
			$this->invokeWaitList();
		} else {
			// If there's not wait function, then reject the promise.
			$this->reject(
				'Cannot wait on a promise that has '
				. 'no internal wait function. You must provide a wait '
				. 'function when constructing the promise to be able to '
				. 'wait on a promise.'
			);
		}

		queue()->run();

		if ( self::PENDING === $this->state ) {
			$this->reject( 'Invoking the wait callback did not resolve the promise' );
		}
	}
	/**
	 * The function is invokeWaitFn.
	 *
	 * @throws \Exception Exception.
	 */
	private function invokeWaitFn() {
		try {
			$wfn          = $this->waitFn; // @codingStandardsIgnoreLine.
			$this->waitFn = null; // @codingStandardsIgnoreLine.
			$wfn( true );
		} catch ( \Exception $reason ) {
			if ( self::PENDING === $this->state ) {
				// The promise has not been resolved yet, so reject the promise
				// with the exception.
				$this->reject( $reason );
			} else {
				// The promise was already resolved, so there's a problem in
				// the application.
				throw $reason;
			}
		}
	}
	/**
	 * The function is invokeWaitList.
	 */
	private function invokeWaitList() {
		$waitList       = $this->waitList; // @codingStandardsIgnoreLine.
		$this->waitList = null; // @codingStandardsIgnoreLine.

		foreach ( $waitList as $result ) { // @codingStandardsIgnoreLine.
			while ( true ) {
				$result->waitIfPending();

				if ( $result->result instanceof Promise ) {
					$result = $result->result;
				} else {
					if ( $result->result instanceof PromiseInterface ) {
						$result->result->wait( false );
					}
					break;
				}
			}
		}
	}
}
