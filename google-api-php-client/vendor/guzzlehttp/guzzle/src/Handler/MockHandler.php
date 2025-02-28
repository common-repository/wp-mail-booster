<?php // @codingStandardsIgnoreLine
/**
 * This file for  MockHandler that uses the default handler stack list of
 * middlewares.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace GuzzleHttp\Handler;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\RejectedPromise;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Handler that returns responses or throw exceptions from a queue.
 */
class MockHandler implements \Countable {
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $queue.
	 */
	private $queue = [];
	private $lastRequest;// @codingStandardsIgnoreLine
	private $lastOptions;// @codingStandardsIgnoreLine
	private $onFulfilled;// @codingStandardsIgnoreLine
	private $onRejected;// @codingStandardsIgnoreLine

	/**
	 * Creates a new MockHandler that uses the default handler stack list of
	 * middlewares.
	 *
	 * @param array    $queue Array of responses, callables, or exceptions.
	 * @param callable $onFulfilled Callback to invoke when the return value is fulfilled.
	 * @param callable $onRejected  Callback to invoke when the return value is rejected.
	 *
	 * @return HandlerStack
	 */
	public static function createWithMiddleware(
		array $queue = null,
		callable $onFulfilled = null,// @codingStandardsIgnoreLine
		callable $onRejected = null// @codingStandardsIgnoreLine
	) {
		return HandlerStack::create( new self( $queue, $onFulfilled, $onRejected ) );// @codingStandardsIgnoreLine
	}

	/**
	 * The passed in value must be an array of
	 * {@see Psr7\Http\Message\ResponseInterface} objects, Exceptions,
	 * callables, or Promises.
	 *
	 * @param array    $queue passes parameter as queue.
	 * @param callable $onFulfilled Callback to invoke when the return value is fulfilled.
	 * @param callable $onRejected  Callback to invoke when the return value is rejected.
	 */
	public function __construct(
		array $queue = null,
		callable $onFulfilled = null,// @codingStandardsIgnoreLine
		callable $onRejected = null// @codingStandardsIgnoreLine
	) {
		$this->onFulfilled = $onFulfilled;// @codingStandardsIgnoreLine
		$this->onRejected  = $onRejected;// @codingStandardsIgnoreLine

		if ( $queue ) {
			call_user_func_array( [ $this, 'append' ], $queue );
		}
	}
	/**
	 * This function is __invoke.
	 *
	 * @param RequestInterface $request passes parameter as request.
	 * @param array            $options passes parameter as options.
	 * @throws \OutOfBoundsException On Error.
	 * @throws \InvalidArgumentException On error.
	 */
	public function __invoke( RequestInterface $request, array $options ) {
		if ( ! $this->queue ) {
			throw new \OutOfBoundsException( 'Mock queue is empty' );
		}

		if ( isset( $options['delay'] ) ) {
			usleep( $options['delay'] * 1000 );
		}

		$this->lastRequest = $request;// @codingStandardsIgnoreLine
		$this->lastOptions = $options;// @codingStandardsIgnoreLine
		$response          = array_shift( $this->queue );

		if ( isset( $options['on_headers'] ) ) {
			if ( ! is_callable( $options['on_headers'] ) ) {
				throw new \InvalidArgumentException( 'on_headers must be callable' );
			}
			try {
				$options['on_headers']($response);
			} catch ( \Exception $e ) {
				$msg      = 'An error was encountered during the on_headers event';
				$response = new RequestException( $msg, $request, $response, $e );
			}
		}

		if ( is_callable( $response ) ) {
			$response = call_user_func( $response, $request, $options );
		}

		$response = $response instanceof \Exception
			? \GuzzleHttp\Promise\rejection_for( $response )
			: \GuzzleHttp\Promise\promise_for( $response );

		return $response->then(
			function ( $value ) use ( $request, $options ) {
				$this->invokeStats( $request, $options, $value );
				if ( $this->onFulfilled ) {// @codingStandardsIgnoreLine
					call_user_func( $this->onFulfilled, $value );// @codingStandardsIgnoreLine
				}
				if ( isset( $options['sink'] ) ) {
					$contents = (string) $value->getBody();
					$sink     = $options['sink'];

					if ( is_resource( $sink ) ) {
						fwrite( $sink, $contents );// @codingStandardsIgnoreLine
					} elseif ( is_string( $sink ) ) {
						file_put_contents( $sink, $contents );// @codingStandardsIgnoreLine
					} elseif ( $sink instanceof \Psr\Http\Message\StreamInterface ) {
						$sink->write( $contents );
					}
				}

				return $value;
			},
			function ( $reason ) use ( $request, $options ) {
				$this->invokeStats( $request, $options, null, $reason );
				if ( $this->onRejected ) {// @codingStandardsIgnoreLine
					call_user_func( $this->onRejected, $reason );// @codingStandardsIgnoreLine
				}
				return \GuzzleHttp\Promise\rejection_for( $reason );
			}
		);
	}

	/**
	 * Adds one or more variadic requests, exceptions, callables, or promises
	 * to the queue.
	 *
	 * @throws \InvalidArgumentException On error.
	 */
	public function append() {
		foreach ( func_get_args() as $value ) {
			if ( $value instanceof ResponseInterface
				|| $value instanceof \Exception
				|| $value instanceof PromiseInterface
				|| is_callable( $value )
			) {
				$this->queue[] = $value;
			} else {
				throw new \InvalidArgumentException(
					'Expected a response or '
					. 'exception. Found ' . \GuzzleHttp\describe_type( $value )
				);
			}
		}
	}

	/**
	 * Get the last received request.
	 *
	 * @return RequestInterface
	 */
	public function getLastRequest() {
		return $this->lastRequest;// @codingStandardsIgnoreLine
	}

	/**
	 * Get the last received request options.
	 *
	 * @return array
	 */
	public function getLastOptions() {
		return $this->lastOptions;// @codingStandardsIgnoreLine
	}

	/**
	 * Returns the number of remaining items in the queue.
	 *
	 * @return int
	 */
	public function count() {
		return count( $this->queue );
	}
	/**
	 * This function is invokeStats.
	 *
	 * @param RequestInterface  $request passes parameter as request.
	 * @param array             $options passes parameter as options.
	 * @param ResponseInterface $response passes parameter as response.
	 * @param null              $reason passes parameter as reason.
	 */
	private function invokeStats(
		RequestInterface $request,
		array $options,
		ResponseInterface $response = null,
		$reason = null
	) {
		if ( isset( $options['on_stats'] ) ) {
			$stats = new TransferStats( $request, $response, 0, $reason );
			call_user_func( $options['on_stats'], $stats );
		}
	}
}
