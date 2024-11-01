<?php // @codingStandardsIgnoreLine
/**
 * This file used for implementing caching FetchAuthTokenInterface
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/
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

namespace Google\Auth;

use Psr\Cache\CacheItemPoolInterface;

/**
 * A class to implement caching for any object implementing
 * FetchAuthTokenInterface
 */
class FetchAuthTokenCache implements FetchAuthTokenInterface {

	use CacheTrait;

	/**
	 * Variable for fetcher
	 *
	 * @var FetchAuthTokenInterface .
	 */
	private $fetcher;

	/**
	 * Variable for cache config
	 *
	 * @var array
	 */
	private $cacheConfig; // @codingStandardsIgnoreLine

	/**
	 * Variable for cache
	 *
	 * @var CacheItemPoolInterface .
	 */
	private $cache;

	/**
	 *
	 * Public constructor
	 *
	 * @param FetchAuthTokenInterface $fetcher .
	 * @param array                   $cacheConfig .
	 * @param CacheItemPoolInterface  $cache .
	 */
	public function __construct(
		FetchAuthTokenInterface $fetcher,
		array $cacheConfig = null, // @codingStandardsIgnoreLine
		CacheItemPoolInterface $cache
	) {
		$this->fetcher     = $fetcher;
		$this->cache       = $cache;
		$this->cacheConfig = array_merge( // @codingStandardsIgnoreLine
			[
				'lifetime' => 1500,
				'prefix'   => '',
			], (array) $cacheConfig // @codingStandardsIgnoreLine
		);
	}

	/**
	 * Implements FetchAuthTokenInterface#fetchAuthToken.
	 *
	 * Checks the cache for a valid auth token and fetches the auth tokens
	 * from the supplied fetcher.
	 *
	 * @param callable $httpHandler callback which delivers psr7 request .
	 *
	 * @return array the response
	 *
	 * @throws \Exception .
	 */
	public function fetchAuthToken( callable $httpHandler = null ) { // @codingStandardsIgnoreLine
		// Use the cached value if its available.
		//
		// TODO: correct caching; update the call to setCachedValue to set the expiry
		// to the value returned with the auth token.
		//
		// TODO: correct caching; enable the cache to be cleared.
		$cacheKey = $this->fetcher->getCacheKey(); // @codingStandardsIgnoreLine
		$cached   = $this->getCachedValue( $cacheKey ); // @codingStandardsIgnoreLine
		if ( ! empty( $cached ) ) {
			return [ 'access_token' => $cached ];
		}

		$auth_token = $this->fetcher->fetchAuthToken( $httpHandler ); // @codingStandardsIgnoreLine

		if ( isset( $auth_token['access_token'] ) ) {
			$this->setCachedValue( $cacheKey, $auth_token['access_token'] ); // @codingStandardsIgnoreLine
		}

		return $auth_token;
	}

	/**
	 * This function is use to get cache
	 *
	 * @return string .
	 */
	public function getCacheKey() {
		return $this->getFullCacheKey( $this->fetcher->getCacheKey() );
	}

	/**
	 * This function is used to get last recieve token
	 *
	 * @return array|null
	 */
	public function getLastReceivedToken() {
		return $this->fetcher->getLastReceivedToken();
	}
}
