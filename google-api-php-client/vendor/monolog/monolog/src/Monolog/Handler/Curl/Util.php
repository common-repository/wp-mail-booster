<?php  // @codingStandardsIgnoreLine.
/**
 * This file is part of the Monolog package.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/util
 * @version 2.0.0
 */

namespace Monolog\Handler\Curl;

/**
 * This class is Util.
 */
class Util {

	private static $retriableErrorCodes = array( // @codingStandardsIgnoreLine.
		CURLE_COULDNT_RESOLVE_HOST,
		CURLE_COULDNT_CONNECT,
		CURLE_HTTP_NOT_FOUND,
		CURLE_READ_ERROR,
		CURLE_OPERATION_TIMEOUTED,
		CURLE_HTTP_POST_ERROR,
		CURLE_SSL_CONNECT_ERROR,
	);

	/**
	 * Executes a CURL request with optional retries and exception on failure .
	 *
	 * @param  resource $ch curl handler .
	 * @param  resource $retries .
	 * @param  resource $closeAfterDone .
	 * @throws \RuntimeException .
	 */
	public static function execute( $ch, $retries = 5, $closeAfterDone = true ) {// @codingStandardsIgnoreLine.
		while ( $retries-- ) {
			if ( curl_exec( $ch ) === false ) {// @codingStandardsIgnoreLine.
				$curlErrno = curl_errno( $ch );// @codingStandardsIgnoreLine.

				if ( false === in_array( $curlErrno, self::$retriableErrorCodes, true ) || ! $retries ) {// @codingStandardsIgnoreLine.
					$curlError = curl_error( $ch );// @codingStandardsIgnoreLine.

					if ( $closeAfterDone ) {// @codingStandardsIgnoreLine.
						curl_close( $ch );// @codingStandardsIgnoreLine.
					}

					throw new \RuntimeException( sprintf( 'Curl error (code %s): %s', $curlErrno, $curlError ) );// @codingStandardsIgnoreLine.
				}

				continue;
			}

			if ( $closeAfterDone ) {// @codingStandardsIgnoreLine.
				curl_close( $ch );// @codingStandardsIgnoreLine.
			}
			break;
		}
	}
}
