<?php // @codingStandardsIgnoreLine
/**
 * This file used for Authenticates requests using IAM credentials.
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

/**
 * Authenticates requests using IAM credentials.
 */
class IAMCredentials {

	const SELECTOR_KEY = 'x-goog-iam-authority-selector';
	const TOKEN_KEY    = 'x-goog-iam-authorization-token';

	/**
	 * Variable for selector
	 *
	 * @var string
	 */
	private $selector;

	/**
	 * Variable for token
	 *
	 * @var string
	 */
	private $token;

	/**
	 * Public constructor
	 *
	 * @param string $selector string the IAM selector .
	 * @param string $token string the IAM token .
	 * @throws \InvalidArgumentException .
	 */
	public function __construct( $selector, $token ) {
		if ( ! is_string( $selector ) ) {
			throw new \InvalidArgumentException(
				'selector must be a string'
			);
		}
		if ( ! is_string( $token ) ) {
			throw new \InvalidArgumentException(
				'token must be a string'
			);
		}

		$this->selector = $selector;
		$this->token    = $token;
	}

	/**
	 * Export a callback function which updates runtime metadata.
	 *
	 * @return array updateMetadata function
	 */
	public function getUpdateMetadataFunc() { // @codingStandardsIgnoreLine
		return array( $this, 'updateMetadata' );
	}

	/**
	 * Updates metadata with the appropriate header metadata.
	 *
	 * @param array    $metadata metadata hashmap .
	 * @param string   $unusedAuthUri optional auth uri .
	 * @param callable $httpHandler callback which delivers psr7 request
	 *        Note: this param is unused here, only included here for
	 *        consistency with other credentials class .
	 *
	 * @return array updated metadata hashmap
	 */
	public function updateMetadata( // @codingStandardsIgnoreLine
		$metadata,
		$unusedAuthUri = null, // @codingStandardsIgnoreLine
		callable $httpHandler = null // @codingStandardsIgnoreLine
	) {
		$metadata_copy                       = $metadata;
		$metadata_copy[ self::SELECTOR_KEY ] = $this->selector;
		$metadata_copy[ self::TOKEN_KEY ]    = $this->token;

		return $metadata_copy;
	}
}
