<?php // @codingStandardsIgnoreLine
/**
 * This file for curl easy handle
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace GuzzleHttp\Handler;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Represents a cURL easy handle and the data it populates.
 *
 * @internal
 */
final class EasyHandle {
	/**
	 * The version of this plugin.
	 *
	 * @access   public
	 * @var      string    $handle.
	 */
	public $handle;
	/**
	 * The version of this plugin.
	 *
	 * @access   public
	 * @var      array    $sink.
	 */
	public $sink;
	/**
	 * The version of this plugin.
	 *
	 * @access   public
	 * @var      array    $headers.
	 */
	public $headers = [];
	/**
	 * The version of this plugin.
	 *
	 * @access   public
	 * @var      array    $response.
	 */
	public $response;
	/**
	 * The version of this plugin.
	 *
	 * @access   public
	 * @var      string    $request.
	 */
	public $request;
	/**
	 * The version of this plugin.
	 *
	 * @access   public
	 * @var      array    $options.
	 */
	public $options = [];
	/**
	 * The version of this plugin.
	 *
	 * @access   public
	 * @var      array    $errno.
	 */
	public $errno = 0;
	/**
	 * The version of this plugin.
	 *
	 * @access   public
	 * @var      array    $onHeadersException.
	 */
	public $onHeadersException;// @codingStandardsIgnoreLine

	/**
	 * Attach a response to the easy handle based on the received headers.
	 *
	 * @throws \RuntimeException If no headers have been received.
	 */
	public function createResponse() {// @codingStandardsIgnoreLine
		if ( empty( $this->headers ) ) {
			throw new \RuntimeException( 'No headers have been received' );
		}

		// HTTP-version SP status-code SP reason-phrase.
		$startLine      = explode( ' ', array_shift( $this->headers ), 3 );// @codingStandardsIgnoreLine
		$headers        = \GuzzleHttp\headers_from_lines( $this->headers );// @codingStandardsIgnoreLine
		$normalizedKeys = \GuzzleHttp\normalize_header_keys( $headers );// @codingStandardsIgnoreLine

		if ( ! empty( $this->options['decode_content'] )
			&& isset( $normalizedKeys['content-encoding'] )// @codingStandardsIgnoreLine
		) {
			$headers['x-encoded-content-encoding']
				= $headers[ $normalizedKeys['content-encoding'] ];// @codingStandardsIgnoreLine
			unset( $headers[ $normalizedKeys['content-encoding'] ] );// @codingStandardsIgnoreLine
			if ( isset( $normalizedKeys['content-length'] ) ) {// @codingStandardsIgnoreLine
				$headers['x-encoded-content-length']
					= $headers[ $normalizedKeys['content-length'] ];// @codingStandardsIgnoreLine

				$bodyLength = (int) $this->sink->getSize();// @codingStandardsIgnoreLine
				if ( $bodyLength ) {// @codingStandardsIgnoreLine
					$headers[ $normalizedKeys['content-length'] ] = $bodyLength;// @codingStandardsIgnoreLine
				} else {
					unset( $headers[ $normalizedKeys['content-length'] ] );// @codingStandardsIgnoreLine
				}
			}
		}

		// Attach a response to the easy handle with the parsed headers.
		$this->response = new Response(
			$startLine[1],// @codingStandardsIgnoreLine
			$headers,
			$this->sink,
			substr( $startLine[0], 5 ),// @codingStandardsIgnoreLine
			isset( $startLine[2] ) ? (string) $startLine[2] : null// @codingStandardsIgnoreLine
		);
	}
	/**
	 * This function is __get.
	 *
	 * @param string $name passes parameter as name.
	 * @throws \BadMethodCallException On error.
	 */
	public function __get( $name ) {
		$msg = $name === 'handle'// @codingStandardsIgnoreLine
			? 'The EasyHandle has been released'
			: 'Invalid property: ' . $name;
		throw new \BadMethodCallException( $msg );
	}
}
