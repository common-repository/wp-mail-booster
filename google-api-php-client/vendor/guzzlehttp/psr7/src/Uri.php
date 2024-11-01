<?php // @codingStandardsIgnoreLine
/**
 * This file PSR-7 URI implementation..
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/composer
 * @version 2.0.0
 */

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\UriInterface;

/**
 * PSR-7 URI implementation.
 */
class Uri implements UriInterface {

	/**
	 * Absolute http and https URIs require a host per RFC 7230 Section 2.7
	 * but in generic URIs the host can be empty. So for http(s) URIs
	 * we apply this default host when no host is given yet to form a
	 * valid URI.
	 */
	const HTTP_DEFAULT_HOST = 'localhost';

	private static $defaultPorts = [// @codingStandardsIgnoreLine
		'http'   => 80,
		'https'  => 443,
		'ftp'    => 21,
		'gopher' => 70,
		'nntp'   => 119,
		'news'   => 119,
		'telnet' => 23,
		'tn3270' => 23,
		'imap'   => 143,
		'pop'    => 110,
		'ldap'   => 389,
	];

	private static $charUnreserved = 'a-zA-Z0-9_\-\.~';// @codingStandardsIgnoreLine
	private static $charSubDelims  = '!\$&\'\(\)\*\+,;=';// @codingStandardsIgnoreLine
	private static $replaceQuery   = [// @codingStandardsIgnoreLine
		'=' => '%3D',
		'&' => '%26',
	];
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $scheme.
	 */
	private $scheme = '';

	private $userInfo = '';// @codingStandardsIgnoreLine
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $host.
	 */
	private $host = '';
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $port.
	 */
	private $port;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $path.
	 */
	private $path = '';

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $query.
	 */
	private $query = '';

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $fragment.
	 */
	private $fragment = '';

	/**
	 * This function is __construct.
	 *
	 * @param string $uri URI to parse.
	 * @throws \InvalidArgumentException On error.
	 */
	public function __construct( $uri = '' ) {
		// weak type check to also accept null until we can add scalar type hints.
		if ( '' != $uri ) {// WPCS: Loose comparison ok.
			$parts = parse_url( $uri );// @codingStandardsIgnoreLine
			if ( false === $parts ) {
				throw new \InvalidArgumentException( "Unable to parse URI: $uri" );
			}
			$this->applyParts( $parts );
		}
	}
	/**
	 * This function is __toString.
	 */
	public function __toString() {
		return self::composeComponents(
			$this->scheme,
			$this->getAuthority(),
			$this->path,
			$this->query,
			$this->fragment
		);
	}

	/**
	 * Composes a URI reference string from its various components.
	 *
	 * Usually this method does not need to be called manually but instead is used indirectly via
	 * `Psr\Http\Message\UriInterface::__toString`.
	 *
	 * PSR-7 UriInterface treats an empty component the same as a missing component as
	 * getQuery(), getFragment() etc. always return a string. This explains the slight
	 * difference to RFC 3986 Section 5.3.
	 *
	 * Another adjustment is that the authority separator is added even when the authority is missing/empty
	 * for the "file" scheme. This is because PHP stream functions like `file_get_contents` only work with
	 * `file:///myfile` but not with `file:/myfile` although they are equivalent according to RFC 3986. But
	 * `file:///` is the more common syntax for the file scheme anyway (Chrome for example redirects to
	 * that format).
	 *
	 * @param string $scheme passes parameter as scheme.
	 * @param string $authority passes parameter as authority.
	 * @param string $path passes parameter as path.
	 * @param string $query passes parameter as query.
	 * @param string $fragment passes parameter as fragment.
	 *
	 * @return string
	 *
	 * @link https://tools.ietf.org/html/rfc3986#section-5.3
	 */
	public static function composeComponents( $scheme, $authority, $path, $query, $fragment ) {
		$uri = '';

		// weak type checks to also accept null until we can add scalar type hints.
		if ( '' != $scheme ) {// WPCS: loose comparison ok.
			$uri .= $scheme . ':';
		}

		if ( '' != $authority || 'file' === $scheme ) {// WPCS: Loose comparison ok.
			$uri .= '//' . $authority;
		}

		$uri .= $path;

		if ( '' != $query ) {// WPCS: Loose comparison ok.
			$uri .= '?' . $query;
		}

		if ( '' != $fragment ) {// WPCS: Loose comparison ok.
			$uri .= '#' . $fragment;
		}

		return $uri;
	}

	/**
	 * Whether the URI has the default port of the current scheme.
	 *
	 * `Psr\Http\Message\UriInterface::getPort` may return null or the standard port. This method can be used
	 * independently of the implementation.
	 *
	 * @param UriInterface $uri passes parameter as uri.
	 *
	 * @return bool
	 */
	public static function isDefaultPort( UriInterface $uri ) {
		return $uri->getPort() === null
			|| ( isset( self::$defaultPorts[ $uri->getScheme() ] ) && $uri->getPort() === self::$defaultPorts[ $uri->getScheme() ] );// @codingStandardsIgnoreLine
	}

	/**
	 * Whether the URI is absolute, i.e. it has a scheme.
	 *
	 * An instance of UriInterface can either be an absolute URI or a relative reference. This method returns true
	 * if it is the former. An absolute URI has a scheme. A relative reference is used to express a URI relative
	 * to another URI, the base URI. Relative references can be divided into several forms:
	 * - network-path references, e.g. '//example.com/path'
	 * - absolute-path references, e.g. '/path'
	 * - relative-path references, e.g. 'subpath'
	 *
	 * @param UriInterface $uri passes parameter as uri.
	 *
	 * @return bool
	 * @see Uri::isNetworkPathReference
	 * @see Uri::isAbsolutePathReference
	 * @see Uri::isRelativePathReference
	 * @link https://tools.ietf.org/html/rfc3986#section-4
	 */
	public static function isAbsolute( UriInterface $uri ) {
		return $uri->getScheme() !== '';
	}

	/**
	 * Whether the URI is a network-path reference.
	 *
	 * A relative reference that begins with two slash characters is termed an network-path reference.
	 *
	 * @param UriInterface $uri passes parameter as uri.
	 *
	 * @return bool
	 * @link https://tools.ietf.org/html/rfc3986#section-4.2
	 */
	public static function isNetworkPathReference( UriInterface $uri ) {
		return $uri->getScheme() === '' && $uri->getAuthority() !== '';
	}

	/**
	 * Whether the URI is a absolute-path reference.
	 *
	 * A relative reference that begins with a single slash character is termed an absolute-path reference.
	 *
	 * @param UriInterface $uri passes parameter as uri.
	 *
	 * @return bool
	 * @link https://tools.ietf.org/html/rfc3986#section-4.2
	 */
	public static function isAbsolutePathReference( UriInterface $uri ) {
		return $uri->getScheme() === ''
			&& $uri->getAuthority() === ''
			&& isset( $uri->getPath()[0] )
			&& $uri->getPath()[0] === '/';// @codingStandardsIgnoreLine
	}

	/**
	 * Whether the URI is a relative-path reference.
	 *
	 * A relative reference that does not begin with a slash character is termed a relative-path reference.
	 *
	 * @param UriInterface $uri passes parameter as uri.
	 *
	 * @return bool
	 * @link https://tools.ietf.org/html/rfc3986#section-4.2
	 */
	public static function isRelativePathReference( UriInterface $uri ) {
		return $uri->getScheme() === ''
			&& $uri->getAuthority() === ''
			&& ( ! isset( $uri->getPath()[0] ) || $uri->getPath()[0] !== '/' );// @codingStandardsIgnoreLine
	}

	/**
	 * Whether the URI is a same-document reference.
	 *
	 * A same-document reference refers to a URI that is, aside from its fragment
	 * component, identical to the base URI. When no base URI is given, only an empty
	 * URI reference (apart from its fragment) is considered a same-document reference.
	 *
	 * @param UriInterface      $uri  The URI to check.
	 * @param UriInterface|null $base An optional base URI to compare against.
	 *
	 * @return bool
	 * @link https://tools.ietf.org/html/rfc3986#section-4.4
	 */
	public static function isSameDocumentReference( UriInterface $uri, UriInterface $base = null ) {
		if ( null !== $base ) {
			$uri = UriResolver::resolve( $base, $uri );

			return ( $uri->getScheme() === $base->getScheme() )
				&& ( $uri->getAuthority() === $base->getAuthority() )
				&& ( $uri->getPath() === $base->getPath() )
				&& ( $uri->getQuery() === $base->getQuery() );
		}

		return $uri->getScheme() === '' && $uri->getAuthority() === '' && $uri->getPath() === '' && $uri->getQuery() === '';
	}

	/**
	 * Removes dot segments from a path and returns the new path.
	 *
	 * @param string $path passes parameter as path.
	 *
	 * @return string
	 *
	 * @deprecated since version 1.4. Use UriResolver::removeDotSegments instead.
	 * @see UriResolver::removeDotSegments
	 */
	public static function removeDotSegments( $path ) {
		return UriResolver::removeDotSegments( $path );
	}

	/**
	 * Converts the relative URI into a new URI that is resolved against the base URI.
	 *
	 * @param UriInterface        $base Base URI.
	 * @param string|UriInterface $rel  Relative URI.
	 *
	 * @return UriInterface
	 *
	 * @deprecated since version 1.4. Use UriResolver::resolve instead.
	 * @see UriResolver::resolve
	 */
	public static function resolve( UriInterface $base, $rel ) {
		if ( ! ( $rel instanceof UriInterface ) ) {
			$rel = new self( $rel );
		}

		return UriResolver::resolve( $base, $rel );
	}

	/**
	 * Creates a new URI with a specific query string value removed.
	 *
	 * Any existing query string values that exactly match the provided key are
	 * removed.
	 *
	 * @param UriInterface $uri URI to use as a base.
	 * @param string       $key Query string key to remove.
	 *
	 * @return UriInterface
	 */
	public static function withoutQueryValue( UriInterface $uri, $key ) {
		$current = $uri->getQuery();
		if ( '' === $current ) {
			return $uri;
		}

		$decodedKey = rawurldecode( $key );// @codingStandardsIgnoreLine
		$result     = array_filter(
			explode( '&', $current ), function ( $part ) use ( $decodedKey ) {// @codingStandardsIgnoreLine
				return rawurldecode( explode( '=', $part )[0] ) !== $decodedKey;// @codingStandardsIgnoreLine
			}
		);

		return $uri->withQuery( implode( '&', $result ) );
	}

	/**
	 * Creates a new URI with a specific query string value.
	 *
	 * Any existing query string values that exactly match the provided key are
	 * removed and replaced with the given key value pair.
	 *
	 * A value of null will set the query string key without a value, e.g. "key"
	 * instead of "key=value".
	 *
	 * @param UriInterface $uri   URI to use as a base.
	 * @param string       $key   Key to set.
	 * @param string|null  $value Value to set.
	 *
	 * @return UriInterface
	 */
	public static function withQueryValue( UriInterface $uri, $key, $value ) {
		$current = $uri->getQuery();

		if ( '' === $current ) {
			$result = [];
		} else {
			$decodedKey = rawurldecode( $key );// @codingStandardsIgnoreLine
			$result     = array_filter(
				explode( '&', $current ), function ( $part ) use ( $decodedKey ) {// @codingStandardsIgnoreLine
					return rawurldecode( explode( '=', $part )[0] ) !== $decodedKey;// @codingStandardsIgnoreLine
				}
			);
		}

		// Query string separators ("=", "&") within the key or value need to be encoded
		// (while preventing double-encoding) before setting the query string. All other
		// chars that need percent-encoding will be encoded by withQuery().
		$key = strtr( $key, self::$replaceQuery );// @codingStandardsIgnoreLine

		if ( null !== $value ) {
			$result[] = $key . '=' . strtr( $value, self::$replaceQuery );// @codingStandardsIgnoreLine
		} else {
			$result[] = $key;
		}

		return $uri->withQuery( implode( '&', $result ) );
	}

	/**
	 * Creates a URI from a hash of `parse_url` components.
	 *
	 * @param array $parts passes parameter as parts.
	 *
	 * @return UriInterface
	 * @link http://php.net/manual/en/function.parse-url.php
	 *
	 * @throws \InvalidArgumentException If the components do not form a valid URI.
	 */
	public static function fromParts( array $parts ) {
		$uri = new self();
		$uri->applyParts( $parts );
		$uri->validateState();

		return $uri;
	}
	/**
	 * This function is getScheme.
	 */
	public function getScheme() {
		return $this->scheme;
	}
	/**
	 * This function is getAuthority.
	 */
	public function getAuthority() {
		$authority = $this->host;
		if ( $this->userInfo !== '' ) {// @codingStandardsIgnoreLine
			$authority = $this->userInfo . '@' . $authority;// @codingStandardsIgnoreLine
		}

		if ( $this->port !== null ) {// @codingStandardsIgnoreLine
			$authority .= ':' . $this->port;
		}

		return $authority;
	}
	/**
	 * This function is getUserInfo.
	 */
	public function getUserInfo() {
		return $this->userInfo;// @codingStandardsIgnoreLine
	}
	/**
	 * This function is getUserInfo.
	 */
	public function getHost() {
		return $this->host;
	}
	/**
	 * This function is getUserInfo.
	 */
	public function getPort() {
		return $this->port;
	}
	/**
	 * This function is getUserInfo.
	 */
	public function getPath() {
		return $this->path;
	}
	/**
	 * This function is getUserInfo.
	 */
	public function getQuery() {
		return $this->query;
	}
	/**
	 * This function is getUserInfo.
	 */
	public function getFragment() {
		return $this->fragment;
	}
	/**
	 * This function is withScheme.
	 *
	 * @param string $scheme passes parameter as scheme.
	 */
	public function withScheme( $scheme ) {
		$scheme = $this->filterScheme( $scheme );

		if ( $this->scheme === $scheme ) {
			return $this;
		}

		$new         = clone $this;
		$new->scheme = $scheme;
		$new->removeDefaultPort();
		$new->validateState();

		return $new;
	}
	/**
	 * This function is withScheme.
	 *
	 * @param string $user passes parameter as user.
	 * @param null   $password passes parameter as scheme.
	 */
	public function withUserInfo( $user, $password = null ) {
		$info = $user;
		if ( '' !== $password ) {
			$info .= ':' . $password;
		}

		if ( $this->userInfo === $info ) {// @codingStandardsIgnoreLine
			return $this;
		}

		$new           = clone $this;
		$new->userInfo = $info;// @codingStandardsIgnoreLine
		$new->validateState();

		return $new;
	}
	/**
	 * This function is withHost.
	 *
	 * @param string $host passes parameter as host.
	 */
	public function withHost( $host ) {
		$host = $this->filterHost( $host );

		if ( $this->host === $host ) {
			return $this;
		}

		$new       = clone $this;
		$new->host = $host;
		$new->validateState();

		return $new;
	}
	/**
	 * This function is withPort.
	 *
	 * @param string $port passes parameter as port.
	 */
	public function withPort( $port ) {
		$port = $this->filterPort( $port );

		if ( $this->port === $port ) {
			return $this;
		}

		$new       = clone $this;
		$new->port = $port;
		$new->removeDefaultPort();
		$new->validateState();

		return $new;
	}
	/**
	 * This function is withPath.
	 *
	 * @param string $path passes parameter as path.
	 */
	public function withPath( $path ) {
		$path = $this->filterPath( $path );

		if ( $this->path === $path ) {
			return $this;
		}

		$new       = clone $this;
		$new->path = $path;
		$new->validateState();

		return $new;
	}
	/**
	 * This function is withQuery.
	 *
	 * @param string $query passes parameter as query.
	 */
	public function withQuery( $query ) {
		$query = $this->filterQueryAndFragment( $query );

		if ( $this->query === $query ) {
			return $this;
		}

		$new        = clone $this;
		$new->query = $query;

		return $new;
	}
	/**
	 * This function is withFragment.
	 *
	 * @param string $fragment passes parameter as fragment.
	 */
	public function withFragment( $fragment ) {
		$fragment = $this->filterQueryAndFragment( $fragment );

		if ( $this->fragment === $fragment ) {
			return $this;
		}

		$new           = clone $this;
		$new->fragment = $fragment;

		return $new;
	}

	/**
	 * Apply parse_url parts to a URI.
	 *
	 * @param array $parts Array of parse_url parts to apply.
	 */
	private function applyParts( array $parts ) {
		$this->scheme   = isset( $parts['scheme'] )
			? $this->filterScheme( $parts['scheme'] )
			: '';
		$this->userInfo = isset( $parts['user'] ) ? $parts['user'] : '';// @codingStandardsIgnoreLine
		$this->host     = isset( $parts['host'] )
			? $this->filterHost( $parts['host'] )
			: '';
		$this->port     = isset( $parts['port'] )
			? $this->filterPort( $parts['port'] )
			: null;
		$this->path     = isset( $parts['path'] )
			? $this->filterPath( $parts['path'] )
			: '';
		$this->query    = isset( $parts['query'] )
			? $this->filterQueryAndFragment( $parts['query'] )
			: '';
		$this->fragment = isset( $parts['fragment'] )
			? $this->filterQueryAndFragment( $parts['fragment'] )
			: '';
		if ( isset( $parts['pass'] ) ) {
			$this->userInfo .= ':' . $parts['pass'];// @codingStandardsIgnoreLine
		}

		$this->removeDefaultPort();
	}

	/**
	 * This function is filterScheme.
	 *
	 * @param string $scheme passes parameter as scheme.
	 * @throws \InvalidArgumentException If the scheme is invalid.
	 */
	private function filterScheme( $scheme ) {
		if ( ! is_string( $scheme ) ) {
			throw new \InvalidArgumentException( 'Scheme must be a string' );
		}

		return strtolower( $scheme );
	}

	/**
	 * This function is filterHost.
	 *
	 * @param string $host passes parameter as host.
	 * @return string
	 *
	 * @throws \InvalidArgumentException If the host is invalid.
	 */
	private function filterHost( $host ) {
		if ( ! is_string( $host ) ) {
			throw new \InvalidArgumentException( 'Host must be a string' );
		}

		return strtolower( $host );
	}

	/**
	 * This function is filterPort.
	 *
	 * @param int|null $port passes parameter as port.
	 *
	 * @return int|null
	 *
	 * @throws \InvalidArgumentException If the port is invalid.
	 */
	private function filterPort( $port ) {
		if ( null === $port ) {
			return null;
		}

		$port = (int) $port;
		if ( 1 > $port || 0xffff < $port ) {
			throw new \InvalidArgumentException(
				sprintf( 'Invalid port: %d. Must be between 1 and 65535', $port )
			);
		}

		return $port;
	}
	/**
	 * This function is removeDefaultPort.
	 */
	private function removeDefaultPort() {
		if ( $this->port !== null && self::isDefaultPort( $this ) ) {// @codingStandardsIgnoreLine
			$this->port = null;
		}
	}

	/**
	 * Filters the path of a URI
	 *
	 * @param string $path passes parameter as path.
	 *
	 * @return string
	 *
	 * @throws \InvalidArgumentException If the path is invalid.
	 */
	private function filterPath( $path ) {
		if ( ! is_string( $path ) ) {
			throw new \InvalidArgumentException( 'Path must be a string' );
		}

		return preg_replace_callback(
			'/(?:[^' . self::$charUnreserved . self::$charSubDelims . '%:@\/]++|%(?![A-Fa-f0-9]{2}))/',// @codingStandardsIgnoreLine
			[ $this, 'rawurlencodeMatchZero' ],
			$path
		);
	}

	/**
	 * Filters the query string or fragment of a URI.
	 *
	 * @param string $str passes parameter as str.
	 *
	 * @return string
	 *
	 * @throws \InvalidArgumentException If the query or fragment is invalid.
	 */
	private function filterQueryAndFragment( $str ) {
		if ( ! is_string( $str ) ) {
			throw new \InvalidArgumentException( 'Query and fragment must be a string' );
		}

		return preg_replace_callback(
			'/(?:[^' . self::$charUnreserved . self::$charSubDelims . '%:@\/\?]++|%(?![A-Fa-f0-9]{2}))/',// @codingStandardsIgnoreLine
			[ $this, 'rawurlencodeMatchZero' ],
			$str
		);
	}
	/**
	 * This function is rawurlencodeMatchZero.
	 *
	 * @param array $match passes parameter as match.
	 */
	private function rawurlencodeMatchZero( array $match ) {
		return rawurlencode( $match[0] );
	}
	/**
	 * This function is validateState.
	 *
	 * @throws \InvalidArgumentException On error.
	 */
	private function validateState() {
		if ( '' === $this->host && ( 'http' === $this->scheme || 'https' === $this->scheme ) ) {
			$this->host = self::HTTP_DEFAULT_HOST;
		}

		if ( $this->getAuthority() === '' ) {
			if ( 0 === strpos( $this->path, '//' ) ) {
				throw new \InvalidArgumentException( 'The path of a URI without an authority must not start with two slashes "//"' );
			}
			if ( '' === $this->scheme && false !== strpos( explode( '/', $this->path, 2 )[0], ':' ) ) {
				throw new \InvalidArgumentException( 'A relative URI must not have a path beginning with a segment containing a colon' );
			}
		} elseif ( isset( $this->path[0] ) && '/' !== $this->path[0] ) {
			@trigger_error(// @codingStandardsIgnoreLine
				'The path of a URI with an authority must start with a slash "/" or be empty. Automagically fixing the URI ' .
				'by adding a leading slash to the path is deprecated since version 1.4 and will throw an exception instead.',
				E_USER_DEPRECATED
			);
			$this->path = '/' . $this->path;
		}
	}
}
