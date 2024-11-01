<?php // @codingStandardsIgnoreLine
/**
 * This file used for handle http.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/
 * @version 2.0.0
 */

namespace Google\Auth\HttpHandler;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * This class handle hhtp access
 */
class Guzzle6HttpHandler {

	/**
	 * Variable client
	 *
	 * @var ClientInterface
	 */
	private $client;

	/**
	 * Public constructor
	 *
	 * @param ClientInterface $client .
	 */
	public function __construct( ClientInterface $client ) {
		$this->client = $client;
	}

	/**
	 * Accepts a PSR-7 request and an array of options and returns a PSR-7 response.
	 *
	 * @param RequestInterface $request .
	 * @param array            $options .
	 *
	 * @return ResponseInterface
	 */
	public function __invoke( RequestInterface $request, array $options = [] ) {
		return $this->client->send( $request, $options );
	}
}
