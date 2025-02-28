<?php // @codingStandardsIgnoreLine
/**
 * This file used for Guzzle Subscriber that adds an Authorization
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

namespace Google\Auth\Subscriber;

use Google\Auth\CacheTrait;
use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\RequestEvents;
use GuzzleHttp\Event\SubscriberInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * ScopedAccessTokenSubscriber is a Guzzle Subscriber that adds an Authorization
 * header provided by a closure.
 *
 * The closure returns an access token, taking the scope, either a single
 * string or an array of strings, as its value.  If provided, a cache will be
 * used to preserve the access token for a given lifetime.
 *
 * Requests will be accessed with the authorization header:
 *
 * 'authorization' 'Bearer <access token obtained from the closure>'
 */
class ScopedAccessTokenSubscriber implements SubscriberInterface {

	use CacheTrait;

	const DEFAULT_CACHE_LIFETIME = 1500;

	/**
	 * Variable for cache
	 *
	 * @var CacheItemPoolInterface
	 */
	private $cache;

	/**
	 * Variable for token func
	 *
	 * @var callable The access token generator function
	 */
	private $tokenFunc; // @codingStandardsIgnoreLine

	/**
	 * Variable for scope
	 *
	 * @var array|string The scopes used to generate the token
	 */
	private $scopes;

	/**
	 * Variable for cache config
	 *
	 * @var array
	 */
	private $cacheConfig; // @codingStandardsIgnoreLine

	/**
	 * Creates a new ScopedAccessTokenSubscriber.
	 *
	 * @param callable               $tokenFunc a token generator function .
	 * @param array|string           $scopes the token authentication scopes .
	 * @param array                  $cacheConfig configuration for the cache when it's present .
	 * @param CacheItemPoolInterface $cache an implementation of CacheItemPoolInterface .
	 * @throws \InvalidArgumentException .
	 */
	public function __construct(
		callable $tokenFunc, // @codingStandardsIgnoreLine
		$scopes,
		array $cacheConfig = null, // @codingStandardsIgnoreLine
		CacheItemPoolInterface $cache = null
	) {
		$this->tokenFunc = $tokenFunc; // @codingStandardsIgnoreLine
		if ( ! ( is_string( $scopes ) || is_array( $scopes ) ) ) {
			throw new \InvalidArgumentException(
				'wants scope should be string or array'
			);
		}
		$this->scopes = $scopes;

		if ( ! is_null( $cache ) ) {
			$this->cache       = $cache;
			$this->cacheConfig = array_merge( // @codingStandardsIgnoreLine
				[
					'lifetime' => self::DEFAULT_CACHE_LIFETIME,
					'prefix'   => '',
				], $cacheConfig // @codingStandardsIgnoreLine
			);
		}
	}

	/**
	 * This funtion to get events
	 *
	 * @return array
	 */
	public function getEvents() {
		return [ 'before' => [ 'onBefore', RequestEvents::SIGN_REQUEST ] ];
	}

	/**
	 * Updates the request with an Authorization header when auth is 'scoped'.
	 *
	 *   E.g this could be used to authenticate using the AppEngine
	 *   AppIdentityService.
	 *
	 *   use google\appengine\api\app_identity\AppIdentityService;
	 *   use Google\Auth\Subscriber\ScopedAccessTokenSubscriber;
	 *   use GuzzleHttp\Client;
	 *
	 *   $scope = 'https://www.googleapis.com/auth/taskqueue'
	 *   $subscriber = new ScopedAccessToken(
	 *       'AppIdentityService::getAccessToken',
	 *       $scope,
	 *       ['prefix' => 'Google\Auth\ScopedAccessToken::'],
	 *       $cache = new Memcache()
	 *   );
	 *
	 *   $client = new Client([
	 *       'base_url' => 'https://www.googleapis.com/taskqueue/v1beta2/projects/',
	 *       'defaults' => ['auth' => 'scoped']
	 *   ]);
	 *   $client->getEmitter()->attach($subscriber);
	 *
	 *   $res = $client->get('myproject/taskqueues/myqueue');
	 *
	 * @param BeforeEvent $event .
	 */
	public function onBefore( BeforeEvent $event ) {
		// Requests using "auth"="scoped" will be authorized.
		$request = $event->getRequest();
		if ( $request->getConfig()['auth'] != 'scoped' ) { // @codingStandardsIgnoreLine
			return;
		}
		$auth_header = 'Bearer ' . $this->fetchToken();
		$request->setHeader( 'authorization', $auth_header );
	}

	/**
	 * This function is used to get cache key
	 *
	 * @return string
	 */
	private function getCacheKey() {
		$key = null;

		if ( is_string( $this->scopes ) ) {
			$key .= $this->scopes;
		} elseif ( is_array( $this->scopes ) ) {
			$key .= implode( ':', $this->scopes );
		}

		return $key;
	}

	/**
	 * Determine if token is available in the cache, if not call tokenFunc to
	 * fetch it.
	 *
	 * @return string
	 */
	private function fetchToken() {
		$cacheKey = $this->getCacheKey(); // @codingStandardsIgnoreLine
		$cached   = $this->getCachedValue( $cacheKey ); // @codingStandardsIgnoreLine

		if ( ! empty( $cached ) ) {
			return $cached;
		}

		$token = call_user_func( $this->tokenFunc, $this->scopes ); // @codingStandardsIgnoreLine
		$this->setCachedValue( $cacheKey, $token ); // @codingStandardsIgnoreLine

		return $token;
	}
}
