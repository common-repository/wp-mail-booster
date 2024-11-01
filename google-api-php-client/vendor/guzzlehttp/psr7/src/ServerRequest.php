<?php // @codingStandardsIgnoreLine
/**
 * This file HTTP request from server side
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/composer
 * @version 2.0.0
 */

namespace GuzzleHttp\Psr7;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Server-side HTTP request
 *
 * Extends the Request definition to add methods for accessing incoming data,
 * specifically server parameters, cookies, matched path parameters, query
 * string arguments, body parameters, and upload file information.
 *
 * "Attributes" are discovered via decomposing the request (and usually
 * specifically the URI path), and typically will be injected by the application.
 *
 * Requests are considered immutable; all methods that might change state are
 * implemented such that they retain the internal state of the current
 * message and return a new instance that contains the changed state.
 */
class ServerRequest extends Request implements ServerRequestInterface {

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $attributes  .
	 */
	private $attributes = [];

	private $cookieParams = [];// @codingStandardsIgnoreLine

	private $parsedBody;// @codingStandardsIgnoreLine

	private $queryParams = [];// @codingStandardsIgnoreLine

	private $serverParams;// @codingStandardsIgnoreLine

	private $uploadedFiles = [];// @codingStandardsIgnoreLine

	/**
	 * This function is __construct.
	 *
	 * @param string                               $method       HTTP method.
	 * @param string|UriInterface                  $uri          URI.
	 * @param array                                $headers      Request headers.
	 * @param string|null|resource|StreamInterface $body         Request body.
	 * @param string                               $version      Protocol version.
	 * @param array                                $serverParams Typically the $_SERVER superglobal.
	 */
	public function __construct(
		$method,
		$uri,
		array $headers = [],
		$body = null,
		$version = '1.1',
		array $serverParams = []// @codingStandardsIgnoreLine
	) {
		$this->serverParams = $serverParams;// @codingStandardsIgnoreLine

		parent::__construct( $method, $uri, $headers, $body, $version );
	}

	/**
	 * Return an UploadedFile instance array.
	 *
	 * @param array $files A array which respect $_FILES structure.
	 * @throws InvalidArgumentException For unrecognized values.
	 * @return array
	 */
	public static function normalizeFiles( array $files ) {
		$normalized = [];

		foreach ( $files as $key => $value ) {
			if ( $value instanceof UploadedFileInterface ) {
				$normalized[ $key ] = $value;
			} elseif ( is_array( $value ) && isset( $value['tmp_name'] ) ) {
				$normalized[ $key ] = self::createUploadedFileFromSpec( $value );
			} elseif ( is_array( $value ) ) {
				$normalized[ $key ] = self::normalizeFiles( $value );
				continue;
			} else {
				throw new InvalidArgumentException( 'Invalid value in files specification' );
			}
		}

		return $normalized;
	}

	/**
	 * Create and return an UploadedFile instance from a $_FILES specification.
	 *
	 * If the specification represents an array of values, this method will
	 * delegate to normalizeNestedFileSpec() and return that return value.
	 *
	 * @param array $value $_FILES struct.
	 * @return array|UploadedFileInterface
	 */
	private static function createUploadedFileFromSpec( array $value ) {
		if ( is_array( $value['tmp_name'] ) ) {
			return self::normalizeNestedFileSpec( $value );
		}

		return new UploadedFile(
			$value['tmp_name'],
			(int) $value['size'],
			(int) $value['error'],
			$value['name'],
			$value['type']
		);
	}

	/**
	 * Normalize an array of file specifications.
	 *
	 * Loops through all nested files and returns a normalized array of
	 * UploadedFileInterface instances.
	 *
	 * @param array $files passes parameter as files.
	 * @return UploadedFileInterface[]
	 */
	private static function normalizeNestedFileSpec( array $files = [] ) {
		$normalizedFiles = [];// @codingStandardsIgnoreLine

		foreach ( array_keys( $files['tmp_name'] ) as $key ) {
			$spec                    = [
				'tmp_name' => $files['tmp_name'][ $key ],
				'size'     => $files['size'][ $key ],
				'error'    => $files['error'][ $key ],
				'name'     => $files['name'][ $key ],
				'type'     => $files['type'][ $key ],
			];
			$normalizedFiles[ $key ] = self::createUploadedFileFromSpec( $spec );// @codingStandardsIgnoreLine
		}

		return $normalizedFiles;// @codingStandardsIgnoreLine
	}

	/**
	 * Return a ServerRequest populated with superglobals:
	 * $_GET
	 * $_POST
	 * $_COOKIE
	 * $_FILES
	 * $_SERVER
	 *
	 * @return ServerRequestInterface
	 */
	public static function fromGlobals() {
		$method   = isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : 'GET';// @codingStandardsIgnoreLine
		$headers  = function_exists( 'getallheaders' ) ? getallheaders() : [];
		$uri      = self::getUriFromGlobals();
		$body     = new LazyOpenStream( 'php://input', 'r+' );
		$protocol = isset( $_SERVER['SERVER_PROTOCOL'] ) ? str_replace( 'HTTP/', '', $_SERVER['SERVER_PROTOCOL'] ) : '1.1';// @codingStandardsIgnoreLine

		$serverRequest = new ServerRequest( $method, $uri, $headers, $body, $protocol, $_SERVER );// @codingStandardsIgnoreLine

		return $serverRequest// @codingStandardsIgnoreLine
			->withCookieParams( $_COOKIE )// @codingStandardsIgnoreLine
			->withQueryParams( $_GET )// WPCS: input var ok, CSRF ok.
			->withParsedBody( $_POST )// WPCS: input var ok, CSRF ok.
			->withUploadedFiles( self::normalizeFiles( $_FILES ) );// WPCS: input var ok.
	}

	/**
	 * Get a Uri populated with values from $_SERVER.
	 *
	 * @return UriInterface
	 */
	public static function getUriFromGlobals() {
		$uri = new Uri( '' );

		$uri = $uri->withScheme( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ? 'https' : 'http' );// WPCS: input var ok.

		$hasPort = false;// @codingStandardsIgnoreLine
		if ( isset( $_SERVER['HTTP_HOST'] ) ) {// WPCS: input var ok.
			$hostHeaderParts = explode( ':', $_SERVER['HTTP_HOST'] );// @codingStandardsIgnoreLine
			$uri             = $uri->withHost( $hostHeaderParts[0] );// @codingStandardsIgnoreLine
			if ( isset( $hostHeaderParts[1] ) ) {// @codingStandardsIgnoreLine
				$hasPort = true;// @codingStandardsIgnoreLine
				$uri     = $uri->withPort( $hostHeaderParts[1] );// @codingStandardsIgnoreLine
			}
		} elseif ( isset( $_SERVER['SERVER_NAME'] ) ) {// WPCS: input var ok.
			$uri = $uri->withHost( $_SERVER['SERVER_NAME'] );// @codingStandardsIgnoreLine
		} elseif ( isset( $_SERVER['SERVER_ADDR'] ) ) {// WPCS: input var ok.
			$uri = $uri->withHost( $_SERVER['SERVER_ADDR'] );// @codingStandardsIgnoreLine
		}

		if ( ! $hasPort && isset( $_SERVER['SERVER_PORT'] ) ) {// @codingStandardsIgnoreLine
			$uri = $uri->withPort( $_SERVER['SERVER_PORT'] );// @codingStandardsIgnoreLine
		}

		$hasQuery = false;// @codingStandardsIgnoreLine
		if ( isset( $_SERVER['REQUEST_URI'] ) ) {// WPCS: input var ok.
			$requestUriParts = explode( '?', $_SERVER['REQUEST_URI'] );// @codingStandardsIgnoreLine
			$uri             = $uri->withPath( $requestUriParts[0] );// @codingStandardsIgnoreLine
			if ( isset( $requestUriParts[1] ) ) {// @codingStandardsIgnoreLine
				$hasQuery = true;// @codingStandardsIgnoreLine
				$uri      = $uri->withQuery( $requestUriParts[1] );// @codingStandardsIgnoreLine
			}
		}

		if ( ! $hasQuery && isset( $_SERVER['QUERY_STRING'] ) ) {// @codingStandardsIgnoreLine
			$uri = $uri->withQuery( $_SERVER['QUERY_STRING'] );// @codingStandardsIgnoreLine
		}

		return $uri;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getServerParams() {
		return $this->serverParams;// @codingStandardsIgnoreLine
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUploadedFiles() {
		return $this->uploadedFiles;// @codingStandardsIgnoreLine
	}

	/**
	 * This function is withUploadedFiles.
	 *
	 * @param array $uploadedFiles passes parameter as uploadedFiles.
	 */
	public function withUploadedFiles( array $uploadedFiles ) {// @codingStandardsIgnoreLine
		$new                = clone $this;
		$new->uploadedFiles = $uploadedFiles;// @codingStandardsIgnoreLine

		return $new;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCookieParams() {
		return $this->cookieParams;// @codingStandardsIgnoreLine
	}

	/**
	 * This function is withCookieParams.
	 *
	 * @param array $cookies passes parameter as cookies.
	 */
	public function withCookieParams( array $cookies ) {
		$new               = clone $this;
		$new->cookieParams = $cookies;// @codingStandardsIgnoreLine

		return $new;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getQueryParams() {
		return $this->queryParams;// @codingStandardsIgnoreLine
	}

	/**
	 * This function is withQueryParams.
	 *
	 * @param array $query passes parameter as query.
	 */
	public function withQueryParams( array $query ) {
		$new              = clone $this;
		$new->queryParams = $query;// @codingStandardsIgnoreLine

		return $new;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getParsedBody() {
		return $this->parsedBody;// @codingStandardsIgnoreLine
	}

	/**
	 * This function is withParsedBody.
	 *
	 * @param string $data passes parameter as data.
	 */
	public function withParsedBody( $data ) {
		$new             = clone $this;
		$new->parsedBody = $data;// @codingStandardsIgnoreLine

		return $new;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAttributes() {
		return $this->attributes;
	}

	/**
	 * This function is getAttribute.
	 *
	 * @param string $attribute passes parameter as attribute.
	 * @param null   $default passes parameter as default.
	 */
	public function getAttribute( $attribute, $default = null ) {
		if ( false === array_key_exists( $attribute, $this->attributes ) ) {
			return $default;
		}

		return $this->attributes[ $attribute ];
	}

	/**
	 * This function is withAttribute.
	 *
	 * @param string $attribute passes parameter as attribute.
	 * @param string $value passes parameter as value.
	 */
	public function withAttribute( $attribute, $value ) {
		$new                           = clone $this;
		$new->attributes[ $attribute ] = $value;

		return $new;
	}

	/**
	 * This function is withoutAttribute.
	 *
	 * @param string $attribute passes parameter as attribute.
	 */
	public function withoutAttribute( $attribute ) {
		if ( false === array_key_exists( $attribute, $this->attributes ) ) {
			return $this;
		}

		$new = clone $this;
		unset( $new->attributes[ $attribute ] );

		return $new;
	}
}
