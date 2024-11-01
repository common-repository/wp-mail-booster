<?php // @codingStandardsIgnoreLine
/**
 * This file for Middleware that retries requests based on the boolean result.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */
namespace GuzzleHttp;

use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\RejectedPromise;
use GuzzleHttp\Psr7;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Middleware that retries requests based on the boolean result of
 * invoking the provided "decider" function.
 */
class RetryMiddleware {

	private $nextHandler;// @codingStandardsIgnoreLine
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $decider.
	 */
	private $decider;

	/**
	 * This function is __construct.
	 *
	 * @param callable $decider     Function that accepts the number of retries,
	 *                              a request, [response], and [exception] and
	 *                              returns true if the request is to be
	 *                              retried.
	 * @param callable $nextHandler Next handler to invoke.
	 * @param callable $delay       Function that accepts the number of retries
	 *                              and [response] and returns the number of
	 *                              milliseconds to delay.
	 */
	public function __construct(
		callable $decider,
		callable $nextHandler,// @codingStandardsIgnoreLine
		callable $delay = null
	) {
		$this->decider     = $decider;
		$this->nextHandler = $nextHandler;// @codingStandardsIgnoreLine
		$this->delay       = $delay ?: __CLASS__ . '::exponentialDelay';
	}

	/**
	 * Default exponential backoff delay function.
	 *
	 * @param string $retries passes parameter as retries.
	 *
	 * @return int
	 */
	public static function exponentialDelay( $retries ) {// @codingStandardsIgnoreLine
		return (int) pow( 2, $retries - 1 );
	}

	/**
	 * This function is __invoke.
	 *
	 * @param RequestInterface $request passes parameter as request.
	 * @param array            $options passes parameter as options.
	 *
	 * @return PromiseInterface
	 */
	public function __invoke( RequestInterface $request, array $options ) {
		if ( ! isset( $options['retries'] ) ) {
			$options['retries'] = 0;
		}

		$fn = $this->nextHandler;// @codingStandardsIgnoreLine
		return $fn( $request, $options )
			->then(
				$this->onFulfilled( $request, $options ),
				$this->onRejected( $request, $options )
			);
	}

	private function onFulfilled( RequestInterface $req, array $options ) {// @codingStandardsIgnoreLine
		return function ( $value ) use ( $req, $options ) {
			if ( ! call_user_func(
				$this->decider,
				$options['retries'],
				$req,
				$value,
				null
			) ) {
				return $value;
			}
			return $this->doRetry( $req, $options, $value );
		};
	}

	private function onRejected( RequestInterface $req, array $options ) {// @codingStandardsIgnoreLine
		return function ( $reason ) use ( $req, $options ) {
			if ( ! call_user_func(
				$this->decider,
				$options['retries'],
				$req,
				null,
				$reason
			) ) {
				return \GuzzleHttp\Promise\rejection_for( $reason );
			}
			return $this->doRetry( $req, $options );
		};
	}

	private function doRetry( RequestInterface $request, array $options, ResponseInterface $response = null ) {// @codingStandardsIgnoreLine
		$options['delay'] = call_user_func( $this->delay, ++$options['retries'], $response );

		return $this( $request, $options );
	}
}
