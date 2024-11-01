<?php // @codingStandardsIgnoreLine
/**
 * This file is to Wrapper around Google Access Tokens.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/src/google/accesstoken
 * @version 2.0.0
 */

/*
 * Copyright 2008 Google Inc.
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

use Firebase\JWT\ExpiredException as ExpiredExceptionV3;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\Cache\CacheItemPoolInterface;
use Google\Auth\Cache\MemoryCacheItemPool;
use Stash\Driver\FileSystem;
use Stash\Pool;

/**
 * Wrapper around Google Access Tokens which provides convenience functions
 */
class Google_AccessToken_Verify {

	const FEDERATED_SIGNON_CERT_URL = 'https://www.googleapis.com/oauth2/v3/certs';
	const OAUTH2_ISSUER             = 'accounts.google.com';
	const OAUTH2_ISSUER_HTTPS       = 'https://accounts.google.com';

	/**
	 * The http client
	 *
	 * @var GuzzleHttp\ClientInterface
	 */
	private $http;

	/**
	 * The cache class
	 *
	 * @var Psr\Cache\CacheItemPoolInterface
	 */
	private $cache;

	/**
	 * Instantiates the class, but does not initiate the login flow, leaving it
	 * to the discretion of the caller.
	 *
	 * @param ClientInterface        $http .
	 * @param CacheItemPoolInterface $cache .
	 * @param string                 $jwt .
	 */
	public function __construct(
		ClientInterface $http = null,
		CacheItemPoolInterface $cache = null,
		$jwt = null
	) {
		if ( null === $http ) {
			$http = new Client();
		}

		if ( null === $cache ) {
			$cache = new MemoryCacheItemPool();
		}

		$this->http  = $http;
		$this->cache = $cache;
		$this->jwt   = $jwt ?: $this->getJwtService();
	}

	/**
	 * Verifies an id token and returns the authenticated apiLoginTicket.
	 * Throws an exception if the id token is not valid.
	 * The audience parameter can be used to control which id tokens are
	 * accepted.  By default, the id token must have been issued to this OAuth2 client.
	 *
	 * @param string $idToken .
	 * @param string $audience .
	 * @return array the token payload, if successful .
	 * @throws LogicException .
	 */
	public function verifyIdToken( $idToken, $audience = null ) { // @codingStandardsIgnoreLine
		if ( empty( $idToken ) ) {// @codingStandardsIgnoreLine
			throw new LogicException( 'id_token cannot be null' );
		}

		// set phpseclib constants if applicable .
		$this->setPhpsecConstants();

		// Check signature .
		$certs = $this->getFederatedSignOnCerts();
		foreach ( $certs as $cert ) {
			$bigIntClass = $this->getBigIntClass();// @codingStandardsIgnoreLine
			$rsaClass    = $this->getRsaClass();// @codingStandardsIgnoreLine
			$modulus     = new $bigIntClass( $this->jwt->urlsafeB64Decode( $cert['n'] ), 256 );// @codingStandardsIgnoreLine
			$exponent    = new $bigIntClass( $this->jwt->urlsafeB64Decode( $cert['e'] ), 256 );// @codingStandardsIgnoreLine

			$rsa = new $rsaClass();// @codingStandardsIgnoreLine
			$rsa->loadKey(
				array(
					'n' => $modulus,
					'e' => $exponent,
				)
			);

			try {
				$payload = $this->jwt->decode(
					$idToken,// @codingStandardsIgnoreLine
					$rsa->getPublicKey(),
					array( 'RS256' )
				);

				if ( property_exists( $payload, 'aud' ) ) {
					if ( $audience && $payload->aud != $audience ) { // WPCS:Loose comparison ok.
						return false;
					}
				}

				// support HTTP and HTTPS issuers
				// @see https://developers.google.com/identity/sign-in/web/backend-auth .
				$issuers = array( self::OAUTH2_ISSUER, self::OAUTH2_ISSUER_HTTPS );
				if ( ! isset( $payload->iss ) || ! in_array( $payload->iss, $issuers ) ) {// @codingStandardsIgnoreLine
					return false;
				}

				return (array) $payload;
			} catch ( ExpiredException $e ) {
				return false;
			} catch ( ExpiredExceptionV3 $e ) {
				return false;
			} catch ( DomainException $e ) { // @codingStandardsIgnoreLine
				// continue .
			}
		}

		return false;
	}
	/**
	 * This function is used to get cache .
	 */
	private function getCache() { // @codingStandardsIgnoreLine
		return $this->cache;
	}

	/**
	 * Retrieve and cache a certificates file.
	 *
	 * @param string $url string location .
	 * @throws Google_Exception .
	 * @return array certificates
	 */
	private function retrieveCertsFromLocation( $url ) { // @codingStandardsIgnoreLine
		// If we're retrieving a local file, just grab it.
		if ( 0 !== strpos( $url, 'http' ) ) {
			if ( ! $file = file_get_contents( $url ) ) { // @codingStandardsIgnoreLine
				throw new Google_Exception(
					"Failed to retrieve verification certificates: '" .
					$url . "'."
				);
			}

			return json_decode( $file, true );
		}

		$response = $this->http->get( $url );

		if ( $response->getStatusCode() == 200 ) { // WPCS:Loose comparison ok .
			return json_decode( (string) $response->getBody(), true );
		}
		throw new Google_Exception(
			sprintf(
				'Failed to retrieve verification certificates: "%s".',
				$response->getBody()->getContents()
			),
			$response->getStatusCode()
		);
	}

	/**
	 * Gets federated sign-on certificates to use for verifying identity tokens.
	 * Returns certs as array structure, where keys are key ids, and values
	 * are PEM encoded certificates.
	 *
	 * @throws InvalidArgumentException .
	 */
	private function getFederatedSignOnCerts() { // @codingStandardsIgnoreLine
		$certs = null;
		if ( $cache = $this->getCache() ) { // @codingStandardsIgnoreLine
			$cacheItem = $cache->getItem( 'federated_signon_certs_v3', 3600 ); // @codingStandardsIgnoreLine
			$certs     = $cacheItem->get(); // @codingStandardsIgnoreLine
		}

		if ( ! $certs ) {
			$certs = $this->retrieveCertsFromLocation(
				self::FEDERATED_SIGNON_CERT_URL
			);

			if ( $cache ) {
				$cacheItem->set( $certs ); // @codingStandardsIgnoreLine
				$cache->save( $cacheItem ); // @codingStandardsIgnoreLine
			}
		}

		if ( ! isset( $certs['keys'] ) ) {
			throw new InvalidArgumentException(
				'federated sign-on certs expects "keys" to be set'
			);
		}

		return $certs['keys'];
	}
	/**
	 * This function is used to get JWT services
	 */
	private function getJwtService() { // @codingStandardsIgnoreLine
		$jwtClass = 'JWT'; // @codingStandardsIgnoreLine
		if ( class_exists( '\Firebase\JWT\JWT' ) ) {
			$jwtClass = 'Firebase\JWT\JWT'; // @codingStandardsIgnoreLine
		}

		if ( property_exists( $jwtClass, 'leeway' ) ) { // @codingStandardsIgnoreLine
			// adds 1 second to JWT leeway
			// @see https://github.com/google/google-api-php-client/issues/827 .
			$jwtClass::$leeway = 1; // @codingStandardsIgnoreLine
		}

		return new $jwtClass(); // @codingStandardsIgnoreLine
	}
	/**
	 * This function is used to get rsa class
	 */
	private function getRsaClass() { // @codingStandardsIgnoreLine
		if ( class_exists( 'phpseclib\Crypt\RSA' ) ) {
			return 'phpseclib\Crypt\RSA';
		}

		return 'Crypt_RSA';
	}
	/**
	 * This function is used to get big int class
	 */
	private function getBigIntClass() { // @codingStandardsIgnoreLine
		if ( class_exists( 'phpseclib\Math\BigInteger' ) ) {
			return 'phpseclib\Math\BigInteger';
		}

		return 'Math_BigInteger';
	}
	/**
	 * This function is used to get open ssl constant
	 *
	 * @throws \Exception .
	 */
	private function getOpenSslConstant() { // @codingStandardsIgnoreLine
		if ( class_exists( 'phpseclib\Crypt\RSA' ) ) {
			return 'phpseclib\Crypt\RSA::MODE_OPENSSL';
		}

		if ( class_exists( 'Crypt_RSA' ) ) {
			return 'CRYPT_RSA_MODE_OPENSSL';
		}

		throw new \Exception( 'Cannot find RSA class' );
	}

	/**
	 * Phpseclib calls "phpinfo" by default, which requires special
	 * whitelisting in the AppEngine VM environment. This function
	 * sets constants to bypass the need for phpseclib to check phpinfo .
	 *
	 * @see phpseclib/Math/BigInteger
	 * @see https://github.com/GoogleCloudPlatform/getting-started-php/issues/85
	 */
	private function setPhpsecConstants() { // @codingStandardsIgnoreLine
		if ( filter_var( getenv( 'GAE_VM' ), FILTER_VALIDATE_BOOLEAN ) ) {
			if ( ! defined( 'MATH_BIGINTEGER_OPENSSL_ENABLED' ) ) {
				define( 'MATH_BIGINTEGER_OPENSSL_ENABLED', true );
			}
			if ( ! defined( 'CRYPT_RSA_MODE' ) ) {
				define( 'CRYPT_RSA_MODE', constant( $this->getOpenSslConstant() ) );
			}
		}
	}
}
