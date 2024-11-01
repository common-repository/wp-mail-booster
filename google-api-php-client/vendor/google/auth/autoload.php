<?php // @codingStandardsIgnoreLine
/**
 * This file to autoload file.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/
 * @version 2.0.0
 */

/**
 * Copyright 2014 Google Inc.
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
 * This function is used to autoload the files
 *
 * @param string $className .
 */
function oauth2client_php_autoload( $className ) { // @codingStandardsIgnoreLine
	$classPath = explode( '_', $className ); // @codingStandardsIgnoreLine
	if ( $classPath[0] != 'Google' ) { // @codingStandardsIgnoreLine
		return;
	}
	if ( count( $classPath ) > 3 ) { // @codingStandardsIgnoreLine
		// Maximum class file path depth in this project is 3.
		$classPath = array_slice( $classPath, 0, 3 ); // @codingStandardsIgnoreLine
	}
	$filePath = dirname( __FILE__ ) . '/src/' . implode( '/', $classPath ) . '.php'; // @codingStandardsIgnoreLine
	if ( file_exists( $filePath ) ) { // @codingStandardsIgnoreLine
		require_once $filePath; // @codingStandardsIgnoreLine
	}
}

spl_autoload_register( 'oauth2client_php_autoload' );
