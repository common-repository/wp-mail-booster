<?php // @codingStandardsIgnoreLine.
/**
 * This Template is SessionCookieJar.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client\vendor\guzzlehttp\guzzle\src\Cookie
 * @version 2.0.0
 */

namespace GuzzleHttp\Cookie;

/**
 * Persists cookies in the client session
 */
class SessionCookieJar extends CookieJar {

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $sessionKey  .
	 */
	private $sessionKey; // @codingStandardsIgnoreLine.

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $storeSessionCookies  .
	 */
	private $storeSessionCookies; // @codingStandardsIgnoreLine.

	/**
	 * Create a new SessionCookieJar object
	 *
	 * @param string $sessionKey        Session key name to store the cookie
	 *                                  data in session .
	 * @param bool   $storeSessionCookies Set to true to store session cookies
	 *                                    in the cookie jar.
	 */
	public function __construct( $sessionKey, $storeSessionCookies = false ) { // @codingStandardsIgnoreLine.
		$this->sessionKey          = $sessionKey; // @codingStandardsIgnoreLine.
		$this->storeSessionCookies = $storeSessionCookies; // @codingStandardsIgnoreLine.
		$this->load();
	}

	/**
	 * Saves cookies to session when shutting down
	 */
	public function __destruct() {
		$this->save();
	}

	/**
	 * Save cookies to the client session
	 */
	public function save() {
		$json = [];
		foreach ( $this as $cookie ) {
			if ( CookieJar::shouldPersist( $cookie, $this->storeSessionCookies ) ) { // @codingStandardsIgnoreLine.
				$json[] = $cookie->toArray();
			}
		}

		$_SESSION[ $this->sessionKey ] = wp_json_encode( $json );// @codingStandardsIgnoreLine.
	}

	/**
	 * Load the contents of the client session into the data array.
	 *
	 * @throws \RuntimeException Exception .
	 */
	protected function load() {
		if ( ! isset( $_SESSION[ $this->sessionKey ] ) ) { // @codingStandardsIgnoreLine.
			return;
		}
		$data = json_decode( $_SESSION[ $this->sessionKey ], true );// @codingStandardsIgnoreLine.
		if ( is_array( $data ) ) {
			foreach ( $data as $cookie ) {
				$this->setCookie( new SetCookie( $cookie ) );// @codingStandardsIgnoreLine.
			}
		} elseif ( strlen( $data ) ) {
			throw new \RuntimeException( 'Invalid cookie data' );
		}
	}
}
