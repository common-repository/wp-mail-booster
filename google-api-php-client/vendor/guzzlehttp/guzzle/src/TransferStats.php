<?php // @codingStandardsIgnoreLine
/**
 * This file to Represents data at the point after it was transferred.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace GuzzleHttp;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Represents data at the point after it was transferred either successfully
 * or after a network error.
 */
final class TransferStats {
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $request.
	 */
	private $request;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $response.
	 */
	private $response;
	private $transferTime;// @codingStandardsIgnoreLine
	private $handlerStats;// @codingStandardsIgnoreLine
	private $handlerErrorData;// @codingStandardsIgnoreLine

	/**
	 * This function is __construct.
	 *
	 * @param RequestInterface  $request          Request that was sent.
	 * @param ResponseInterface $response         Response received (if any).
	 * @param null              $transferTime     Total handler transfer time.
	 * @param mixed             $handlerErrorData Handler error data.
	 * @param array             $handlerStats     Handler specific stats.
	 */
	public function __construct(
		RequestInterface $request,
		ResponseInterface $response = null,
		$transferTime = null,// @codingStandardsIgnoreLine
		$handlerErrorData = null,// @codingStandardsIgnoreLine
		$handlerStats = []// @codingStandardsIgnoreLine
	) {
		$this->request          = $request;
		$this->response         = $response;
		$this->transferTime     = $transferTime;// @codingStandardsIgnoreLine
		$this->handlerErrorData = $handlerErrorData;// @codingStandardsIgnoreLine
		$this->handlerStats     = $handlerStats;// @codingStandardsIgnoreLine
	}

	/**
	 * This function is getRequest.
	 *
	 * @return RequestInterface
	 */
	public function getRequest() {// @codingStandardsIgnoreLine
		return $this->request;
	}

	/**
	 * Returns the response that was received (if any).
	 *
	 * @return ResponseInterface|null
	 */
	public function getResponse() {// @codingStandardsIgnoreLine
		return $this->response;
	}

	/**
	 * Returns true if a response was received.
	 *
	 * @return bool
	 */
	public function hasResponse() {// @codingStandardsIgnoreLine
		return $this->response !== null;// @codingStandardsIgnoreLine
	}

	/**
	 * Gets handler specific error data.
	 *
	 * This might be an exception, a integer representing an error code, or
	 * anything else. Relying on this value assumes that you know what handler
	 * you are using.
	 *
	 * @return mixed
	 */
	public function getHandlerErrorData() {// @codingStandardsIgnoreLine
		return $this->handlerErrorData;// @codingStandardsIgnoreLine
	}

	/**
	 * Get the effective URI the request was sent to.
	 *
	 * @return UriInterface
	 */
	public function getEffectiveUri() {// @codingStandardsIgnoreLine
		return $this->request->getUri();
	}

	/**
	 * Get the estimated time the request was being transferred by the handler.
	 *
	 * @return float Time in seconds.
	 */
	public function getTransferTime() {// @codingStandardsIgnoreLine
		return $this->transferTime;// @codingStandardsIgnoreLine
	}

	/**
	 * Gets an array of all of the handler specific transfer data.
	 *
	 * @return array
	 */
	public function getHandlerStats() {// @codingStandardsIgnoreLine
		return $this->handlerStats;// @codingStandardsIgnoreLine
	}

	/**
	 * Get a specific handler statistic from the handler by name.
	 *
	 * @param string $stat Handler specific transfer stat to retrieve.
	 *
	 * @return mixed|null
	 */
	public function getHandlerStat( $stat ) {// @codingStandardsIgnoreLine
		return isset( $this->handlerStats[ $stat ] )// @codingStandardsIgnoreLine
			? $this->handlerStats[ $stat ]// @codingStandardsIgnoreLine
			: null;
	}
}
