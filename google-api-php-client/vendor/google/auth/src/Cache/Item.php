<?php // @codingStandardsIgnoreLine
/**
 * This file used for a cache item .
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

/**
 * A cache item.
 */
final class Item implements CacheItemInterface {

	/**
	 * Variable key .
	 *
	 * @var string
	 */
	private $key;

	/**
	 * Variable value .
	 *
	 * @var mixed
	 */
	private $value;

	/**
	 * Variable expiration .
	 *
	 * @var \DateTime
	 */
	private $expiration;

	/**
	 * Variable is hit .
	 *
	 * @var bool
	 */
	private $isHit = false; // @codingStandardsIgnoreLine

	/**
	 * Public constructor
	 *
	 * @param string $key .
	 */
	public function __construct( $key ) {
		$this->key = $key;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getKey() {
		return $this->key;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get() {
		return $this->isHit() ? $this->value : null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isHit() {
		if ( ! $this->isHit ) { // @codingStandardsIgnoreLine
			return false;
		}

		if ( null === $this->expiration ) {
			return true;
		}

		return new \DateTime() < $this->expiration;
	}

	/**
	 * Function to set value
	 *
	 * @param string $value .
	 */
	public function set( $value ) {
		$this->isHit = true; // @codingStandardsIgnoreLine
		$this->value = $value;

		return $this;
	}

	/**
	 * Function for expire
	 *
	 * @param string $expiration .
	 */
	public function expiresAt( $expiration ) {
		if ( $this->isValidExpiration( $expiration ) ) {
			$this->expiration = $expiration;

			return $this;
		}

		$implementationMessage = interface_exists( 'DateTimeInterface' ) // @codingStandardsIgnoreLine
			? 'implement interface DateTimeInterface'
			: 'be an instance of DateTime';

		$error = sprintf(
			'Argument 1 passed to %s::expiresAt() must %s, %s given',
			get_class( $this ),
			$implementationMessage, // @codingStandardsIgnoreLine
			gettype( $expiration )
		);

		$this->handleError( $error );
	}

	/**
	 * Function for expire after
	 *
	 * @param string $time .
	 */
	public function expiresAfter( $time ) {
		if ( is_int( $time ) ) {
			$this->expiration = new \DateTime( "now + $time seconds" );
		} elseif ( $time instanceof \DateInterval ) {
			$this->expiration = ( new \DateTime() )->add( $time );
		} elseif ( null === $time ) {
			$this->expiration = $time;
		} else {
			$message = 'Argument 1 passed to %s::expiresAfter() must be an ' .
						'instance of DateInterval or of the type integer, %s given';
			$error   = sprintf( $message, get_class( $this ), gettype( $expiration ) );

			$this->handleError( $error );
		}

		return $this;
	}

	/**
	 * Handles an error.
	 *
	 * @param string $error .
	 * @throws \TypeError .
	 */
	private function handleError( $error ) {
		if ( class_exists( 'TypeError' ) ) {
			throw new \TypeError( $error );
		}

		trigger_error( $error, E_USER_ERROR ); // @codingStandardsIgnoreLine
	}

	/**
	 * Determines if an expiration is valid based on the rules defined by PSR6.
	 *
	 * @param mixed $expiration .
	 * @return bool
	 */
	private function isValidExpiration( $expiration ) {
		if ( null === $expiration ) {
			return true;
		}

		// We test for two types here due to the fact the DateTimeInterface
		// was not introduced until PHP 5.5. Checking for the DateTime type as
		// well allows us to support 5.4.
		if ( $expiration instanceof \DateTimeInterface ) {
			return true;
		}

		if ( $expiration instanceof \DateTime ) {
			return true;
		}

		return false;
	}
}
