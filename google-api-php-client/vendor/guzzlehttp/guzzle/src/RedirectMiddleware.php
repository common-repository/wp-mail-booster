<?php // @codingStandardsIgnoreLine
/**
 * This file to Request redirect middleware.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */
namespace GuzzleHttp;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Request redirect middleware.
 *
 * Apply this middleware like other middleware using
 * {@see GuzzleHttp\Middleware::redirect()}.
 */
class RedirectMiddleware {

	const HISTORY_HEADER = 'X-Guzzle-Redirect-History';

	const STATUS_HISTORY_HEADER = 'X-Guzzle-Redirect-Status-History';

	public static $defaultSettings = [// @codingStandardsIgnoreLine
		'max'             => 5,
		'protocols'       => [ 'http', 'https' ],
		'strict'          => false,
		'referer'         => false,
		'track_redirects' => false,
	];

	private $nextHandler;// @codingStandardsIgnoreLine

	/**
	 * This function is __construct.
	 *
	 * @param callable $nextHandler Next handler to invoke.
	 */
	public function __construct( callable $nextHandler ) {// @codingStandardsIgnoreLine
		$this->nextHandler = $nextHandler;// @codingStandardsIgnoreLine
	}

	/**
	 * This function is __invoke.
	 *
	 * @param RequestInterface $request passes parameter as request.
	 * @param array            $options passes parameter as options.
	 * @return PromiseInterface
	 * @throws \InvalidArgumentException On error.
	 */
	public function __invoke( RequestInterface $request, array $options ) {
		$fn = $this->nextHandler;// @codingStandardsIgnoreLine

		if ( empty( $options['allow_redirects'] ) ) {
			return $fn( $request, $options );
		}

		if ( true === $options['allow_redirects'] ) {
			$options['allow_redirects'] = self::$defaultSettings;// @codingStandardsIgnoreLine
		} elseif ( ! is_array( $options['allow_redirects'] ) ) {
			throw new \InvalidArgumentException( 'allow_redirects must be true, false, or array' );
		} else {
			// Merge the default settings with the provided settings.
			$options['allow_redirects'] += self::$defaultSettings;// @codingStandardsIgnoreLine
		}

		if ( empty( $options['allow_redirects']['max'] ) ) {
			return $fn( $request, $options );
		}

		return $fn( $request, $options )
			->then(
				function ( ResponseInterface $response ) use ( $request, $options ) {
					return $this->checkRedirect( $request, $options, $response );
				}
			);
	}

	/**
	 * This function is checkRedirect.
	 *
	 * @param RequestInterface                   $request passes parameter as request.
	 * @param array                              $options passes parameter as options.
	 * @param ResponseInterface|PromiseInterface $response passes parameter as response.
	 *
	 * @return ResponseInterface|PromiseInterface
	 */
	public function checkRedirect(// @codingStandardsIgnoreLine
		RequestInterface $request,
		array $options,
		ResponseInterface $response
	) {
		if ( substr( $response->getStatusCode(), 0, 1 ) != '3'// WPCS: Loose comparison ok.
			|| ! $response->hasHeader( 'Location' )
		) {
			return $response;
		}

		$this->guardMax( $request, $options );
		$nextRequest = $this->modifyRequest( $request, $options, $response );// @codingStandardsIgnoreLine

		if ( isset( $options['allow_redirects']['on_redirect'] ) ) {
			call_user_func(
				$options['allow_redirects']['on_redirect'],
				$request,
				$response,
				$nextRequest->getUri()// @codingStandardsIgnoreLine
			);
		}

		$promise = $this( $nextRequest, $options );// @codingStandardsIgnoreLine

		// Add headers to be able to track history of redirects.
		if ( ! empty( $options['allow_redirects']['track_redirects'] ) ) {
			return $this->withTracking(
				$promise,
				(string) $nextRequest->getUri(),// @codingStandardsIgnoreLine
				$response->getStatusCode()
			);
		}

		return $promise;
	}

	private function withTracking( PromiseInterface $promise, $uri, $statusCode ) {// @codingStandardsIgnoreLine
		return $promise->then(
			function ( ResponseInterface $response ) use ( $uri, $statusCode ) {// @codingStandardsIgnoreLine
				// Note that we are pushing to the front of the list as this
				// would be an earlier response than what is currently present
				// in the history header.
				$historyHeader = $response->getHeader( self::HISTORY_HEADER );// @codingStandardsIgnoreLine
				$statusHeader  = $response->getHeader( self::STATUS_HISTORY_HEADER );// @codingStandardsIgnoreLine
				array_unshift( $historyHeader, $uri );// @codingStandardsIgnoreLine
				array_unshift( $statusHeader, $statusCode );// @codingStandardsIgnoreLine
				return $response->withHeader( self::HISTORY_HEADER, $historyHeader )// @codingStandardsIgnoreLine
								->withHeader( self::STATUS_HISTORY_HEADER, $statusHeader );// @codingStandardsIgnoreLine
			}
		);
	}

	private function guardMax( RequestInterface $request, array &$options ) {// @codingStandardsIgnoreLine
		$current                     = isset( $options['__redirect_count'] )
			? $options['__redirect_count']
			: 0;
		$options['__redirect_count'] = $current + 1;
		$max                         = $options['allow_redirects']['max'];

		if ( $options['__redirect_count'] > $max ) {
			throw new TooManyRedirectsException(
				"Will not follow more than {$max} redirects",
				$request
			);
		}
	}

	/**
	 * This function is modifyRequest.
	 *
	 * @param RequestInterface  $request passes parameter as request.
	 * @param array             $options passes parameter as options.
	 * @param ResponseInterface $response passes parameter as response.
	 *
	 * @return RequestInterface
	 */
	public function modifyRequest(// @codingStandardsIgnoreLine
		RequestInterface $request,
		array $options,
		ResponseInterface $response
	) {
		// Request modifications to apply.
		$modify    = [];
		$protocols = $options['allow_redirects']['protocols'];

		// Use a GET request if this is an entity enclosing request and we are
		// not forcing RFC compliance, but rather emulating what all browsers
		// would do.
		$statusCode = $response->getStatusCode();// @codingStandardsIgnoreLine
		if ( $statusCode == 303 ||// @codingStandardsIgnoreLine
			( $statusCode <= 302 && $request->getBody() && ! $options['allow_redirects']['strict'] )// @codingStandardsIgnoreLine
		) {
			$modify['method'] = 'GET';
			$modify['body']   = '';
		}

		$modify['uri'] = $this->redirectUri( $request, $response, $protocols );
		Psr7\rewind_body( $request );

		// Add the Referer header if it is told to do so and only
		// add the header if we are not redirecting from https to http.
		if ( $options['allow_redirects']['referer']
			&& $modify['uri']->getScheme() === $request->getUri()->getScheme()
		) {
			$uri                              = $request->getUri()->withUserInfo( '', '' );
			$modify['set_headers']['Referer'] = (string) $uri;
		} else {
			$modify['remove_headers'][] = 'Referer';
		}

		// Remove Authorization header if host is different.
		if ( $request->getUri()->getHost() !== $modify['uri']->getHost() ) {
			$modify['remove_headers'][] = 'Authorization';
		}

		return Psr7\modify_request( $request, $modify );
	}

	/**
	 * Set the appropriate URL on the request based on the location header
	 *
	 * @param RequestInterface  $request passes parameter as request.
	 * @param ResponseInterface $response passes parameter as response.
	 * @param array             $protocols passes parameter as protocols.
	 * @return UriInterface
	 * @throws BadResponseException .
	 */
	private function redirectUri(// @codingStandardsIgnoreLine
		RequestInterface $request,
		ResponseInterface $response,
		array $protocols
	) {
		$location = Psr7\UriResolver::resolve(
			$request->getUri(),
			new Psr7\Uri( $response->getHeaderLine( 'Location' ) )
		);

		// Ensure that the redirect URI is allowed based on the protocols.
		if ( ! in_array( $location->getScheme(), $protocols ) ) {// @codingStandardsIgnoreLine
			throw new BadResponseException(
				sprintf(
					'Redirect URI, %s, does not use one of the allowed redirect protocols: %s',
					$location,
					implode( ', ', $protocols )
				),
				$request,
				$response
			);
		}

		return $location;
	}
}
