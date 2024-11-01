<?php // @codingStandardsIgnoreLine.
/**
 * This Template is TaskQueueInterface.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client\vendor\guzzlehttp\promises\src
 * @version 2.0.0
 */

namespace GuzzleHttp\Promise;

/**
 * A promise that has been fulfilled.
 *
 * Thenning off of this promise will invoke the onFulfilled callback
 * immediately and ignore other callbacks.
 */
class FulfilledPromise implements PromiseInterface {
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $value  .
	 */
	private $value;
	/**
	 * The function is __construct .
	 *
	 * @param string $value .
	 *
	 * @throws \InvalidArgumentException Exception.
	 */
	public function __construct( $value ) {
		if ( method_exists( $value, 'then' ) ) {
			throw new \InvalidArgumentException(
				'You cannot create a FulfilledPromise with a promise.'
			);
		}

		$this->value = $value;
	}
	/**
	 * The function is then .
	 *
	 * @param callable $onFulfilled .
	 * @param callable $onRejected .
	 */
	public function then(
		callable $onFulfilled = null, // @codingStandardsIgnoreLine.
		callable $onRejected = null // @codingStandardsIgnoreLine.
	) {
		// Return itself if there is no onFulfilled function.
		if ( ! $onFulfilled ) { // @codingStandardsIgnoreLine.
			return $this;
		}

		$queue = queue();
		$p     = new Promise( [ $queue, 'run' ] );
		$value = $this->value;
		$queue->add(
			static function () use ( $p, $value, $onFulfilled ) { // @codingStandardsIgnoreLine.
				if ( $p->getState() === self::PENDING ) {
					try {
						$p->resolve( $onFulfilled( $value ) ); // @codingStandardsIgnoreLine.
					} catch ( \Throwable $e ) {
						$p->reject( $e );
					} catch ( \Exception $e ) {
						$p->reject( $e );
					}
				}
			}
		);

		return $p;
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
	 * @param null   $defaultDelivery .
	 */
	public function wait( $unwrap = true, $defaultDelivery = null ) { // @codingStandardsIgnoreLine.
		return $unwrap ? $this->value : null;
	}
	/**
	 * The function is getState.
	 */
	public function getState() {
		return self::FULFILLED;
	}
	/**
	 * The function is resolve.
	 *
	 * @param string $value .
	 *
	 * @throws \LogicException Exception.
	 */
	public function resolve( $value ) {
		if ( $value !== $this->value ) {
			throw new \LogicException( 'Cannot resolve a fulfilled promise' );
		}
	}
	/**
	 * The function is reject.
	 *
	 * @param string $reason .
	 *
	 * @throws \LogicException Exception.
	 */
	public function reject( $reason ) {
		throw new \LogicException( 'Cannot reject a fulfilled promise' );
	}
	/**
	 * The function is cancel.
	 */
	public function cancel() {
	}
}
