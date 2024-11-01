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
use Google\Auth\Subscriber\AuthTokenSubscriber;
use Google\Auth\Subscriber\ScopedAccessTokenSubscriber;
use Google\Auth\Subscriber\SimpleSubscriber;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * This class is used for handling google auth authentication
 */
class Google_AuthHandler_Guzzle5AuthHandler {
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
	public function __construct( CacheItemPoolInterface $cache = null, array $cacheConfig = [] ) { // @codingStandardsIgnoreLine
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
		$subscriber      = new AuthTokenSubscriber(
			$credentials,
			$authHttpHandler, // @codingStandardsIgnoreLine
			$tokenCallback // @codingStandardsIgnoreLine
		);

		$http->setDefaultOption( 'auth', 'google_auth' );
		$http->getEmitter()->attach( $subscriber );

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

		$subscriber = new ScopedAccessTokenSubscriber(
			$tokenFunc, // @codingStandardsIgnoreLine
			$scopes,
			$this->cacheConfig, // @codingStandardsIgnoreLine
			$this->cache
		);

		$http->setDefaultOption( 'auth', 'scoped' );
		$http->getEmitter()->attach( $subscriber );

		return $http;
	}
	/**
	 * This function  is used to attach key .
	 *
	 * @param ClientInterface $http .
	 * @param array           $key .
	 */
	public function attachKey( ClientInterface $http, $key ) { // @codingStandardsIgnoreLine
		$subscriber = new SimpleSubscriber( [ 'key' => $key ] );

		$http->setDefaultOption( 'auth', 'simple' );
		$http->getEmitter()->attach( $subscriber );

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
				'base_url' => $http->getBaseUrl(),
				'defaults' => [
					'exceptions' => true,
					'verify'     => $http->getDefaultOption( 'verify' ),
					'proxy'      => $http->getDefaultOption( 'proxy' ),
				],
			]
		);
	}
}
