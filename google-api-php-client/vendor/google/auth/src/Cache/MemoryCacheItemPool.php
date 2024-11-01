<?php // @codingStandardsIgnoreLine
/**
 * This file used to in-memory cache implementation.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/
 * @version 2.0.0
 */

/**
 * Copyright 2016 Google Inc.
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

namespace Google\Auth\Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Simple in-memory cache implementation.
 */
final class MemoryCacheItemPool implements CacheItemPoolInterface {

	/**
	 * Variable items
	 *
	 * @var CacheItemInterface[]
	 */
	private $items;

	/**
	 * Variable Deffered items
	 *
	 * @var CacheItemInterface[]
	 */
	private $deferredItems; // @codingStandardsIgnoreLine

	/**
	 * {@inheritdoc}
	 *
	 * @param array $key .
	 */
	public function getItem( $key ) {
		return current( $this->getItems( [ $key ] ) );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param array $keys .
	 */
	public function getItems( array $keys = [] ) {
		$items = [];

		foreach ( $keys as $key ) {
			$this->isValidKey( $key );
			$items[ $key ] = $this->hasItem( $key ) ? clone $this->items[ $key ] : new Item( $key );
		}

		return $items;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param array $key .
	 */
	public function hasItem( $key ) {
		$this->isValidKey( $key );

		return isset( $this->items[ $key ] ) && $this->items[ $key ]->isHit();
	}

	/**
	 * {@inheritdoc}
	 */
	public function clear() {
		$this->items    = [];
		$this->deferred = [];

		return true;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param array $key .
	 */
	public function deleteItem( $key ) {
		return $this->deleteItems( [ $key ] );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param array $keys .
	 */
	public function deleteItems( array $keys ) {
		array_walk( $keys, [ $this, 'isValidKey' ] );

		foreach ( $keys as $key ) {
			unset( $this->items[ $key ] );
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param CacheItemInterface $item .
	 */
	public function save( CacheItemInterface $item ) {
		$this->items[ $item->getKey() ] = $item;

		return true;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param CacheItemInterface $item .
	 */
	public function saveDeferred( CacheItemInterface $item ) {
		$this->deferredItems[ $item->getKey() ] = $item; // @codingStandardsIgnoreLine

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function commit() {
		foreach ( $this->deferredItems as $item ) { // @codingStandardsIgnoreLine
			$this->save( $item );
		}

		$this->deferredItems = []; // @codingStandardsIgnoreLine

		return true;
	}

	/**
	 * Determines if the provided key is valid.
	 *
	 * @param string $key .
	 * @return bool
	 * @throws InvalidArgumentException .
	 */
	private function isValidKey( $key ) {
		$invalidCharacters = '{}()/\\\\@:'; // @codingStandardsIgnoreLine

		if ( ! is_string( $key ) || preg_match( "#[$invalidCharacters]#", $key ) ) { // @codingStandardsIgnoreLine
			throw new InvalidArgumentException( 'The provided key is not valid: ' . var_export( $key, true ) ); // @codingStandardsIgnoreLine
		}

		return true;
	}
}
