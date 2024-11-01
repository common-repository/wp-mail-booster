<?php // @codingStandardsIgnoreLine
/**
 * This file used to supports authorization using a Google service account.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/
 * @version 2.0.0
 */

/*
 * Copyright 2015 Google Inc.
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

namespace Google\Auth\Credentials;

use Google\Auth\CredentialsLoader;
use Google\Auth\OAuth2;

/**
 * ServiceAccountCredentials supports authorization using a Google service
 * account.
 *
 * (cf https://developers.google.com/accounts/docs/OAuth2ServiceAccount)
 *
 * It's initialized using the json key file that's downloadable from developer
 * console, which should contain a private_key and client_email fields that it
 * uses.
 *
 * Use it with AuthTokenMiddleware to authorize http requests:
 *
 *   use Google\Auth\Credentials\ServiceAccountCredentials;
 *   use Google\Auth\Middleware\AuthTokenMiddleware;
 *   use GuzzleHttp\Client;
 *   use GuzzleHttp\HandlerStack;
 *
 *   $sa = new ServiceAccountCredentials(
 *       'https://www.googleapis.com/auth/taskqueue',
 *       '/path/to/your/json/key_file.json'
 *   );
 *   $middleware = new AuthTokenMiddleware($sa);
 *   $stack = HandlerStack::create();
 *   $stack->push($middleware);
 *
 *   $client = new Client([
 *       'handler' => $stack,
 *       'base_uri' => 'https://www.googleapis.com/taskqueue/v1beta2/projects/',
 *       'auth' => 'google_auth' // authorize all requests
 *   ]);
 *
 *   $res = $client->get('myproject/taskqueues/myqueue');
 */
class ServiceAccountCredentials extends CredentialsLoader {

	/**
	 * The OAuth2 instance used to conduct authorization.
	 *
	 * @var OAuth2
	 */
	protected $auth;

	/**
	 * Create a new ServiceAccountCredentials.
	 *
	 * @param string|array $scope the scope of the access request, expressed
	 *   either as an Array or as a space-delimited String.
	 * @param string|array $jsonKey JSON credential file path or JSON credentials
	 *   as an associative array .
	 * @param string       $sub an email address account to impersonate, in situations when
	 *         the service account has been delegated domain wide access.
	 * @throws \InvalidArgumentException .
	 * @throws \LogicException .
	 */
	public function __construct(
		$scope,
		$jsonKey, // @codingStandardsIgnoreLine
		$sub = null
	) {
		if ( is_string( $jsonKey ) ) { // @codingStandardsIgnoreLine
			if ( ! file_exists( $jsonKey ) ) { // @codingStandardsIgnoreLine
				throw new \InvalidArgumentException( 'file does not exist' );
			}
			$jsonKeyStream = file_get_contents( $jsonKey ); // @codingStandardsIgnoreLine
			if ( ! $jsonKey = json_decode( $jsonKeyStream, true ) ) { // @codingStandardsIgnoreLine
				throw new \LogicException( 'invalid json for auth config' );
			}
		}
		if ( ! array_key_exists( 'client_email', $jsonKey ) ) { // @codingStandardsIgnoreLine
			throw new \InvalidArgumentException(
				'json key is missing the client_email field'
			);
		}
		if ( ! array_key_exists( 'private_key', $jsonKey ) ) { // @codingStandardsIgnoreLine
			throw new \InvalidArgumentException(
				'json key is missing the private_key field'
			);
		}
		$this->auth = new OAuth2(
			[
				'audience'           => self::TOKEN_CREDENTIAL_URI,
				'issuer'             => $jsonKey['client_email'], // @codingStandardsIgnoreLine
				'scope'              => $scope,
				'signingAlgorithm'   => 'RS256',
				'signingKey'         => $jsonKey['private_key'], // @codingStandardsIgnoreLine
				'sub'                => $sub,
				'tokenCredentialUri' => self::TOKEN_CREDENTIAL_URI,
			]
		);
	}

	/**
	 * To fetch auth token
	 *
	 * @param callable $httpHandler .
	 *
	 * @return array
	 */
	public function fetchAuthToken( callable $httpHandler = null ) { // @codingStandardsIgnoreLine
		return $this->auth->fetchAuthToken( $httpHandler ); // @codingStandardsIgnoreLine
	}

	/**
	 * This function for get cache key
	 *
	 * @return string
	 */
	public function getCacheKey() {
		$key = $this->auth->getIssuer() . ':' . $this->auth->getCacheKey();
		if ( $sub = $this->auth->getSub() ) { // @codingStandardsIgnoreLine
			$key .= ':' . $sub;
		}

		return $key;
	}

	/**
	 * This function to get last recieve token
	 *
	 * @return array
	 */
	public function getLastReceivedToken() {
		return $this->auth->getLastReceivedToken();
	}

	/**
	 * Updates metadata with the authorization token.
	 *
	 * @param array    $metadata metadata hashmap .
	 * @param string   $authUri optional auth uri .
	 * @param callable $httpHandler callback which delivers psr7 request .
	 *
	 * @return array updated metadata hashmap
	 */
	public function updateMetadata(
		$metadata,
		$authUri = null, // @codingStandardsIgnoreLine
		callable $httpHandler = null // @codingStandardsIgnoreLine
	) {
		// scope exists. use oauth implementation .
		$scope = $this->auth->getScope();
		if ( ! is_null( $scope ) ) {
			return parent::updateMetadata( $metadata, $authUri, $httpHandler ); // @codingStandardsIgnoreLine
		}

		// no scope found. create jwt with the auth uri .
		$credJson = array( // @codingStandardsIgnoreLine
			'private_key'  => $this->auth->getSigningKey(),
			'client_email' => $this->auth->getIssuer(),
		);
		$jwtCreds = new ServiceAccountJwtAccessCredentials( $credJson ); // @codingStandardsIgnoreLine

		return $jwtCreds->updateMetadata( $metadata, $authUri, $httpHandler ); // @codingStandardsIgnoreLine
	}

	/**
	 * This function for an email address account to impersonate, in situations when
	 *   the service account has been delegated domain wide access
	 *
	 * @param string $sub .
	 */
	public function setSub( $sub ) {
		$this->auth->setSub( $sub );
	}
}
