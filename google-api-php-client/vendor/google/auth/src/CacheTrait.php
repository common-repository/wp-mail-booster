<?php // @codingStandardsIgnoreLine
/**
 * This file used for cachetrait
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

namespace Google\Auth;

trait CacheTrait {
	/**
	 * Variable for max key length
	 *
	 * @var $maxKeyLength .
	 */
	private $maxKeyLength = 64; // @codingStandardsIgnoreLine

	/**
	 * Gets the cached value if it is present in the cache when that is
	 * available.
	 *
	 * @param string $k .
	 */
	private function getCachedValue( $k ) { // @codingStandardsIgnoreLine
		if ( is_null( $this->cache ) ) {
			return;
		}

		$key = $this->getFullCacheKey( $k );
		if ( is_null( $key ) ) {
			return;
		}

		$cacheItem = $this->cache->getItem( $key ); // @codingStandardsIgnoreLine
		if ( $cacheItem->isHit() ) { // @codingStandardsIgnoreLine
			return $cacheItem->get(); // @codingStandardsIgnoreLine
		}
	}

	/**
	 * Saves the value in the cache when that is available.
	 *
	 * @param string $k .
	 * @param string $v .
	 */
	private function setCachedValue( $k, $v ) { // @codingStandardsIgnoreLine
		if ( is_null( $this->cache ) ) {
			return;
		}

		$key = $this->getFullCacheKey( $k );
		if ( is_null( $key ) ) {
			return;
		}

		$cacheItem = $this->cache->getItem( $key ); // @codingStandardsIgnoreLine
		$cacheItem->set( $v ); // @codingStandardsIgnoreLine
		$cacheItem->expiresAfter( $this->cacheConfig['lifetime'] ); // @codingStandardsIgnoreLine
		return $this->cache->save( $cacheItem ); // @codingStandardsIgnoreLine
	}

	/**
	 * This function is used to get full cache key
	 *
	 * @param string $key .
	 */
	private function getFullCacheKey( $key ) { // @codingStandardsIgnoreLine
		if ( is_null( $key ) ) {
			return;
		}

		$key = $this->cacheConfig['prefix'] . $key; // @codingStandardsIgnoreLine

		// ensure we do not have illegal characters .
		$key = preg_replace( '|[^a-zA-Z0-9_\.!]|', '', $key );

		// Hash keys if they exceed $maxKeyLength (defaults to 64) .
		if ( $this->maxKeyLength && strlen( $key ) > $this->maxKeyLength ) { // @codingStandardsIgnoreLine
			$key = substr( hash( 'sha256', $key ), 0, $this->maxKeyLength ); // @codingStandardsIgnoreLine
		}

		return $key;
	}
}
