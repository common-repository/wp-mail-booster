<?php // @codingStandardsIgnoreLine.
/**
 * This file of sampling handler
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

/**
 * Sampling handler
 *
 * A sampled event stream can be useful for logging high frequency events in
 * a production environment where you only need an idea of what is happening
 * and are not concerned with capturing every occurrence. Since the decision to
 * handle or not handle a particular event is determined randomly, the
 * resulting sampled log is not guaranteed to contain 1/N of the events that
 * occurred in the application, but based on the Law of large numbers, it will
 * tend to be close to this ratio with a large number of attempts.
 */
class SamplingHandler extends AbstractHandler {

	/**
	 * Variable for handler
	 *
	 * @var callable|HandlerInterface $handler .
	 */
	protected $handler;

	/**
	 * Variable for factor
	 *
	 * @var int $factor
	 */
	protected $factor;

	/**
	 * Public constructor
	 *
	 * @param callable|HandlerInterface $handler Handler or factory callable($record, $fingersCrossedHandler) .
	 * @param int                       $factor  Sample factor .
	 * @throws \RuntimeException .
	 */
	public function __construct( $handler, $factor ) {
		parent::__construct();
		$this->handler = $handler;
		$this->factor  = $factor;

		if ( ! $this->handler instanceof HandlerInterface && ! is_callable( $this->handler ) ) {
			throw new \RuntimeException( 'The given handler (' . json_encode( $this->handler ) . ') is not a callable nor a Monolog\Handler\HandlerInterface object' ); // @codingStandardsIgnoreLine.
		}
	}
	/**
	 * Function to handle recoprd
	 *
	 * @param array $record .
	 */
	public function isHandling( array $record ) {
		return $this->handler->isHandling( $record );
	}
	/**
	 * Function to handle record
	 *
	 * @param array $record .
	 * @throws \RuntimeException .
	 */
	public function handle( array $record ) {
		if ( $this->isHandling( $record ) && mt_rand( 1, $this->factor ) === 1 ) {
			// The same logic as in FingersCrossedHandler .
			if ( ! $this->handler instanceof HandlerInterface ) {
				$this->handler = call_user_func( $this->handler, $record, $this );
				if ( ! $this->handler instanceof HandlerInterface ) {
					throw new \RuntimeException( 'The factory callable should return a HandlerInterface' );
				}
			}

			if ( $this->processors ) {
				foreach ( $this->processors as $processor ) {
					$record = call_user_func( $processor, $record );
				}
			}

			$this->handler->handle( $record );
		}

		return false === $this->bubble;
	}
}
