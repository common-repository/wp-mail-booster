<?php // @codingStandardsIgnoreLine
/**
 * This file is for google services
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/src/google
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

/**
 * This class is used for google services
 */
class Google_Service {
	/**
	 * This variable used for the lbatch path
	 *
	 * @var string
	 */
	public $batchPath;// @codingStandardsIgnoreLine
	/**
	 * This variable used for the root ur
	 *
	 * @var string
	 */
	public $rootUrl;// @codingStandardsIgnoreLine
	/**
	 * This variable used for the version
	 *
	 * @var string
	 */
	public $version;
	/**
	 * This variable used for the services path
	 *
	 * @var string
	 */
	public $servicePath;// @codingStandardsIgnoreLine
	/**
	 * This variable used for the available scope
	 *
	 * @var string
	 */
	public $availableScopes;// @codingStandardsIgnoreLine
	/**
	 * This variable used for resources
	 *
	 * @var string
	 */
	public $resource;
	/**
	 * This variable used for the client
	 *
	 * @var string
	 */
	private $client;

	/**
	 * This function is for contruct.
	 *
	 * @param Google_Client $client .
	 */
	public function __construct( Google_Client $client ) {
		$this->client = $client;
	}

	/**
	 * Return the associated Google_Client class.
	 *
	 * @return Google_Client
	 */
	public function getClient() { // @codingStandardsIgnoreLine
		return $this->client;
	}

	/**
	 * Create a new HTTP Batch handler for this service
	 *
	 * @return Google_Http_Batch
	 */
	public function createBatch() { // @codingStandardsIgnoreLine
		return new Google_Http_Batch(
			$this->client,
			false,
			$this->rootUrl, // @codingStandardsIgnoreLine
			$this->batchPath // @codingStandardsIgnoreLine
		);
	}
}
