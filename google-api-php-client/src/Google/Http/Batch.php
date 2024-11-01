<?php // @codingStandardsIgnoreLine
/**
 * This file is to handle batched requests to the Google API service .
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/src/google/http
 * @version 2.0.0
 */

/*
 * Copyright 2012 Google Inc.
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

use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class to handle batched requests to the Google API service.
 */
class Google_Http_Batch {

	const BATCH_PATH = 'batch';
	/**
	 * Variable to establish connection header
	 *
	 * @var $CONNECTION_ESTABLISHED_HEADERS .
	 */
	private static $CONNECTION_ESTABLISHED_HEADERS = array( // @codingStandardsIgnoreLineng
		"HTTP/1.0 200 Connection established\r\n\r\n",
		"HTTP/1.1 200 Connection established\r\n\r\n",
	);

	/**
	 * Multipart Boundary.
	 *
	 * @var string .
	 */
	private $boundary;
	/**
	 * Service requests to be executed .
	 *
	 * @var array .
	 */
	private $requests = array();
	/**
	 * Google_Client.
	 *
	 * @var Google_Client.
	 */
	private $client;
	/**
	 * VAriable for root url .
	 *
	 * @var string .
	 */
	private $rootUrl; // @codingStandardsIgnoreLine
	/**
	 * VAriable for batch path .
	 *
	 * @var string Multipart Boundary.
	 */
	private $batchPath; // @codingStandardsIgnoreLine
	/**
	 * Public constructor .
	 *
	 * @param Google_Client $client .
	 * @param bool          $boundary .
	 * @param string        $rootUrl .
	 * @param string        $batchPath .
	 */
	public function __construct(
		Google_Client $client,
		$boundary = false,
		$rootUrl = null, // @codingStandardsIgnoreLine
		$batchPath = null // @codingStandardsIgnoreLine
	) {
		$this->client    = $client;
		$this->boundary  = $boundary ?: mt_rand();
		$this->rootUrl   = rtrim( $rootUrl ?: $this->client->getConfig( 'base_path' ), '/' ); // @codingStandardsIgnoreLine
		$this->batchPath = $batchPath ?: self::BATCH_PATH; // @codingStandardsIgnoreLine
	}

	/**
	 * This function is used to request interface
	 *
	 * @param RequestInterface $request .
	 * @param bool             $key .
	 */
	public function add( RequestInterface $request, $key = false ) {
		if ( false == $key ) { // WPCS:Loose comparison ok .
			$key = mt_rand();
		}

		$this->requests[ $key ] = $request;
	}
	/**
	 * This function is used to execute
	 */
	public function execute() { // @codingStandardsIgnoreStart.
		$body              = '';
		$classes           = array();
		$batchHttpTemplate = <<<EOF
--%s
Content-Type: application/http
Content-Transfer-Encoding: binary
MIME-Version: 1.0
Content-ID: %s

%s
%s%s


EOF;

		foreach ( $this->requests as $key => $request ) {
			$firstLine = sprintf( // @codingStandardsIgnoreEnd
				'%s %s HTTP/%s',
				$request->getMethod(),
				$request->getRequestTarget(),
				$request->getProtocolVersion()
			);

			$content = (string) $request->getBody();

			$headers = '';
			foreach ( $request->getHeaders() as $name => $values ) {
				$headers .= sprintf( "%s:%s\r\n", $name, implode( ', ', $values ) );
			}

			$body .= sprintf(
				$batchHttpTemplate, // @codingStandardsIgnoreLine
				$this->boundary,
				$key,
				$firstLine, // @codingStandardsIgnoreLine
				$headers,
				$content ? "\n" . $content : ''
			);

			$classes[ 'response-' . $key ] = $request->getHeaderLine( 'X-Php-Expected-Class' );
		}

		$body   .= "--{$this->boundary}--";
		$body    = trim( $body );
		$url     = $this->rootUrl . '/' . $this->batchPath; // @codingStandardsIgnoreLine
		$headers = array(
			'Content-Type'   => sprintf( 'multipart/mixed; boundary=%s', $this->boundary ),
			'Content-Length' => strlen( $body ),
		);

		$request = new Request(
			'POST',
			$url,
			$headers,
			$body
		);

		$response = $this->client->execute( $request );

		return $this->parseResponse( $response, $classes );
	}
	/**
	 * This function is to parse the response
	 *
	 * @param ResponseInterface $response .
	 * @param array             $classes .
	 */
	public function parseResponse( ResponseInterface $response, $classes = array() ) { // @codingStandardsIgnoreLine
		$contentType = $response->getHeaderLine( 'content-type' ); // @codingStandardsIgnoreLine
		$contentType = explode( ';', $contentType ); // @codingStandardsIgnoreLine
		$boundary    = false;
		foreach ( $contentType as $part ) { // @codingStandardsIgnoreLine
			$part = explode( '=', $part, 2 );
			if ( isset( $part[0] ) && 'boundary' == trim( $part[0] ) ) { // WPCS:Loose comparison ok .
				$boundary = $part[1];
			}
		}

		$body = (string) $response->getBody();
		if ( ! empty( $body ) ) {
			$body      = str_replace( "--$boundary--", "--$boundary", $body );
			$parts     = explode( "--$boundary", $body );
			$responses = array();
			$requests  = array_values( $this->requests );

			foreach ( $parts as $i => $part ) {
				$part = trim( $part );
				if ( ! empty( $part ) ) {
					list($rawHeaders, $part) = explode( "\r\n\r\n", $part, 2 ); // @codingStandardsIgnoreLine
					$headers                 = $this->parseRawHeaders( $rawHeaders ); // @codingStandardsIgnoreLine

					$status = substr( $part, 0, strpos( $part, "\n" ) );
					$status = explode( ' ', $status );
					$status = $status[1];

					list($partHeaders, $partBody) = $this->parseHttpResponse( $part, false ); // @codingStandardsIgnoreLine
					$response                     = new Response(
						$status,
						$partHeaders, // @codingStandardsIgnoreLine
						Psr7\stream_for( $partBody ) // @codingStandardsIgnoreLine
					);

					// Need content id.
					$key = $headers['content-id'];

					try {
						$response = Google_Http_REST::decodeHttpResponse( $response, $requests[ $i - 1 ] );
					} catch ( Google_Service_Exception $e ) {
						// Store the exception as the response, so successful responses
						// can be processed.
						$response = $e;
					}

					$responses[ $key ] = $response;
				}
			}

			return $responses;
		}

		return null;
	}
	/**
	 * This function is to parse the raw headers
	 *
	 * @param array $rawHeaders .
	 */
	private function parseRawHeaders( $rawHeaders ) { // @codingStandardsIgnoreLine
		$headers             = array();
		$responseHeaderLines = explode( "\r\n", $rawHeaders ); // @codingStandardsIgnoreLine
		foreach ( $responseHeaderLines as $headerLine ) { // @codingStandardsIgnoreLine
			if ( $headerLine && strpos( $headerLine, ':' ) !== false ) { // @codingStandardsIgnoreLine
				list($header, $value) = explode( ': ', $headerLine, 2 ); // @codingStandardsIgnoreLine
				$header               = strtolower( $header );
				if ( isset( $headers[ $header ] ) ) {
					$headers[ $header ] .= "\n" . $value;
				} else {
					$headers[ $header ] = $value;
				}
			}
		}
		return $headers;
	}

	/**
	 * Used by the IO lib and also the batch processing.
	 *
	 * @param string $respData .
	 * @param string $headerSize .
	 * @return array
	 */
	private function parseHttpResponse( $respData, $headerSize ) { // @codingStandardsIgnoreLine
		// check proxy header
		foreach ( self::$CONNECTION_ESTABLISHED_HEADERS as $established_header ) { // @codingStandardsIgnoreLine
			if ( stripos( $respData, $established_header ) !== false ) { // @codingStandardsIgnoreLine
				// existed, remove it
				$respData = str_ireplace( $established_header, '', $respData ); // @codingStandardsIgnoreLine
				// Subtract the proxy header size unless the cURL bug prior to 7.30.0
				// is present which prevented the proxy header size from being taken into
				// account.
				// @TODO look into this
				// if (!$this->needsQuirk()) {
				// $headerSize -= strlen($established_header);
				// }
				break;
			}
		}

		if ( $headerSize ) { // @codingStandardsIgnoreLine
			$responseBody    = substr( $respData, $headerSize ); // @codingStandardsIgnoreLine
			$responseHeaders = substr( $respData, 0, $headerSize ); // @codingStandardsIgnoreLine
		} else {
			$responseSegments = explode( "\r\n\r\n", $respData, 2 ); // @codingStandardsIgnoreLine
			$responseHeaders  = $responseSegments[0]; // @codingStandardsIgnoreLine
			$responseBody     = isset( $responseSegments[1] ) ? $responseSegments[1] : null; // @codingStandardsIgnoreLine
		}

		$responseHeaders = $this->parseRawHeaders( $responseHeaders ); // @codingStandardsIgnoreLine

		return array( $responseHeaders, $responseBody ); // @codingStandardsIgnoreLine
	}
}
