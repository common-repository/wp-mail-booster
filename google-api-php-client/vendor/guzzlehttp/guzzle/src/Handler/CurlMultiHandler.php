<?php // @codingStandardsIgnoreLine
/**
 * This file for handle multi curl
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace GuzzleHttp\Handler;

use GuzzleHttp\Promise as P;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7;
use Psr\Http\Message\RequestInterface;

/**
 * Returns an asynchronous response using curl_multi_* functions.
 *
 * When using the CurlMultiHandler, custom curl options can be specified as an
 * associative array of curl option constants mapping to values in the
 * **curl** key of the provided request options.
 *
 * @property resource $_mh Internal use only. Lazy loaded multi-handle.
 */
class CurlMultiHandler {
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $factory.
	 */
	private $factory;
	private $selectTimeout;// @codingStandardsIgnoreLine
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $active.
	 */
	private $active;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $handles.
	 */
	private $handles = [];
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $delays.
	 */
	private $delays = [];

	/**
	 * This handler accepts the following options:
	 *
	 * - handle_factory: An optional factory  used to create curl handles
	 * - select_timeout: Optional timeout (in seconds) to block before timing
	 *   out while selecting curl handles. Defaults to 1 second.
	 *
	 * @param array $options passes parameter as options.
	 */
	public function __construct( array $options = [] ) {
		$this->factory       = isset( $options['handle_factory'] )
			? $options['handle_factory'] : new CurlFactory( 50 );
		$this->selectTimeout = isset( $options['select_timeout'] )// @codingStandardsIgnoreLine
			? $options['select_timeout'] : 1;
	}
	/**
	 * This function is __get.
	 *
	 * @param string $name passes parameter as name.
	 * @throws \BadMethodCallException On error.
	 */
	public function __get( $name ) {
		if ( '_mh' === $name ) {
			return $this->_mh = curl_multi_init();// @codingStandardsIgnoreLine
		}

		throw new \BadMethodCallException();
	}
	/**
	 * This function is __destruct.
	 */
	public function __destruct() {
		if ( isset( $this->_mh ) ) {
			curl_multi_close( $this->_mh );// @codingStandardsIgnoreLine
			unset( $this->_mh );
		}
	}
	/**
	 * This function is __invoke.
	 *
	 * @param RequestInterface $request passes parameter as request.
	 * @param array            $options passes parameter as options.
	 */
	public function __invoke( RequestInterface $request, array $options ) {
		$easy = $this->factory->create( $request, $options );
		$id   = (int) $easy->handle;

		$promise = new Promise(
			[ $this, 'execute' ],
			function () use ( $id ) {
				return $this->cancel( $id ); }
		);

		$this->addRequest(
			[
				'easy'     => $easy,
				'deferred' => $promise,
			]
		);

		return $promise;
	}

	/**
	 * Ticks the curl event loop.
	 */
	public function tick() {
		// Add any delayed handles if needed.
		if ( $this->delays ) {
			$currentTime = microtime( true );// @codingStandardsIgnoreLine
			foreach ( $this->delays as $id => $delay ) {
				if ( $currentTime >= $delay ) {// @codingStandardsIgnoreLine
					unset( $this->delays[ $id ] );
					curl_multi_add_handle(// @codingStandardsIgnoreLine
						$this->_mh,
						$this->handles[ $id ]['easy']->handle
					);
				}
			}
		}

		// Step through the task queue which may add additional requests.
		P\queue()->run();

		if ( $this->active &&
			curl_multi_select( $this->_mh, $this->selectTimeout ) === -1// @codingStandardsIgnoreLine
		) {
			// Perform a usleep if a select returns -1.
			// See: https://bugs.php.net/bug.php?id=61141.
			usleep( 250 );
		}

		while ( curl_multi_exec( $this->_mh, $this->active ) === CURLM_CALL_MULTI_PERFORM ) {// @codingStandardsIgnoreLine
		}

		$this->processMessages();
	}

	/**
	 * Runs until all outstanding connections have completed.
	 */
	public function execute() {
		$queue = P\queue();

		while ( $this->handles || ! $queue->isEmpty() ) {
			// If there are no transfers, then sleep for the next delay.
			if ( ! $this->active && $this->delays ) {
				usleep( $this->timeToNext() );
			}
			$this->tick();
		}
	}
	/**
	 * This function is addRequest.
	 *
	 * @param array $entry passes parameter as entry.
	 */
	private function addRequest( array $entry ) {// @codingStandardsIgnoreLine
		$easy                 = $entry['easy'];
		$id                   = (int) $easy->handle;
		$this->handles[ $id ] = $entry;
		if ( empty( $easy->options['delay'] ) ) {
			curl_multi_add_handle( $this->_mh, $easy->handle );// @codingStandardsIgnoreLine
		} else {
			$this->delays[ $id ] = microtime( true ) + ( $easy->options['delay'] / 1000 );
		}
	}

	/**
	 * Cancels a handle from sending and removes references to it.
	 *
	 * @param int $id Handle ID to cancel and remove.
	 *
	 * @return bool True on success, false on failure.
	 */
	private function cancel( $id ) {
		// Cannot cancel if it has been processed.
		if ( ! isset( $this->handles[ $id ] ) ) {
			return false;
		}

		$handle = $this->handles[ $id ]['easy']->handle;
		unset( $this->delays[ $id ], $this->handles[ $id ] );
		curl_multi_remove_handle( $this->_mh, $handle );// @codingStandardsIgnoreLine
		curl_close( $handle );// @codingStandardsIgnoreLine

		return true;
	}
	/**
	 * This function is processMessages.
	 */
	private function processMessages() {// @codingStandardsIgnoreLine
		while ( $done = curl_multi_info_read( $this->_mh ) ) {// @codingStandardsIgnoreLine
			$id = (int) $done['handle'];
			curl_multi_remove_handle( $this->_mh, $done['handle'] );// @codingStandardsIgnoreLine

			if ( ! isset( $this->handles[ $id ] ) ) {
				// Probably was cancelled.
				continue;
			}

			$entry = $this->handles[ $id ];
			unset( $this->handles[ $id ], $this->delays[ $id ] );
			$entry['easy']->errno = $done['result'];
			$entry['deferred']->resolve(
				CurlFactory::finish(
					$this,
					$entry['easy'],
					$this->factory
				)
			);
		}
	}
	/**
	 * This function is timeToNext.
	 */
	private function timeToNext() {// @codingStandardsIgnoreLine
		$currentTime = microtime( true );// @codingStandardsIgnoreLine
		$nextTime    = PHP_INT_MAX;// @codingStandardsIgnoreLine
		foreach ( $this->delays as $time ) {
			if ( $time < $nextTime ) {// @codingStandardsIgnoreLine
				$nextTime = $time;// @codingStandardsIgnoreLine
			}
		}

		return max( 0, $nextTime - $currentTime ) * 1000000;// @codingStandardsIgnoreLine
	}
}
