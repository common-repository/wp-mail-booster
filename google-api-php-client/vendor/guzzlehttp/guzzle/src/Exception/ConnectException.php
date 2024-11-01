<?php // @codingStandardsIgnoreLine
/**
 * This file for Exception thrown when a connection cannot be established.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace GuzzleHttp\Exception;

use Psr\Http\Message\RequestInterface;

/**
 * Exception thrown when a connection cannot be established.
 *
 * Note that no response is present for a ConnectException
 */
class ConnectException extends RequestException {
	/**
	 * This function is __construct.
	 *
	 * @param string           $message passes parameter as message.
	 * @param RequestInterface $request passes parameter as request.
	 * @param \Exception       $previous passes parameter as exception.
	 * @param array            $handlerContext passes parameter as handlerContext.
	 */
	public function __construct(
		$message,
		RequestInterface $request,
		\Exception $previous = null,
		array $handlerContext = []// @codingStandardsIgnoreLine
	) {
		parent::__construct( $message, $request, null, $previous, $handlerContext );// @codingStandardsIgnoreLine
	}

	/**
	 * This function is getResponse.
	 */
	public function getResponse() {
		return null;
	}

	/**
	 * This function is hasResponse.
	 */
	public function hasResponse() {
		return false;
	}
}
