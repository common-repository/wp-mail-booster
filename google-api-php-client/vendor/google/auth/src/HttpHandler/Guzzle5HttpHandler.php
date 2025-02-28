<?php // @codingStandardsIgnoreLine
/**
 * This file used for handle http.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/
 * @version 2.0.0
 */

/**
 * Copyright 2015 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Google\Auth\HttpHandler;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * This class handle hhtp access
 */
class Guzzle5HttpHandler {

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
	 * Accepts a PSR-7 Request and an array of options and returns a PSR-7 response.
	 *
	 * @param RequestInterface $request .
	 * @param array            $options .
	 *
	 * @return ResponseInterface
	 */
	public function __invoke( RequestInterface $request, array $options = [] ) {
		$request = $this->client->createRequest(
			$request->getMethod(),
			$request->getUri(),
			array_merge(
				[
					'headers' => $request->getHeaders(),
					'body'    => $request->getBody(),
				], $options
			)
		);

		$response = $this->client->send( $request );

		return new Response(
			$response->getStatusCode(),
			$response->getHeaders() ?: [],
			$response->getBody(),
			$response->getProtocolVersion(),
			$response->getReasonPhrase()
		);
	}
}
