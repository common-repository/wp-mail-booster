<?php // @codingStandardsIgnoreLine
/**
 * This file used to Authenticates requests using Google's Service Account credentials.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/
 * @version 2.0.0
 */

/**
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
 * Authenticates requests using Google's Service Account credentials via
 * JWT Access.
 *
 * This class allows authorizing requests for service accounts directly
 * from credentials from a json key file downloaded from the developer
 * console (via 'Generate new Json Key').  It is not part of any OAuth2
 * flow, rather it creates a JWT and sends that as a credential.
 */
class ServiceAccountJwtAccessCredentials extends CredentialsLoader {

	/**
	 * The OAuth2 instance used to conduct authorization.
	 *
	 * @var OAuth2
	 */
	protected $auth;

	/**
	 * Create a new ServiceAccountJwtAccessCredentials.
	 *
	 * @param string|array $jsonKey JSON credential file path or JSON credentials
	 *   as an associative array .
	 * @throws \InvalidArgumentException .
	 * @throws \LogicException .
	 */
	public function __construct( $jsonKey ) { // @codingStandardsIgnoreLine
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
				'issuer'           => $jsonKey['client_email'], // @codingStandardsIgnoreLine
				'sub'              => $jsonKey['client_email'], // @codingStandardsIgnoreLine
				'signingAlgorithm' => 'RS256',
				'signingKey'       => $jsonKey['private_key'], // @codingStandardsIgnoreLine
			]
		);
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
		if ( empty( $authUri ) ) { // @codingStandardsIgnoreLine
			return $metadata;
		}

		$this->auth->setAudience( $authUri ); // @codingStandardsIgnoreLine

		return parent::updateMetadata( $metadata, $authUri, $httpHandler ); // @codingStandardsIgnoreLine
	}

	/**
	 * Implements FetchAuthTokenInterface#fetchAuthToken.
	 *
	 * @param callable $httpHandler .
	 *
	 * @return array|void
	 */
	public function fetchAuthToken( callable $httpHandler = null ) { // @codingStandardsIgnoreLine
		$audience = $this->auth->getAudience();
		if ( empty( $audience ) ) {
			return null;
		}

		$access_token = $this->auth->toJwt();

		return array( 'access_token' => $access_token );
	}

	/**
	 * Function is used to get cache key
	 *
	 * @return string
	 */
	public function getCacheKey() {
		return $this->auth->getCacheKey();
	}

	/**
	 * Function is used to get last receive token
	 *
	 * @return array
	 */
	public function getLastReceivedToken() {
		return $this->auth->getLastReceivedToken();
	}
}
