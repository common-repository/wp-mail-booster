<?php // @codingStandardsIgnoreLine
/**
 * This file is to Builds out a default http handler for the installed version of guzzle.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/src/google/authhandler
 * @version 2.0.0
 */

/**
 * Copyright 2015 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
/**
 * Class for google auth handler factory .
 */
class Google_AuthHandler_AuthHandlerFactory {

	/**
	 * Builds out a default http handler for the installed version of guzzle.
	 *
	 * @param string $cache .
	 * @param array  $cacheConfig .
	 * @return Google_AuthHandler_Guzzle5AuthHandler|Google_AuthHandler_Guzzle6AuthHandler
	 * @throws Exception .
	 */
	public static function build( $cache = null, array $cacheConfig = [] ) { // @codingStandardsIgnoreLine
		$version = ClientInterface::VERSION;

		switch ( $version[0] ) {
			case '5':
				return new Google_AuthHandler_Guzzle5AuthHandler( $cache, $cacheConfig ); // @codingStandardsIgnoreLine
			case '6':
				return new Google_AuthHandler_Guzzle6AuthHandler( $cache, $cacheConfig ); // @codingStandardsIgnoreLine
			default:
				throw new Exception( 'Version not supported' );
		}
	}
}
