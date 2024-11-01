<?php // @codingStandardsIgnoreLine
/**
 * This file is to Builds out a default http handler for the installed version of guzzle.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/src/google/authhandler
 * @version 2.0.0
 */

use Google\Auth\CredentialsLoader;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use Google\Auth\FetchAuthTokenCache;
use Google\Auth\Middleware\AuthTokenMiddleware;
use Google\Auth\Middleware\ScopedAccessTokenMiddleware;
use Google\Auth\Middleware\SimpleMiddleware;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * This class is uaed to handle google auth authentication
 */
class Google_AuthHandler_Guzzle6AuthHandler {
	/**
	 * Variable cache
	 *
	 * @var $cache .
	 */
	protected $cache;
	/**
	 * Variable to config cache
	 *
	 * @var $cacheConfig .
	 */
	protected $cacheConfig; // @codingStandardsIgnoreLine
	/**
	 * Public constructor
	 *
	 * @param CacheItemPoolInterface $cache .
	 * @param array                  $cacheConfig .
	 */
	public function __construct( CacheItemPoolInterface $cache = null, array $cacheConfig = [] ) { //@codingStandardsIgnoreLine
		$this->cache       = $cache;
		$this->cacheConfig = $cacheConfig; // @codingStandardsIgnoreLine
	}
	/**
	 * To attach credentials
	 *
	 * @param ClientInterface   $http .
	 * @param CredentialsLoader $credentials .
	 * @param callable          $tokenCallback .
	 */
	public function attachCredentials( // @codingStandardsIgnoreLine
		ClientInterface $http,
		CredentialsLoader $credentials,
		callable $tokenCallback = null // @codingStandardsIgnoreLine
	) {
		// use the provided cache .
		if ( $this->cache ) {
			$credentials = new FetchAuthTokenCache(
				$credentials,
				$this->cacheConfig, // @codingStandardsIgnoreLine
				$this->cache
			);
		}
		// if we end up needing to make an HTTP request to retrieve credentials, we
		// can use our existing one, but we need to throw exceptions so the error
		// bubbles up.
		$authHttp        = $this->createAuthHttp( $http ); // @codingStandardsIgnoreLine
		$authHttpHandler = HttpHandlerFactory::build( $authHttp ); // @codingStandardsIgnoreLine
		$middleware      = new AuthTokenMiddleware(
			$credentials,
			$authHttpHandler, // @codingStandardsIgnoreLine
			$tokenCallback // @codingStandardsIgnoreLine
		);

		$config = $http->getConfig();
		$config['handler']->remove( 'google_auth' );
		$config['handler']->push( $middleware, 'google_auth' );
		$config['auth'] = 'google_auth';
		$http           = new Client( $config );

		return $http;
	}
	/**
	 * This function  is used to attach token .
	 *
	 * @param ClientInterface $http .
	 * @param array           $token .
	 * @param array           $scopes .
	 */
	public function attachToken( ClientInterface $http, array $token, array $scopes ) { // @codingStandardsIgnoreLine
		$tokenFunc = function ( $scopes ) use ( $token ) { // @codingStandardsIgnoreLine
			return $token['access_token'];
		};

		$middleware = new ScopedAccessTokenMiddleware(
			$tokenFunc, // @codingStandardsIgnoreLine
			$scopes,
			$this->cacheConfig, // @codingStandardsIgnoreLine
			$this->cache
		);

		$config = $http->getConfig();
		$config['handler']->remove( 'google_auth' );
		$config['handler']->push( $middleware, 'google_auth' );
		$config['auth'] = 'scoped';
		$http           = new Client( $config );

		return $http;
	}
	/**
	 * This function  is used to attach key .
	 *
	 * @param ClientInterface $http .
	 * @param array           $key .
	 */
	public function attachKey( ClientInterface $http, $key ) { // @codingStandardsIgnoreLine
		$middleware = new SimpleMiddleware( [ 'key' => $key ] );

		$config = $http->getConfig();
		$config['handler']->remove( 'google_auth' );
		$config['handler']->push( $middleware, 'google_auth' );
		$config['auth'] = 'simple';
		$http           = new Client( $config );

		return $http;
	}
	/**
	 * This function  is used to create auth http .
	 *
	 * @param ClientInterface $http .
	 */
	private function createAuthHttp( ClientInterface $http ) { // @codingStandardsIgnoreLine
		return new Client(
			[
				'base_uri'   => $http->getConfig( 'base_uri' ),
				'exceptions' => true,
				'verify'     => $http->getConfig( 'verify' ),
				'proxy'      => $http->getConfig( 'proxy' ),
			]
		);
	}
}
