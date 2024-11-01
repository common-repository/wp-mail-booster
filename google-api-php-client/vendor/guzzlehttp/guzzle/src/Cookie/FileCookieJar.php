<?php // @codingStandardsIgnoreLine.
/**
 * This Template is FileCookieJar.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client\vendor\guzzlehttp\guzzle\src\Cookie
 * @version 2.0.0
 */

namespace GuzzleHttp\Cookie;

/**
 * Persists non-session cookies using a JSON formatted file
 */
class FileCookieJar extends CookieJar {

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $filename .
	 */
	private $filename;

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $storeSessionCookies .
	 */
	private $storeSessionCookies; // @codingStandardsIgnoreLine.

	/**
	 * Create a new FileCookieJar object
	 *
	 * @param string $cookieFile        File to store the cookie data .
	 * @param bool   $storeSessionCookies Set to true to store session cookies
	 *                                    in the cookie jar.
	 *
	 * @throws \RuntimeException If the file cannot be found or created.
	 */
	public function __construct( $cookieFile, $storeSessionCookies = false ) { // @codingStandardsIgnoreLine.
		$this->filename            = $cookieFile; // @codingStandardsIgnoreLine.
		$this->storeSessionCookies = $storeSessionCookies; // @codingStandardsIgnoreLine.

		if ( file_exists( $cookieFile ) ) {// @codingStandardsIgnoreLine.
			$this->load( $cookieFile );// @codingStandardsIgnoreLine.
		}
	}

	/**
	 * Saves the file when shutting down
	 */
	public function __destruct() {
		$this->save( $this->filename );
	}

	/**
	 * Saves the cookies to a file.
	 *
	 * @param string $filename File to save.
	 * @throws \RuntimeException If the file cannot be found or created .
	 */
	public function save( $filename ) {
		$json = [];
		foreach ( $this as $cookie ) {
			if ( CookieJar::shouldPersist( $cookie, $this->storeSessionCookies ) ) { // @codingStandardsIgnoreLine.
				$json[] = $cookie->toArray();
			}
		}

		$jsonStr = \GuzzleHttp\json_encode( $json );// @codingStandardsIgnoreLine.
		if ( false === file_put_contents( $filename, $jsonStr ) ) {// @codingStandardsIgnoreLine.
			throw new \RuntimeException( "Unable to save file {$filename}" );
		}
	}

	/**
	 * Load cookies from a JSON formatted file.
	 *
	 * Old cookies are kept unless overwritten by newly loaded ones.
	 *
	 * @param string $filename Cookie file to load.
	 * @throws \RuntimeException If the file cannot be loaded.
	 */
	public function load( $filename ) {
		$json = file_get_contents( $filename );// @codingStandardsIgnoreLine.
		if ( false === $json ) {
			throw new \RuntimeException( "Unable to load file {$filename}" );
		} elseif ( '' === $json ) {
			return;
		}

		$data = \GuzzleHttp\json_decode( $json, true );
		if ( is_array( $data ) ) {
			foreach ( json_decode( $json, true ) as $cookie ) {
				$this->setCookie( new SetCookie( $cookie ) );// @codingStandardsIgnoreLine.
			}
		} elseif ( strlen( $data ) ) {
			throw new \RuntimeException( "Invalid cookie file: {$filename}" );
		}
	}
}
