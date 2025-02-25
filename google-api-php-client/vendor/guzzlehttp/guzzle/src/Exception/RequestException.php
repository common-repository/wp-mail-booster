<?php // @codingStandardsIgnoreLine
/**
 * This file to http request exception.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace GuzzleHttp\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\UriInterface;

/**
 * HTTP Request exception
 */
class RequestException extends TransferException {
	/**
	 * Varaiable Request.
	 *
	 * @access   private
	 * @var      string    $request.
	 */
	private $request;
	/**
	 * Variable response.
	 *
	 * @access   private
	 * @var      string    $response.
	 */
	private $response;
	/**
	 * Variable handle context
	 *
	 * @access   private
	 * @var      string    $handlerContext.
	 */
	private $handlerContext;// @codingStandardsIgnoreLine

	/**
	 * Public construstor
	 *
	 * @param string            $message .
	 * @param RequestInterface  $request .
	 * @param ResponseInterface $response .
	 * @param \Exception        $previous .
	 * @param array             $handlerContext .
	 */
	public function __construct( // @codingStandardsIgnoreLine
		$message,
		RequestInterface $request,
		ResponseInterface $response = null,
		\Exception $previous = null,
		array $handlerContext = [] // @codingStandardsIgnoreLine
	) {
		// Set the code of the exception if the response is set and not future.
		$code = $response && ! ( $response instanceof PromiseInterface )
			? $response->getStatusCode()
			: 0;
		parent::__construct( $message, $code, $previous );
		$this->request        = $request;
		$this->response       = $response;
		$this->handlerContext = $handlerContext; // @codingStandardsIgnoreLine
	}

	/**
	 * Wrap non-RequestExceptions with a RequestException
	 *
	 * @param RequestInterface $request .
	 * @param \Exception       $e .
	 *
	 * @return RequestException
	 */
	public static function wrapException( RequestInterface $request, \Exception $e ) {
		return $e instanceof RequestException
			? $e
			: new RequestException( $e->getMessage(), $request, null, $e );
	}

	/**
	 * Factory method to create a new exception with a normalized error message
	 *
	 * @param RequestInterface  $request  Request .
	 * @param ResponseInterface $response Response received .
	 * @param \Exception        $previous Previous exception .
	 * @param array             $ctx      Optional handler context.
	 *
	 * @return self
	 */
	public static function create(
		RequestInterface $request,
		ResponseInterface $response = null,
		\Exception $previous = null,
		array $ctx = []
	) {
		if ( ! $response ) {
			return new self(
				'Error completing request',
				$request,
				null,
				$previous,
				$ctx
			);
		}

		$level = (int) floor( $response->getStatusCode() / 100 );
		if ( 4 === $level ) {
			$label     = 'Client error';
			$className = ClientException::class; // @codingStandardsIgnoreLine
		} elseif ( 5 === $level ) {
			$label     = 'Server error';
			$className = ServerException::class; // @codingStandardsIgnoreLine
		} else {
			$label     = 'Unsuccessful request';
			$className = __CLASS__; // @codingStandardsIgnoreLine
		}

		$uri = $request->getUri();
		$uri = static::obfuscateUri( $uri );

		$message = sprintf(
			'%s: `%s %s` resulted in a `%s %s` response',
			$label,
			$request->getMethod(),
			$uri,
			$response->getStatusCode(),
			$response->getReasonPhrase()
		);

		$summary = static::getResponseBodySummary( $response );

		if ( null !== $summary ) {
			$message .= ":\n{$summary}\n";
		}

		return new $className( $message, $request, $response, $previous, $ctx ); // @codingStandardsIgnoreLine
	}

	/**
	 * Get a short summary of the response
	 *
	 * Will return `null` if the response is not printable.
	 *
	 * @param ResponseInterface $response .
	 *
	 * @return string|null
	 */
	public static function getResponseBodySummary( ResponseInterface $response ) {
		$body = $response->getBody();

		if ( ! $body->isSeekable() ) {
			return null;
		}

		$size = $body->getSize();

		if ( 0 === $size ) {
			return null;
		}

		$summary = $body->read( 120 );
		$body->rewind();

		if ( $size > 120 ) {
			$summary .= ' (truncated...)';
		}

		// Matches any printable character, including unicode characters:
		// letters, marks, numbers, punctuation, spacing, and separators.
		if ( preg_match( '/[^\pL\pM\pN\pP\pS\pZ\n\r\t]/', $summary ) ) {
			return null;
		}

		return $summary;
	}

	/**
	 * Obfuscates URI if there is an username and a password present
	 *
	 * @param UriInterface $uri .
	 *
	 * @return UriInterface
	 */
	private static function obfuscateUri( $uri ) {
		$userInfo = $uri->getUserInfo(); // @codingStandardsIgnoreLine

		if ( false !== ( $pos = strpos( $userInfo, ':' ) ) ) { // @codingStandardsIgnoreLine
			return $uri->withUserInfo( substr( $userInfo, 0, $pos ), '***' ); // @codingStandardsIgnoreLine
		}

		return $uri;
	}

	/**
	 * Get the request that caused the exception
	 *
	 * @return RequestInterface
	 */
	public function getRequest() {
		return $this->request;
	}

	/**
	 * Get the associated response
	 *
	 * @return ResponseInterface|null
	 */
	public function getResponse() {
		return $this->response;
	}

	/**
	 * Check if a response was received
	 *
	 * @return bool
	 */
	public function hasResponse() {
		return null !== $this->response;
	}

	/**
	 * Get contextual information about the error from the underlying handler.
	 *
	 * The contents of this array will vary depending on which handler you are
	 * using. It may also be just an empty array. Relying on this data will
	 * couple you to a specific handler, but can give more debug information
	 * when needed.
	 *
	 * @return array
	 */
	public function getHandlerContext() {
		return $this->handlerContext; // @codingStandardsIgnoreLine
	}
}
