<?php // @codingStandardsIgnoreLine.
/**
 * This Template is RejectedPromise.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client\vendor\guzzlehttp\promises\src
 * @version 2.0.0
 */
namespace GuzzleHttp\Promise;

/**
 * A promise that has been rejected.
 *
 * Thenning off of this promise will invoke the onRejected callback
 * immediately and ignore other callbacks.
 */
class RejectedPromise implements PromiseInterface {
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $reason  .
	 */
	private $reason;
	/**
	 * This is __construct .
	 *
	 * @param string $reason .
	 *
	 * @throws \InvalidArgumentException Exception .
	 */
	public function __construct( $reason ) {
		if ( method_exists( $reason, 'then' ) ) {
			throw new \InvalidArgumentException(
				'You cannot create a RejectedPromise with a promise.'
			);
		}

		$this->reason = $reason;
	}
	/**
	 * The function is then .
	 *
	 * @param callback $onFulfilled .
	 * @param callback $onRejected .
	 */
	public function then( callable $onFulfilled = null, callable $onRejected = null ) { // @codingStandardsIgnoreLine.
		// If there's no onRejected callback then just return self.
		if ( ! $onRejected ) { // @codingStandardsIgnoreLine.
			return $this;
		}

		$queue  = queue();
		$reason = $this->reason;
		$p      = new Promise( [ $queue, 'run' ] );
		$queue->add(
			static function () use ( $p, $reason, $onRejected ) { // @codingStandardsIgnoreLine.
				if ( $p->getState() === self::PENDING ) {
					try {
						// Return a resolved promise if onRejected does not throw.
						$p->resolve( $onRejected( $reason ) ); // @codingStandardsIgnoreLine.
					} catch ( \Throwable $e ) {
						// onRejected threw, so return a rejected promise.
						$p->reject( $e );
					} catch ( \Exception $e ) {
						// onRejected threw, so return a rejected promise.
						$p->reject( $e );
					}
				}
			}
		);

		return $p;
	}
	/**
	 * The function is otherwise .
	 *
	 * @param callback $onRejected .
	 */
	public function otherwise( callable $onRejected ) { // @codingStandardsIgnoreLine.
		return $this->then( null, $onRejected ); // @codingStandardsIgnoreLine.
	}
	/**
	 * The function is wait .
	 *
	 * @param callback $unwrap .
	 * @param null     $defaultDelivery .
	 *
	 * @throws exception_for .
	 */
	public function wait( $unwrap = true, $defaultDelivery = null ) { // @codingStandardsIgnoreLine.
		if ( $unwrap ) {
			throw exception_for( $this->reason );
		}
	}
	/**
	 * The function is getState.
	 */
	public function getState() {
		return self::REJECTED;
	}
	/**
	 * The function is resolve.
	 *
	 * @param string $value .
	 *
	 * @throws \LogicException Exception .
	 */
	public function resolve( $value ) {
		throw new \LogicException( 'Cannot resolve a rejected promise' );
	}
	/**
	 * The function is reject.
	 *
	 * @param string $reason .
	 *
	 * @throws \LogicException Exception .
	 */
	public function reject( $reason ) {
		if ( $reason !== $this->reason ) {
			throw new \LogicException( 'Cannot reject a rejected promise' );
		}
	}
	/**
	 * The function is cancel.
	 */
	public function cancel() {
	}
}
