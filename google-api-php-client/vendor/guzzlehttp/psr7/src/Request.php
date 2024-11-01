<?php // @codingStandardsIgnoreLine
/**
 * This file implement PSR-7 request
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/composer
 * @version 2.0.0
 */

namespace GuzzleHttp\Psr7;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * PSR-7 request implementation.
 */
class Request implements RequestInterface {

	use MessageTrait;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $method  .
	 */
	private $method;

	private $requestTarget;// @codingStandardsIgnoreLine
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $uri  .
	 */
	private $uri;

	/**
	 * This function is __construct.
	 *
	 * @param string                               $method  HTTP method.
	 * @param string|UriInterface                  $uri     URI.
	 * @param array                                $headers Request headers.
	 * @param string|null|resource|StreamInterface $body    Request body.
	 * @param string                               $version Protocol version.
	 */
	public function __construct(
		$method,
		$uri,
		array $headers = [],
		$body = null,
		$version = '1.1'
	) {
		if ( ! ( $uri instanceof UriInterface ) ) {
			$uri = new Uri( $uri );
		}

		$this->method = strtoupper( $method );
		$this->uri    = $uri;
		$this->setHeaders( $headers );
		$this->protocol = $version;

		if ( ! $this->hasHeader( 'Host' ) ) {
			$this->updateHostFromUri();
		}

		if ( $body !== '' && $body !== null ) {// @codingStandardsIgnoreLine
			$this->stream = stream_for( $body );
		}
	}
	/**
	 * This function is getRequestTarget.
	 */
	public function getRequestTarget() {
		if ( $this->requestTarget !== null ) {// @codingStandardsIgnoreLine
			return $this->requestTarget;// @codingStandardsIgnoreLine
		}

		$target = $this->uri->getPath();
		if ( '' == $target ) {// WPCS: Loose comparison ok.
			$target = '/';
		}
		if ( $this->uri->getQuery() != '' ) {// WPCS: Loose comparison ok.
			$target .= '?' . $this->uri->getQuery();
		}

		return $target;
	}

	public function withRequestTarget( $requestTarget ) {// @codingStandardsIgnoreLine
		if ( preg_match( '#\s#', $requestTarget ) ) {// @codingStandardsIgnoreLine
			throw new InvalidArgumentException(
				'Invalid request target provided; cannot contain whitespace'
			);
		}

		$new                = clone $this;
		$new->requestTarget = $requestTarget;// @codingStandardsIgnoreLine
		return $new;
	}
	/**
	 * This Function is getMethod.
	 */
	public function getMethod() {
		return $this->method;
	}
	/**
	 * This Function is withMethod.
	 *
	 * @param string $method passes parameter as method.
	 */
	public function withMethod( $method ) {
		$new         = clone $this;
		$new->method = strtoupper( $method );
		return $new;
	}
	/**
	 * This Function is getUri.
	 */
	public function getUri() {
		return $this->uri;
	}
	/**
	 * This Function is withUri.
	 *
	 * @param UriInterface $uri passes parameter as uri.
	 * @param string       $preserveHost passes parameter as preservehost.
	 */
	public function withUri( UriInterface $uri, $preserveHost = false ) {// @codingStandardsIgnoreLine
		if ( $uri === $this->uri ) {
			return $this;
		}

		$new      = clone $this;
		$new->uri = $uri;

		if ( ! $preserveHost ) {// @codingStandardsIgnoreLine
			$new->updateHostFromUri();
		}

		return $new;
	}
	/**
	 * This function is updateHostFromUri.
	 */
	private function updateHostFromUri() {
		$host = $this->uri->getHost();

		if ( '' == $host ) {// WPCS: Loose comparison ok.
			return;
		}

		if ( ( $port = $this->uri->getPort() ) !== null ) {// @codingStandardsIgnoreLine
			$host .= ':' . $port;
		}

		if ( isset( $this->headerNames['host'] ) ) {// @codingStandardsIgnoreLine
			$header = $this->headerNames['host'];// @codingStandardsIgnoreLine
		} else {
			$header                    = 'Host';
			$this->headerNames['host'] = 'Host';// @codingStandardsIgnoreLine
		}
		// Ensure Host is the first header.
		// See: http://tools.ietf.org/html/rfc7230#section-5.4.
		$this->headers = [ $header => [ $host ] ] + $this->headers;
	}
}
