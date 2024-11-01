<?php // @codingStandardsIgnoreLine
/**
 * This file for Exception when an HTTP error occurs.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace GuzzleHttp\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Exception when an HTTP error occurs (4xx or 5xx error)
 */
class BadResponseException extends RequestException {
	/**
	 * This function is __construct.
	 *
	 * @param string            $message passes parameter as message.
	 * @param RequestInterface  $request passes parameter as request.
	 * @param ResponseInterface $response passes parameter as response.
	 * @param \Exception        $previous passes parameter as previous.
	 * @param array             $handlerContext passes parameter as handlercontext.
	 */
	public function __construct(
		$message,
		RequestInterface $request,
		ResponseInterface $response = null,
		\Exception $previous = null,
		array $handlerContext = []// @codingStandardsIgnoreLine
	) {
		if ( null === $response ) {
			@trigger_error(// @codingStandardsIgnoreLine
				'Instantiating the ' . __CLASS__ . ' class without a Response is deprecated since version 6.3 and will be removed in 7.0.',
				E_USER_DEPRECATED
			);
		}
		parent::__construct( $message, $request, $response, $previous, $handlerContext );// @codingStandardsIgnoreLine
	}
}
