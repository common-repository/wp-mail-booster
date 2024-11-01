<?php //@codingStandardsIgnoreLine
/**
 * This file is to handle media file upload .
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/src/google/http
 * @version 2.0.0
 */

/**
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
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;

/**
 * Manage large file uploads, which may be media but can be any type
 * of sizable data.
 */
class Google_Http_MediaFileUpload {

	const UPLOAD_MEDIA_TYPE     = 'media';
	const UPLOAD_MULTIPART_TYPE = 'multipart';
	const UPLOAD_RESUMABLE_TYPE = 'resumable';

	/**
	 * Variable for mime type .
	 *
	 * @var string $mimeType .
	 */
	private $mimeType; //@codingStandardsIgnoreLine
	/**
	 * Variable for data .
	 *
	 * @var string $data .
	 */
	private $data;
	/**
	 * Variable for resumable .
	 *
	 * @var bool $resumable .
	 */
	private $resumable;
	/**
	 * Variable for chunkSize .
	 *
	 * @var int $chunkSize .
	 */
	private $chunkSize; //@codingStandardsIgnoreLine
	/**
	 * Variable for size.
	 *
	 * @var int $size .
	 */
	private $size;
	/**
	 * Variable for resume Uri .
	 *
	 * @var string $resumeUri .
	 */
	private $resumeUri; //@codingStandardsIgnoreLine
	/**
	 * Variable for progress .
	 *
	 * @var int $progress .
	 */
	private $progress;
	/**
	 * Variable for client .
	 *
	 * @var Google_Client .
	 */
	private $client;
	/**
	 * Variable for request .
	 *
	 * @var Psr\Http\Message\RequestInterface .
	 */
	private $request;
	/**
	 * Variable for $boundary.
	 *
	 * @var string .
	 */
	private $boundary;

	/**
	 * Result code from last HTTP call
	 *
	 * @var int
	 */
	private $httpResultCode; //@codingStandardsIgnoreLine

	/**
	 * Public constructor
	 *
	 * @param Google_Client    $client .
	 * @param RequestInterface $request .
	 * @param string           $mimeType .
	 * @param string           $data The bytes you want to upload .
	 * @param bool             $resumable .
	 * @param bool             $chunkSize File will be uploaded in chunks of this many bytes.
	 *                                     only used if resumable=True .
	 */
	public function __construct(
		Google_Client $client,
		RequestInterface $request,
		$mimeType, //@codingStandardsIgnoreLine
		$data,
		$resumable = false,
		$chunkSize = false //@codingStandardsIgnoreLine
	) {
		$this->client    = $client;
		$this->request   = $request;
		$this->mimeType  = $mimeType; //@codingStandardsIgnoreLine
		$this->data      = $data;
		$this->resumable = $resumable;
		$this->chunkSize = $chunkSize; //@codingStandardsIgnoreLine
		$this->progress  = 0;

		$this->process();
	}

	/**
	 * Set the size of the file that is being uploaded.
	 *
	 * @param int $size - int file size in bytes .
	 */
	public function setFileSize( $size ) { //@codingStandardsIgnoreLine
		$this->size = $size;
	}

	/**
	 * Return the progress on the upload
	 *
	 * @return int progress in bytes uploaded.
	 */
	public function getProgress() { //@codingStandardsIgnoreLine
		return $this->progress;
	}

	/**
	 * Send the next part of the file to upload.
	 *
	 * @param bool $chunk the next set of bytes to send. If false will used $data passed .
	 *  at construct time.
	 */
	public function nextChunk( $chunk = false ) { //@codingStandardsIgnoreLine
		$resumeUri = $this->getResumeUri(); //@codingStandardsIgnoreLine

		if ( false == $chunk ) { // WPCS:Loose comparison ok .
			$chunk = substr( $this->data, $this->progress, $this->chunkSize ); //@codingStandardsIgnoreLine
		}

		$lastBytePos = $this->progress + strlen( $chunk ) - 1; //@codingStandardsIgnoreLine
		$headers     = array(
			'content-range'  => "bytes $this->progress-$lastBytePos/$this->size", //@codingStandardsIgnoreLine
			'content-length' => strlen( $chunk ),
			'expect'         => '',
		);

		$request = new Request(
			'PUT',
			$resumeUri, //@codingStandardsIgnoreLine
			$headers,
			Psr7\stream_for( $chunk )
		);

		return $this->makePutRequest( $request );
	}

	/**
	 * Return the HTTP result code from the last call made.
	 *
	 * @return int code
	 */
	public function getHttpResultCode() { //@codingStandardsIgnoreLine
		return $this->httpResultCode; //@codingStandardsIgnoreLine
	}

	/**
	 * Sends a PUT-Request to google drive and parses the response,
	 * setting the appropiate variables from the response()
	 *
	 * @param Google_Http_Request $request the Reuqest which will be send .
	 *
	 * @return false|mixed false when the upload is unfinished or the decoded http response
	 */
	private function makePutRequest( RequestInterface $request ) { //@codingStandardsIgnoreLine
		$response             = $this->client->execute( $request );
		$this->httpResultCode = $response->getStatusCode(); //@codingStandardsIgnoreLine

		if ( 308 == $this->httpResultCode ) { //@codingStandardsIgnoreLine
			// Track the amount uploaded.
			$range = $response->getHeaderLine( 'range' );
			if ( $range ) {
				$range_array    = explode( '-', $range );
				$this->progress = $range_array[1] + 1;
			}

			// Allow for changing upload URLs.
			$location = $response->getHeaderLine( 'location' );
			if ( $location ) {
				$this->resumeUri = $location; //@codingStandardsIgnoreLine
			}

			// No problems, but upload not complete.
			return false;
		}

		return Google_Http_REST::decodeHttpResponse( $response, $this->request );
	}

	/**
	 * Resume a previously unfinished upload
	 *
	 * @param string $resumeUri the resume-URI of the unfinished, resumable upload.
	 */
	public function resume( $resumeUri ) { //@codingStandardsIgnoreLine
		$this->resumeUri = $resumeUri; //@codingStandardsIgnoreLine
		$headers         = array(
			'content-range'  => "bytes */$this->size",
			'content-length' => 0,
		);
		$httpRequest     = new Request( //@codingStandardsIgnoreLine
			'PUT',
			$this->resumeUri, //@codingStandardsIgnoreLine
			$headers
		);

		return $this->makePutRequest( $httpRequest ); //@codingStandardsIgnoreLine
	}

	/**
	 * This function is to process
	 *
	 * @return Psr\Http\Message\RequestInterface $request .
	 * @visible for testing
	 */
	private function process() {
		$this->transformToUploadUrl();
		$request = $this->request;

		$postBody    = ''; //@codingStandardsIgnoreLine
		$contentType = false; //@codingStandardsIgnoreLine

		$meta = (string) $request->getBody();
		$meta = is_string( $meta ) ? json_decode( $meta, true ) : $meta;

		$uploadType = $this->getUploadType( $meta ); //@codingStandardsIgnoreLine
		$request    = $request->withUri(
			Uri::withQueryValue( $request->getUri(), 'uploadType', $uploadType ) //@codingStandardsIgnoreLine
		);

		$mimeType = $this->mimeType ?: $request->getHeaderLine( 'content-type' ); //@codingStandardsIgnoreLine

		if ( self::UPLOAD_RESUMABLE_TYPE == $uploadType ) { //@codingStandardsIgnoreLine
			$contentType = $mimeType; //@codingStandardsIgnoreLine
			$postBody    = is_string( $meta ) ? $meta : json_encode( $meta ); //@codingStandardsIgnoreLine
		} elseif ( self::UPLOAD_MEDIA_TYPE == $uploadType ) { //@codingStandardsIgnoreLine
			$contentType = $mimeType; //@codingStandardsIgnoreLine
			$postBody    = $this->data; //@codingStandardsIgnoreLine
		} elseif ( self::UPLOAD_MULTIPART_TYPE == $uploadType ) { //@codingStandardsIgnoreLine
			// This is a multipart/related upload.
			$boundary    = $this->boundary ?: mt_rand();
			$boundary    = str_replace( '"', '', $boundary );
			$contentType = 'multipart/related; boundary=' . $boundary; //@codingStandardsIgnoreLine
			$related     = "--$boundary\r\n";
			$related    .= "Content-Type: application/json; charset=UTF-8\r\n";
			$related    .= "\r\n" . json_encode( $meta ) . "\r\n"; //@codingStandardsIgnoreLine
			$related    .= "--$boundary\r\n";
			$related    .= "Content-Type: $mimeType\r\n"; //@codingStandardsIgnoreLine
			$related    .= "Content-Transfer-Encoding: base64\r\n";
			$related    .= "\r\n" . base64_encode( $this->data ) . "\r\n";
			$related    .= "--$boundary--";
			$postBody    = $related; //@codingStandardsIgnoreLine
		}

		$request = $request->withBody( Psr7\stream_for( $postBody ) ); //@codingStandardsIgnoreLine

		if ( isset( $contentType ) && $contentType ) { //@codingStandardsIgnoreLine
			$request = $request->withHeader( 'content-type', $contentType ); //@codingStandardsIgnoreLine
		}

		return $this->request = $request; //@codingStandardsIgnoreLine
	}

	/**
	 * Valid upload types:
	 * - resumable (UPLOAD_RESUMABLE_TYPE)
	 * - media (UPLOAD_MEDIA_TYPE)
	 * - multipart (UPLOAD_MULTIPART_TYPE)
	 *
	 * @param string $meta .
	 * @return string
	 * @visible for testing
	 */
	public function getUploadType( $meta ) { //@codingStandardsIgnoreLine
		if ( $this->resumable ) {
			return self::UPLOAD_RESUMABLE_TYPE;
		}

		if ( false == $meta && $this->data ) { // WPCS:Loose comparison ok .
			return self::UPLOAD_MEDIA_TYPE;
		}

		return self::UPLOAD_MULTIPART_TYPE;
	}
	/**
	 * This function is used to get resume uri
	 */
	public function getResumeUri() { //@codingStandardsIgnoreLine
		if ( null === $this->resumeUri ) { //@codingStandardsIgnoreLine
			$this->resumeUri = $this->fetchResumeUri(); //@codingStandardsIgnoreLine
		}

		return $this->resumeUri; //@codingStandardsIgnoreLine
	}
	/**
	 * This function is used to fetch resume uri
	 *
	 * @throws Google_Exception .
	 */
	private function fetchResumeUri() { //@codingStandardsIgnoreLine
		$body = $this->request->getBody();
		if ( $body ) {
			$headers = array(
				'content-type'            => 'application/json; charset=UTF-8',
				'content-length'          => $body->getSize(),
				'x-upload-content-type'   => $this->mimeType, //@codingStandardsIgnoreLine
				'x-upload-content-length' => $this->size,
				'expect'                  => '',
			);
			foreach ( $headers as $key => $value ) {
				$this->request = $this->request->withHeader( $key, $value );
			}
		}

		$response = $this->client->execute( $this->request, false );
		$location = $response->getHeaderLine( 'location' );
		$code     = $response->getStatusCode();

		if ( 200 == $code && true == $location ) { // WPCS:Loose comparison ok .
			return $location;
		}

		$message = $code;
		$body    = json_decode( (string) $this->request->getBody(), true );
		if ( isset( $body['error']['errors'] ) ) {
			$message .= ': ';
			foreach ( $body['error']['errors'] as $error ) {
				$message .= "{$error[domain]}, {$error[message]};";
			}
			$message = rtrim( $message, ';' );
		}

		$error = "Failed to start the resumable upload (HTTP {$message})";
		$this->client->getLogger()->error( $error );

		throw new Google_Exception( $error );
	}
	/**
	 * This function is used to transform to upload uri
	 */
	private function transformToUploadUrl() { //@codingStandardsIgnoreLine
		$parts = parse_url( (string) $this->request->getUri() ); //@codingStandardsIgnoreLine
		if ( ! isset( $parts['path'] ) ) {
			$parts['path'] = '';
		}
		$parts['path'] = '/upload' . $parts['path'];
		$uri           = Uri::fromParts( $parts );
		$this->request = $this->request->withUri( $uri );
	}
	/**
	 * This function is used to set chunk size
	 *
	 * @param string $chunkSize .
	 */
	public function setChunkSize( $chunkSize ) { //@codingStandardsIgnoreLine
		$this->chunkSize = $chunkSize; //@codingStandardsIgnoreLine
	}
	/**
	 * This function is used to get request .
	 */
	public function getRequest() { //@codingStandardsIgnoreLine
		return $this->request;
	}
}
