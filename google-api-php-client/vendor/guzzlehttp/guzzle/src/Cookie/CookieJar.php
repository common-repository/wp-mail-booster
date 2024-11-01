<?php // @codingStandardsIgnoreLine.
/**
 * This Template is CookieJar.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client\vendor\guzzlehttp\guzzle\src\Cookie
 * @version 2.0.0
 */

namespace GuzzleHttp\Cookie;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Cookie jar that stores cookies as an array
 */
class CookieJar implements CookieJarInterface {

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $cookies  .
	 */
	private $cookies = [];

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string   $strictMode  .
	 */
	private $strictMode; // @codingStandardsIgnoreLine.

	/**
	 * The function is __construct .
	 *
	 * @param bool  $strictMode   Set to true to throw exceptions when invalid
	 *                            cookies are added to the cookie jar.
	 * @param array $cookieArray Array of SetCookie objects or a hash of
	 *                           arrays that can be used with the SetCookie
	 *                           constructor.
	 */
	public function __construct( $strictMode = false, $cookieArray = [] ) { // @codingStandardsIgnoreLine.
		$this->strictMode = $strictMode; // @codingStandardsIgnoreLine.

		foreach ( $cookieArray as $cookie ) { // @codingStandardsIgnoreLine.
			if ( ! ( $cookie instanceof SetCookie ) ) { // @codingStandardsIgnoreLine.
				$cookie = new SetCookie( $cookie ); // @codingStandardsIgnoreLine.
			}
			$this->setCookie( $cookie );
		}
	}

	/**
	 * Create a new Cookie jar from an associative array and domain.
	 *
	 * @param array  $cookies Cookies to create the jar from .
	 * @param string $domain  Domain to set the cookies to .
	 *
	 * @return self
	 */
	public static function fromArray( array $cookies, $domain ) {
		$cookieJar = new self(); // @codingStandardsIgnoreLine.
		foreach ( $cookies as $name => $value ) {
			$cookieJar->setCookie( // @codingStandardsIgnoreLine.
				new SetCookie( // @codingStandardsIgnoreLine.
					[
						'Domain'  => $domain,
						'Name'    => $name,
						'Value'   => $value,
						'Discard' => true,
					]
				)
			);
		}

		return $cookieJar;// @codingStandardsIgnoreLine.
	}

	/**
	 * The function is getCookieValue.
	 *
	 * @param string $value .
	 *
	 * @deprecated
	 */
	public static function getCookieValue( $value ) {
		return $value;
	}

	/**
	 * Evaluate if this cookie should be persisted to storage
	 * that survives between requests.
	 *
	 * @param SetCookie $cookie Being evaluated.
	 * @param bool      $allowSessionCookies If we should persist session cookies.
	 * @return bool
	 */
	public static function shouldPersist( SetCookie $cookie, $allowSessionCookies = false ) { // @codingStandardsIgnoreLine.
		if ( $cookie->getExpires() || $allowSessionCookies ) { // @codingStandardsIgnoreLine.
			if ( ! $cookie->getDiscard() ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Finds and returns the cookie based on the name
	 *
	 * @param string $name cookie name to search for .
	 * @return SetCookie|null cookie that was found or null if not found .
	 */
	public function getCookieByName( $name ) {
		// don't allow a null name .
		if ( null === $name ) {
			return null;
		}
		foreach ( $this->cookies as $cookie ) {
			if ( $cookie->getName() !== null && strcasecmp( $cookie->getName(), $name ) === 0 ) {
				return $cookie;
			}
		}
	}
	/**
	 * The function is toArray.
	 */
	public function toArray() {
		return array_map(
			function ( SetCookie $cookie ) { // @codingStandardsIgnoreLine.
				return $cookie->toArray();
			}, $this->getIterator()->getArrayCopy()
		);
	}
	/**
	 * The fucntion is clear.
	 *
	 * @param null $domain .
	 * @param null $path .
	 * @param null $name .
	 */
	public function clear( $domain = null, $path = null, $name = null ) {
		if ( ! $domain ) {
			$this->cookies = [];
			return;
		} elseif ( ! $path ) {
			$this->cookies = array_filter(
				$this->cookies,
				function ( SetCookie $cookie ) use ( $path, $domain ) { // @codingStandardsIgnoreLine.
					return ! $cookie->matchesDomain( $domain );
				}
			);
		} elseif ( ! $name ) {
			$this->cookies = array_filter(
				$this->cookies,
				function ( SetCookie $cookie ) use ( $path, $domain ) { // @codingStandardsIgnoreLine.
					return ! ( $cookie->matchesPath( $path ) &&
						$cookie->matchesDomain( $domain ) );
				}
			);
		} else {
			$this->cookies = array_filter(
				$this->cookies,
				function ( SetCookie $cookie ) use ( $path, $domain, $name ) { // @codingStandardsIgnoreLine.
					return ! ( $cookie->getName() == $name && $cookie->matchesPath( $path ) && $cookie->matchesDomain( $domain ) ); // WPCS: loose comparison ok.
				}
			);
		}
	}
	/**
	 * The function is clearSessionCookies.
	 */
	public function clearSessionCookies() {
		$this->cookies = array_filter(
			$this->cookies,
			function ( SetCookie $cookie ) { // @codingStandardsIgnoreLine.
				return ! $cookie->getDiscard() && $cookie->getExpires();
			}
		);
	}
	/**
	 * The function is setCookie.
	 *
	 * @param setCookie $cookie .
	 *
	 * @throws  \RuntimeException Exception .
	 */
	public function setCookie( SetCookie $cookie ) { // @codingStandardsIgnoreLine.
		// If the name string is empty (but not 0), ignore the set-cookie.
		// string entirely.
		$name = $cookie->getName();
		if ( ! $name && '0' !== $name ) {
			return false;
		}

		// Only allow cookies with set and valid domain, name, value.
		$result = $cookie->validate();
		if ( true !== $result ) {
			if ( $this->strictMode ) { //@codingStandardsIgnoreLine.
				throw new \RuntimeException( 'Invalid cookie: ' . $result );
			} else {
				$this->removeCookieIfEmpty( $cookie );
				return false;
			}
		}

		// Resolve conflicts with previously set cookies.
		foreach ( $this->cookies as $i => $c ) {

			// Two cookies are identical, when their path, and domain are
			// identical.
			if ( $c->getPath() != $cookie->getPath() || $c->getDomain() != $cookie->getDomain() || $c->getName() != $cookie->getName() ) { // WPCS: loose comparison ok.
				continue;
			}

			// The previously set cookie is a discard cookie and this one is
			// not so allow the new cookie to be set.
			if ( ! $cookie->getDiscard() && $c->getDiscard() ) {
				unset( $this->cookies[ $i ] );
				continue;
			}

			// If the new cookie's expiration is further into the future, then
			// replace the old cookie.
			if ( $cookie->getExpires() > $c->getExpires() ) {
				unset( $this->cookies[ $i ] );
				continue;
			}

			// If the value has changed, we better change it.
			if ( $cookie->getValue() !== $c->getValue() ) {
				unset( $this->cookies[ $i ] );
				continue;
			}

			// The cookie exists, so no need to continue.
			return false;
		}

		$this->cookies[] = $cookie;

		return true;
	}
	/**
	 * The function is count.
	 */
	public function count() {
		return count( $this->cookies );
	}
	/**
	 * The function is getIterator.
	 */
	public function getIterator() {
		return new \ArrayIterator( array_values( $this->cookies ) );
	}
	/**
	 * The function is extractCookies.
	 *
	 * @param RequestInterface  $request .
	 * @param ResponseInterface $response .
	 */
	public function extractCookies(
		RequestInterface $request,
		ResponseInterface $response
	) {
		if ( $cookieHeader = $response->getHeader( 'Set-Cookie' ) ) { // @codingStandardsIgnoreLine.
			foreach ( $cookieHeader as $cookie ) { // @codingStandardsIgnoreLine.
				$sc = SetCookie::fromString( $cookie ); // @codingStandardsIgnoreLine.
				if ( ! $sc->getDomain() ) {
					$sc->setDomain( $request->getUri()->getHost() );
				}
				if ( 0 !== strpos( $sc->getPath(), '/' ) ) {
					$sc->setPath( $this->getCookiePathFromRequest( $request ) );
				}
				$this->setCookie( $sc );
			}
		}
	}

	/**
	 * Computes cookie path following RFC 6265 section 5.1.4
	 *
	 * @link https://tools.ietf.org/html/rfc6265#section-5.1.4
	 *
	 * @param RequestInterface $request .
	 * @return string
	 */
	private function getCookiePathFromRequest( RequestInterface $request ) {
		$uriPath = $request->getUri()->getPath(); // @codingStandardsIgnoreLine.
		if ( '' === $uriPath ) { // @codingStandardsIgnoreLine.
			return '/';
		}
		if ( 0 !== strpos( $uriPath, '/' ) ) { // @codingStandardsIgnoreLine.
			return '/';
		}
		if ( '/' === $uriPath ) { // @codingStandardsIgnoreLine.
			return '/';
		}
		if ( 0 === $lastSlashPos = strrpos( $uriPath, '/' ) ) { // @codingStandardsIgnoreLine.
			return '/';
		}

		return substr( $uriPath, 0, $lastSlashPos ); // @codingStandardsIgnoreLine.
	}
	/**
	 * The function is withCookieHeader .
	 *
	 * @param RequestInterface $request .
	 */
	public function withCookieHeader( RequestInterface $request ) {
		$values = [];
		$uri    = $request->getUri();
		$scheme = $uri->getScheme();
		$host   = $uri->getHost();
		$path   = $uri->getPath() ?: '/';

		foreach ( $this->cookies as $cookie ) {
			if ( $cookie->matchesPath( $path ) &&
				$cookie->matchesDomain( $host ) &&
				! $cookie->isExpired() &&
				( ! $cookie->getSecure() || 'https' === $scheme )
			) {
				$values[] = $cookie->getName() . '='
					. $cookie->getValue();
			}
		}

		return $values
			? $request->withHeader( 'Cookie', implode( '; ', $values ) )
			: $request;
	}

	/**
	 * If a cookie already exists and the server asks to set it again with a
	 * null value, the cookie must be deleted.
	 *
	 * @param SetCookie $cookie .
	 */
	private function removeCookieIfEmpty( SetCookie $cookie ) { // @codingStandardsIgnoreLine.
		$cookieValue = $cookie->getValue(); // @codingStandardsIgnoreLine.
		if ( null === $cookieValue || '' === $cookieValue ) { // @codingStandardsIgnoreLine.
			$this->clear(
				$cookie->getDomain(),
				$cookie->getPath(),
				$cookie->getName()
			);
		}
	}
}
