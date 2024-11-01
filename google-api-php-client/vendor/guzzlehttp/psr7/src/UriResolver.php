<?php // @codingStandardsIgnoreLine
/**
 * This file to resolve uri references.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/composer
 * @version 2.0.0
 */

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\UriInterface;

/**
 * Resolves a URI reference in the context of a base URI and the opposite way.
 *
 * @author Tobias Schultze
 *
 * @link https://tools.ietf.org/html/rfc3986#section-5
 */
final class UriResolver {

	/**
	 * Removes dot segments from a path and returns the new path.
	 *
	 * @param string $path passes parameter as path.
	 *
	 * @return string
	 * @link http://tools.ietf.org/html/rfc3986#section-5.2.4
	 */
	public static function removeDotSegments( $path ) {// @codingStandardsIgnoreLine
		if ( '' === $path || '/' === $path ) {
			return $path;
		}

		$results  = [];
		$segments = explode( '/', $path );
		foreach ( $segments as $segment ) {
			if ( '..' === $segment ) {
				array_pop( $results );
			} elseif ( '.' !== $segment ) {
				$results[] = $segment;
			}
		}

		$newPath = implode( '/', $results );// @codingStandardsIgnoreLine

		if ( $path[0] === '/' && ( ! isset( $newPath[0] ) || $newPath[0] !== '/' ) ) {// @codingStandardsIgnoreLine
			// Re-add the leading slash if necessary for cases like "/.."
			$newPath = '/' . $newPath;// @codingStandardsIgnoreLine
		} elseif ( $newPath !== '' && ( $segment === '.' || $segment === '..' ) ) {// @codingStandardsIgnoreLine
			// Add the trailing slash if necessary
			// If newPath is not empty, then $segment must be set and is the last segment from the foreach
			$newPath .= '/';// @codingStandardsIgnoreLine
		}

		return $newPath;// @codingStandardsIgnoreLine
	}

	/**
	 * Converts the relative URI into a new URI that is resolved against the base URI.
	 *
	 * @param UriInterface $base Base URI.
	 * @param UriInterface $rel  Relative URI.
	 *
	 * @return UriInterface
	 * @link http://tools.ietf.org/html/rfc3986#section-5.2
	 */
	public static function resolve( UriInterface $base, UriInterface $rel ) {
		if ( (string) $rel === '' ) {// @codingStandardsIgnoreLine
			// we can simply return the same base URI instance for this same-document reference.
			return $base;
		}

		if ( $rel->getScheme() != '' ) {// WPCS: Loose comparison ok.
			return $rel->withPath( self::removeDotSegments( $rel->getPath() ) );
		}

		if ( $rel->getAuthority() != '' ) {// WPCS: Loose comparison ok.
			$targetAuthority = $rel->getAuthority();// @codingStandardsIgnoreLine
			$targetPath      = self::removeDotSegments( $rel->getPath() );// @codingStandardsIgnoreLine
			$targetQuery     = $rel->getQuery();// @codingStandardsIgnoreLine
		} else {
			$targetAuthority = $base->getAuthority();// @codingStandardsIgnoreLine
			if ( $rel->getPath() === '' ) {
				$targetPath  = $base->getPath();// @codingStandardsIgnoreLine
				$targetQuery = $rel->getQuery() != '' ? $rel->getQuery() : $base->getQuery();// @codingStandardsIgnoreLine
			} else {
				if ( $rel->getPath()[0] === '/' ) {// @codingStandardsIgnoreLine
					$targetPath = $rel->getPath();// @codingStandardsIgnoreLine
				} else {
					if ( $targetAuthority != '' && $base->getPath() === '' ) {// @codingStandardsIgnoreLine
						$targetPath = '/' . $rel->getPath();// @codingStandardsIgnoreLine
					} else {
						$lastSlashPos = strrpos( $base->getPath(), '/' );// @codingStandardsIgnoreLine
						if ( $lastSlashPos === false ) {// @codingStandardsIgnoreLine
							$targetPath = $rel->getPath();// @codingStandardsIgnoreLine
						} else {
							$targetPath = substr( $base->getPath(), 0, $lastSlashPos + 1 ) . $rel->getPath();// @codingStandardsIgnoreLine
						}
					}
				}
				$targetPath  = self::removeDotSegments( $targetPath );// @codingStandardsIgnoreLine
				$targetQuery = $rel->getQuery();// @codingStandardsIgnoreLine
			}
		}

		return new Uri(
			Uri::composeComponents(
				$base->getScheme(),
				$targetAuthority,// @codingStandardsIgnoreLine
				$targetPath,// @codingStandardsIgnoreLine
				$targetQuery,// @codingStandardsIgnoreLine
				$rel->getFragment()
			)
		);
	}

	/**
	 * Returns the target URI as a relative reference from the base URI.
	 *
	 * This method is the counterpart to resolve():
	 *
	 *    (string) $target === (string) UriResolver::resolve($base, UriResolver::relativize($base, $target))
	 *
	 * One use-case is to use the current request URI as base URI and then generate relative links in your documents
	 * to reduce the document size or offer self-contained downloadable document archives.
	 *
	 *    $base = new Uri('http://example.com/a/b/');
	 *    echo UriResolver::relativize($base, new Uri('http://example.com/a/b/c'));  // prints 'c'.
	 *    echo UriResolver::relativize($base, new Uri('http://example.com/a/x/y'));  // prints '../x/y'.
	 *    echo UriResolver::relativize($base, new Uri('http://example.com/a/b/?q')); // prints '?q'.
	 *    echo UriResolver::relativize($base, new Uri('http://example.org/a/b/'));   // prints '//example.org/a/b/'.
	 *
	 * This method also accepts a target that is already relative and will try to relativize it further. Only a
	 * relative-path reference will be returned as-is.
	 *
	 *    echo UriResolver::relativize($base, new Uri('/a/b/c'));  // prints 'c' as well
	 *
	 * @param UriInterface $base   Base URI.
	 * @param UriInterface $target Target URI.
	 *
	 * @return UriInterface The relative URI reference
	 */
	public static function relativize( UriInterface $base, UriInterface $target ) {
		if ( $target->getScheme() !== '' &&
			( $base->getScheme() !== $target->getScheme() || $target->getAuthority() === '' && $base->getAuthority() !== '' )
		) {
			return $target;
		}

		if ( Uri::isRelativePathReference( $target ) ) {
			// As the target is already highly relative we return it as-is. It would be possible to resolve
			// the target with `$target = self::resolve($base, $target);` and then try make it more relative
			// by removing a duplicate query. But let's not do that automatically.
			return $target;
		}

		if ( $target->getAuthority() !== '' && $base->getAuthority() !== $target->getAuthority() ) {
			return $target->withScheme( '' );
		}

		// We must remove the path before removing the authority because if the path starts with two slashes, the URI
		// would turn invalid. And we also cannot set a relative path before removing the authority, as that is also
		// invalid.
		$emptyPathUri = $target->withScheme( '' )->withPath( '' )->withUserInfo( '' )->withPort( null )->withHost( '' );// @codingStandardsIgnoreLine

		if ( $base->getPath() !== $target->getPath() ) {
			return $emptyPathUri->withPath( self::getRelativePath( $base, $target ) );// @codingStandardsIgnoreLine
		}

		if ( $base->getQuery() === $target->getQuery() ) {
			// Only the target fragment is left. And it must be returned even if base and target fragment are the same.
			return $emptyPathUri->withQuery( '' );// @codingStandardsIgnoreLine
		}

		// If the base URI has a query but the target has none, we cannot return an empty path reference as it would
		// inherit the base query component when resolving.
		if ( $target->getQuery() === '' ) {
			$segments    = explode( '/', $target->getPath() );
			$lastSegment = end( $segments );// @codingStandardsIgnoreLine

			return $emptyPathUri->withPath( $lastSegment === '' ? './' : $lastSegment );// @codingStandardsIgnoreLine
		}

		return $emptyPathUri;// @codingStandardsIgnoreLine
	}

	private static function getRelativePath( UriInterface $base, UriInterface $target ) {// @codingStandardsIgnoreLine
		$sourceSegments = explode( '/', $base->getPath() );// @codingStandardsIgnoreLine
		$targetSegments = explode( '/', $target->getPath() );// @codingStandardsIgnoreLine
		array_pop( $sourceSegments );// @codingStandardsIgnoreLine
		$targetLastSegment = array_pop( $targetSegments );// @codingStandardsIgnoreLine
		foreach ( $sourceSegments as $i => $segment ) {// @codingStandardsIgnoreLine
			if ( isset( $targetSegments[ $i ] ) && $segment === $targetSegments[ $i ] ) {// @codingStandardsIgnoreLine
				unset( $sourceSegments[ $i ], $targetSegments[ $i ] );// @codingStandardsIgnoreLine
			} else {
				break;
			}
		}
		$targetSegments[] = $targetLastSegment;// @codingStandardsIgnoreLine
		$relativePath     = str_repeat( '../', count( $sourceSegments ) ) . implode( '/', $targetSegments );// @codingStandardsIgnoreLine

		// A reference to am empty last segment or an empty first sub-segment must be prefixed with "./".
		// This also applies to a segment with a colon character (e.g., "file:colon") that cannot be used
		// as the first segment of a relative-path reference, as it would be mistaken for a scheme name.
		if ( '' === $relativePath || false !== strpos( explode( '/', $relativePath, 2 )[0], ':' ) ) {// @codingStandardsIgnoreLine
			$relativePath = "./$relativePath";// @codingStandardsIgnoreLine
		} elseif ( '/' === $relativePath[0] ) {// @codingStandardsIgnoreLine
			if ( $base->getAuthority() != '' && $base->getPath() === '' ) {// WPCS: loose comparison ok.
				// In this case an extra slash is added by resolve() automatically. So we must not add one here.
				$relativePath = ".$relativePath";// @codingStandardsIgnoreLine
			} else {
				$relativePath = "./$relativePath";// @codingStandardsIgnoreLine
			}
		}

		return $relativePath;// @codingStandardsIgnoreLine
	}
	/**
	 * This function is __construct.
	 */
	private function __construct() {
		// cannot be instantiated.
	}
}
