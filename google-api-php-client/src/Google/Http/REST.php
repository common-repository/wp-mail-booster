<?php // @codingStandardsIgnoreLine
/**
 * This file is to implements the RESTful transport of apiServiceRequest.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/src/google/services
 * @version 2.0.0
 */

/**
 * Copyright 2010 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use Google\Auth\HttpHandler\HttpHandlerFactory;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * This class implements the RESTful transport of apiServiceRequest()'s
 */
class Google_Http_REST {

	/**
	 * Executes a Psr\Http\Message\RequestInterface and (if applicable) automatically retries
	 * when errors occur.
	 *
	 * @param ClientInterface  $client .
	 * @param RequestInterface $request .
	 * @param string           $expectedClass .
	 * @param array            $config .
	 * @param string           $retryMap .
	 * @return array decoded result
	 * @throws Google_Service_Exception On server side error (ie: not authenticated,
	 *  invalid or malformed post body, invalid url) .
	 */
	public static function execute(
		ClientInterface $client,
		RequestInterface $request,
		$expectedClass = null, // @codingStandardsIgnoreLine
		$config = array(),
		$retryMap = null // @codingStandardsIgnoreLine
	) {
		$runner = new Google_Task_Runner(
			$config,
			sprintf( '%s %s', $request->getMethod(), (string) $request->getUri() ),
			array( get_class(), 'doExecute' ),
			array( $client, $request, $expectedClass ) // @codingStandardsIgnoreLine
		);

		if ( null !== $retryMap ) { // @codingStandardsIgnoreLine
			$runner->setRetryMap( $retryMap ); // @codingStandardsIgnoreLine
		}

		return $runner->run();
	}

	/**
	 * Executes a Psr\Http\Message\RequestInterface
	 *
	 * @param ClientInterface  $client .
	 * @param RequestInterface $request .
	 * @param string           $expectedClass .
	 * @return array decoded result
	 * @throws RequestException On server side error (ie: not authenticated,
	 *  invalid or malformed post body, invalid url) .
	 */
	public static function doExecute( ClientInterface $client, RequestInterface $request, $expectedClass = null ) { // @codingStandardsIgnoreLine
		try {
			$httpHandler = HttpHandlerFactory::build( $client ); // @codingStandardsIgnoreLine
			$response    = $httpHandler( $request ); // @codingStandardsIgnoreLine
		} catch ( RequestException $e ) {
			// if Guzzle throws an exception, catch it and handle the response .
			if ( ! $e->hasResponse() ) {
				throw $e;
			}

			$response = $e->getResponse();
			// specific checking for Guzzle 5: convert to PSR7 response .
			if ( $response instanceof \GuzzleHttp\Message\ResponseInterface ) {
				$response = new Response(
					$response->getStatusCode(),
					$response->getHeaders() ?: [],
					$response->getBody(),
					$response->getProtocolVersion(),
					$response->getReasonPhrase()
				);
			}
		}

		return self::decodeHttpResponse( $response, $request, $expectedClass ); // @codingStandardsIgnoreLine
	}

	/**
	 * Decode an HTTP Response.
	 *
	 * @static
	 * @throws Google_Service_Exception .
	 * @param ResponseInterface $response The http response to be decoded.
	 * @param RequestInterface  $request .
	 * @param string            $expectedClass .
	 * @return mixed|null
	 */
	public static function decodeHttpResponse( // @codingStandardsIgnoreLine
		ResponseInterface $response,
		RequestInterface $request = null,
		$expectedClass = null // @codingStandardsIgnoreLine
	) {
		$code = $response->getStatusCode();

		// retry strategy .
		if ( intVal( $code ) >= 400 ) {
			// if we errored out, it should be safe to grab the response body .
			$body = (string) $response->getBody();

			// Check if we received errors, and add those to the Exception for convenience .
			throw new Google_Service_Exception( $body, $code, null, self::getResponseErrors( $body ) );
		}

		// Ensure we only pull the entire body into memory if the request is not
		// of media type .
		$body = self::decodeBody( $response, $request );

		if ( $expectedClass = self::determineExpectedClass( $expectedClass, $request ) ) { // @codingStandardsIgnoreLine
			$json = json_decode( $body, true );

			return new $expectedClass( $json ); // @codingStandardsIgnoreLine
		}

		return $response;
	}
	/**
	 * Decode an HTTP body.
	 *
	 * @param ResponseInterface $response The http response to be decoded.
	 * @param RequestInterface  $request .
	 */
	private static function decodeBody( ResponseInterface $response, RequestInterface $request = null ) { // @codingStandardsIgnoreLine
		if ( self::isAltMedia( $request ) ) {
			// don't decode the body, it's probably a really long string .
			return '';
		}

		return (string) $response->getBody();
	}
	/**
	 * To determine expected class.
	 *
	 * @param string           $expectedClass .
	 * @param RequestInterface $request .
	 */
	private static function determineExpectedClass( $expectedClass, RequestInterface $request = null ) { // @codingStandardsIgnoreLine
		// "false" is used to explicitly prevent an expected class from being returned
		if ( false === $expectedClass ) { // @codingStandardsIgnoreLine
			return null;
		}

		// if we don't have a request, we just use what's passed in .
		if ( null === $request ) {
			return $expectedClass; // @codingStandardsIgnoreLine
		}

		// return what we have in the request header if one was not supplied .
		return $expectedClass ?: $request->getHeaderLine( 'X-Php-Expected-Class' ); // @codingStandardsIgnoreLine
	}
	/**
	 * Get response header.
	 *
	 * @param string $body .
	 */
	private static function getResponseErrors( $body ) { // @codingStandardsIgnoreLine
		$json = json_decode( $body, true );

		if ( isset( $json['error']['errors'] ) ) {
			return $json['error']['errors'];
		}

		return null;
	}
	/**
	 * Used to alt media.
	 *
	 * @param RequestInterface $request .
	 */
	private static function isAltMedia( RequestInterface $request = null ) { // @codingStandardsIgnoreLine
		if ( $request && $qs = $request->getUri()->getQuery() ) { // @codingStandardsIgnoreLine
			parse_str( $qs, $query );
			if ( isset( $query['alt'] ) && 'media' == $query['alt'] ) { // WPCS:Loose comparison ok .
				return true;
			}
		}

		return false;
	}
}
